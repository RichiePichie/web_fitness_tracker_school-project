<?php
require_once 'auth_check.php';
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['id'] ?? null;
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $user_type = $_POST['user_type'] ?? 'user';
    $gender = $_POST['gender'] ?? null;
    $height = !empty($_POST['height']) ? trim($_POST['height']) : null;
    $weight = !empty($_POST['weight']) ? trim($_POST['weight']) : null;
    $date_of_birth = !empty($_POST['date_of_birth']) ? trim($_POST['date_of_birth']) : null;
    $new_password = $_POST['password'] ?? '';

    // Store form data in session in case of error
    $_SESSION['form_data'] = $_POST;

    if (!$user_id || !filter_var($user_id, FILTER_VALIDATE_INT)) {
        $_SESSION['edit_user_error'] = 'Invalid user ID.';
        header('Location: users.php');
        exit;
    }

    // Basic Validation
    if (empty($username) || empty($email) || !in_array($user_type, ['user', 'admin'])) {
        $_SESSION['edit_user_error'] = 'Username, Email, and User Type are required.';
        header('Location: edit_user.php?id=' . $user_id);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['edit_user_error'] = 'Invalid email format.';
        header('Location: edit_user.php?id=' . $user_id);
        exit;
    }
    if ($gender !== null && !in_array($gender, ['male', 'female', 'other', ''])) {
        $_SESSION['edit_user_error'] = 'Invalid gender value.';
         header('Location: edit_user.php?id=' . $user_id);
        exit;
    }
    if ($height !== null && !filter_var($height, FILTER_VALIDATE_FLOAT) && $height !== '') {
        $_SESSION['edit_user_error'] = 'Invalid height value.';
        header('Location: edit_user.php?id=' . $user_id);
        exit;
    }
    if ($weight !== null && !filter_var($weight, FILTER_VALIDATE_FLOAT) && $weight !== '') {
        $_SESSION['edit_user_error'] = 'Invalid weight value.';
        header('Location: edit_user.php?id=' . $user_id);
        exit;
    }
    if ($date_of_birth !== null && !
        DateTime::createFromFormat('Y-m-d', $date_of_birth) && $date_of_birth !== '') {
        $_SESSION['edit_user_error'] = 'Invalid date of birth format. Use YYYY-MM-DD.';
        header('Location: edit_user.php?id=' . $user_id);
        exit;
    }

    try {
        // Check for unique username (if changed)
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username AND id != :id");
        $stmt->execute([':username' => $username, ':id' => $user_id]);
        if ($stmt->fetch()) {
            $_SESSION['edit_user_error'] = 'Username already taken by another user.';
            header('Location: edit_user.php?id=' . $user_id);
            exit;
        }

        // Check for unique email (if changed)
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
        $stmt->execute([':email' => $email, ':id' => $user_id]);
        if ($stmt->fetch()) {
            $_SESSION['edit_user_error'] = 'Email already taken by another user.';
            header('Location: edit_user.php?id=' . $user_id);
            exit;
        }

        // Prepare SQL update statement
        $sql_parts = [];
        $params = [];

        $sql_parts[] = "username = :username"; $params[':username'] = $username;
        $sql_parts[] = "email = :email"; $params[':email'] = $email;
        $sql_parts[] = "user_type = :user_type"; $params[':user_type'] = $user_type;
        $sql_parts[] = "gender = :gender"; $params[':gender'] = ($gender === '') ? null : $gender;
        $sql_parts[] = "height = :height"; $params[':height'] = ($height === '') ? null : $height;
        $sql_parts[] = "weight = :weight"; $params[':weight'] = ($weight === '') ? null : $weight;
        $sql_parts[] = "date_of_birth = :date_of_birth"; $params[':date_of_birth'] = ($date_of_birth === '') ? null : $date_of_birth;

        if (!empty($new_password)) {
            if (strlen($new_password) < 6) { // Basic password length check
                 $_SESSION['edit_user_error'] = 'New password must be at least 6 characters long.';
                 header('Location: edit_user.php?id=' . $user_id);
                 exit;
            }
            $sql_parts[] = "password = :password";
            $params[':password'] = password_hash($new_password, PASSWORD_DEFAULT);
        }

        $params[':id'] = $user_id;
        $sql = "UPDATE users SET " . implode(', ', $sql_parts) . " WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        unset($_SESSION['form_data']);
        $_SESSION['edit_user_success'] = 'User updated successfully.';
        header('Location: edit_user.php?id=' . $user_id);
        exit;

    } catch (PDOException $e) {
        // error_log('User update error: ' . $e->getMessage());
        $_SESSION['edit_user_error'] = 'An error occurred while updating the user: ' . $e->getMessage();
        header('Location: edit_user.php?id=' . $user_id);
        exit;
    }

} else {
    header('Location: users.php');
    exit;
}
?>
