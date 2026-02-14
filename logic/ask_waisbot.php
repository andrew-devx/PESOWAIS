<?php
header('Content-Type: application/json');
session_start();

$userMessage = isset($_POST['message']) ? trim($_POST['message']) : '';

if (empty($userMessage)) {
    echo json_encode(['reply' => 'Please say something!']);
    exit();
}

// Get user financial data if logged in
$userContext = '';
if (isset($_SESSION['user_id'])) {
    require_once dirname(__DIR__) . '/includes/connection.php';
    $user_id = $_SESSION['user_id'];
    
    // Fetch user's financial summary
    $financialData = getUserFinancialSummary($connection, $user_id);
    
    if ($financialData) {
        $userContext = "\n\nUSER FINANCIAL CONTEXT:\n";
        $userContext .= "Total Income (This Month): ₱" . number_format($financialData['total_income'], 2) . "\n";
        $userContext .= "Total Expenses (This Month): ₱" . number_format($financialData['total_expenses'], 2) . "\n";
        $userContext .= "Net Balance: ₱" . number_format($financialData['net_balance'], 2) . "\n";
        
        if ($financialData['total_loans'] > 0) {
            $userContext .= "Outstanding Loans: ₱" . number_format($financialData['total_loans'], 2) . "\n";
        }
        
        if ($financialData['active_goals'] > 0) {
            $userContext .= "Active Savings Goals: " . $financialData['active_goals'] . "\n";
        }
        
        if (!empty($financialData['top_expense_category'])) {
            $userContext .= "Biggest Expense Category: " . $financialData['top_expense_category'] . " (₱" . number_format($financialData['top_expense_amount'], 2) . ")\n";
        }
        
        $userContext .= "\nUse this data to give PERSONALIZED advice. Reference their actual spending patterns.";
    }
}

// Load API configuration from secure config file
require_once dirname(__DIR__) . '/includes/config.php';
$apiKey = GEMINI_API_KEY; 

// API Configuration
$model = 'gemini-2.5-flash';
$systemInstruction = "You are WaisBot, a street-smart and resourceful financial guide for Filipinos.
Your personality is 'Wais' (practical, savvy, and hates wasting money).
Speak in casual, friendly Taglish.
Keep answers under 4-5 sentences.
Focus on 'Tipid Tips', avoiding 'Budol' (impulse buying), and finding cheaper alternatives.
When given user financial data, analyze it and give specific advice based on their actual situation.
End your advice with a short motivating phrase like 'Kaya mo yan!' or 'Ipon lang!' or 'Wais spending!'";

$fullPrompt = $systemInstruction . $userContext . "\n\nUser asks: " . $userMessage;

$data = [
    "contents" => [
        [ "parts" => [ ["text" => $fullPrompt] ] ]
    ]
];

$reply = null;
$lastError = null;
$usedModel = 'gemini-2.5-flash';

$url = "https://generativelanguage.googleapis.com/v1/models/" . $model . ":generateContent?key=" . $apiKey;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response) {
    $decoded = json_decode($response, true);

    if (isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
        $reply = $decoded['candidates'][0]['content']['parts'][0]['text'];
    } elseif (isset($decoded['error']['message'])) {
        $lastError = $decoded['error']['message'];
    } else {
        $lastError = "Unexpected response from AI.";
    }
} else {
    $lastError = $curlError ? $curlError : "HTTP " . $httpCode . ". Check API key or network.";
}

if ($reply === null) {
    $reply = "AI Error: " . ($lastError ?? "Unknown error");
}

echo json_encode(['reply' => $reply]);

// Function to get user's financial summary
function getUserFinancialSummary($connection, $user_id) {
    $data = [
        'total_income' => 0,
        'total_expenses' => 0,
        'net_balance' => 0,
        'total_loans' => 0,
        'active_goals' => 0,
        'top_expense_category' => '',
        'top_expense_amount' => 0
    ];
    
    // Get current month's income and expenses
    $currentMonth = date('Y-m');
    
    // Total Income
    $stmt = $connection->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'Income' AND DATE_FORMAT(transaction_date, '%Y-%m') = ?");
    $stmt->bind_param("is", $user_id, $currentMonth);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $data['total_income'] = (float)$row['total'];
    }
    $stmt->close();
    
    // Total Expenses
    $stmt = $connection->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'Expense' AND DATE_FORMAT(transaction_date, '%Y-%m') = ?");
    $stmt->bind_param("is", $user_id, $currentMonth);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $data['total_expenses'] = (float)$row['total'];
    }
    $stmt->close();
    
    // Calculate net balance
    $data['net_balance'] = $data['total_income'] - $data['total_expenses'];
    
    // Total outstanding loans
    $stmt = $connection->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM loans WHERE user_id = ? AND status = 'Pending'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $data['total_loans'] = (float)$row['total'];
    }
    $stmt->close();
    
    // Count active goals
    $stmt = $connection->prepare("SELECT COUNT(*) as count FROM goals WHERE user_id = ? AND status = 'Active'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $data['active_goals'] = (int)$row['count'];
    }
    $stmt->close();
    
    // Top expense category this month
    $stmt = $connection->prepare("SELECT category, SUM(amount) as total FROM transactions WHERE user_id = ? AND type = 'Expense' AND DATE_FORMAT(transaction_date, '%Y-%m') = ? GROUP BY category ORDER BY total DESC LIMIT 1");
    $stmt->bind_param("is", $user_id, $currentMonth);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $data['top_expense_category'] = $row['category'];
        $data['top_expense_amount'] = (float)$row['total'];
    }
    $stmt->close();
    
    return $data;
}
?>