<?php
class ExerciseController {
    private $exerciseModel;
    
    public function __construct($exerciseModel) {
        $this->exerciseModel = $exerciseModel;
    }
    
    // Zobrazení přehledu cvičení
    public function showExercises() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $exercises = $this->exerciseModel->getAllByUser($userId);
        
        include 'src/views/exercises.php';
    }
    
    // Zobrazení formuláře pro přidání cvičení
    public function showAddExercise() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        include 'src/views/add_exercise.php';
    }
    
    // Zpracování přidání cvičení
    public function addExercise() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $exerciseType = $_POST['exercise_type'] ?? '';
            $duration = (int)($_POST['duration'] ?? 0);
            $caloriesBurned = (int)($_POST['calories_burned'] ?? 0);
            $date = $_POST['date'] ?? date('Y-m-d');
            
            $errors = [];
            
            // Validace vstupu
            if (empty($title)) {
                $errors['title'] = 'Název cvičení je povinný';
            }
            
            if (empty($exerciseType)) {
                $errors['exercise_type'] = 'Typ cvičení je povinný';
            }
            
            if ($duration <= 0) {
                $errors['duration'] = 'Doba trvání musí být větší než 0';
            }
            
            if (empty($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $errors['date'] = 'Neplatné datum';
            }
            
            if (empty($errors)) {
                $exerciseId = $this->exerciseModel->create(
                    $userId,
                    $title,
                    $description,
                    $exerciseType,
                    $duration,
                    $caloriesBurned,
                    $date
                );
                
                if ($exerciseId) {
                    $_SESSION['exercise_added'] = true;
                    header('Location: index.php?page=exercises');
                    exit;
                } else {
                    $errors['general'] = 'Nastala chyba při přidávání cvičení';
                }
            }
            
            // Pokud došlo k chybě, uložíme chyby a data do session
            $_SESSION['exercise_errors'] = $errors;
            $_SESSION['exercise_data'] = [
                'title' => $title,
                'description' => $description,
                'exercise_type' => $exerciseType,
                'duration' => $duration,
                'calories_burned' => $caloriesBurned,
                'date' => $date
            ];
            
            header('Location: index.php?page=add_exercise');
            exit;
        }
    }
    
    // Zobrazení formuláře pro editaci cvičení
    public function showEditExercise() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $exerciseId = $_GET['id'] ?? 0;
        
        $exercise = $this->exerciseModel->getById($exerciseId);
        
        if (!$exercise || $exercise['user_id'] != $userId) {
            header('Location: index.php?page=exercises');
            exit;
        }
        
        include 'src/views/edit_exercise.php';
    }
    
    // Zpracování editace cvičení
    public function updateExercise() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $exerciseId = $_POST['id'] ?? 0;
            
            $exercise = $this->exerciseModel->getById($exerciseId);
            
            if (!$exercise || $exercise['user_id'] != $userId) {
                header('Location: index.php?page=exercises');
                exit;
            }
            
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $exerciseType = $_POST['exercise_type'] ?? '';
            $duration = (int)($_POST['duration'] ?? 0);
            $caloriesBurned = (int)($_POST['calories_burned'] ?? 0);
            $date = $_POST['date'] ?? date('Y-m-d');
            
            $errors = [];
            
            // Validace vstupu
            if (empty($title)) {
                $errors['title'] = 'Název cvičení je povinný';
            }
            
            if (empty($exerciseType)) {
                $errors['exercise_type'] = 'Typ cvičení je povinný';
            }
            
            if ($duration <= 0) {
                $errors['duration'] = 'Doba trvání musí být větší než 0';
            }
            
            if (empty($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $errors['date'] = 'Neplatné datum';
            }
            
            if (empty($errors)) {
                $data = [
                    'title' => $title,
                    'description' => $description,
                    'exercise_type' => $exerciseType,
                    'duration' => $duration,
                    'calories_burned' => $caloriesBurned,
                    'date' => $date
                ];
                
                $result = $this->exerciseModel->update($exerciseId, $data);
                
                if ($result) {
                    $_SESSION['exercise_updated'] = true;
                    header('Location: index.php?page=exercises');
                    exit;
                } else {
                    $errors['general'] = 'Nastala chyba při aktualizaci cvičení';
                }
            }
            
            // Pokud došlo k chybě, uložíme chyby do session
            $_SESSION['exercise_errors'] = $errors;
            header('Location: index.php?page=edit_exercise&id=' . $exerciseId);
            exit;
        }
    }
    
    // Zpracování odstranění cvičení
    public function deleteExercise() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $exerciseId = $_POST['id'] ?? 0;
            
            $exercise = $this->exerciseModel->getById($exerciseId);
            
            if (!$exercise || $exercise['user_id'] != $userId) {
                header('Location: index.php?page=exercises');
                exit;
            }
            
            $result = $this->exerciseModel->delete($exerciseId);
            
            if ($result) {
                $_SESSION['exercise_deleted'] = true;
            } else {
                $_SESSION['exercise_delete_error'] = true;
            }
            
            header('Location: index.php?page=exercises');
            exit;
        }
    }
    
    // Zobrazení statistik cvičení
    public function showStats() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $stats = $this->exerciseModel->getUserStats($userId);
        $recentExercises = $this->exerciseModel->getRecentByUser($userId, 30);
        
        include 'src/views/exercise_stats.php';
    }
}
?> 