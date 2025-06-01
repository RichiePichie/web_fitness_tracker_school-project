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
        $exercises = $this->exerciseModel->getAllTrainingSessionsByUser($userId);
        include __DIR__ . '/../views/exercises.php';
    }
    
    // Zobrazení formuláře pro přidání cvičení
    public function showAddExercise() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $exerciseModel = $this->exerciseModel;
        include __DIR__ . '/../views/add_exercise.php';
    }
    
    // Zobrazení stránky pro výběr cviku
    public function showSelectExercise() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $exerciseModel = $this->exerciseModel;
        include __DIR__ . '/../views/select_exercise.php';
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
        
        $exercise = $this->exerciseModel->getTrainingSessionById($exerciseId);
        
        if (!$exercise || $exercise['user_id'] != $userId) {
            header('Location: index.php?page=exercises');
            exit;
        }
        
        include __DIR__ . '/../views/edit_exercise.php';
    }
    
    // Zpracování editace cvičení
    public function updateExercise() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $exerciseId = $_GET['id'] ?? 0;
            
            $exercise = $this->exerciseModel->getTrainingSessionById($exerciseId);
            
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
                $errors['title'] = 'Nazev cviceni je povinny';
            }
            
            if (empty($exerciseType)) {
                $errors['exercise_type'] = 'Typ cviceni je povinny';
            }
            
            if ($duration <= 0) {
                $errors['duration'] = 'Doba trvani musi byt vetsi nez 0';
            }
            
            if (empty($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $errors['date'] = 'Neplatne datum';
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
                    $errors['general'] = 'Nastala chyba pri aktualizaci cviceni';
                }
            }
            
            // Pokud doslo k chybe, ulozime chyby do session
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
            $exerciseId = $_GET['id'] ?? 0;
            
            $exercise = $this->exerciseModel->getTrainingSessionById($exerciseId);
            
            if (!$exercise || $exercise['user_id'] != $userId) {
                header('Location: index.php?page=exercises');
                exit;
            }
            
            $result = $this->exerciseModel->deleteTrainingSession($exerciseId);
            
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
        
        include __DIR__ . '/../views/exercise_stats.php';
    }
    
    // Zpracování vybraného cviku
    public function handleSelectedExercise() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        if (isset($_GET['selected_exercise']) && isset($_GET['index'])) {
            $selectedExerciseId = $_GET['selected_exercise'];
            $selectedExercise = $this->exerciseModel->getExerciseById($selectedExerciseId);
            
            if ($selectedExercise) {
                // Inicializace session pole pro cviky, pokud neexistuje
                if (!isset($_SESSION['selected_exercises'])) {
                    $_SESSION['selected_exercises'] = [];
                }
                
                // Najdeme nejvyšší dostupný index
                $maxIndex = -1;
                if (!empty($_SESSION['selected_exercises'])) {
                    $maxIndex = max(array_keys($_SESSION['selected_exercises']));
                }
                
                // Použijeme buď nejvyšší index + 1, nebo 0, pokud je pole prázdné
                $newIndex = $maxIndex + 1;

                // Zachováme existující data cviku, pokud existují
                $existingExercise = isset($_SESSION['selected_exercises'][$newIndex]) ? $_SESSION['selected_exercises'][$newIndex] : null;
                
                // Vytvoříme nový záznam cviku s kombinací nových a existujících dat
                $_SESSION['selected_exercises'][$newIndex] = [
                    'id' => $selectedExercise['id'],
                    'name' => $selectedExercise['name'],
                    'description' => $selectedExercise['description'],
                    'exercise_type' => $selectedExercise['exercise_type'],
                    'sets' => $existingExercise['sets'] ?? '',
                    'reps' => $existingExercise['reps'] ?? '',
                    'weight' => $existingExercise['weight'] ?? '',
                    'distance' => $existingExercise['distance'] ?? ''
                ];
            }
        }
        
        // Přesměrování zpět na formulář
        $returnTo = $_GET['return_to'] ?? '';
        header('Location: index.php?page=' . $returnTo);
        exit;
    }

    // Odstranění cviku ze sessionu
    public function removeExerciseFromSession() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['index'])) {
            $index = $_POST['index'];
            
            if (isset($_SESSION['selected_exercises'][$index])) {
                unset($_SESSION['selected_exercises'][$index]);
                // Přeskládání indexů
                $_SESSION['selected_exercises'] = array_values($_SESSION['selected_exercises']);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['error' => 'Exercise not found in session']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
        }
        exit;
    }

    // Zpracování přidání tréninku s cviky
    public function saveTraining() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $date = $_POST['date'] ?? '';
            $notes = $_POST['notes'] ?? '';
            $exercises = $_POST['exercises'] ?? [];

            // Debug: Vypis prijatych dat
            error_log('Data z formulare: ' . print_r(["user_id" => $userId, "date" => $date, "notes" => $notes, "exercises" => $exercises], true));

            $errors = [];
            
            // Validace vstupu
            if (empty($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $errors['date'] = 'Neplatné datum';
            }

            if (empty($exercises)) {
                $errors['exercises'] = 'Musite pridat alespoň jeden cvik';
                error_log('Chyba: zadne cviky nebyly zadany');
            }
            
            // Validace cviků
            foreach ($exercises as $index => $exercise) {
                if (empty($exercise['exercise_id'])) {
                    $errors['exercises'][$index] = 'Musíte vybrat cvik';
                    continue;
                }
                
                // Validace podle typu cviku
                $exerciseData = $this->exerciseModel->getExerciseById($exercise['exercise_id']);
                if ($exerciseData) {
                    if ($exerciseData['exercise_type'] === 'strength') {
                        if (empty($exercise['sets']) || empty($exercise['reps'])) {
                            $errors['exercises'][$index] = 'Pro silový cvik musíte vyplnit série a opakování';
                        }
                    } elseif ($exerciseData['exercise_type'] === 'cardio') {
                        if (empty($exercise['distance'])) {
                            $errors['exercises'][$index] = 'Pro kardio cvik musíte vyplnit vzdálenost';
                        }
                    }
                }
            }
            
            if (empty($errors)) {
                try {
                    // Začátek transakce
                    $this->exerciseModel->beginTransaction();
                    
                    // Vytvoření tréninkové jednotky
                    $trainingSessionId = $this->exerciseModel->createTrainingSession(
                        $userId, 
                        $date, 
                        null, // total_duration
                        null, // total_calories_burned
                        $notes
                    );
                    
                    if (!$trainingSessionId) {
                        throw new Exception('Nepodařilo se vytvořit tréninkovou jednotku');
                    }
                    
                    // Přidání cviků do tréninku
                    foreach ($exercises as $exercise) {
                        $success = $this->exerciseModel->addExerciseToSession(
                            $trainingSessionId,
                            $exercise['exercise_id'],
                            $exercise['sets'] ?? null,
                            $exercise['reps'] ?? null,
                            $exercise['weight'] ?? null,
                            $exercise['distance'] ?? null
                        );
                        
                        if (!$success) {
                            throw new Exception('Nepodařilo se přidat cvik do tréninku');
                        }
                    }
                    
                    // Commit transakce
                    $this->exerciseModel->commitTransaction();
                    
                    // Vyčištění session
                    unset($_SESSION['selected_exercises']);
                    unset($_SESSION['exercise_data']);
                    
                    $_SESSION['training_added'] = true;
                    header('Location: index.php?page=exercises');
                    exit;
                    
                } catch (Exception $e) {
                    // Rollback v případě chyby
                    $this->exerciseModel->rollbackTransaction();
                    $errors['general'] = 'Nastala chyba při ukládání tréninku: ' . $e->getMessage();
                }
            }
            
            // Pokud došlo k chybě, uložíme chyby a data do session
            $_SESSION['exercise_errors'] = $errors;
            $_SESSION['exercise_data'] = [
                'date' => $date,
                'notes' => $notes,
                'exercises' => $exercises,
                'duration' => $duration,
                'calories_burned' => $caloriesBurned
            ];
            
            header('Location: index.php?page=add_exercise');
            exit;
        }
    }
}
?> 