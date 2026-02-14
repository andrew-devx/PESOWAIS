<?php
    // Enable error reporting for debugging (remove after fixing)
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Use require_once with absolute path for shared hosting
    require_once dirname(__DIR__) . '/includes/connection.php';

    // Verify database connection exists
    if (!isset($connection) || $connection->connect_error) {
        die("Database connection failed. Please check your constants.php configuration.");
    }

    if(isset($_POST['registerBTN'])) {
        $email = trim($_POST['email']);
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        if($password !== $confirm_password) {
            header("Location: ../register.php?error=password_mismatch");
            exit();
        }

        // Check if email already exists
        $check_stmt = $connection->prepare("SELECT email FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if($check_result->num_rows > 0) {
            $check_stmt->close();
            header("Location: ../register.php?error=duplicate_email&email=" . urlencode($email));
            exit();
        }
        $check_stmt->close();

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $connection->prepare("INSERT INTO users (email, username, password, email_verified) VALUES (?, ?, ?, 0)");
        $stmt->bind_param("sss", $email, $username, $hashed_password);
        if($stmt->execute()) {
            // Store registration data in session for verification
            $_SESSION['pending_verification_email'] = $email;
            $_SESSION['pending_verification_username'] = $username;
            
            // Redirect to verification page
            header("Location: ../verify.php?email=" . urlencode($email));
            exit();
        } else {
            header("Location: ../register.php?error=registration_failed");
            exit();
        }
        $stmt->close();
        $connection->close();
    }