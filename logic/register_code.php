<?php
    include_once '../includes/connection.php';

    if(isset($_POST['registerBTN'])) {
        $email = trim($_POST['email']);
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        if($password !== $confirm_password) {
            header("Location: ../register.php?error=password_mismatch"); //will change later into a modal
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $connection->prepare("INSERT INTO users (email, username, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $username, $hashed_password);
        if($stmt->execute()) {
            header("Location: ../login.php?success=account_created");
            exit();
        } else {
            header("Location: ../register.php?error=registration_failed");
            exit();
        }
        $stmt->close();
        $connection->close();
    }