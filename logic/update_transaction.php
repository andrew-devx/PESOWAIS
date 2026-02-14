<?php
// ============================================================================
// Transaction Update/Delete Handler
// ============================================================================

ob_start();
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================================
// Helper Function: Send JSON Response
// ============================================================================

function sendResponse($status, $message, $code = 200) {
    ob_end_clean();
    http_response_code($code);
    echo json_encode(["status" => $status, "message" => $message]);
    exit();
}

// ============================================================================
// Validation & Setup
// ============================================================================

try {
    // Verify authentication
    if (!isset($_SESSION['user_id'])) {
        sendResponse("error", "Unauthorized", 401);
    }

    // Verify POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse("error", "Invalid request method", 405);
    }

    // Get and validate action
    $action = isset($_POST['action']) ? trim($_POST['action']) : '';
    if (empty($action)) {
        sendResponse("error", "Action is required", 400);
    }

    // Setup database connection
    require_once dirname(__DIR__) . '/includes/constants.php';
    $connection = @new mysqli($hostname, $username, $password, $database);

    if ($connection->connect_error) {
        sendResponse("error", "Database connection failed", 500);
    }

    $user_id = intval($_SESSION['user_id']);

    // ========================================================================
    // Handle Update Action
    // ========================================================================

    if ($action === 'update') {
        // Extract and validate POST data
        $transaction_id = isset($_POST['transaction_id']) ? intval($_POST['transaction_id']) : 0;
        $category = isset($_POST['category']) ? trim($_POST['category']) : '';
        $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';

        // Validate all required fields
        if ($transaction_id <= 0) {
            sendResponse("error", "Invalid transaction ID", 400);
        }
        if (empty($category)) {
            sendResponse("error", "Category is required", 400);
        }
        if ($amount <= 0) {
            sendResponse("error", "Amount must be greater than zero", 400);
        }
        if (empty($description)) {
            sendResponse("error", "Description is required", 400);
        }

        // Update database
        $query = "UPDATE transactions SET category = ?, amount = ?, description = ?, transaction_date = NOW() WHERE transaction_id = ? AND user_id = ?";
        $stmt = $connection->prepare($query);

        if (!$stmt) {
            sendResponse("error", "Database error", 500);
        }

        $stmt->bind_param("sdsii", $category, $amount, $description, $transaction_id, $user_id);

        if (!$stmt->execute()) {
            $stmt->close();
            $connection->close();
            sendResponse("error", "Failed to update transaction", 500);
        }

        $success = $stmt->affected_rows > 0;
        $stmt->close();
        $connection->close();

        if ($success) {
            sendResponse("success", "Transaction updated successfully", 200);
        } else {
            sendResponse("error", "Transaction not found", 404);
        }

    // ========================================================================
    // Handle Delete Action
    // ========================================================================

    } elseif ($action === 'delete') {
        // Extract and validate transaction ID
        $transaction_id = isset($_POST['transaction_id']) ? intval($_POST['transaction_id']) : 0;

        if ($transaction_id <= 0) {
            sendResponse("error", "Invalid transaction ID", 400);
        }

        // Delete from database
        $query = "DELETE FROM transactions WHERE transaction_id = ? AND user_id = ?";
        $stmt = $connection->prepare($query);

        if (!$stmt) {
            sendResponse("error", "Database error", 500);
        }

        $stmt->bind_param("ii", $transaction_id, $user_id);

        if (!$stmt->execute()) {
            $stmt->close();
            $connection->close();
            sendResponse("error", "Failed to delete transaction", 500);
        }

        $success = $stmt->affected_rows > 0;
        $stmt->close();
        $connection->close();

        if ($success) {
            sendResponse("success", "Transaction deleted successfully", 200);
        } else {
            sendResponse("error", "Transaction not found", 404);
        }

    } else {
        sendResponse("error", "Invalid action", 400);
    }

} catch (Throwable $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
    exit();
}

ob_end_clean();
http_response_code(500);
echo json_encode(["status" => "error", "message" => "Unknown error"]);
exit();
?>
