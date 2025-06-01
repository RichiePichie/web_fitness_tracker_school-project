<?php
require_once 'auth_check.php';
require_once '../config.php';

$pageTitle = "Goal Details - Admin Panel";
$goal = null;
$user = null;
$error_message = '';

if (isset($_GET['id'])) {
    try {
        // Get goal details with user info
        $stmt = $pdo->prepare("
            SELECT g.*, u.username, u.email
            FROM user_goals g
            JOIN users u ON g.user_id = u.id
            WHERE g.id = ?
        ");
        $stmt->execute([$_GET['id']]);
        $goal = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$goal) {
            $_SESSION['goal_management_error'] = "Goal not found.";
            header("Location: manage_goals.php");
            exit;
        }

    } catch (PDOException $e) {
        $error_message = "Error loading goal details: " . $e->getMessage();
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
                            <a href="manage_goals.php" class="active">
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
                    <h1>Goal Details</h1>
                    <div class="header-actions">
                        <a href="edit_goal_admin.php?id=<?php echo $goal['id']; ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i>
                            Edit Goal
                        </a>
                        <button class="btn btn-danger" onclick="confirmDelete(<?php echo $goal['id']; ?>)">
                            <i class="fas fa-trash-alt"></i>
                            Delete Goal
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
                    <!-- Goal Overview -->
                    <div class="content-card">
                        <div class="card-header">
                            <h2>Goal Information</h2>
                        </div>
                        <div class="card-body">
                            <div class="info-grid">
                                <!-- User Info -->
                                <div class="info-item">
                                    <label>User:</label>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <?php echo strtoupper(substr($goal['username'], 0, 1)); ?>
                                        </div>
                                        <div class="user-details">
                                            <a href="user_details.php?id=<?php echo $goal['user_id']; ?>" class="user-name">
                                                <?php echo htmlspecialchars($goal['username']); ?>
                                            </a>
                                            <span class="user-meta"><?php echo htmlspecialchars($goal['email']); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Goal Title -->
                                <div class="info-item full-width">
                                    <label>Goal Title:</label>
                                    <span class="goal-title"><?php echo htmlspecialchars($goal['title']); ?></span>
                                </div>

                                <!-- Goal Type -->
                                <div class="info-item">
                                    <label>Type:</label>
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

                                <!-- Goal Progress -->
                                <div class="info-item">
                                    <label>Progress:</label>
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
                                            <?php
                                            switch ($goal['goal_type']) {
                                                case 'weight_loss':
                                                    echo 'kg';
                                                    break;
                                                case 'distance':
                                                    echo 'km';
                                                    break;
                                                case 'strength':
                                                    echo 'reps';
                                                    break;
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Timeline -->
                                <div class="info-item">
                                    <label>Timeline:</label>
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
                                </div>

                                <!-- Status -->
                                <div class="info-item">
                                    <label>Status:</label>
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
                                </div>

                                <!-- Description -->
                                <div class="info-item full-width">
                                    <label>Description:</label>
                                    <p class="goal-description">
                                        <?php echo nl2br(htmlspecialchars($goal['description'] ?? 'No description provided.')); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="manage_goals.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i>
                        Back to Goals
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this goal? This action cannot be undone.')) {
                window.location.href = 'delete_goal_admin.php?id=' + id;
            }
        }
    </script>
</body>
</html> 