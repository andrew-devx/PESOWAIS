<?php
session_start();

if(isset($_POST['reset_password'])){
    $new_password = $_POST['new_password'];
    
    // Check if email is verified via OTP
    if(!isset($_SESSION['email_verified']) || !$_SESSION['email_verified']) {
        echo json_encode(['success' => false, 'message' => 'Session expired. Please start over.']);
        exit;
    }
    
    $email = $_SESSION['verified_email'];
    
    // Validate password
    if(strlen($new_password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long.']);
        exit;
    }
    
    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    
    // Update password in database
    require_once dirname(__DIR__) . '/includes/connection.php';
    
    $stmt = $connection->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed_password, $email);
    
    if($stmt->execute()) {
        // Clear session data
        unset($_SESSION['email_verified']);
        unset($_SESSION['verified_email']);
        
        echo json_encode(['success' => true, 'message' => 'Password reset successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to reset password. Please try again.']);
    }
    
    $stmt->close();
    $connection->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
