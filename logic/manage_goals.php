<?php
// logic/manage_goals.php
ob_start();
header('Content-Type: application/json');

error_reporting(0);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function sendResponse($status, $message, $code = 200) {
    ob_end_clean();
    http_response_code($code);
    echo json_encode(["status" => $status, "message" => $message]);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    sendResponse("error", "Unauthorized", 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse("error", "Invalid Request Method", 405);
}

require_once dirname(__DIR__) . '/includes/constants.php';
$connection = @new mysqli($hostname, $username, $password, $database);

if ($connection->connect_error) {
    sendResponse("error", "Database connection failed", 500);
}

$user_id = intval($_SESSION['user_id']);
$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $goal_name = trim($_POST['goal_name'] ?? '');
    $target_amount = floatval($_POST['target_amount'] ?? 0);
    $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
    $current_amount = isset($_POST['current_amount']) ? floatval($_POST['current_amount']) : 0;
    $status = 'Active';

    if ($goal_name === '') {
        sendResponse("error", "Goal name is required", 400);
    }
    if ($target_amount <= 0) {
        sendResponse("error", "Target amount must be greater than zero", 400);
    }

    $stmt = $connection->prepare("INSERT INTO goals (user_id, goal_name, target_amount, current_amount, deadline, status) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        sendResponse("error", "Database prepare error", 500);
    }

    $stmt->bind_param("isddss", $user_id, $goal_name, $target_amount, $current_amount, $deadline, $status);

    if ($stmt->execute()) {
        $stmt->close();
        $connection->close();
        sendResponse("success", "New goal set! Kayang-kaya yan.");
    }

    $stmt->close();
    $connection->close();
    sendResponse("error", "Failed to create goal", 500);
}

if ($action === 'add_money') {
    $goal_id = intval($_POST['goal_id'] ?? 0);
    $amount_to_add = floatval($_POST['amount'] ?? 0);

    if ($goal_id <= 0 || $amount_to_add <= 0) {
        sendResponse("error", "Invalid goal or amount", 400);
    }

    $checkQuery = $connection->prepare("SELECT current_amount, target_amount FROM goals WHERE goal_id = ? AND user_id = ?");
    if (!$checkQuery) {
        sendResponse("error", "Database prepare error", 500);
    }

    $checkQuery->bind_param("ii", $goal_id, $user_id);
    $checkQuery->execute();
    $result = $checkQuery->get_result();

    if ($row = $result->fetch_assoc()) {
        $new_total = $row['current_amount'] + $amount_to_add;
        $target = $row['target_amount'];
        $new_status = ($new_total >= $target) ? 'Achieved' : 'Active';

        $updateStmt = $connection->prepare("UPDATE goals SET current_amount = ?, status = ? WHERE goal_id = ? AND user_id = ?");
        if (!$updateStmt) {
            sendResponse("error", "Database prepare error", 500);
        }

        $updateStmt->bind_param("dsii", $new_total, $new_status, $goal_id, $user_id);

        if ($updateStmt->execute()) {
            $msg = ($new_status === 'Achieved') ? "Congrats! Goal Reached! 🎉" : "Savings added! Keep going.";
            $updateStmt->close();
            $checkQuery->close();
            $connection->close();
            sendResponse("success", $msg);
        }

        $updateStmt->close();
        $checkQuery->close();
        $connection->close();
        sendResponse("error", "Update failed", 500);
    }

    $checkQuery->close();
    $connection->close();
    sendResponse("error", "Goal not found", 404);
}

if ($action === 'delete') {
    $goal_id = intval($_POST['goal_id'] ?? 0);

    if ($goal_id <= 0) {
        sendResponse("error", "Invalid goal", 400);
    }

    $stmt = $connection->prepare("DELETE FROM goals WHERE goal_id = ? AND user_id = ?");
    if (!$stmt) {
        sendResponse("error", "Database prepare error", 500);
    }

    $stmt->bind_param("ii", $goal_id, $user_id);

    if ($stmt->execute()) {
        $stmt->close();
        $connection->close();
        sendResponse("success", "Goal deleted.");
    }

    $stmt->close();
    $connection->close();
    sendResponse("error", "Could not delete goal.", 500);
}

if ($action === 'update') {
    $goal_id = intval($_POST['goal_id'] ?? 0);
    $goal_name = trim($_POST['goal_name'] ?? '');
    $target_amount = floatval($_POST['target_amount'] ?? 0);
    $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;

    if ($goal_id <= 0) {
        sendResponse("error", "Invalid goal", 400);
    }

    if ($goal_name === '') {
        sendResponse("error", "Goal name is required", 400);
    }

    if ($target_amount <= 0) {
        sendResponse("error", "Target amount must be greater than zero", 400);
    }

    $stmt = $connection->prepare("UPDATE goals SET goal_name = ?, target_amount = ?, deadline = ? WHERE goal_id = ? AND user_id = ?");
    if (!$stmt) {
        sendResponse("error", "Database prepare error", 500);
    }

    $stmt->bind_param("sdsii", $goal_name, $target_amount, $deadline, $goal_id, $user_id);

    if ($stmt->execute()) {
        $stmt->close();
        $connection->close();
        sendResponse("success", "Goal updated successfully!");
    }

    $stmt->close();
    $connection->close();
    sendResponse("error", "Failed to update goal.", 500);
}

$connection->close();
sendResponse("error", "Invalid action parameter", 400);
?>
   