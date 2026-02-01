<?php
header('Content-Type: application/json');
session_start();
include '../includes/connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // --- 1. ADD NEW LOAN ---
    if ($action === 'add_loan') {
        // FIX: Capture the 'type' (payable/receivable)
        $type = $_POST['type']; 
        
        // FIX: Match the HTML name attribute 'lender_name'
        $lender_name = trim($_POST['lender_name']); 
        
        $amount = floatval($_POST['amount']);
        $due_date = $_POST['due_date'];
        
        // FIX: Set default status to 'unpaid'
        $status = 'unpaid';

        // Update SQL to include 'type' and 'status'
        $stmt = $connection->prepare("INSERT INTO loans (user_id, type, lender_name, amount, due_date, status) VALUES (?, ?, ?, ?, ?, ?)");
        
        // "issdss" -> int, string, string, double, string, string
        $stmt->bind_param("issdss", $user_id, $type, $lender_name, $amount, $due_date, $status);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Loan record added!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "SQL Error: " . $stmt->error]);
        }
        $stmt->close();
    } 

    // --- 2. DELETE LOAN ---
    elseif ($action === 'delete_loan') {
        $loan_id = intval($_POST['loan_id']);

        $stmt = $connection->prepare("DELETE FROM loans WHERE loan_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $loan_id, $user_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Record deleted."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete."]);
        }
        $stmt->close();
    }

    // --- 3. NEW: UPDATE STATUS (Mark Paid/Collected) ---
    elseif ($action === 'update_status') {
        $loan_id = intval($_POST['loan_id']);
        $new_status = 'paid'; // We only toggle to paid for now

        $stmt = $connection->prepare("UPDATE loans SET status = ? WHERE loan_id = ? AND user_id = ?");
        $stmt->bind_param("sii", $new_status, $loan_id, $user_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Status updated!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Update failed."]);
        }
        $stmt->close();
    }

    else {
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
    }

    $connection->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>