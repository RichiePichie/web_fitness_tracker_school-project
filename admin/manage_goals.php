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
    $error_message = "Error loading goals: " . $e->getMessage();
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
                            <a href="activities.php">
                                <i class="fas fa-running"></i>
                                <span>Activities</span>
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
                    <h1>Goals Management</h1>
                    <div class="header-actions">
                        <button class="btn btn-primary" onclick="location.href='edit_goal_admin.php'">
                            <i class="fas fa-plus"></i>
                            Add Goal
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

                <div class="content-card">
                    <div class="card-header">
                        <h2>Goals Overview</h2>
                    </div>

                    <?php if (empty($goals) && !$error_message): ?>
                        <div class="empty-state">
                            <i class="fas fa-bullseye"></i>
                            <p>No goals found</p>
                            <button class="btn btn-primary" onclick="location.href='edit_goal_admin.php'">Add First Goal</button>
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
                                        <th>Goal Details</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th>Timeline</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($goals as $goal): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="goal-select" value="<?php echo $goal['id']; ?>">
                                            </td>
                                            <td>
                                                <div class="user-info">
                                                    <div class="user-avatar">
                                                        <?php echo strtoupper(substr($goal['username'], 0, 1)); ?>
                                                    </div>
                                                    <div class="user-details">
                                                        <span class="user-name"><?php echo htmlspecialchars($goal['username']); ?></span>
                                                        <span class="user-meta">ID: <?php echo htmlspecialchars($goal['user_id']); ?></span>
                                                    </div>
                                                </div>
                                            </td>
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
                                                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $goal['goal_type']))); ?>
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
                                                    <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $goal['status']))); ?>
                                                </span>
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
                                            <td class="actions">
                                                <div class="action-buttons">
                                                    <button class="btn btn-icon btn-info" title="View Details" onclick="location.href='edit_goal_admin.php?id=<?php echo $goal['id']; ?>&view=true'">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-icon btn-warning" title="Edit Goal" onclick="location.href='edit_goal_admin.php?id=<?php echo $goal['id']; ?>'">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-icon btn-danger" title="Delete Goal" onclick="confirmDelete(<?php echo $goal['id']; ?>)">
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
        document.getElementById('goalSearch').addEventListener('input', function(e) {
            const searchText = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.admin-table tbody tr');
            
            rows.forEach(row => {
                const username = row.querySelector('.user-name').textContent.toLowerCase();
                const goalTitle = row.querySelector('.goal-title').textContent.toLowerCase();
                const goalType = row.querySelector('.goal-type').textContent.toLowerCase();
                const shouldShow = username.includes(searchText) || 
                                 goalTitle.includes(searchText) || 
                                 goalType.includes(searchText);
                row.style.display = shouldShow ? '' : 'none';
            });
        });

        // Select all functionality
        document.getElementById('selectAll').addEventListener('change', function(e) {
            const checkboxes = document.querySelectorAll('.goal-select');
            checkboxes.forEach(checkbox => checkbox.checked = e.target.checked);
        });

        // Delete confirmation
        function confirmDelete(goalId) {
            if (confirm('Are you sure you want to delete this goal? This action cannot be undone.')) {
                window.location.href = 'delete_goal_admin.php?id=' + goalId;
            }
        }
    </script>
</body>
</html>
