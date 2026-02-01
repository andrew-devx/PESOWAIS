<?php
    header('Content-Type: application/json');
    include  '../includes/connection.php';
    include '../includes/auth_check.php';
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "error", "message" => "Unauthorized"]);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_SESSION['user_id'];
        $amount = floatval($_POST['amount']);
        $type = $_POST['type'];
        $category = $_POST['category'];
        $description = trim($_POST['description']);
        $date = $_POST['date'];

        $stmt = $connection->prepare("INSERT INTO transactions (user_id, amount, type, category, description, transaction_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("idssss", $user_id, $amount, $type, $category, $description, $date);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Transaction added successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to add transaction"]);
        }

        $stmt->close();
        $connection->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    }
?>