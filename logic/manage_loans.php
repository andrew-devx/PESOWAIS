<?php
header('Content-Type: application/json');
session_start();
require_once dirname(__DIR__) . '/includes/connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // --- 0. FETCH LOANS FOR USER (AJAX) ---
    if ($action === 'fetch_loans') {
        $stmt = $connection->prepare("SELECT loan_id, person_name, type, amount, due_date, status FROM loans WHERE user_id = ? ORDER BY status ASC, due_date ASC, created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $loans = [];
        while ($row = $result->fetch_assoc()) {
            $loans[] = $row;
        }
        echo json_encode(["loans" => $loans]);
        $stmt->close();
        $connection->close();
        exit();
    }

    // --- 1. ADD NEW LOAN ---
    if ($action === 'add_loan') {
        // FIX: Capture the 'type' (payable/receivable)
        $type = $_POST['type']; 
        
        // Use 'person_name' to match DB schema
        $person_name = trim($_POST['person_name']); 
        
        $amount = floatval($_POST['amount']);
        $due_date = $_POST['due_date'];
        
        // Set default status to 'Pending' (matches ENUM)
        $status = 'Pending';

        // Update SQL to use 'person_name' and match DB
        $stmt = $connection->prepare("INSERT INTO loans (user_id, type, person_name, amount, due_date, status) VALUES (?, ?, ?, ?, ?, ?)");
        // "issdss" -> int, string, string, double, string, string
        $stmt->bind_param("issdss", $user_id, $type, $person_name, $amount, $due_date, $status);

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

    // --- 3. NEW: ADD PAYMENT (Hulugan) ---
    elseif ($action === 'add_payment') {
        $loan_id = intval($_POST['loan_id']);
        $payment_amount = floatval($_POST['payment_amount']);

        if ($payment_amount <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid payment amount."]);
            exit();
        }

        // 1. Get current loan details
        $stmt = $connection->prepare("SELECT amount, paid_amount FROM loans WHERE loan_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $loan_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $loan = $result->fetch_assoc();
        $stmt->close();

        if (!$loan) {
            echo json_encode(["status" => "error", "message" => "Loan not found."]);
            exit();
        }

        $current_paid = floatval($loan['paid_amount']);
        $total_amount = floatval($loan['amount']);
        $new_paid = $current_paid + $payment_amount;
        
        // Determine status
        // Allow overpayment? For now, cap it or just mark as Paid if >= total.
        $new_status = ($new_paid >= $total_amount) ? 'Paid' : 'Pending';

        // 2. Update loan record
        $updateStmt = $connection->prepare("UPDATE loans SET paid_amount = ?, status = ? WHERE loan_id = ? AND user_id = ?");
        $updateStmt->bind_param("dsii", $new_paid, $new_status, $loan_id, $user_id);

        if ($updateStmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Payment added!", "new_balance" => $total_amount - $new_paid, "status_code" => $new_status]);
        } else {
            echo json_encode(["status" => "error", "message" => "Update failed."]);
        }
        $updateStmt->close();
    }

    // --- 4. UPDATE LOAN DETAILS ---
    elseif ($action === 'update_loan') {
        $loan_id = intval($_POST['loan_id']);
        $person_name = trim($_POST['person_name']);
        $type = $_POST['type'];
        $amount_raw = $_POST['amount'];
        $amount = floatval($amount_raw);
        $due_date = $_POST['due_date'];

        // Debug: Log received values
        error_log("Update loan - Raw Amount: '$amount_raw', Converted: $amount");

        if (empty($amount_raw) || $amount <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid amount. Received: '$amount_raw', Converted to: $amount"]);
            exit();
        }

        $stmt = $connection->prepare("UPDATE loans SET person_name = ?, type = ?, amount = ?, due_date = ? WHERE loan_id = ? AND user_id = ?");
        $stmt->bind_param("ssdsii", $person_name, $type, $amount, $due_date, $loan_id, $user_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Loan updated successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Update failed: " . $stmt->error]);
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