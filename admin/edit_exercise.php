<?php
require_once 'auth_check.php'; // Ensures admin is logged in
require_once '../config.php';   // Contains PDO connection

$pageTitle = "Edit Exercise - Admin Panel";
$error_message = '';
$success_message = '';
$exercise = null;

// Check if this is a new exercise or editing existing
$isNewExercise = !isset($_GET['id']);

if (!$isNewExercise) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM individual_exercises WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $exercise = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$exercise) {
            $_SESSION['exercise_management_error'] = "Exercise not found.";
            header("Location: exercises.php");
            exit;
        }
    } catch (PDOException $e) {
        $error_message = "Error loading exercise: " . $e->getMessage();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $errors = [];
    
    // Validate name
    if (empty($_POST['name'])) {
        $errors[] = "Exercise name is required.";
    }
    
    // Validate exercise type
    $valid_types = ['cardio', 'strength', 'flexibility', 'balance', 'other'];
    if (empty($_POST['exercise_type']) || !in_array($_POST['exercise_type'], $valid_types)) {
        $errors[] = "Please select a valid exercise type.";
    }
    
    // Validate subtype
    $valid_subtypes = ['Distance', 'RepsSetsWeight', 'Reps'];
    if (empty($_POST['subtype']) || !in_array($_POST['subtype'], $valid_subtypes)) {
        $errors[] = "Please select a valid exercise subtype.";
    }
    
    // Validate subtype matches exercise type
    $valid_combinations = [
        'cardio' => ['Distance', 'Reps'],
        'strength' => ['RepsSetsWeight', 'Reps'],
        'flexibility' => ['Reps'],
        'balance' => ['Reps'],
        'other' => ['Reps']
    ];
    
    if (!empty($_POST['exercise_type']) && !empty($_POST['subtype'])) {
        if (!in_array($_POST['subtype'], $valid_combinations[$_POST['exercise_type']])) {
            $errors[] = "The selected subtype is not valid for this exercise type.";
        }
    }

    if (empty($errors)) {
        try {
            // Check if name already exists
            $checkStmt = $pdo->prepare("SELECT id FROM individual_exercises WHERE name = ? AND id != ?");
            $checkStmt->execute([$_POST['name'], $isNewExercise ? 0 : $_GET['id']]);
            if ($checkStmt->fetch()) {
                $error_message = "An exercise with this name already exists.";
            } else {
                if ($isNewExercise) {
                    // Create new exercise
                    $stmt = $pdo->prepare("INSERT INTO individual_exercises (name, description, exercise_type, subtype) VALUES (?, ?, ?, ?)");
                    $stmt->execute([
                        trim($_POST['name']),
                        trim($_POST['description']),
                        $_POST['exercise_type'],
                        $_POST['subtype']
                    ]);
                    $success_message = "Exercise created successfully!";
                } else {
                    // Update existing exercise
                    $stmt = $pdo->prepare("UPDATE individual_exercises SET name = ?, description = ?, exercise_type = ?, subtype = ? WHERE id = ?");
                    $stmt->execute([
                        trim($_POST['name']),
                        trim($_POST['description']),
                        $_POST['exercise_type'],
                        $_POST['subtype'],
                        $_GET['id']
                    ]);
                    $success_message = "Exercise updated successfully!";
                }
                
                $_SESSION['exercise_management_success'] = $success_message;
                header("Location: exercises.php");
                exit;
            }
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log("Error in edit_exercise.php: " . $e->getMessage());
            
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $error_message = "An exercise with this name already exists.";
            } else {
                $error_message = "Error saving exercise. Please try again.";
            }
        }
    } else {
        $error_message = implode("<br>", $errors);
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
    <link rel="stylesheet" href="../public/css/admin.css">
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
                            <a href="users.php">
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
                            <a href="exercises.php" class="active">
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
                    <h1><?php echo $isNewExercise ? 'Add New Exercise' : 'Edit Exercise'; ?></h1>
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
                            <label for="name">Exercise Name:</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($exercise['name'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="exercise_type">Exercise Type:</label>
                            <select id="exercise_type" name="exercise_type" required>
                                <option value="">Select Type</option>
                                <option value="cardio" <?php echo (($exercise['exercise_type'] ?? '') === 'cardio') ? 'selected' : ''; ?>>Cardio</option>
                                <option value="strength" <?php echo (($exercise['exercise_type'] ?? '') === 'strength') ? 'selected' : ''; ?>>Strength</option>
                                <option value="flexibility" <?php echo (($exercise['exercise_type'] ?? '') === 'flexibility') ? 'selected' : ''; ?>>Flexibility</option>
                                <option value="balance" <?php echo (($exercise['exercise_type'] ?? '') === 'balance') ? 'selected' : ''; ?>>Balance</option>
                                <option value="other" <?php echo (($exercise['exercise_type'] ?? '') === 'other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="subtype">Exercise Subtype:</label>
                            <select id="subtype" name="subtype" required>
                                <option value="">Select Subtype</option>
                                <option value="Distance" <?php echo (($exercise['subtype'] ?? '') === 'Distance') ? 'selected' : ''; ?>>Distance</option>
                                <option value="RepsSetsWeight" <?php echo (($exercise['subtype'] ?? '') === 'RepsSetsWeight') ? 'selected' : ''; ?>>Reps, Sets & Weight</option>
                                <option value="Reps" <?php echo (($exercise['subtype'] ?? '') === 'Reps') ? 'selected' : ''; ?>>Reps Only</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($exercise['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                <?php echo $isNewExercise ? 'Create Exercise' : 'Update Exercise'; ?>
                            </button>
                            <a href="exercises.php" class="btn btn-outline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 