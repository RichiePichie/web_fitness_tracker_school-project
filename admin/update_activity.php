<?php
require_once 'auth_check.php';
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: activities.php');
    exit;
}

$session_id = $_POST['session_id'] ?? null;
$user_id = $_POST['user_id'] ?? null; // For context, not usually changed here
$date = trim($_POST['date'] ?? '');
$start_time_str = trim($_POST['start_at'] ?? '');
$end_time_str = trim($_POST['end_at'] ?? '');
$notes = trim($_POST['notes'] ?? '');
$total_duration = !empty($_POST['total_duration']) ? (int)$_POST['total_duration'] : null;
$total_calories_burned = !empty($_POST['total_calories_burned']) ? (int)$_POST['total_calories_burned'] : null;
$exercise_entries_data = $_POST['entries'] ?? [];

// Store form data in session in case of error and redirect
$_SESSION['form_data'] = $_POST; 

if (!$session_id || !filter_var($session_id, FILTER_VALIDATE_INT)) {
    $_SESSION['edit_activity_error'] = 'Invalid Session ID.';
    header('Location: edit_activity.php?id=' . $session_id); // Or activities.php if ID is totally invalid
    exit;
}

// Basic Validation
if (empty($date)) {
    $_SESSION['edit_activity_error'] = 'Date is required.';
    header('Location: edit_activity.php?id=' . $session_id);
    exit;
}

$date_obj = DateTime::createFromFormat('Y-m-d', $date);
if (!$date_obj || $date_obj->format('Y-m-d') !== $date) {
    $_SESSION['edit_activity_error'] = 'Invalid date format. Use YYYY-MM-DD.';
    header('Location: edit_activity.php?id=' . $session_id);
    exit;
}

// Prepare start_at and end_at datetimes
$start_at_datetime = null;
if (!empty($start_time_str)) {
    if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $start_time_str)) {
        $_SESSION['edit_activity_error'] = 'Invalid start time format. Use HH:MM or HH:MM:SS.';
        header('Location: edit_activity.php?id=' . $session_id);
        exit;
    }
    $start_at_datetime = $date . ' ' . $start_time_str;
} else {
    // If start time is cleared, we might need to fetch original or handle as error
    // For now, let's assume it might be an error if it was previously set and now empty
    // Or, fetch the original start_at from DB to keep it if not provided.
    // For simplicity, if it's critical, make it required or handle fetching.
    // Here, we'll allow it to be null if the DB schema supports it, or it might fail if NOT NULL.
    // The schema says start_at DATETIME NOT NULL, so this needs to be handled.
    // Fetching original if cleared:
    try {
        $stmt_orig_start = $pdo->prepare("SELECT start_at FROM training_sessions WHERE id = :id");
        $stmt_orig_start->execute([':id' => $session_id]);
        $original_start_at = $stmt_orig_start->fetchColumn();
        if ($original_start_at) {
            $start_at_datetime = $original_start_at;
        } else {
            $_SESSION['edit_activity_error'] = 'Start time is required and could not be determined.';
            header('Location: edit_activity.php?id=' . $session_id);
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['edit_activity_error'] = 'Error fetching original start time.';
        header('Location: edit_activity.php?id=' . $session_id);
        exit;
    }
}

$end_at_datetime = null;
if (!empty($end_time_str)) {
    if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $end_time_str)) {
        $_SESSION['edit_activity_error'] = 'Invalid end time format. Use HH:MM or HH:MM:SS.';
        header('Location: edit_activity.php?id=' . $session_id);
        exit;
    }
    $end_at_datetime = $date . ' ' . $end_time_str;
}

try {
    $pdo->beginTransaction();

    // Update training_sessions table
    $sql_session = "UPDATE training_sessions 
                      SET date = :date, notes = :notes, total_duration = :total_duration, 
                          total_calories_burned = :total_calories_burned, start_at = :start_at, end_at = :end_at
                      WHERE id = :session_id";
    $stmt_session = $pdo->prepare($sql_session);
    $stmt_session->execute([
        ':date' => $date,
        ':notes' => $notes,
        ':total_duration' => $total_duration,
        ':total_calories_burned' => $total_calories_burned,
        ':start_at' => $start_at_datetime,
        ':end_at' => $end_at_datetime,
        ':session_id' => $session_id
    ]);

    // Update training_exercise_entries
    if (!empty($exercise_entries_data)) {
        $sql_entry = "UPDATE training_exercise_entries 
                        SET sets = :sets, reps = :reps, weight = :weight, 
                            duration = :duration, distance = :distance, calories_burned = :calories
                        WHERE id = :entry_id AND training_session_id = :session_id AND individual_exercise_id = :individual_exercise_id";
        $stmt_entry = $pdo->prepare($sql_entry);

        foreach ($exercise_entries_data as $entry_data) {
            $entry_id = $entry_data['entry_id'] ?? null;
            $individual_exercise_id = $entry_data['individual_exercise_id'] ?? null; // For verification

            if (!$entry_id || !filter_var($entry_id, FILTER_VALIDATE_INT) || 
                !$individual_exercise_id || !filter_var($individual_exercise_id, FILTER_VALIDATE_INT)) {
                throw new Exception('Invalid exercise entry ID or individual exercise ID.');
            }

            $params = [
                ':sets' => !empty($entry_data['sets']) ? (int)$entry_data['sets'] : null,
                ':reps' => !empty($entry_data['reps']) ? (int)$entry_data['reps'] : null,
                ':weight' => !empty($entry_data['weight']) ? (float)$entry_data['weight'] : null,
                ':duration' => !empty($entry_data['duration']) ? (int)$entry_data['duration'] : null,
                ':distance' => !empty($entry_data['distance']) ? (float)$entry_data['distance'] : null,
                ':calories' => !empty($entry_data['calories']) ? (int)$entry_data['calories'] : null,
                ':entry_id' => $entry_id,
                ':session_id' => $session_id, // Ensure entry belongs to the current session
                ':individual_exercise_id' => $individual_exercise_id // Ensure we're updating the correct exercise type
            ];
            $stmt_entry->execute($params);
        }
    }

    $pdo->commit();
    unset($_SESSION['form_data']); // Clear form data on success
    $_SESSION['edit_activity_success'] = 'Activity session updated successfully.';

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['edit_activity_error'] = 'Database error updating activity: ' . $e->getMessage();
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['edit_activity_error'] = 'Error updating activity: ' . $e->getMessage();
}

header('Location: edit_activity.php?id=' . $session_id);
exit;
?>
