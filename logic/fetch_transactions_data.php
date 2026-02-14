<?php
// logic/fetch_transactions_data.php

if (!isset($_SESSION['user_id'])) {
    return;
}

$user_id = $_SESSION['user_id'];

// Get all transactions for the user
$allTransactions = [];
$txnStmt = $connection->prepare("SELECT transaction_id, transaction_date, description, category, amount, type FROM transactions WHERE user_id = ? ORDER BY transaction_id DESC");
$txnStmt->bind_param("i", $user_id);
$txnStmt->execute();
$txnResult = $txnStmt->get_result();
while ($row = $txnResult->fetch_assoc()) {
    $allTransactions[] = $row;
}
$txnStmt->close();

// Get category breakdown for doughnut chart
$categoryBreakdown = [];
$categoryStmt = $connection->prepare("SELECT category, SUM(amount) as total FROM transactions WHERE user_id = ? AND type = 'Expense' GROUP BY category ORDER BY total DESC");
$categoryStmt->bind_param("i", $user_id);
$categoryStmt->execute();
$categoryResult = $categoryStmt->get_result();
while ($row = $categoryResult->fetch_assoc()) {
    $row['category'] = trim($row['category']);
    $categoryBreakdown[] = $row;
}
$categoryStmt->close();

// Get unique categories for filter
$categories = array_unique(array_column($allTransactions, 'category'));
$categories = array_map('trim', $categories);
sort($categories);

// Financial totals
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
?>
