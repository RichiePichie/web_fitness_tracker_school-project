<?php
require_once 'auth_check.php';
require_once '../config.php';

$pageTitle = "User Details - Admin Panel";
$user = null;
$activities = [];
$goals = [];
$error_message = '';

if (isset($_GET['id'])) {
    try {
        // Get user details
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['user_management_error'] = "User not found.";
            header("Location: users.php");
            exit;
        }

        // Get user's recent activities
        $stmt = $pdo->prepare("
            SELECT ts.*, COUNT(tee.id) as exercise_count
            FROM training_sessions ts
            LEFT JOIN training_exercise_entries tee ON ts.id = tee.training_session_id
            WHERE ts.user_id = ?
            GROUP BY ts.id
            ORDER BY ts.date DESC
            LIMIT 5
        ");
        $stmt->execute([$_GET['id']]);
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get user's goals
        $stmt = $pdo->prepare("
            SELECT * FROM user_goals 
            WHERE user_id = ?
            ORDER BY start_date DESC
        ");
        $stmt->execute([$_GET['id']]);
        $goals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $error_message = "Error loading user details: " . $e->getMessage();
    }
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
                            <a href="users.php" class="active">
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
                    <h1>User Details</h1>
                    <div class="header-actions">
                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i>
                            Edit User
                        </a>
                        <button class="btn btn-danger" onclick="confirmDelete(<?php echo $user['id']; ?>)">
                            <i class="fas fa-trash-alt"></i>
                            Delete User
                        </button>
                    </div>
                </div>
            </header>

            <div class="admin-content">
                <?php if ($error_message): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <div class="content-grid">
                    <!-- User Overview -->
                    <div class="content-card">
                        <div class="card-header">
                            <h2>User Information</h2>
                        </div>
                        <div class="card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="user-info">
                                        <div class="user-avatar large">
                                            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                        </div>
                                        <div class="user-details">
                                            <span class="user-name"><?php echo htmlspecialchars($user['username']); ?></span>
                                            <span class="user-meta">
                                                <i class="fas <?php echo $user['user_type'] === 'admin' ? 'fa-shield-alt' : 'fa-user'; ?>"></i>
                                                <?php echo ucfirst($user['user_type']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <label>Email:</label>
                                    <span><?php echo htmlspecialchars($user['email']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Gender:</label>
                                    <span><?php echo ucfirst($user['gender']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Height:</label>
                                    <span><?php echo $user['height'] ? $user['height'] . ' cm' : 'Not set'; ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Weight:</label>
                                    <span><?php echo $user['weight'] ? $user['weight'] . ' kg' : 'Not set'; ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Date of Birth:</label>
                                    <span><?php echo $user['date_of_birth'] ? date('F j, Y', strtotime($user['date_of_birth'])) : 'Not set'; ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Member Since:</label>
                                    <span><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="content-card">
                        <div class="card-header">
                            <h2>Recent Activities</h2>
                            <a href="edit_activity.php?user_id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i>
                                Add Activity
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($activities)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-running"></i>
                                    <p>No activities recorded yet</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="admin-table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Duration</th>
                                                <th>Exercises</th>
                                                <th>Calories</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($activities as $activity): ?>
                                                <tr>
                                                    <td>
                                                        <div class="date-info">
                                                            <?php echo date('M j, Y', strtotime($activity['date'])); ?>
                                                            <span class="date-meta">
                                                                <?php echo date('H:i', strtotime($activity['start_at'])); ?>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="duration-badge">
                                                            <i class="fas fa-clock"></i>
                                                            <?php echo $activity['total_duration']; ?> min
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            <?php echo $activity['exercise_count']; ?> exercises
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="calories-badge">
                                                            <i class="fas fa-fire"></i>
                                                            <?php echo $activity['total_calories_burned']; ?> kcal
                                                        </span>
                                                    </td>
                                                    <td class="actions">
                                                        <div class="action-buttons">
                                                            <a href="activity_details.php?id=<?php echo $activity['id']; ?>" class="btn btn-icon btn-info" title="View Details">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="edit_activity.php?id=<?php echo $activity['id']; ?>" class="btn btn-icon btn-warning" title="Edit Activity">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
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

                    <!-- User Goals -->
                    <div class="content-card">
                        <div class="card-header">
                            <h2>Goals</h2>
                            <a href="edit_goal_admin.php?user_id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i>
                                Add Goal
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($goals)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-bullseye"></i>
                                    <p>No goals set yet</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="admin-table">
                                        <thead>
                                            <tr>
                                                <th>Goal</th>
                                                <th>Progress</th>
                                                <th>Timeline</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($goals as $goal): ?>
                                                <tr>
                                                    <td>
                                                        <div class="goal-info">
                                                            <span class="goal-title"><?php echo htmlspecialchars($goal['title']); ?></span>
                                                            <span class="goal-type">
                                                                <?php 
                                                                $typeIcon = '';
                                                                switch ($goal['goal_type']) {
                                                                    case 'weight_loss':
                                                                        $typeIcon = 'fa-weight-scale';
                                                                        break;
                                                                    case 'distance':
                                                                        $typeIcon = 'fa-road';
                                                                        break;
                                                                    case 'strength':
                                                                        $typeIcon = 'fa-dumbbell';
                                                                        break;
                                                                    default:
                                                                        $typeIcon = 'fa-bullseye';
                                                                }
                                                                ?>
                                                                <i class="fas <?php echo $typeIcon; ?>"></i>
                                                                <?php echo ucfirst(str_replace('_', ' ', $goal['goal_type'])); ?>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="goal-progress">
                                                            <?php
                                                            $progress = 0;
                                                            if ($goal['target_value'] > 0) {
                                                                $progress = min(100, ($goal['current_value'] / $goal['target_value']) * 100);
                                                            }
                                                            ?>
                                                            <div class="progress-bar">
                                                                <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                                                            </div>
                                                            <span class="progress-text">
                                                                <?php echo number_format($goal['current_value'], 1); ?> / <?php echo number_format($goal['target_value'], 1); ?>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="date-info">
                                                            <?php 
                                                            $startDate = new DateTime($goal['start_date']);
                                                            $endDate = new DateTime($goal['end_date']);
                                                            $now = new DateTime();
                                                            $daysLeft = $now->diff($endDate)->days;
                                                            $isExpired = $now > $endDate;
                                                            ?>
                                                            <span class="date-range">
                                                                <?php echo $startDate->format('M d') . ' - ' . $endDate->format('M d, Y'); ?>
                                                            </span>
                                                            <span class="date-meta <?php echo $isExpired ? 'text-danger' : ''; ?>">
                                                                <?php
                                                                if ($isExpired) {
                                                                    echo 'Expired';
                                                                } else {
                                                                    echo $daysLeft . ' days left';
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $statusClass = '';
                                                        $statusIcon = '';
                                                        switch ($goal['status']) {
                                                            case 'completed':
                                                                $statusClass = 'status-success';
                                                                $statusIcon = 'fa-check-circle';
                                                                break;
                                                            case 'in_progress':
                                                                $statusClass = 'status-warning';
                                                                $statusIcon = 'fa-clock';
                                                                break;
                                                            case 'failed':
                                                                $statusClass = 'status-danger';
                                                                $statusIcon = 'fa-times-circle';
                                                                break;
                                                            default:
                                                                $statusClass = 'status-info';
                                                                $statusIcon = 'fa-info-circle';
                                                        }
                                                        ?>
                                                        <span class="status-badge <?php echo $statusClass; ?>">
                                                            <i class="fas <?php echo $statusIcon; ?>"></i>
                                                            <?php echo ucfirst(str_replace('_', ' ', $goal['status'])); ?>
                                                        </span>
                                                    </td>
                                                    <td class="actions">
                                                        <div class="action-buttons">
                                                            <a href="edit_goal_admin.php?id=<?php echo $goal['id']; ?>" class="btn btn-icon btn-warning" title="Edit Goal">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button class="btn btn-icon btn-danger" onclick="confirmDeleteGoal(<?php echo $goal['id']; ?>)" title="Delete Goal">
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
                </div>

                <div class="form-actions">
                    <a href="users.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i>
                        Back to Users
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this user? This will also delete all their activities and goals. This action cannot be undone.')) {
                window.location.href = 'delete_user.php?id=' + id;
            }
        }

        function confirmDeleteGoal(id) {
            if (confirm('Are you sure you want to delete this goal? This action cannot be undone.')) {
                window.location.href = 'delete_goal_admin.php?id=' + id;
            }
        }
    </script>
</body>
</html> 