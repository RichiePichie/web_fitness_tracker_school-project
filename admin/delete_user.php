<?php
require_once 'auth_check.php'; // Ensures admin is logged in
require_once '../config.php';   // Contains PDO connection

$user_id_to_delete = $_GET['id'] ?? null;

if (!$user_id_to_delete || !filter_var($user_id_to_delete, FILTER_VALIDATE_INT)) {
    $_SESSION['user_management_error'] = 'Invalid user ID for deletion.';
    header('Location: users.php');
    exit;
}

// Prevent admin from deleting their own account
if (isset($_SESSION['admin_id']) && (int)$user_id_to_delete === (int)$_SESSION['admin_id']) {
    $_SESSION['user_management_error'] = 'You cannot delete your own administrator account.';
    header('Location: users.php');
    exit;
}

try {
    // Check if user exists before attempting to delete
    $stmt_check = $pdo->prepare("SELECT id FROM users WHERE id = :id");
    $stmt_check->bindParam(':id', $user_id_to_delete, PDO::PARAM_INT);
    $stmt_check->execute();
    if ($stmt_check->fetch() === false) {
        $_SESSION['user_management_error'] = 'User not found, cannot delete.';
        header('Location: users.php');
        exit;
    }

    // Proceed with deletion
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindParam(':id', $user_id_to_delete, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            $_SESSION['user_management_success'] = 'User deleted successfully.';
        } else {
            // This case should ideally be caught by the check above, but as a fallback
            $_SESSION['user_management_error'] = 'User not found or already deleted.';
        }
    } else {
        $_SESSION['user_management_error'] = 'Failed to delete user. An error occurred.';
    }

} catch (PDOException $e) {
    // Log error properly in a real application
    // error_log('User deletion error: ' . $e->getMessage());
    $_SESSION['user_management_error'] = 'An error occurred during user deletion: ' . $e->getMessage();
    // Check for foreign key constraint violation if possible (error codes vary by DB)
    if ($e->getCode() == '23000') { // General integrity constraint violation
         $_SESSION['user_management_error'] = 'Cannot delete user. They may have associated records (e.g., training sessions, goals). Please reassign or delete those records first.';
    }
}

header('Location: users.php');
exit;
?>
