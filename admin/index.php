<?php
require_once 'auth_check.php'; // Ensures admin is logged in
require_once '../config.php';   // Contains PDO connection

$pageTitle = "Admin Panel - Fitness Tracker";

// Fetch real statistics from database
$stats = [
    'total_users' => 0,
    'active_sessions' => 0,
    'total_goals' => 0,
    'completion_rate' => 0
];

try {
    // Get total users
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $stats['total_users'] = $stmt->fetchColumn();

    // Get active sessions (from last 24 hours)
    $stmt = $pdo->query("SELECT COUNT(*) FROM training_sessions WHERE start_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    $stats['active_sessions'] = $stmt->fetchColumn();

    // Get total goals
    $stmt = $pdo->query("SELECT COUNT(*) FROM user_goals");
    $stats['total_goals'] = $stmt->fetchColumn();

    // Calculate completion rate
    $stmt = $pdo->query("SELECT 
        ROUND(
            (COUNT(CASE WHEN status = 'completed' THEN 1 END) * 100.0) / 
            COUNT(*), 1
        ) as completion_rate
        FROM user_goals 
        WHERE status IN ('completed', 'failed')");
    $stats['completion_rate'] = $stmt->fetchColumn() ?: 0;

    // Get recent activities
    $stmt = $pdo->query("SELECT 
        'user_registration' as type,
        u.username,
        u.created_at as timestamp,
        NULL as details
        FROM users u
        WHERE u.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        UNION ALL
        SELECT 
        'goal_achieved' as type,
        u.username,
        g.end_date as timestamp,
        g.title as details
        FROM user_goals g
        JOIN users u ON g.user_id = u.id
        WHERE g.status = 'completed'
        AND g.end_date >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY timestamp DESC
        LIMIT 5");
    $recent_activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Log error and show generic message
    error_log("Database error: " . $e->getMessage());
    $_SESSION['admin_error'] = "Error loading dashboard data. Please try again later.";
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
                            <a href="index.php" class="active">
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
                            <a href="activities.php">
                                <i class="fas fa-running"></i>
                                <span>Activities</span>
                            </a>
                        </li>
                        <li>
                            <a href="exercises.php">
                                <i class="fas fa-dumbbell"></i>
                                <span>Exercises</span>
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
                    <h1>Dashboard</h1>
                    <div class="header-actions">
                        <span class="admin-user">
                            <i class="fas fa-user"></i>
                            <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                        </span>
                    </div>
                </div>
            </header>

            <div class="admin-content">
                <?php if (isset($_SESSION['admin_error'])): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($_SESSION['admin_error']); ?>
                    </div>
                    <?php unset($_SESSION['admin_error']); ?>
                <?php endif; ?>

                <!-- Stats Overview -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: var(--primary-light);">
                            <i class="fas fa-users" style="color: var(--primary-color);"></i>
                        </div>
                        <div class="stat-details">
                            <h3>Total Users</h3>
                            <p class="stat-value"><?php echo number_format($stats['total_users']); ?></p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: var(--success-light);">
                            <i class="fas fa-running" style="color: var(--success-color);"></i>
                        </div>
                        <div class="stat-details">
                            <h3>Active Sessions (24h)</h3>
                            <p class="stat-value"><?php echo number_format($stats['active_sessions']); ?></p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: var(--warning-light);">
                            <i class="fas fa-bullseye" style="color: var(--warning-color);"></i>
                        </div>
                        <div class="stat-details">
                            <h3>Total Goals</h3>
                            <p class="stat-value"><?php echo number_format($stats['total_goals']); ?></p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: var(--info-light);">
                            <i class="fas fa-chart-line" style="color: var(--info-color);"></i>
                        </div>
                        <div class="stat-details">
                            <h3>Goal Completion Rate</h3>
                            <p class="stat-value"><?php echo number_format($stats['completion_rate'], 1); ?>%</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity and Quick Actions -->
                <div class="content-grid">
                    <div class="content-card recent-activities">
                        <div class="card-header">
                            <h2>Recent Activities</h2>
                        </div>
                        <div class="activity-list">
                            <?php if (empty($recent_activities)): ?>
                                <div class="empty-state">
                                    <p>No recent activities</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($recent_activities as $activity): ?>
                                    <div class="activity-item">
                                        <div class="activity-icon" style="background: <?php echo $activity['type'] === 'user_registration' ? 'var(--primary-light)' : 'var(--success-light)'; ?>">
                                            <i class="fas <?php echo $activity['type'] === 'user_registration' ? 'fa-user-plus' : 'fa-trophy'; ?>"></i>
                                        </div>
                                        <div class="activity-details">
                                            <p>
                                                <?php if ($activity['type'] === 'user_registration'): ?>
                                                    New user registration: <?php echo htmlspecialchars($activity['username']); ?>
                                                <?php else: ?>
                                                    Goal achieved by <?php echo htmlspecialchars($activity['username']); ?>:
                                                    <?php echo htmlspecialchars($activity['details']); ?>
                                                <?php endif; ?>
                                            </p>
                                            <span class="activity-time">
                                                <?php 
                                                $timestamp = new DateTime($activity['timestamp']);
                                                $now = new DateTime();
                                                $diff = $now->diff($timestamp);
                                                if ($diff->i < 1) {
                                                    echo "Just now";
                                                } elseif ($diff->h < 1) {
                                                    echo $diff->i . " minutes ago";
                                                } elseif ($diff->d < 1) {
                                                    echo $diff->h . " hours ago";
                                                } else {
                                                    echo $timestamp->format('M d, H:i');
                                                }
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="content-card quick-actions">
                        <div class="card-header">
                            <h2>Quick Actions</h2>
                        </div>
                        <div class="quick-actions-grid">
                            <a href="users.php" class="quick-action-btn">
                                <i class="fas fa-user-plus"></i>
                                <span>Add User</span>
                            </a>
                            <a href="activities.php" class="quick-action-btn">
                                <i class="fas fa-plus-circle"></i>
                                <span>New Activity</span>
                            </a>
                            <a href="manage_goals.php" class="quick-action-btn">
                                <i class="fas fa-bullseye"></i>
                                <span>Goals</span>
                            </a>
                            <a href="exercises.php" class="quick-action-btn">
                                <i class="fas fa-dumbbell"></i>
                                <span>Exercises</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Empty script tag kept for potential future JavaScript needs
    </script>
</body>
</html>
