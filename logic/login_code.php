<?php 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    require_once dirname(__DIR__) . '/includes/connection.php';
    if(isset($_POST['loginBTN'])) {

    $email = trim($_POST['email']); 
    $password = $_POST['password'];

    $stmt = $connection->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if(password_verify($password, $user['password'])) {
            // Check if email is verified
            if($user['email_verified'] == 0) {
                header("Location: ../login.php?error=email_not_verified&email=" . urlencode($user['email']));
                exit();
            }
            
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            header("Location: ../dashboard.php");
            exit();

        } else {
            header("Location: ../login.php?error=invalid_credentials");
            exit();
        }
    } else {
        header("Location: ../login.php?error=invalid_credentials");
        exit();
    }

    $stmt->close(); 
    $connection->close();
    }
