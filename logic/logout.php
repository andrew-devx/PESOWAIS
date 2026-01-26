<?php
    include_once '../includes/connection.php';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION = [];
    session_unset();
    session_destroy();
    header("Location: ../index.php?message=logged_out");
    exit();
?>