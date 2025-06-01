<?php
require_once 'auth_check.php'; // Ensures admin is logged in
require_once '../config.php';   // Contains PDO connection

if (isset($_GET['id'])) {
    try {
        // Check if exercise exists
        $stmt = $pdo->prepare("SELECT id FROM individual_exercises WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        if (!$stmt->fetch()) {
            $_SESSION['exercise_management_error'] = "Exercise not found.";
        } else {
            // Delete the exercise
            $stmt = $pdo->prepare("DELETE FROM individual_exercises WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $_SESSION['exercise_management_success'] = "Exercise deleted successfully.";
        }
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
            $_SESSION['exercise_management_error'] = "Cannot delete this exercise because it is being used in activities or goals.";
        } else {
            $_SESSION['exercise_management_error'] = "Error deleting exercise: " . $e->getMessage();
        }
    }
}

header("Location: exercises.php");
exit; 