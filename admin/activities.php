<?php
require_once 'auth_check.php'; // Ensures admin is logged in
require_once '../config.php';   // Contains PDO connection

$pageTitle = "Manage Activities - Admin Panel";

// Fetch activities from the database
$activities = [];
$error_message = '';
try {
    $sql = "SELECT ts.id, ts.user_id, u.username, ts.date, ts.total_duration, ts.total_calories_burned, ts.notes, ts.start_at 
            FROM training_sessions ts
            JOIN users u ON ts.user_id = u.id
            ORDER BY ts.start_at DESC";
    $stmt = $pdo->query($sql);
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching activities: " . $e->getMessage();
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
            <h2>Training Sessions List</h2>

            <?php 
            if (isset($_SESSION['activity_management_error'])) {
                echo '<p class="error-message">' . htmlspecialchars($_SESSION['activity_management_error']) . '</p>';
                unset($_SESSION['activity_management_error']);
            }
            if (isset($_SESSION['activity_management_success'])) {
                echo '<p class="success-message" style="color: green; border: 1px solid green; padding: 10px; margin-bottom: 20px; background-color: #e6ffe6; border-radius: 4px;">' . htmlspecialchars($_SESSION['activity_management_success']) . '</p>';
                unset($_SESSION['activity_management_success']);
            }
            ?>
            <?php if ($error_message): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <?php if (empty($activities) && !$error_message): ?>
                <p>No activities found.</p>
            <?php elseif (!$error_message): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Date</th>
                            <th>Duration (min)</th>
                            <th>Calories Burned</th>
                            <th class="notes-column">Notes</th>
                            <th>Logged At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activities as $activity): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($activity['id']); ?></td>
                                <td><?php echo htmlspecialchars($activity['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($activity['username']); ?></td>
                                <td><?php echo htmlspecialchars($activity['date']); ?></td>
                                <td><?php echo htmlspecialchars($activity['total_duration'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($activity['total_calories_burned'] ?? 'N/A'); ?></td>
                                <td class="notes-column"><?php echo nl2br(htmlspecialchars($activity['notes'] ?? 'N/A')); ?></td>
                                <td><?php echo htmlspecialchars($activity['start_at']); ?></td>
                                <td class="actions">
                                    <a href="edit_activity.php?id=<?php echo $activity['id']; ?>" style="margin-right:10px;">Edit</a>
                                    <a href="activity_details.php?id=<?php echo $activity['id']; ?>">Details</a>
                                    <a href="delete_activity.php?id=<?php echo $activity['id']; ?>" onclick="return confirm('Are you sure you want to delete this activity session and all its entries? This action cannot be undone.');" style="color:red; margin-left:10px;">Delete</a>
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
