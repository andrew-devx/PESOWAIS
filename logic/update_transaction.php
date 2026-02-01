<?php
header('Content-Type: application/json');
include '../includes/connection.php';
include '../includes/auth_check.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // --- UPDATE TRANSACTION ---
    if ($action === 'update') {
        $transaction_id = intval($_POST['transaction_id']);
        $category = trim($_POST['category']);
        $amount = floatval($_POST['amount']);
        $description = trim($_POST['description']);
        $date = $_POST['date'] . ' ' . date('H:i:s'); // Add current time to date

        $stmt = $connection->prepare("UPDATE transactions SET category = ?, amount = ?, description = ?, transaction_date = ? WHERE transaction_id = ? AND user_id = ?");
        $stmt->bind_param("sdsii", $category, $amount, $description, $date, $transaction_id, $user_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Transaction updated successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update transaction: " . $stmt->error]);
        }
        $stmt->close();
    }

    // --- DELETE TRANSACTION ---
    elseif ($action === 'delete') {
        $transaction_id = intval($_POST['transaction_id']);

        $stmt = $connection->prepare("DELETE FROM transactions WHERE transaction_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $transaction_id, $user_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Transaction deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete transaction"]);
        }
        $stmt->close();
    }

    // --- INVALID ACTION ---
    else {
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

$connection->close();
?>
