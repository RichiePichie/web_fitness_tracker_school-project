<?php
require_once 'auth_check.php'; // Ensures admin is logged in
require_once '../config.php';   // Contains PDO connection

$pageTitle = "Activity Details - Admin Panel";
$activity = null;
$exercises = [];
$error_message = '';

if (isset($_GET['id'])) {
    try {
        // Get activity details
        $stmt = $pdo->prepare("
            SELECT ts.*, u.username 
            FROM training_sessions ts
            JOIN users u ON ts.user_id = u.id
            WHERE ts.id = ?
        ");
        $stmt->execute([$_GET['id']]);
        $activity = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$activity) {
            $_SESSION['activity_management_error'] = "Activity not found.";
            header("Location: activities.php");
            exit;
        }

        // Get exercises for this activity
        $stmt = $pdo->prepare("
            SELECT tee.*, ie.name, ie.exercise_type
            FROM training_exercise_entries tee
            JOIN individual_exercises ie ON tee.individual_exercise_id = ie.id
            WHERE tee.training_session_id = ?
        ");
        $stmt->execute([$_GET['id']]);
        $exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_message = "Error loading activity details: " . $e->getMessage();
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
                            <a href="activities.php" class="active">
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
                    <h1>Activity Details</h1>
                    <div class="header-actions">
                        <a href="edit_activity.php?id=<?php echo $activity['id']; ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i>
                            Edit Activity
                        </a>
                        <button class="btn btn-danger" onclick="confirmDelete(<?php echo $activity['id']; ?>)">
                            <i class="fas fa-trash-alt"></i>
                            Delete Activity
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
                    <!-- Activity Overview -->
                    <div class="content-card">
                        <div class="card-header">
                            <h2>Session Information</h2>
                        </div>
                        <div class="card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>User:</label>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <?php echo strtoupper(substr($activity['username'], 0, 1)); ?>
                                        </div>
                                        <div class="user-details">
                                            <span class="user-name"><?php echo htmlspecialchars($activity['username']); ?></span>
                                            <span class="user-meta">ID: <?php echo htmlspecialchars($activity['user_id']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <label>Date:</label>
                                    <span><?php echo date('F j, Y', strtotime($activity['date'])); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Start Time:</label>
                                    <span><?php echo date('H:i', strtotime($activity['start_at'])); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Duration:</label>
                                    <span class="duration-badge">
                                        <i class="fas fa-clock"></i>
                                        <?php echo htmlspecialchars($activity['total_duration']); ?> minutes
                                    </span>
                                </div>
                                <div class="info-item">
                                    <label>Calories Burned:</label>
                                    <span class="calories-badge">
                                        <i class="fas fa-fire"></i>
                                        <?php echo htmlspecialchars($activity['total_calories_burned']); ?> kcal
                                    </span>
                                </div>
                                <div class="info-item full-width">
                                    <label>Notes:</label>
                                    <p class="notes"><?php echo nl2br(htmlspecialchars($activity['notes'] ?? 'No notes')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Exercises List -->
                    <div class="content-card">
                        <div class="card-header">
                            <h2>Exercises Performed</h2>
                        </div>
                        <div class="card-body">
                            <?php if (empty($exercises)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-dumbbell"></i>
                                    <p>No exercises recorded for this session</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="admin-table">
                                        <thead>
                                            <tr>
                                                <th>Exercise</th>
                                                <th>Type</th>
                                                <th>Sets</th>
                                                <th>Reps</th>
                                                <th>Weight (kg)</th>
                                                <th>Duration (min)</th>
                                                <th>Distance (km)</th>
                                                <th>Calories</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($exercises as $exercise): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($exercise['name']); ?></td>
                                                    <td>
                                                        <span class="badge badge-primary">
                                                            <?php echo htmlspecialchars(ucfirst($exercise['exercise_type'])); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo $exercise['sets'] ?? '-'; ?></td>
                                                    <td><?php echo $exercise['reps'] ?? '-'; ?></td>
                                                    <td><?php echo $exercise['weight'] ? number_format($exercise['weight'], 2) : '-'; ?></td>
                                                    <td><?php echo $exercise['duration'] ?? '-'; ?></td>
                                                    <td><?php echo $exercise['distance'] ? number_format($exercise['distance'], 2) : '-'; ?></td>
                                                    <td><?php echo $exercise['calories_burned'] ?? '-'; ?></td>
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
                    <a href="activities.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i>
                        Back to Activities
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this activity session and all its entries? This action cannot be undone.')) {
                window.location.href = 'delete_activity.php?id=' + id;
            }
        }
    </script>
</body>
</html>
