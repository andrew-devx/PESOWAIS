<?php
// logic/fetch_loans_data.php

if (!isset($_SESSION['user_id'])) {
    return;
}

$user_id = $_SESSION['user_id'];
$stmt = $connection->prepare("SELECT loan_id, person_name, type, amount, due_date, status, created_at FROM loans WHERE user_id = ? ORDER BY status ASC, due_date ASC, created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$loans = [];
while ($row = $result->fetch_assoc()) {
    $loans[] = $row;
}
$stmt->close();

// Calculate totals
$totalPayable = 0;
$totalReceivable = 0;
$pendingLoans = ['payable' => [], 'receivable' => []];
$paidLoans = [];

foreach ($loans as $loan) {
    if ($loan['status'] === 'Pending') {
        if ($loan['type'] === 'Payable') {
            $totalPayable += $loan['amount'];
            $pendingLoans['payable'][] = $loan;
        } else {
            $totalReceivable += $loan['amount'];
            $pendingLoans['receivable'][] = $loan;
        }
    } else {
        $paidLoans[] = $loan;
    }
}
$netBalance = $totalReceivable - $totalPayable;
?>
