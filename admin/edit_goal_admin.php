<?php
require_once 'auth_check.php'; // Ensures admin is logged in
require_once '../config.php';   // Contains PDO connection

$pageTitle = "Edit Goal - Admin Panel";
$goal_id = $_GET['id'] ?? null;
$goal_details = null;
$error_message = '';
$success_message = '';

if (!$goal_id || !filter_var($goal_id, FILTER_VALIDATE_INT)) {
    $_SESSION['goal_management_error'] = 'Invalid goal ID.';
    header('Location: manage_goals.php');
    exit;
}

// Fetch goal details
try {
    $stmt_goal = $pdo->prepare(
        "SELECT ug.*, u.username 
         FROM user_goals ug
         JOIN users u ON ug.user_id = u.id
         WHERE ug.id = :id"
    );
    $stmt_goal->bindParam(':id', $goal_id, PDO::PARAM_INT);
    $stmt_goal->execute();
    $goal_details = $stmt_goal->fetch(PDO::FETCH_ASSOC);

    if (!$goal_details) {
        $_SESSION['goal_management_error'] = 'Goal not found.';
        header('Location: manage_goals.php');
        exit;
    }
} catch (PDOException $e) {
    $error_message = "Error fetching goal details: " . $e->getMessage();
    // Log this error
}

// Handle form submission for updating the goal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_goal'])) {
    // Retrieve and sanitize/validate inputs
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $goal_type = $_POST['goal_type'] ?? '';
    $target_value = filter_var($_POST['target_value'] ?? '', FILTER_VALIDATE_FLOAT);
    $current_value = filter_var($_POST['current_value'] ?? '', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
    $start_date = $_POST['start_date'] ?? '';
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $status = $_POST['status'] ?? '';

    // Basic validation
    if (empty($title) || empty($goal_type) || $target_value === false || empty($start_date) || empty($status)) {
        $error_message = "Please fill in all required fields correctly.";
    } else {
        try {
            $update_sql = "UPDATE user_goals SET 
                            title = :title, 
                            description = :description, 
                            goal_type = :goal_type, 
                            target_value = :target_value, 
                            current_value = :current_value, 
                            start_date = :start_date, 
                            end_date = :end_date, 
                            status = :status,
                            updated_at = CURRENT_TIMESTAMP
                          WHERE id = :goal_id";
            
            $stmt_update = $pdo->prepare($update_sql);
            
            $stmt_update->bindParam(':title', $title);
            $stmt_update->bindParam(':description', $description);
            $stmt_update->bindParam(':goal_type', $goal_type);
            $stmt_update->bindParam(':target_value', $target_value);
            $stmt_update->bindParam(':current_value', $current_value);
            $stmt_update->bindParam(':start_date', $start_date);
            $stmt_update->bindParam(':end_date', $end_date);
            $stmt_update->bindParam(':status', $status);
            $stmt_update->bindParam(':goal_id', $goal_id, PDO::PARAM_INT);

            if ($stmt_update->execute()) {
                $success_message = "Goal updated successfully!";
                // Re-fetch goal details to display updated data
                $stmt_goal->execute(); // Re-run the fetch query
                $goal_details = $stmt_goal->fetch(PDO::FETCH_ASSOC);
            } else {
                $error_message = "Failed to update goal. Please try again.";
            }
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
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
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($pageTitle); ?> (ID: <?php echo htmlspecialchars($goal_id); ?>)</h1>
    </header>

    <nav>
        <ul>
            <li><a href="../index.php" target="_blank">View Main Site</a></li>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="users.php">Manage Users</a></li>
            <li><a href="activities.php">Manage Activities</a></li>
            <li><a href="manage_goals.php">Manage Goals</a></li>
            <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['admin_username']); ?>)</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="content">
            <a href="manage_goals.php" class="back-link">&laquo; Back to Goals List</a>

            <?php if ($error_message): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
            <?php endif; ?>

            <?php if ($goal_details && !$error_message): ?>
                <form action="edit_goal_admin.php?id=<?php echo htmlspecialchars($goal_id); ?>" method="POST" class="admin-form">
                    <fieldset>
                        <legend>Edit Goal Details for User: <?php echo htmlspecialchars($goal_details['username']); ?></legend>
                        
                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($goal_details['title']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($goal_details['description']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="goal_type">Goal Type:</label>
                            <select id="goal_type" name="goal_type" required>
                                <?php 
                                $goal_types = ['weight', 'exercise_frequency', 'duration', 'distance', 'other'];
                                foreach ($goal_types as $type): ?>
                                    <option value="<?php echo $type; ?>" <?php echo ($goal_details['goal_type'] == $type) ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($type); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="target_value">Target Value:</label>
                            <input type="number" step="0.01" id="target_value" name="target_value" value="<?php echo htmlspecialchars($goal_details['target_value']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="current_value">Current Value:</label>
                            <input type="number" step="0.01" id="current_value" name="current_value" value="<?php echo htmlspecialchars($goal_details['current_value']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="start_date">Start Date:</label>
                            <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($goal_details['start_date']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="end_date">End Date (Optional):</label>
                            <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($goal_details['end_date']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select id="status" name="status" required>
                                <?php 
                                $statuses = ['active', 'completed', 'failed', 'cancelled'];
                                foreach ($statuses as $status): ?>
                                    <option value="<?php echo $status; ?>" <?php echo ($goal_details['status'] == $status) ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($status); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- TODO: Add field for individual_exercise_id if applicable, perhaps a select dropdown -->

                        <div class="form-group">
                            <button type="submit" name="update_goal" class="button-primary">Update Goal</button>
                        </div>
                    </fieldset>
                </form>
            <?php elseif (!$error_message):
                echo "<p>Goal data could not be loaded.</p>";
            endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Fitness Tracker Admin Panel</p>
    </footer>
</body>
</html>
