<?php
    require_once dirname(__DIR__) . '/includes/connection.php';
    require_once dirname(__DIR__) . '/includes/auth_check.php';

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "error", "message" => "Unauthorized"]);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_SESSION['user_id'];
        $transaction_id = intval($_POST['transaction_id']);

        $stmt = $connection->prepare("DELETE FROM transactions WHERE transaction_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $transaction_id, $user_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(["status" => "success", "message" => "Transaction deleted successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Transaction not found or unauthorized"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete transaction"]);
        }

        $stmt->close();
        $connection->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    }
?>