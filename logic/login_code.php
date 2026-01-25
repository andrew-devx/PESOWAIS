<?php 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    include_once '../includes/connection.php';
    if(isset($_POST['loginBTN'])) {

    $username = trim($_POST['username']); 
    $password = $_POST['password'];

    $stmt = $connection->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if(password_verify($password, $user['password'])) {
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
