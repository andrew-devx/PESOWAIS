<?php
ob_start(); // Start output buffering
error_reporting(0); // Disable error reporting for cleaner JSON
ini_set('display_errors', 0);
session_start();

if(isset($_POST['verifyotp'])){
    $entered_otp = $_POST['otp'];
    
    // Check if OTP exists in session
    if(!isset($_SESSION['otp']) || !isset($_SESSION['otp_time'])){
        echo json_encode(['success' => false, 'message' => 'No OTP found. Please request a new one.']);
        exit;
    }
    
    // Check if OTP has expired (10 minutes = 600 seconds)
    $otp_age = time() - $_SESSION['otp_time'];
    if($otp_age > 600){
        // Clear expired OTP
        unset($_SESSION['otp']);
        unset($_SESSION['otp_time']);
        echo json_encode(['success' => false, 'message' => 'OTP has expired. Please request a new one.']);
        exit;
    }
    
    // Verify OTP
    if($entered_otp == $_SESSION['otp']){
        // OTP is correct
        $verified_email = $_SESSION['email'];
        
        // Update email_verified status in database
        require_once dirname(__DIR__) . '/includes/connection.php';
        $stmt = $connection->prepare("UPDATE users SET email_verified = 1 WHERE email = ?");
        $stmt->bind_param("s", $verified_email);
        $stmt->execute();
        $stmt->close();
        
        // Clear OTP from session
        unset($_SESSION['otp']);
        unset($_SESSION['otp_time']);
        
        // Mark email as verified
        $_SESSION['email_verified'] = true;
        $_SESSION['verified_email'] = $verified_email;
        
        ob_clean();
        echo json_encode([
            'success' => true, 
            'message' => 'Email verified successfully!',
            'email' => $verified_email
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid OTP. Please try again.']);
    }
} else {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
