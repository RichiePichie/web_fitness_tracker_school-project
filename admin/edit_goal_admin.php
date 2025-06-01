<?php
require_once 'auth_check.php';
require_once '../config.php';

$pageTitle = "Edit Goal - Admin Panel";
$error_message = '';
$success_message = '';
$goal = null;

// Check if this is a new goal or editing existing
$isNewGoal = !isset($_GET['id']);

if (!$isNewGoal) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM user_goals WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $goal = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$goal) {
            $_SESSION['goal_management_error'] = "Goal not found.";
            header("Location: manage_goals.php");
            exit;
        }
    } catch (PDOException $e) {
        $error_message = "Error loading goal: " . $e->getMessage();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($isNewGoal) {
            // Create new goal
            $stmt = $pdo->prepare("INSERT INTO user_goals (user_id, title, description, goal_type, target_value, current_value, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['user_id'],
                $_POST['title'],
                $_POST['description'],
                $_POST['goal_type'],
                $_POST['target_value'],
                $_POST['current_value'] ?? 0,
                $_POST['start_date'],
                $_POST['end_date'],
                $_POST['status']
            ]);
            $success_message = "Goal created successfully!";
        } else {
            // Update existing goal
            $stmt = $pdo->prepare("UPDATE user_goals SET user_id = ?, title = ?, description = ?, goal_type = ?, target_value = ?, current_value = ?, start_date = ?, end_date = ?, status = ? WHERE id = ?");
            $stmt->execute([
                $_POST['user_id'],
                $_POST['title'],
                $_POST['description'],
                $_POST['goal_type'],
                $_POST['target_value'],
                $_POST['current_value'],
                $_POST['start_date'],
                $_POST['end_date'],
                $_POST['status'],
                $_GET['id']
            ]);
            $success_message = "Goal updated successfully!";
        }
        
        $_SESSION['goal_management_success'] = $success_message;
        header("Location: manage_goals.php");
        exit;
    } catch (PDOException $e) {
        $error_message = "Error saving goal: " . $e->getMessage();
    }
}

// Get users for dropdown
try {
    $stmt = $pdo->query("SELECT id, username FROM users ORDER BY username");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error loading users: " . $e->getMessage();
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
                    <h1><?php echo $isNewGoal ? 'Add New Goal' : 'Edit Goal'; ?></h1>
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
                    <form method="POST" class="admin-form">
                        <div class="form-group">
                            <label for="user_id">User:</label>
                            <select id="user_id" name="user_id" required>
                                <option value="">Select User</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['id']; ?>" <?php echo (($goal['user_id'] ?? '') == $user['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($user['username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="title">Goal Title:</label>
                            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($goal['title'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($goal['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="goal_type">Goal Type:</label>
                            <select id="goal_type" name="goal_type" required>
                                <option value="">Select Type</option>
                                <option value="weight_loss" <?php echo (($goal['goal_type'] ?? '') == 'weight_loss') ? 'selected' : ''; ?>>Weight Loss</option>
                                <option value="distance" <?php echo (($goal['goal_type'] ?? '') == 'distance') ? 'selected' : ''; ?>>Distance</option>
                                <option value="strength" <?php echo (($goal['goal_type'] ?? '') == 'strength') ? 'selected' : ''; ?>>Strength</option>
                                <option value="endurance" <?php echo (($goal['goal_type'] ?? '') == 'endurance') ? 'selected' : ''; ?>>Endurance</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="target_value">Target Value:</label>
                            <input type="number" id="target_value" name="target_value" step="0.01" value="<?php echo htmlspecialchars($goal['target_value'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="current_value">Current Value:</label>
                            <input type="number" id="current_value" name="current_value" step="0.01" value="<?php echo htmlspecialchars($goal['current_value'] ?? '0'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="start_date">Start Date:</label>
                            <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($goal['start_date'] ?? date('Y-m-d')); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="end_date">End Date:</label>
                            <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($goal['end_date'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select id="status" name="status" required>
                                <option value="in_progress" <?php echo (($goal['status'] ?? '') == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                <option value="completed" <?php echo (($goal['status'] ?? '') == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                <option value="failed" <?php echo (($goal['status'] ?? '') == 'failed') ? 'selected' : ''; ?>>Failed</option>
                            </select>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                <?php echo $isNewGoal ? 'Create Goal' : 'Update Goal'; ?>
                            </button>
                            <a href="manage_goals.php" class="btn btn-outline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
