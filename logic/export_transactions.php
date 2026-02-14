<?php
// logic/export_transactions.php
session_start();
include '../includes/connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$range = $_GET['range'] ?? 'monthly';
$startDate = $_GET['start'] ?? '';
$endDate = $_GET['end'] ?? '';
$category = $_GET['category'] ?? '';

// Build Query
$query = "SELECT transaction_date, description, category, amount, type FROM transactions WHERE user_id = ?";
$params = [$user_id];
$types = "i";

// Date Filtering
if ($range === 'custom' && !empty($startDate) && !empty($endDate)) {
    $query .= " AND transaction_date BETWEEN ? AND ?";
    $params[] = $startDate;
    $params[] = $endDate;
    $types .= "ss";
} elseif ($range === 'weekly') {
    $query .= " AND transaction_date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
} elseif ($range === 'monthly') {
    $query .= " AND MONTH(transaction_date) = MONTH(CURDATE()) AND YEAR(transaction_date) = YEAR(CURDATE())";
} elseif ($range === 'yearly') {
    $query .= " AND YEAR(transaction_date) = YEAR(CURDATE())";
}

// Category Filtering
if (!empty($category)) {
    $query .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

$query .= " ORDER BY transaction_date DESC";

$stmt = $connection->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Set Headers for Download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="transactions_export_' . date('Y-m-d') . '.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Add CSV Headers
fputcsv($output, ['Date', 'Description', 'Category', 'Amount', 'Type']);

// Add Rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['transaction_date'],
        $row['description'],
        $row['category'],
        $row['amount'],
        $row['type']
    ]);
}

fclose($output);
exit();
?>
