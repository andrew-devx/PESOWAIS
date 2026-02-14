<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

require_once dirname(__DIR__) . '/includes/connection.php';
require_once dirname(__DIR__) . '/includes/constants.php';

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

// Update Username
if ($action === 'update_username') {
    $new_username = trim($_POST['new_username'] ?? '');
    
    // Validation
    if (empty($new_username) || strlen($new_username) < 3 || strlen($new_username) > 50) {
        echo json_encode(['status' => 'error', 'message' => 'Username must be 3-50 characters']);
        exit();
    }
    
    // Check if username already exists
    $check_stmt = $connection->prepare("SELECT user_id FROM users WHERE username = ? AND user_id != ?");
    $check_stmt->bind_param("si", $new_username, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $check_stmt->close();
        echo json_encode(['status' => 'error', 'message' => 'Username already taken']);
        exit();
    }
    $check_stmt->close();
    
    // Update username
    $stmt = $connection->prepare("UPDATE users SET username = ? WHERE user_id = ?");
    $stmt->bind_param("si", $new_username, $user_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        $_SESSION['username'] = $new_username;
        echo json_encode(['status' => 'success', 'message' => 'Username updated successfully']);
    } else {
        $stmt->close();
        echo json_encode(['status' => 'error', 'message' => 'Failed to update username']);
    }
    exit();
}

// Update Password
if ($action === 'update_password') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    
    // Validation
    if (empty($current_password) || empty($new_password)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit();
    }
    
    if (strlen($new_password) < 8) {
        echo json_encode(['status' => 'error', 'message' => 'New password must be at least 8 characters']);
        exit();
    }
    
    // Fetch current password hash
    $stmt = $connection->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit();
    }
    
    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
        exit();
    }
    
    // Hash new password
    $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);
    
    // Update password
    $stmt = $connection->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $stmt->bind_param("si", $new_password_hash, $user_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
    } else {
        $stmt->close();
        echo json_encode(['status' => 'error', 'message' => 'Failed to update password']);
    }
    exit();
}

// Delete Account
if ($action === 'delete_account') {
    // Start transaction
    $connection->begin_transaction();
    
    try {
        // Delete related data using prepared statements
        $delete_types = [
            "DELETE FROM transactions WHERE user_id = ?",
            "DELETE FROM goals WHERE user_id = ?",
            "DELETE FROM loans WHERE user_id = ?",
            "DELETE FROM budgets WHERE user_id = ?",
            "DELETE FROM subscriptions WHERE user_id = ?"
        ];
        
        foreach ($delete_types as $query) {
            $stmt = $connection->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
        }
        
        // Delete user account
        $stmt = $connection->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        
        // Commit transaction
        $connection->commit();
        
        // Clear session
        session_destroy();
        
        echo json_encode(['status' => 'success', 'message' => 'Account deleted successfully']);
    } catch (Exception $e) {
        $connection->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete account']);
    }
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
?>
