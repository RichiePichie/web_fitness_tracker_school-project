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
                    <h1>Users Management</h1>
                    <div class="header-actions">
                        <button class="btn btn-primary" onclick="location.href='edit_user.php'">
                            <i class="fas fa-user-plus"></i>
                            Add New User
                        </button>
                    </div>
                </div>
            </header>

            <div class="admin-content">
                <?php 
                if (isset($_SESSION['user_management_error'])) {
                    echo '<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($_SESSION['user_management_error']) . '</div>';
                    unset($_SESSION['user_management_error']);
                }
                if (isset($_SESSION['user_management_success'])) {
                    echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' . htmlspecialchars($_SESSION['user_management_success']) . '</div>';
                    unset($_SESSION['user_management_success']);
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
                        <h2>User List</h2>
                    </div>

                    <?php if (empty($users) && !$error_message): ?>
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <p>No users found</p>
                            <button class="btn btn-primary" onclick="location.href='edit_user.php'">Add First User</button>
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
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Registered</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="user-select" value="<?php echo $user['id']; ?>">
                                            </td>
                                            <td>
                                                <div class="user-info">
                                                    <div class="user-avatar">
                                                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                                    </div>
                                                    <div class="user-details">
                                                        <span class="user-name"><?php echo htmlspecialchars($user['username']); ?></span>
                                                        <span class="user-meta">
                                                            <?php 
                                                            $details = [];
                                                            if ($user['gender']) $details[] = ucfirst($user['gender']);
                                                            if ($user['date_of_birth']) $details[] = date('Y', strtotime($user['date_of_birth'])) . ' born';
                                                            echo htmlspecialchars(implode(' • ', $details));
                                                            ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $user['user_type'] === 'admin' ? 'badge-primary' : 'badge-secondary'; ?>">
                                                    <?php echo htmlspecialchars($user['user_type']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge status-active">
                                                    <i class="fas fa-circle"></i>
                                                    Active
                                                </span>
                                            </td>
                                            <td>
                                                <div class="date-info">
                                                    <?php 
                                                    $date = new DateTime($user['created_at']);
                                                    echo $date->format('M d, Y');
                                                    ?>
                                                    <span class="date-meta">
                                                        <?php echo $date->format('H:i'); ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="actions">
                                                <div class="action-buttons">
                                                    <button class="btn btn-icon btn-info" title="View Details" onclick="location.href='user_details.php?id=<?php echo $user['id']; ?>'">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-icon btn-warning" title="Edit User" onclick="location.href='edit_user.php?id=<?php echo $user['id']; ?>'">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-icon btn-danger" title="Delete User" onclick="confirmDelete(<?php echo $user['id']; ?>)">
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
        document.getElementById('userSearch').addEventListener('input', function(e) {
            const searchText = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.admin-table tbody tr');
            
            rows.forEach(row => {
                const username = row.querySelector('.user-name').textContent.toLowerCase();
                const email = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const shouldShow = username.includes(searchText) || email.includes(searchText);
                row.style.display = shouldShow ? '' : 'none';
            });
        });

        // Select all functionality
        document.getElementById('selectAll').addEventListener('change', function(e) {
            const checkboxes = document.querySelectorAll('.user-select');
            checkboxes.forEach(checkbox => checkbox.checked = e.target.checked);
        });

        // Delete confirmation
        function confirmDelete(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                window.location.href = 'delete_user.php?id=' + userId;
            }
        }
    </script>
</body>
</html>
