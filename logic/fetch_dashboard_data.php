<?php
// logic/fetch_dashboard_data.php
// This file handles fetching all data required for the dashboard.

if (!isset($_SESSION['user_id'])) {
    // Should be handled by auth_check.php, but just in case
    return;
}

$user_id = $_SESSION['user_id'];

// 1. Financial totals
$incomeStmt = $connection->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM transactions WHERE user_id = ? AND type = 'Income'");
$incomeStmt->bind_param("i", $user_id);
$incomeStmt->execute();
$incomeResult = $incomeStmt->get_result()->fetch_assoc();
$totalIncome = (float) ($incomeResult['total'] ?? 0);
$incomeStmt->close();

$expenseStmt = $connection->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM transactions WHERE user_id = ? AND type = 'Expense'");
$expenseStmt->bind_param("i", $user_id);
$expenseStmt->execute();
$expenseResult = $expenseStmt->get_result()->fetch_assoc();
$totalExpenses = (float) ($expenseResult['total'] ?? 0);
$expenseStmt->close();

$current_balance = $totalIncome - $totalExpenses;

// 2. Average daily spending (last 30 days)
$avgStmt = $connection->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM transactions WHERE user_id = ? AND type = 'Expense' AND transaction_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
$avgStmt->bind_param("i", $user_id);
$avgStmt->execute();
$avgResult = $avgStmt->get_result()->fetch_assoc();
$last30Expenses = (float) ($avgResult['total'] ?? 0);
$avgStmt->close();

$averageDailySpending = $last30Expenses > 0 ? ($last30Expenses / 30) : 0;

$financialData = [
    'currentBalance' => $current_balance,
    'totalIncome' => $totalIncome,
    'totalExpenses' => $totalExpenses,
    'averageDailySpending' => $averageDailySpending,
];

// 3. Calculate safe days
$safeDays = ($financialData['currentBalance'] > 0 && $financialData['averageDailySpending'] > 0)
    ? floor($financialData['currentBalance'] / $financialData['averageDailySpending'])
    : 0;

// 4. Recent transactions
$recentTransactions = [];
$txnStmt = $connection->prepare("SELECT transaction_id, transaction_date, description, category, amount, type FROM transactions WHERE user_id = ? ORDER BY transaction_id DESC LIMIT 8");
$txnStmt->bind_param("i", $user_id);
$txnStmt->execute();
$txnResult = $txnStmt->get_result();
while ($row = $txnResult->fetch_assoc()) {
    $recentTransactions[] = $row;
}
$txnStmt->close();

// 5. Utang Tracker (Debts/Loans)
$utangData = [];
$utangStmt = $connection->prepare("SELECT person_name, amount, paid_amount, due_date FROM loans WHERE user_id = ? AND type = 'Payable' AND status = 'Pending' ORDER BY due_date ASC");
$utangStmt->bind_param("i", $user_id);
$utangStmt->execute();
$utangResult = $utangStmt->get_result();
while ($row = $utangResult->fetch_assoc()) {
    $remainingBalance = (float) $row['amount'] - (float) $row['paid_amount'];
    $utangData[] = [
        'creditor' => $row['person_name'],
        'amount' => $remainingBalance,
        'dueDate' => $row['due_date'],
    ];
}
$utangStmt->close();
$totalUtang = array_sum(array_column($utangData, 'amount'));

// 6. Savings Goals
$savingsGoals = [];
$goalStmt = $connection->prepare("SELECT goal_name, current_amount, target_amount, status FROM goals WHERE user_id = ? AND status IN ('Active', 'Achieved') ORDER BY created_at DESC");
$goalStmt->bind_param("i", $user_id);
$goalStmt->execute();
$goalResult = $goalStmt->get_result();
while ($row = $goalResult->fetch_assoc()) {
    $savingsGoals[] = [
        'name' => $row['goal_name'],
        'saved' => (float) $row['current_amount'],
        'target' => (float) $row['target_amount'],
        'status' => $row['status'],
    ];
}
$goalStmt->close();

// 7. Budget vs Actual
$budgetData = [];
$budgetStmt = $connection->prepare("SELECT category, amount FROM budgets WHERE user_id = ? ORDER BY category ASC");
$budgetStmt->bind_param("i", $user_id);
$budgetStmt->execute();
$budgetResult = $budgetStmt->get_result();

while ($row = $budgetResult->fetch_assoc()) {
    $category = $row['category'];
    $budgetAmount = (float) $row['amount'];
    
    // Get spending for this category (this month)
    $spendingStmt = $connection->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM transactions WHERE user_id = ? AND category = ? AND type = 'Expense' AND MONTH(transaction_date) = MONTH(CURDATE()) AND YEAR(transaction_date) = YEAR(CURDATE())");
    $spendingStmt->bind_param("is", $user_id, $category);
    $spendingStmt->execute();
    $spendingResult = $spendingStmt->get_result()->fetch_assoc();
    $spent = (float) ($spendingResult['total'] ?? 0);
    $spendingStmt->close();
    
    $percentage = $budgetAmount > 0 ? ($spent / $budgetAmount) * 100 : 0;
    $color = $percentage >= 80 ? 'bg-red-400' : 'bg-green-400';
    
    $budgetData[] = [
        'category' => $category,
        'spent' => $spent,
        'budget' => $budgetAmount,
        'percentage' => $percentage,
        'color' => $color,
    ];
}
$budgetStmt->close();

// 8. Recurring subscriptions
$subscriptions = [];
$subStmt = $connection->prepare("SELECT service_name, amount, due_day, status, last_payment_date FROM subscriptions WHERE user_id = ? ORDER BY created_at DESC");
$subStmt->bind_param("i", $user_id);
$subStmt->execute();
$subResult = $subStmt->get_result();
while ($row = $subResult->fetch_assoc()) {
    $subscriptions[] = [
        'name' => $row['service_name'],
        'amount' => (float) $row['amount'],
        'dueDate' => (int) $row['due_day'],
        'status' => $row['status'],
        'last_payment_date' => $row['last_payment_date'],
    ];
}
$subStmt->close();

// 9. Mood Meter Logic & Balance Status
$spendingPercentage = $totalIncome > 0 ? ($totalExpenses / $totalIncome) * 100 : 0;
$balanceStatus = $current_balance >= 0 ? 'Positive' : 'Negative';

if ($spendingPercentage <= 40) {
    // Zone A: Good (0% - 40%)
    $mood = [
        'emoji' => '😎',
        'text' => 'Chill ka lang, wais tayo.',
        'color' => 'from-green-100 to-green-50',
        'borderColor' => 'border-green-300',
        'textColor' => 'text-green-900',
        'badgeColor' => 'bg-green-200 text-green-800',
        'insight' => 'You\'re spending smart! Keep it up.'
    ];
} elseif ($spendingPercentage <= 70) {
    // Zone B: Warning (41% - 70%)
    $mood = [
        'emoji' => '🤔',
        'text' => 'Huy, Hinay-hinay Lang!',
        'color' => 'from-amber-100 to-amber-50',
        'borderColor' => 'border-amber-300',
        'textColor' => 'text-amber-900',
        'badgeColor' => 'bg-amber-200 text-amber-800',
        'insight' => 'Monitor your spending closely.'
    ];
} elseif ($spendingPercentage <= 90) {
    // Zone C: Danger (71% - 90%)
    $mood = [
        'emoji' => '😨',
        'text' => 'Tigil na, please.',
        'color' => 'from-orange-100 to-orange-50',
        'borderColor' => 'border-orange-300',
        'textColor' => 'text-orange-900',
        'badgeColor' => 'bg-orange-200 text-orange-800',
        'insight' => 'You\'re overspending! Cut back now.'
    ];
} else {
    // Zone D: Critical (91% - 100%+)
    $mood = [
        'emoji' => '💀',
        'text' => 'Nganga na next week.',
        'color' => 'from-red-100 to-red-50',
        'borderColor' => 'border-red-300',
        'textColor' => 'text-red-900',
        'badgeColor' => 'bg-red-200 text-red-800',
        'insight' => 'Nganga na next week.'
    ];
}
?>
