<?php
require_once 'auth_check.php'; // Ensures admin is logged in
require_once '../config.php';   // Contains PDO connection

$pageTitle = "Manage Users - Admin Panel";

// Fetch users from the database
$users = [];
$error_message = '';
try {
    $stmt = $pdo->query("SELECT id, username, email, user_type, gender, height, weight, date_of_birth, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching users: " . $e->getMessage();
    // In a real application, log this error more robustly
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
            <h2>User List</h2>

            <?php 
            if (isset($_SESSION['user_management_error'])) {
                echo '<p class="error-message">' . htmlspecialchars($_SESSION['user_management_error']) . '</p>';
                unset($_SESSION['user_management_error']);
            }
            if (isset($_SESSION['user_management_success'])) {
                echo '<p class="success-message">' . htmlspecialchars($_SESSION['user_management_success']) . '</p>';
                unset($_SESSION['user_management_success']);
            }
            ?>
            <?php if ($error_message): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <?php if (empty($users) && !$error_message): ?>
                <p>No users found.</p>
            <?php elseif (!$error_message): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>User Type</th>
                            <th>Gender</th>
                            <th>Height (cm)</th>
                            <th>Weight (kg)</th>
                            <th>Date of Birth</th>
                            <th>Registered At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['user_type']); ?></td>
                                <td><?php echo htmlspecialchars($user['gender'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($user['height'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($user['weight'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($user['date_of_birth'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                <td class="actions">
                                    <a href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a>
                                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Fitness Tracker Admin Panel</p>
    </footer>
</body>
</html>
