<?php
require_once 'auth_check.php'; // Ensures admin is logged in
require_once '../config.php';   // Contains PDO connection

$pageTitle = "Edit Activity - Admin Panel";
$session_id = $_GET['id'] ?? null;
$session_details = null;
$exercise_entries = [];
$error_message = '';
$success_message = '';

if (!$session_id || !filter_var($session_id, FILTER_VALIDATE_INT)) {
    $_SESSION['activity_management_error'] = 'Invalid activity ID.';
    header('Location: activities.php');
    exit;
}

// Fetch session data and associated exercises
try {
    $stmt_session = $pdo->prepare(
        "SELECT ts.id, ts.user_id, ts.date, ts.total_duration, ts.total_calories_burned, ts.notes, ts.start_at, ts.end_at, u.username 
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
        "SELECT tee.id as entry_id, tee.individual_exercise_id, ie.name as exercise_name, ie.exercise_type, 
                tee.sets, tee.reps, tee.weight, tee.duration as exercise_duration, tee.distance, tee.calories_burned as exercise_calories
         FROM training_exercise_entries tee
         JOIN individual_exercises ie ON tee.individual_exercise_id = ie.id
         WHERE tee.training_session_id = :session_id
         ORDER BY tee.id ASC"
    );
    $stmt_exercises->bindParam(':session_id', $session_id, PDO::PARAM_INT);
    $stmt_exercises->execute();
    $exercise_entries = $stmt_exercises->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_message = "Error fetching activity data: " . $e->getMessage();
}

// Handle session messages for feedback
if (isset($_SESSION['edit_activity_error'])) {
    $error_message = $_SESSION['edit_activity_error'];
    unset($_SESSION['edit_activity_error']);
}
if (isset($_SESSION['edit_activity_success'])) {
    $success_message = $_SESSION['edit_activity_success'];
    unset($_SESSION['edit_activity_success']);
}
// If form data was stored due to an error, repopulate (more complex for exercises)
if (isset($_SESSION['form_data'])) {
    $form_data = $_SESSION['form_data'];
    $session_details['date'] = $form_data['date'] ?? $session_details['date'];
    $session_details['notes'] = $form_data['notes'] ?? $session_details['notes'];
    // Repopulating exercise entries from session is more complex and usually handled by re-fetching or careful JS
    unset($_SESSION['form_data']);
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
            <h2>Edit Activity Session (ID: <?php echo htmlspecialchars($session_id); ?>)</h2>
            <p><strong>User:</strong> <?php echo htmlspecialchars($session_details['username'] ?? 'N/A'); ?></p>

            <?php if ($error_message): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
            <?php endif; ?>

            <?php if ($session_details && !$error_message): ?>
            <form action="update_activity.php" method="post">
                <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($session_details['id']); ?>">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($session_details['user_id']); ?>">

                <h3>Session Details</h3>
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($session_details['date'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="start_at">Start Time (HH:MM:SS or HH:MM):</label>
                    <input type="text" id="start_at" name="start_at" value="<?php echo htmlspecialchars(substr($session_details['start_at'], 11, 8) ?? ''); ?>" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?">
                    <small>Original full start time: <?php echo htmlspecialchars($session_details['start_at'] ?? ''); ?></small>
                </div>
                 <div class="form-group">
                    <label for="end_at">End Time (HH:MM:SS or HH:MM, optional):</label>
                    <input type="text" id="end_at" name="end_at" value="<?php echo htmlspecialchars(substr($session_details['end_at'] ?? '', 11, 8) ?? ''); ?>" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?">
                     <small>Original full end time: <?php echo htmlspecialchars($session_details['end_at'] ?? 'Not set'); ?></small>
                </div>
                <div class="form-group">
                    <label for="notes">Notes:</label>
                    <textarea id="notes" name="notes"><?php echo htmlspecialchars($session_details['notes'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="total_duration">Total Duration (minutes, optional, will be auto-calculated if exercises have duration):</label>
                    <input type="number" id="total_duration" name="total_duration" value="<?php echo htmlspecialchars($session_details['total_duration'] ?? ''); ?>" min="0" step="1">
                </div>
                <div class="form-group">
                    <label for="total_calories_burned">Total Calories Burned (optional, will be auto-calculated if exercises have calories):</label>
                    <input type="number" id="total_calories_burned" name="total_calories_burned" value="<?php echo htmlspecialchars($session_details['total_calories_burned'] ?? ''); ?>" min="0" step="1">
                </div>

                <h3>Logged Exercises</h3>
                <?php if (!empty($exercise_entries)): ?>
                    <table class="exercise-table">
                        <thead>
                            <tr>
                                <th>Exercise Name</th>
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
                            <?php foreach ($exercise_entries as $index => $entry): ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($entry['exercise_name']); ?>
                                        <input type="hidden" name="entries[<?php echo $index; ?>][entry_id]" value="<?php echo htmlspecialchars($entry['entry_id']); ?>">
                                        <input type="hidden" name="entries[<?php echo $index; ?>][individual_exercise_id]" value="<?php echo htmlspecialchars($entry['individual_exercise_id']); ?>">
                                    </td>
                                    <td><?php echo htmlspecialchars($entry['exercise_type']); ?></td>
                                    <td><input type="number" name="entries[<?php echo $index; ?>][sets]" value="<?php echo htmlspecialchars($entry['sets'] ?? ''); ?>" min="0"></td>
                                    <td><input type="number" name="entries[<?php echo $index; ?>][reps]" value="<?php echo htmlspecialchars($entry['reps'] ?? ''); ?>" min="0"></td>
                                    <td><input type="number" step="0.01" name="entries[<?php echo $index; ?>][weight]" value="<?php echo htmlspecialchars($entry['weight'] ?? ''); ?>" min="0"></td>
                                    <td><input type="number" name="entries[<?php echo $index; ?>][duration]" value="<?php echo htmlspecialchars($entry['exercise_duration'] ?? ''); ?>" min="0"></td>
                                    <td><input type="number" step="0.01" name="entries[<?php echo $index; ?>][distance]" value="<?php echo htmlspecialchars($entry['distance'] ?? ''); ?>" min="0"></td>
                                    <td><input type="number" name="entries[<?php echo $index; ?>][calories]" value="<?php echo htmlspecialchars($entry['exercise_calories'] ?? ''); ?>" min="0"></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No specific exercises were logged for this session. You can add them if this feature is implemented.</p>
                <?php endif; ?>
                
                <!-- Placeholder for adding new exercises to the session -->
                <!-- <button type="button" id="addExerciseButton">Add Another Exercise</button> -->

                <div class="form-actions">
                    <button type="submit">Update Activity</button>
                    <a href="activity_details.php?id=<?php echo htmlspecialchars($session_id); ?>">Cancel</a>
                </div>
            </form>
            <?php elseif (!$error_message): ?>
                <p>Activity session data could not be loaded.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Fitness Tracker Admin Panel</p>
    </footer>
    <!-- Script for potential future enhancements like adding exercises dynamically -->
    <!-- <script>
        document.getElementById('addExerciseButton')?.addEventListener('click', function() {
            // Logic to add a new row to the exercises table
            alert('Functionality to add new exercises dynamically to be implemented.');
        });
    </script> -->
</body>
</html>
