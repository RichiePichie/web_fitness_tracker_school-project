<?php
session_start();
// If already logged in as admin, redirect to admin dashboard
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
    header('Location: index.php');
    exit;
}

$pageTitle = "Admin Login - Fitness Tracker";
$error_message = '';
if (isset($_SESSION['login_error'])) {
    $error_message = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.2); width: 320px; text-align: center; }
        .login-container h1 { margin-bottom: 20px; color: #333; }
        .login-container label { display: block; text-align: left; margin-bottom: 5px; color: #555; }
        .login-container input[type="text"], .login-container input[type="password"] { width: calc(100% - 20px); padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; }
        .login-container button { background-color: #333; color: #fff; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; }
        .login-container button:hover { background-color: #555; }
        .error-message { color: red; margin-bottom: 15px; font-size: 0.9em; }
        .back-to-site a { color: #337ab7; text-decoration: none; font-size: 0.9em; }
        .back-to-site a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Admin Login</h1>
        <?php if ($error_message): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form action="authenticate.php" method="post">
            <div>
                <label for="username">Username or Email:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p class="back-to-site" style="margin-top: 20px;"><a href="../index.php">Back to Main Site</a></p>
    </div>
</body>
</html>
