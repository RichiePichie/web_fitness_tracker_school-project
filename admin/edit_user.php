<?php
require_once 'auth_check.php'; // Ensures admin is logged in
require_once '../config.php';   // Contains PDO connection

$pageTitle = "Edit User - Admin Panel";
$error_message = '';
$success_message = '';
$user = null;

// Check if this is a new user or editing existing
$isNewUser = !isset($_GET['id']);

if (!$isNewUser) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            $_SESSION['user_management_error'] = "User not found.";
            header("Location: users.php");
            exit;
        }
    } catch (PDOException $e) {
        $error_message = "Error loading user: " . $e->getMessage();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Check if email already exists
        $checkEmailStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $checkEmailStmt->execute([$_POST['email'], $isNewUser ? 0 : $_GET['id']]);
        if ($checkEmailStmt->fetch()) {
            $error_message = "A user with this email address already exists.";
        } else if ($isNewUser) {
            // Validate required password for new users
            if (empty($_POST['password'])) {
                $error_message = "Password is required for new users.";
            } else {
                // Create new user with password hash
                $stmt = $pdo->prepare("INSERT INTO users (username, email, user_type, gender, height, weight, date_of_birth, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                try {
                    $stmt->execute([
                        $_POST['username'],
                        $_POST['email'],
                        $_POST['user_type'],
                        $_POST['gender'],
                        !empty($_POST['height']) ? $_POST['height'] : null,
                        !empty($_POST['weight']) ? $_POST['weight'] : null,
                        !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
                        password_hash($_POST['password'], PASSWORD_DEFAULT)
                    ]);
                    $success_message = "User created successfully!";
                } catch (PDOException $e) {
                    // Check for username duplicate if that's the issue
                    if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'username') !== false) {
                        $error_message = "This username is already taken.";
                    } else {
                        throw $e;
                    }
                }
            }
        } else {
            // Update existing user
            $updateFields = [
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'user_type' => $_POST['user_type'],
                'gender' => $_POST['gender'],
                'height' => !empty($_POST['height']) ? $_POST['height'] : null,
                'weight' => !empty($_POST['weight']) ? $_POST['weight'] : null,
                'date_of_birth' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null
            ];
            
            // Add password to update if provided
            if (!empty($_POST['password'])) {
                $updateFields['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }
            
            $sql = "UPDATE users SET " . 
                   implode(", ", array_map(fn($field) => "$field = ?", array_keys($updateFields))) .
                   " WHERE id = ?";
            
            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute([...array_values($updateFields), $_GET['id']]);
                $success_message = "User updated successfully!";
            } catch (PDOException $e) {
                // Check for username duplicate
                if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'username') !== false) {
                    $error_message = "This username is already taken.";
                } else {
                    throw $e;
                }
            }
        }
        
        if (empty($error_message)) {
            // Redirect back to users list with success message
            $_SESSION['user_management_success'] = $success_message;
            header("Location: users.php");
            exit;
        }
    } catch (PDOException $e) {
        $error_message = "An error occurred while saving the user. Please try again.";
    }
}

if (isset($_SESSION['form_data'])) {
    $user = array_merge($user, $_SESSION['form_data']);
    unset($_SESSION['form_data']);
}
if (isset($_SESSION['edit_user_error'])) {
    $error_message = $_SESSION['edit_user_error'];
    unset($_SESSION['edit_user_error']);
}
if (isset($_SESSION['edit_user_success'])) {
    $success_message = $_SESSION['edit_user_success'];
    unset($_SESSION['edit_user_success']);
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
                    <h1><?php echo $isNewUser ? 'Add New User' : 'Edit User: ' . htmlspecialchars($user['username']); ?></h1>
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
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="user_type">User Type:</label>
                            <select id="user_type" name="user_type" required>
                                <option value="user" <?php echo (($user['user_type'] ?? '') === 'user') ? 'selected' : ''; ?>>User</option>
                                <option value="admin" <?php echo (($user['user_type'] ?? '') === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="gender">Gender:</label>
                            <select id="gender" name="gender" required>
                                <option value="male" <?php echo (($user['gender'] ?? '') === 'male') ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo (($user['gender'] ?? '') === 'female') ? 'selected' : ''; ?>>Female</option>
                                <option value="other" <?php echo (($user['gender'] ?? '') === 'other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="height">Height (cm):</label>
                            <input type="number" id="height" name="height" step="0.01" value="<?php echo htmlspecialchars($user['height'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="weight">Weight (kg):</label>
                            <input type="number" id="weight" name="weight" step="0.01" value="<?php echo htmlspecialchars($user['weight'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth:</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($user['date_of_birth'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="password"><?php echo $isNewUser ? 'Password:' : 'New Password (leave blank to keep current):'; ?></label>
                            <input type="password" id="password" name="password" <?php echo $isNewUser ? 'required' : ''; ?>>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                <?php echo $isNewUser ? 'Create User' : 'Update User'; ?>
                            </button>
                            <a href="users.php" class="btn btn-outline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
