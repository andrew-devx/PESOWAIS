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

    // --- CREATE/UPDATE BUDGET ---
    if ($action === 'set') {
        $category = trim($_POST['category']);
        $amount = floatval($_POST['amount']);

        // Check if budget exists
        $checkQuery = $connection->prepare("SELECT budget_id FROM budgets WHERE user_id = ? AND category = ?");
        $checkQuery->bind_param("is", $user_id, $category);
        $checkQuery->execute();
        $result = $checkQuery->get_result();
        $checkQuery->close();

        if ($result->num_rows > 0) {
            // Update existing budget
            $stmt = $connection->prepare("UPDATE budgets SET amount = ? WHERE user_id = ? AND category = ?");
            $stmt->bind_param("dis", $amount, $user_id, $category);
        } else {
            // Insert new budget
            $stmt = $connection->prepare("INSERT INTO budgets (user_id, category, amount) VALUES (?, ?, ?)");
            $stmt->bind_param("isd", $user_id, $category, $amount);
        }

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Budget set successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to set budget"]);
        }
        $stmt->close();
    }

    // --- DELETE BUDGET ---
    elseif ($action === 'delete') {
        $category = trim($_POST['category']);

        $stmt = $connection->prepare("DELETE FROM budgets WHERE user_id = ? AND category = ?");
        $stmt->bind_param("is", $user_id, $category);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Budget deleted"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete budget"]);
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
