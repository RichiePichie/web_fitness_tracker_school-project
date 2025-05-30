<?php
require_once 'auth_check.php'; // Ensures admin is logged in
require_once '../config.php';   // Contains PDO connection

$pageTitle = "Edit User - Admin Panel";
$user_id = $_GET['id'] ?? null;
$user = null;
$error_message = '';
$success_message = '';

if (!$user_id || !filter_var($user_id, FILTER_VALIDATE_INT)) {
    $_SESSION['user_management_error'] = 'Invalid user ID.';
    header('Location: users.php');
    exit;
}

// Fetch user data
try {
    $stmt = $pdo->prepare("SELECT id, username, email, user_type, gender, height, weight, date_of_birth FROM users WHERE id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['user_management_error'] = 'User not found.';
        header('Location: users.php');
        exit;
    }
} catch (PDOException $e) {
    $error_message = "Error fetching user data: " . $e->getMessage();
    // In a real application, log this error
}

if (isset($_SESSION['form_data'])) {
    $user = array_merge($user, $_SESSION['form_data']);
    unset($_SESSION['form_data']);
}
if (isset($_SESSION['edit_user_error'])) {
    $error_message = $_SESSION['edit_user_error'];
    unset($_SESSION['edit_user_error']);
}
if (isset($_SESSION['edit_user_success'])) {
    $success_message = $_SESSION['edit_user_success'];
    unset($_SESSION['edit_user_success']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="../public/css/styles.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
    </header>

    <nav>
        <ul>
            <li><a href="../index.php" target="_blank">View Main Site</a></li>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="users.php">Manage Users</a></li>
            <li><a href="activities.php">Manage Activities</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['admin_username']); ?>)</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="content">
            <h2>Edit User: <?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></h2>

            <?php if ($error_message): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
            <?php endif; ?>

            <?php if ($user): ?>
            <form action="update_user.php" method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">

                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="user_type">User Type:</label>
                    <select id="user_type" name="user_type" required>
                        <option value="user" <?php echo (($user['user_type'] ?? '') === 'user') ? 'selected' : ''; ?>>User</option>
                        <option value="admin" <?php echo (($user['user_type'] ?? '') === 'admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender">
                        <option value="" <?php echo empty($user['gender']) ? 'selected' : ''; ?>>Select Gender</option>
                        <option value="male" <?php echo (($user['gender'] ?? '') === 'male') ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo (($user['gender'] ?? '') === 'female') ? 'selected' : ''; ?>>Female</option>
                        <option value="other" <?php echo (($user['gender'] ?? '') === 'other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="height">Height (cm):</label>
                    <input type="number" id="height" name="height" value="<?php echo htmlspecialchars($user['height'] ?? ''); ?>" step="0.01" min="0">
                </div>

                <div class="form-group">
                    <label for="weight">Weight (kg):</label>
                    <input type="number" id="weight" name="weight" value="<?php echo htmlspecialchars($user['weight'] ?? ''); ?>" step="0.01" min="0">
                </div>

                <div class="form-group">
                    <label for="date_of_birth">Date of Birth:</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($user['date_of_birth'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">New Password (leave blank to keep current):</label>
                    <input type="password" id="password" name="password" autocomplete="new-password">
                </div>

                <div class="form-actions">
                    <button type="submit">Update User</button>
                    <a href="users.php">Cancel</a>
                </div>
            </form>
            <?php elseif(!$error_message): ?>
                <p>User data could not be loaded.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Fitness Tracker Admin Panel</p>
    </footer>
</body>
</html>
