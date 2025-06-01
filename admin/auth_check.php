<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    // Not logged in or not an admin, redirect to login page
    $_SESSION['login_error'] = 'You must be logged in as an administrator to access this page.';
    header('Location: ../index.php?page=login');
    exit;
}
// User is authenticated as admin
?>
