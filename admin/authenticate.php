<?php
session_start();
require_once '../config.php'; // Contains PDO connection and autoloader

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($usernameOrEmail) || empty($password)) {
        $_SESSION['login_error'] = 'Please enter both username/email and password.';
        header('Location: login.php');
        exit;
    }

    try {
        // Check if input is email or username
        $sql = "SELECT id, username, email, password, user_type FROM users WHERE (username = :identifier OR email = :identifier) AND user_type = 'admin'";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':identifier', $usernameOrEmail);
        $stmt->execute();

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            // Password is correct, set session variables
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['is_admin'] = true;
            $_SESSION['user_type'] = 'admin'; // Explicitly set for checks

            // Regenerate session ID for security
            session_regenerate_id(true);
            
            header('Location: index.php'); // Redirect to admin dashboard
            exit;
        } else {
            $_SESSION['login_error'] = 'Invalid credentials or not an admin account.';
            header('Location: login.php');
            exit;
        }
    } catch (PDOException $e) {
        // Log error properly in a real application
        // error_log('Authentication error: ' . $e->getMessage());
        $_SESSION['login_error'] = 'An error occurred. Please try again later.';
        header('Location: login.php');
        exit;
    }
} else {
    // Not a POST request, redirect to login
    header('Location: login.php');
    exit;
}
?>
