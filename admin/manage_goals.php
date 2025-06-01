<?php
require_once 'auth_check.php'; // Ensures admin is logged in
require_once '../config.php';   // Contains PDO connection

$pageTitle = "Manage Goals - Admin Panel";

$goals = [];
$error_message = '';
try {
    $sql = "SELECT ug.id, ug.user_id, u.username, ug.title, ug.description, ug.goal_type, ug.target_value, ug.current_value, ug.start_date, ug.end_date, ug.status, ug.individual_exercise_id 
            FROM user_goals ug
            JOIN users u ON ug.user_id = u.id
            ORDER BY ug.start_date DESC, u.username ASC";
    $stmt = $pdo->query($sql);
    $goals = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Chyba při načítání cílů: " . $e->getMessage();
    // V reálné aplikaci by zde mělo být robustnější logování chyby
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
            <li><a href="manage_goals.php">Manage Goals</a></li>
            <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['admin_username']); ?>)</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="content">
            <h2>Goals Overview</h2>
            
            <?php if ($error_message): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <?php if (empty($goals) && !$error_message): ?>
                <p>No goals found or feature not yet implemented.</p>
            <?php elseif (!$error_message): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Uživatel</th>
                            <th>Název cíle</th>
                            <th>Typ</th>
                            <th>Stav</th>
                            <th>Od</th>
                            <th>Do</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($goals as $goal): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($goal['id']); ?></td>
                                <td><?php echo htmlspecialchars($goal['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($goal['username']); ?></td>
                                <td><?php echo htmlspecialchars($goal['title']); ?></td>
                                <td><?php echo htmlspecialchars($goal['goal_type']); ?></td>
                                <td><?php echo htmlspecialchars($goal['status']); ?></td>
                                <td><?php echo htmlspecialchars($goal['start_date'] ? date('d.m.Y', strtotime($goal['start_date'])) : 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($goal['end_date'] ? date('d.m.Y', strtotime($goal['end_date'])) : 'N/A'); ?></td>
                                <td class="actions">
                                    <a href="edit_goal_admin.php?id=<?php echo $goal['id']; ?>">Edit</a>
                                    <a href="delete_goal_admin.php?id=<?php echo $goal['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this goal?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <p>Goal management functionality will be implemented here. Admins will be able to view, and potentially manage, user goals.</p>

        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Fitness Tracker Admin Panel</p>
    </footer>
</body>
</html>
