<?php
require_once 'auth_check.php'; // Ensures admin is logged in
require_once '../config.php';   // Contains PDO connection

$pageTitle = "Activity Details - Admin Panel";
$session_id = $_GET['id'] ?? null;
$session_details = null;
$exercise_entries = [];
$error_message = '';

if (!$session_id || !filter_var($session_id, FILTER_VALIDATE_INT)) {
    $_SESSION['activity_management_error'] = 'Invalid activity ID.';
    header('Location: activities.php');
    exit;
}

// Fetch session details
try {
    $stmt_session = $pdo->prepare(
        "SELECT ts.*, u.username 
         FROM training_sessions ts
         JOIN users u ON ts.user_id = u.id
         WHERE ts.id = :id"
    );
    $stmt_session->bindParam(':id', $session_id, PDO::PARAM_INT);
    $stmt_session->execute();
    $session_details = $stmt_session->fetch(PDO::FETCH_ASSOC);

    if (!$session_details) {
        $_SESSION['activity_management_error'] = 'Activity session not found.';
        header('Location: activities.php');
        exit;
    }

    // Fetch exercise entries for this session
    $stmt_exercises = $pdo->prepare(
        "SELECT tee.*, ie.name as exercise_name, ie.exercise_type 
         FROM training_exercise_entries tee
         JOIN individual_exercises ie ON tee.individual_exercise_id = ie.id
         WHERE tee.training_session_id = :session_id
         ORDER BY tee.id ASC"
    );
    $stmt_exercises->bindParam(':session_id', $session_id, PDO::PARAM_INT);
    $stmt_exercises->execute();
    $exercise_entries = $stmt_exercises->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_message = "Error fetching activity details: " . $e->getMessage();
    // Log this error
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; color: #333; }
        .container { max-width: 900px; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        header { background-color: #333; color: #fff; padding: 1em 0; text-align: center; }
        header h1 { margin: 0; }
        nav ul { list-style-type: none; padding: 0; text-align: center; background-color: #444; margin-bottom: 20px; }
        nav ul li { display: inline; }
        nav ul li a { display: inline-block; padding: 10px 20px; color: #fff; text-decoration: none; }
        nav ul li a:hover { background-color: #555; }
        .content { padding: 20px; }
        .content h2, .content h3 { color: #333; margin-bottom: 15px; }
        .details-section, .exercises-section { margin-bottom: 30px; padding: 15px; border: 1px solid #eee; border-radius: 5px; background-color: #fdfdfd; }
        .details-section p { margin: 5px 0; }
        .details-section strong { display: inline-block; width: 150px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table th { background-color: #f0f0f0; }
        .error-message { color: red; border: 1px solid red; padding: 10px; margin-bottom: 20px; background-color: #ffecec; }
        .back-link { display: inline-block; margin-bottom:20px; color: #337ab7; text-decoration:none; }
        .back-link:hover { text-decoration:underline; }
        footer { text-align: center; padding: 20px; background-color: #333; color: #fff; margin-top: 30px;}
    </style>
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
    </header>

    <nav>
        <ul>
            <li><a href="../index.php" target="_blank">View Main Site</a></li>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="users.php">Manage Users</a></li>
            <li><a href="activities.php">Manage Activities</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['admin_username']); ?>)</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="content">
            <a href="activities.php" class="back-link">&laquo; Back to Activities List</a>
            <h2>Activity Session Details</h2>

            <?php if ($error_message): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <?php if ($session_details && !$error_message): ?>
                <div class="details-section">
                    <h3>Session Information</h3>
                    <div style="margin-bottom: 15px;">
                        <a href="edit_activity.php?id=<?php echo $session_details['id']; ?>" 
                           style="background-color: #5bc0de; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; font-size: 0.9em; margin-right: 10px;">Edit This Activity</a>
                        <a href="delete_activity.php?id=<?php echo $session_details['id']; ?>" 
                           onclick="return confirm('Are you sure you want to delete this activity session and all its entries? This action cannot be undone.');" 
                           style="background-color: #d9534f; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; font-size: 0.9em;">Delete This Activity</a>
                    </div>
                    <p><strong>Session ID:</strong> <?php echo htmlspecialchars($session_details['id']); ?></p>
                    <p><strong>User:</strong> <?php echo htmlspecialchars($session_details['username']); ?> (ID: <?php echo htmlspecialchars($session_details['user_id']); ?>)</p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($session_details['date']); ?></p>
                    <p><strong>Start Time:</strong> <?php echo htmlspecialchars($session_details['start_at']); ?></p>
                    <p><strong>End Time:</strong> <?php echo htmlspecialchars($session_details['end_at'] ?? 'N/A'); ?></p>
                    <p><strong>Total Duration:</strong> <?php echo htmlspecialchars($session_details['total_duration'] ?? 'N/A'); ?> minutes</p>
                    <p><strong>Calories Burned:</strong> <?php echo htmlspecialchars($session_details['total_calories_burned'] ?? 'N/A'); ?></p>
                    <p><strong>Notes:</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($session_details['notes'] ?? 'N/A')); ?></p>
                </div>

                <div class="exercises-section">
                    <h3>Exercises Performed</h3>
                    <?php if (!empty($exercise_entries)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Exercise Name</th>
                                    <th>Type</th>
                                    <th>Sets</th>
                                    <th>Reps</th>
                                    <th>Weight (kg)</th>
                                    <th>Duration (min)</th>
                                    <th>Distance (km)</th>
                                    <th>Calories Burned</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($exercise_entries as $entry): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($entry['exercise_name']); ?></td>
                                        <td><?php echo htmlspecialchars($entry['exercise_type']); ?></td>
                                        <td><?php echo htmlspecialchars($entry['sets'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($entry['reps'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($entry['weight'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($entry['duration'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($entry['distance'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($entry['calories_burned'] ?? 'N/A'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No specific exercises logged for this session.</p>
                    <?php endif; ?>
                </div>
            <?php elseif (!$error_message): ?>
                <p>Activity session data could not be loaded.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Fitness Tracker Admin Panel</p>
    </footer>
</body>
</html>
