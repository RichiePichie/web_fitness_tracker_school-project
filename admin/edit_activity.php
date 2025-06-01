<?php
require_once 'auth_check.php'; // Ensures admin is logged in
require_once '../config.php';   // Contains PDO connection

$pageTitle = "Edit Activity - Admin Panel";
$error_message = '';
$success_message = '';
$activity = null;

// Check if this is a new activity or editing existing
$isNewActivity = !isset($_GET['id']);

if (!$isNewActivity) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM training_sessions WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $activity = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$activity) {
            $_SESSION['activity_management_error'] = "Activity not found.";
            header("Location: activities.php");
            exit;
        }
    } catch (PDOException $e) {
        $error_message = "Error loading activity: " . $e->getMessage();
    }
}

// Get users for dropdown
try {
    $stmt = $pdo->query("SELECT id, username FROM users ORDER BY username");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get exercises for dropdown
    $stmt = $pdo->query("SELECT id, name, exercise_type, subtype FROM individual_exercises ORDER BY name");
    $exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If editing, get existing exercises for this activity
    $activityExercises = [];
    if (!$isNewActivity) {
        $stmt = $pdo->prepare("
            SELECT tee.*, ie.name, ie.exercise_type, ie.subtype 
            FROM training_exercise_entries tee
            JOIN individual_exercises ie ON tee.individual_exercise_id = ie.id
            WHERE tee.training_session_id = ?
        ");
        $stmt->execute([$_GET['id']]);
        $activityExercises = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $error_message = "Error loading data: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        if ($isNewActivity) {
            // Create new activity
            $stmt = $pdo->prepare("INSERT INTO training_sessions (user_id, date, total_duration, total_calories_burned, notes, start_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $_POST['user_id'],
                $_POST['date'],
                $_POST['total_duration'],
                $_POST['total_calories_burned'],
                $_POST['notes']
            ]);
            $activityId = $pdo->lastInsertId();
            $success_message = "Activity created successfully!";
        } else {
            // Update existing activity
            $stmt = $pdo->prepare("UPDATE training_sessions SET user_id = ?, date = ?, total_duration = ?, total_calories_burned = ?, notes = ? WHERE id = ?");
            $stmt->execute([
                $_POST['user_id'],
                $_POST['date'],
                $_POST['total_duration'],
                $_POST['total_calories_burned'],
                $_POST['notes'],
                $_GET['id']
            ]);
            $activityId = $_GET['id'];
            
            // Delete existing exercise entries
            $stmt = $pdo->prepare("DELETE FROM training_exercise_entries WHERE training_session_id = ?");
            $stmt->execute([$activityId]);
        }

        // Add exercise entries if any were submitted
        if (isset($_POST['exercises']) && is_array($_POST['exercises'])) {
            $stmt = $pdo->prepare("
                INSERT INTO training_exercise_entries 
                (training_session_id, individual_exercise_id, sets, reps, weight, duration, distance, calories_burned) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($_POST['exercises'] as $exercise) {
                if (empty($exercise['exercise_id'])) continue;
                
                $stmt->execute([
                    $activityId,
                    $exercise['exercise_id'],
                    $exercise['sets'] ?? null,
                    $exercise['reps'] ?? null,
                    $exercise['weight'] ?? null,
                    $exercise['duration'] ?? null,
                    $exercise['distance'] ?? null,
                    $exercise['calories_burned'] ?? null
                ]);
            }
        }

        $pdo->commit();
        $_SESSION['activity_management_success'] = $success_message;
        header("Location: activities.php");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error_message = "Error saving activity: " . $e->getMessage();
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
                            <a href="activities.php" class="active">
                                <i class="fas fa-running"></i>
                                <span>Activities</span>
                            </a>
                        </li>
                        <li>
                            <a href="exercises.php">
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
                    <h1><?php echo $isNewActivity ? 'Add New Activity' : 'Edit Activity'; ?></h1>
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
                            <label for="user_id">User:</label>
                            <select id="user_id" name="user_id" required>
                                <option value="">Select User</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['id']; ?>" <?php echo (($activity['user_id'] ?? '') == $user['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($user['username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="date">Date:</label>
                            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($activity['date'] ?? date('Y-m-d')); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="total_duration">Duration (minutes):</label>
                            <input type="number" id="total_duration" name="total_duration" value="<?php echo htmlspecialchars($activity['total_duration'] ?? ''); ?>" required min="1">
                        </div>

                        <div class="form-group">
                            <label for="total_calories_burned">Calories Burned:</label>
                            <input type="number" id="total_calories_burned" name="total_calories_burned" value="<?php echo htmlspecialchars($activity['total_calories_burned'] ?? ''); ?>" required min="0">
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes:</label>
                            <textarea id="notes" name="notes" rows="4"><?php echo htmlspecialchars($activity['notes'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Exercises:</label>
                            <div id="exercises-container">
                                <?php if (!empty($activityExercises)): ?>
                                    <?php foreach ($activityExercises as $index => $exercise): ?>
                                        <div class="exercise-entry">
                                            <select name="exercises[<?php echo $index; ?>][exercise_id]" class="exercise-select" required>
                                                <option value="">Select Exercise</option>
                                                <?php foreach ($exercises as $ex): ?>
                                                    <option value="<?php echo $ex['id']; ?>" 
                                                            data-type="<?php echo $ex['exercise_type']; ?>"
                                                            data-subtype="<?php echo $ex['subtype']; ?>"
                                                            <?php echo ($exercise['individual_exercise_id'] == $ex['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($ex['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="exercise-details">
                                                <div class="sets-reps-weight" style="display: <?php echo in_array($exercise['subtype'], ['RepsSetsWeight', 'Reps']) ? 'flex' : 'none'; ?>">
                                                    <input type="number" name="exercises[<?php echo $index; ?>][sets]" placeholder="Sets" value="<?php echo htmlspecialchars($exercise['sets'] ?? ''); ?>" min="1">
                                                    <input type="number" name="exercises[<?php echo $index; ?>][reps]" placeholder="Reps" value="<?php echo htmlspecialchars($exercise['reps'] ?? ''); ?>" min="1">
                                                    <?php if ($exercise['subtype'] === 'RepsSetsWeight'): ?>
                                                        <input type="number" name="exercises[<?php echo $index; ?>][weight]" placeholder="Weight (kg)" value="<?php echo htmlspecialchars($exercise['weight'] ?? ''); ?>" step="0.1" min="0">
                                                    <?php endif; ?>
                                                </div>
                                                <div class="duration-distance" style="display: <?php echo $exercise['subtype'] === 'Distance' ? 'flex' : 'none'; ?>">
                                                    <input type="number" name="exercises[<?php echo $index; ?>][distance]" placeholder="Distance (km)" value="<?php echo htmlspecialchars($exercise['distance'] ?? ''); ?>" step="0.01" min="0">
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-icon btn-danger remove-exercise" title="Remove Exercise">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn btn-outline" id="add-exercise">
                                <i class="fas fa-plus"></i> Add Exercise
                            </button>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                <?php echo $isNewActivity ? 'Create Activity' : 'Update Activity'; ?>
                            </button>
                            <a href="activities.php" class="btn btn-outline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <template id="exercise-template">
        <div class="exercise-entry">
            <select name="exercises[INDEX][exercise_id]" class="exercise-select" required>
                <option value="">Select Exercise</option>
                <?php foreach ($exercises as $exercise): ?>
                    <option value="<?php echo $exercise['id']; ?>" 
                            data-type="<?php echo $exercise['exercise_type']; ?>"
                            data-subtype="<?php echo $exercise['subtype']; ?>">
                        <?php echo htmlspecialchars($exercise['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="exercise-details">
                <div class="sets-reps-weight" style="display: none">
                    <input type="number" name="exercises[INDEX][sets]" placeholder="Sets" min="1">
                    <input type="number" name="exercises[INDEX][reps]" placeholder="Reps" min="1">
                    <input type="number" name="exercises[INDEX][weight]" placeholder="Weight (kg)" step="0.1" min="0" class="weight-input">
                </div>
                <div class="duration-distance" style="display: none">
                    <input type="number" name="exercises[INDEX][distance]" placeholder="Distance (km)" step="0.01" min="0">
                </div>
            </div>
            <button type="button" class="btn btn-icon btn-danger remove-exercise" title="Remove Exercise">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('exercises-container');
            const addButton = document.getElementById('add-exercise');
            const template = document.getElementById('exercise-template');
            let exerciseCount = <?php echo !empty($activityExercises) ? count($activityExercises) : 0; ?>;

            function updateExerciseFields(select) {
                const entry = select.closest('.exercise-entry');
                const option = select.selectedOptions[0];
                const subtype = option?.dataset?.subtype;
                
                const setsRepsWeight = entry.querySelector('.sets-reps-weight');
                const durationDistance = entry.querySelector('.duration-distance');
                const weightInput = entry.querySelector('.weight-input');
                
                setsRepsWeight.style.display = ['RepsSetsWeight', 'Reps'].includes(subtype) ? 'flex' : 'none';
                durationDistance.style.display = subtype === 'Distance' ? 'flex' : 'none';
                
                if (weightInput) {
                    weightInput.style.display = subtype === 'RepsSetsWeight' ? 'block' : 'none';
                }
            }

            function addExercise() {
                const clone = template.content.cloneNode(true);
                const newEntry = clone.querySelector('.exercise-entry');
                
                // Update indices
                newEntry.querySelectorAll('[name*="[INDEX]"]').forEach(element => {
                    element.name = element.name.replace('INDEX', exerciseCount);
                });
                
                // Add event listeners
                const select = newEntry.querySelector('.exercise-select');
                select.addEventListener('change', () => updateExerciseFields(select));
                
                newEntry.querySelector('.remove-exercise').addEventListener('click', function() {
                    newEntry.remove();
                });
                
                container.appendChild(newEntry);
                exerciseCount++;
            }

            // Add event listener to add button
            addButton.addEventListener('click', addExercise);

            // Add event listeners to existing exercise selects
            document.querySelectorAll('.exercise-select').forEach(select => {
                select.addEventListener('change', () => updateExerciseFields(select));
            });

            // Add event listeners to existing remove buttons
            document.querySelectorAll('.remove-exercise').forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.exercise-entry').remove();
                });
            });

            // Add at least one exercise entry if none exist
            if (exerciseCount === 0) {
                addExercise();
            }
        });
    </script>
</body>
</html>
