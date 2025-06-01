<?php
require_once 'auth_check.php'; // Ensures admin is logged in
require_once '../config.php';   // Contains PDO connection

if (isset($_GET['id'])) {
    try {
        // Check if goal exists
        $stmt = $pdo->prepare("SELECT id FROM user_goals WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        if (!$stmt->fetch()) {
            $_SESSION['goal_management_error'] = "Goal not found.";
        } else {
            // Delete the goal
            $stmt = $pdo->prepare("DELETE FROM user_goals WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $_SESSION['goal_management_success'] = "Goal deleted successfully.";
        }
    } catch (PDOException $e) {
        // Log error for debugging
        error_log("Error in delete_goal_admin.php: " . $e->getMessage());
        $_SESSION['goal_management_error'] = "Error deleting goal. Please try again.";
    }
}

// Redirect back to goals page
header("Location: manage_goals.php");
exit; 