<?php
require_once 'auth_check.php'; // Ensures admin is logged in
require_once '../config.php';   // Contains PDO connection

$session_id_to_delete = $_GET['id'] ?? null;

if (!$session_id_to_delete || !filter_var($session_id_to_delete, FILTER_VALIDATE_INT)) {
    $_SESSION['activity_management_error'] = 'Invalid activity ID for deletion.';
    header('Location: activities.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // Check if activity session exists
    $stmt_check = $pdo->prepare("SELECT id FROM training_sessions WHERE id = :id");
    $stmt_check->bindParam(':id', $session_id_to_delete, PDO::PARAM_INT);
    $stmt_check->execute();
    if ($stmt_check->fetch() === false) {
        $pdo->rollBack();
        $_SESSION['activity_management_error'] = 'Activity session not found, cannot delete.';
        header('Location: activities.php');
        exit;
    }

    // Delete associated training exercise entries first
    $stmt_delete_entries = $pdo->prepare("DELETE FROM training_exercise_entries WHERE training_session_id = :session_id");
    $stmt_delete_entries->bindParam(':session_id', $session_id_to_delete, PDO::PARAM_INT);
    $stmt_delete_entries->execute();

    // Then delete the training session itself
    $stmt_delete_session = $pdo->prepare("DELETE FROM training_sessions WHERE id = :id");
    $stmt_delete_session->bindParam(':id', $session_id_to_delete, PDO::PARAM_INT);
    
    if ($stmt_delete_session->execute()) {
        if ($stmt_delete_session->rowCount() > 0) {
            $pdo->commit();
            $_SESSION['activity_management_success'] = 'Activity session and its associated entries deleted successfully.';
        } else {
            $pdo->rollBack();
            $_SESSION['activity_management_error'] = 'Activity session not found or already deleted (after checking entries).';
        }
    } else {
        $pdo->rollBack();
        $_SESSION['activity_management_error'] = 'Failed to delete activity session. An error occurred.';
    }

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Log error properly in a real application
    // error_log('Activity deletion error: ' . $e->getMessage());
    $_SESSION['activity_management_error'] = 'An error occurred during activity deletion: ' . $e->getMessage();
}

header('Location: activities.php');
exit;
?>
