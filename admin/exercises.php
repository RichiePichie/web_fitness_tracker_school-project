<?php
require_once 'auth_check.php'; // Ensures admin is logged in
require_once '../config.php';   // Contains PDO connection

$pageTitle = "Manage Exercises - Admin Panel";

// Fetch exercises from the database
$exercises = [];
$error_message = '';
try {
    $sql = "SELECT * FROM individual_exercises ORDER BY name";
    $stmt = $pdo->query($sql);
    $exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching exercises: " . $e->getMessage();
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
                            <a href="activities.php">
                                <i class="fas fa-running"></i>
                                <span>Activities</span>
                            </a>
                        </li>
                        <li>
                            <a href="exercises.php" class="active">
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
                    <h1>Exercises Management</h1>
                    <div class="header-actions">
                        <button class="btn btn-primary" onclick="location.href='edit_exercise.php'">
                            <i class="fas fa-plus"></i>
                            Add Exercise
                        </button>
                    </div>
                </div>
            </header>

            <div class="admin-content">
                <?php 
                if (isset($_SESSION['exercise_management_error'])) {
                    echo '<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($_SESSION['exercise_management_error']) . '</div>';
                    unset($_SESSION['exercise_management_error']);
                }
                if (isset($_SESSION['exercise_management_success'])) {
                    echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' . htmlspecialchars($_SESSION['exercise_management_success']) . '</div>';
                    unset($_SESSION['exercise_management_success']);
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
                        <h2>Available Exercises</h2>
                    </div>

                    <?php if (empty($exercises) && !$error_message): ?>
                        <div class="empty-state">
                            <i class="fas fa-dumbbell"></i>
                            <p>No exercises found</p>
                            <button class="btn btn-primary" onclick="location.href='edit_exercise.php'">Add First Exercise</button>
                        </div>
                    <?php elseif (!$error_message): ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Subtype</th>
                                        <th>Description</th>
                                        <th>Actions</th>
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
                                            <td>
                                                <span class="badge badge-secondary">
                                                    <?php echo htmlspecialchars($exercise['subtype']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars(substr($exercise['description'] ?? '', 0, 100) . (strlen($exercise['description']) > 100 ? '...' : '')); ?>
                                            </td>
                                            <td class="actions">
                                                <div class="action-buttons">
                                                    <button class="btn btn-icon btn-warning" title="Edit Exercise" onclick="location.href='edit_exercise.php?id=<?php echo $exercise['id']; ?>'">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-icon btn-danger" title="Delete Exercise" onclick="confirmDelete(<?php echo $exercise['id']; ?>)">
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
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this exercise? This action cannot be undone.')) {
                window.location.href = 'delete_exercise.php?id=' + id;
            }
        }
    </script>
</body>
</html> 