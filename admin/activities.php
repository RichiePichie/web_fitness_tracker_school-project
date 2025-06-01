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
    <link rel="stylesheet" href="../public/css/admin.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-layout">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-logo">
                    <i class="fas fa-dumbbell"></i>
                    <span>Fitness Admin</span>
                </h2>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <h3 class="nav-title">Navigation</h3>
                    <ul>
                        <li>
                            <a href="index.php">
                                <i class="fas fa-home"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="users.php">
                                <i class="fas fa-users"></i>
                                <span>Users</span>
                            </a>
                        </li>
                        <li>
                            <a href="activities.php" class="active">
                                <i class="fas fa-running"></i>
                                <span>Activities</span>
                            </a>
                        </li>
                        <li>
                            <a href="manage_goals.php">
                                <i class="fas fa-bullseye"></i>
                                <span>Goals</span>
                            </a>
                        </li>
                        <li>
                            <a href="logout.php" class="logout-link">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <div class="header-content">
                    <h1>Activities Management</h1>
                    <div class="header-actions">
                        <button class="btn btn-primary" onclick="location.href='edit_activity.php'">
                            <i class="fas fa-plus"></i>
                            Add Activity
                        </button>
                    </div>
                </div>
            </header>

            <div class="admin-content">
                <?php 
                if (isset($_SESSION['activity_management_error'])) {
                    echo '<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($_SESSION['activity_management_error']) . '</div>';
                    unset($_SESSION['activity_management_error']);
                }
                if (isset($_SESSION['activity_management_success'])) {
                    echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' . htmlspecialchars($_SESSION['activity_management_success']) . '</div>';
                    unset($_SESSION['activity_management_success']);
                }
                ?>
                <?php if ($error_message): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <div class="content-card">
                    <div class="card-header">
                        <h2>Training Sessions</h2>
                    </div>

                    <?php if (empty($activities) && !$error_message): ?>
                        <div class="empty-state">
                            <i class="fas fa-running"></i>
                            <p>No activities found</p>
                            <button class="btn btn-primary" onclick="location.href='edit_activity.php'">Add First Activity</button>
                        </div>
                    <?php elseif (!$error_message): ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll">
                                        </th>
                                        <th>User</th>
                                        <th>Activity Details</th>
                                        <th>Duration</th>
                                        <th>Calories</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($activities as $activity): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="activity-select" value="<?php echo $activity['id']; ?>">
                                            </td>
                                            <td>
                                                <div class="user-info">
                                                    <div class="user-avatar">
                                                        <?php echo strtoupper(substr($activity['username'], 0, 1)); ?>
                                                    </div>
                                                    <div class="user-details">
                                                        <span class="user-name"><?php echo htmlspecialchars($activity['username']); ?></span>
                                                        <span class="user-meta">ID: <?php echo htmlspecialchars($activity['user_id']); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="activity-info">
                                                    <span class="activity-notes">
                                                        <?php echo nl2br(htmlspecialchars(substr($activity['notes'] ?? 'No notes', 0, 50) . (strlen($activity['notes']) > 50 ? '...' : ''))); ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="duration-badge">
                                                    <i class="fas fa-clock"></i>
                                                    <?php echo htmlspecialchars($activity['total_duration'] ?? '0'); ?> min
                                                </span>
                                            </td>
                                            <td>
                                                <span class="calories-badge">
                                                    <i class="fas fa-fire"></i>
                                                    <?php echo htmlspecialchars($activity['total_calories_burned'] ?? '0'); ?> kcal
                                                </span>
                                            </td>
                                            <td>
                                                <div class="date-info">
                                                    <?php 
                                                    $date = new DateTime($activity['date']);
                                                    echo $date->format('M d, Y');
                                                    ?>
                                                    <span class="date-meta">
                                                        Logged: <?php echo (new DateTime($activity['start_at']))->format('H:i'); ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="actions">
                                                <div class="action-buttons">
                                                    <button class="btn btn-icon btn-info" title="View Details" onclick="location.href='activity_details.php?id=<?php echo $activity['id']; ?>'">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-icon btn-warning" title="Edit Activity" onclick="location.href='edit_activity.php?id=<?php echo $activity['id']; ?>'">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-icon btn-danger" title="Delete Activity" onclick="confirmDelete(<?php echo $activity['id']; ?>)">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Search functionality
        document.getElementById('activitySearch').addEventListener('input', function(e) {
            const searchText = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.admin-table tbody tr');
            
            rows.forEach(row => {
                const username = row.querySelector('.user-name').textContent.toLowerCase();
                const notes = row.querySelector('.activity-notes').textContent.toLowerCase();
                const shouldShow = username.includes(searchText) || notes.includes(searchText);
                row.style.display = shouldShow ? '' : 'none';
            });
        });

        // Select all functionality
        document.getElementById('selectAll').addEventListener('change', function(e) {
            const checkboxes = document.querySelectorAll('.activity-select');
            checkboxes.forEach(checkbox => checkbox.checked = e.target.checked);
        });

        // Delete confirmation
        function confirmDelete(activityId) {
            if (confirm('Are you sure you want to delete this activity session and all its entries? This action cannot be undone.')) {
                window.location.href = 'delete_activity.php?id=' + activityId;
            }
        }
    </script>
</body>
</html>
