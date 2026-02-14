<?php
// logic/manage_subscriptions.php
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
    $service_name = trim($_POST['service_name'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $due_day = intval($_POST['due_day'] ?? 0);
    $status = $_POST['status'] ?? 'Active';
    $last_payment_date = !empty($_POST['last_payment_date']) ? $_POST['last_payment_date'] : null;

    if ($service_name === '') {
        sendResponse("error", "Service name is required", 400);
    }
    if ($amount <= 0) {
        sendResponse("error", "Amount must be greater than zero", 400);
    }
    if ($due_day < 1 || $due_day > 31) {
        sendResponse("error", "Billing day must be between 1 and 31", 400);
    }
    if ($status !== 'Active' && $status !== 'Inactive') {
        $status = 'Active';
    }

    $stmt = $connection->prepare("INSERT INTO subscriptions (user_id, service_name, amount, due_day, status, last_payment_date) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        sendResponse("error", "Database prepare error", 500);
    }

    $stmt->bind_param("isdiss", $user_id, $service_name, $amount, $due_day, $status, $last_payment_date);

    if ($stmt->execute()) {
        $stmt->close();
        $connection->close();
        sendResponse("success", "Subscription added successfully.");
    }

    $stmt->close();
    $connection->close();
    sendResponse("error", "Failed to add subscription", 500);
}

if ($action === 'update') {
    $service_name_original = trim($_POST['service_name_original'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $due_day = intval($_POST['due_day'] ?? 0);
    $status = $_POST['status'] ?? 'Active';
    $last_payment_date = !empty($_POST['last_payment_date']) ? $_POST['last_payment_date'] : null;

    if ($service_name_original === '') {
        sendResponse("error", "Service name is required", 400);
    }
    if ($amount <= 0) {
        sendResponse("error", "Amount must be greater than zero", 400);
    }
    if ($due_day < 1 || $due_day > 31) {
        sendResponse("error", "Billing day must be between 1 and 31", 400);
    }
    if ($status !== 'Active' && $status !== 'Inactive') {
        $status = 'Active';
    }

    $stmt = $connection->prepare("UPDATE subscriptions SET amount = ?, due_day = ?, status = ?, last_payment_date = ? WHERE service_name = ? AND user_id = ?");
    if (!$stmt) {
        sendResponse("error", "Database prepare error", 500);
    }

    $stmt->bind_param("disssi", $amount, $due_day, $status, $last_payment_date, $service_name_original, $user_id);

    if ($stmt->execute()) {
        $stmt->close();
        $connection->close();
        sendResponse("success", "Subscription updated successfully.");
    }

    $stmt->close();
    $connection->close();
    sendResponse("error", "Failed to update subscription", 500);
}

if ($action === 'delete') {
    $service_name = trim($_POST['service_name'] ?? '');

    if ($service_name === '') {
        sendResponse("error", "Service name is required", 400);
    }

    $stmt = $connection->prepare("DELETE FROM subscriptions WHERE service_name = ? AND user_id = ?");
    if (!$stmt) {
        sendResponse("error", "Database prepare error", 500);
    }

    $stmt->bind_param("si", $service_name, $user_id);

    if ($stmt->execute()) {
        $stmt->close();
        $connection->close();
        sendResponse("success", "Subscription deleted successfully.");
    }

    $stmt->close();
    $connection->close();
    sendResponse("error", "Failed to delete subscription", 500);
}

$connection->close();
sendResponse("error", "Invalid action parameter", 400);
?>

