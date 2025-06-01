<?php
class ExerciseController {
    private $exerciseModel;
    
    public function __construct($exerciseModel) {
        $this->exerciseModel = $exerciseModel;
    }
    
    // Zobrazeni prehledu cviceni
    public function showExercises() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        // Clear stale exercise form session data
        unset($_SESSION['exercise_errors']);
        unset($_SESSION['exercise_data']);
        unset($_SESSION['selected_exercises']);
        
        $userId = $_SESSION['user_id'];
        $exercises = $this->exerciseModel->getAllTrainingSessionsByUser($userId);
        // $exerciseModel = $this->exerciseModel; // This line is redundant
        include __DIR__ . '/../views/exercises.php';
    }
    
    // Zobrazeni formulare pro pridani cviceni
    public function showAddExercise() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $exerciseModel = $this->exerciseModel;
        include __DIR__ . '/../views/add_exercise.php';
    }
    
    // Zobrazeni stranky pro vyber cviku
    public function showSelectExercise() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $exerciseModel = $this->exerciseModel;
        include __DIR__ . '/../views/select_exercise.php';
    }
    
    // Zpracovani pridani cviceni
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
                // $exerciseId = $this->exerciseModel->create(
                //     $userId,
                //     $title,
                //     $description,
                //     $exerciseType,
                //     $duration,
                //     $caloriesBurned,
                //     $date
                // );
                
                // if ($exerciseId) {
                //     $_SESSION['exercise_added'] = true;
                //     header('Location: index.php?page=exercises');
                //     exit;
                // } else {
                //     $errors['general'] = 'Nastala chyba pri pridavani cviceni';
                // }
                $errors['general'] = 'Funkce pro pridavani tohoto typu cviceni neni momentalne dostupna.';
            }
            
            // Pokud doslo k chybe, ulozime chyby a data do session
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
    
    // Zobrazeni formulare pro editaci cviceni
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
    
    // Zpracovani editace cviceni
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
    
    // Zpracovani odstraneni cviceni
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
    
    // Zobrazeni statistik cviceni
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
    
    // Zpracovani vybraneho cviku
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
                
                // Najdeme nejvyssi dostupny index
                $maxIndex = -1;
                if (!empty($_SESSION['selected_exercises'])) {
                    $maxIndex = max(array_keys($_SESSION['selected_exercises']));
                }
                
                // Pouzijeme buď nejvyssi index + 1, nebo 0, pokud je pole prazdne
                $newIndex = $maxIndex + 1;

                // Zachovame existujici data cviku, pokud existuji
                $existingExercise = isset($_SESSION['selected_exercises'][$newIndex]) ? $_SESSION['selected_exercises'][$newIndex] : null;
                
                // Vytvorime novy zaznam cviku s kombinaci novych a existujicich dat
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
        
        // Presmerovani zpet na formular
        $returnTo = $_GET['return_to'] ?? '';
        header('Location: index.php?page=' . $returnTo);
        exit;
    }

    // Odstraneni cviku ze sessionu
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
                // Preskladani indexu
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

    // Zpracovani pridani treninku s cviky
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
                $errors['date'] = 'Neplatne datum';
            }

            if (empty($exercises)) {
                $errors['exercises'] = 'Musite pridat alespoň jeden cvik';
                error_log('Chyba: zadne cviky nebyly zadany');
            }

            // Validace cviku
            foreach ($exercises as $index => $exercise) {
                if (empty($exercise['exercise_id'])) {
                    $errors['exercises'][$index] = 'Musite vybrat cvik';
                    error_log('Chyba: Cvik #' . $index . ' nema zadane ID');
                    continue;
                }

                // Validace podle typu cviku
                $exerciseData = $this->exerciseModel->getExerciseById($exercise['exercise_id']);
                if ($exerciseData) {
                    error_log('Cvik #' . $index . ' (' . $exercise['exercise_id'] . '): ' . json_encode($exerciseData));
                    // Kontrola podle enum hodnoty v databazi
                    if ($exerciseData['exercise_type'] === 'strength' || $exerciseData['exercise_type'] === 'silovy') {
                        if (empty($exercise['sets']) || empty($exercise['reps'])) {
                            $errors['exercises'][$index] = 'Pro silovy cvik musite vyplnit serie a opakovani';
                            error_log('Chyba: Silovy cvik #' . $index . ' nema serie nebo opakovani');
                        }
                    } elseif ($exerciseData['exercise_type'] === 'cardio' || $exerciseData['exercise_type'] === 'kardio') {
                        if (empty($exercise['distance'])) {
                            $errors['exercises'][$index] = 'Pro kardio cvik musite vyplnit vzdalenost';
                            error_log('Chyba: Kardio cvik #' . $index . ' nema vzdalenost');
                        }
                    }
                    // Vypsani typu cviceni pro debug ucely
                    error_log('Typ cviceni: ' . $exerciseData['exercise_type']);
                } else {
                    $errors['exercises'][$index] = 'Vybrany cvik neexistuje.';
                    error_log('Chyba: Cvik s ID ' . $exercise['exercise_id'] . ' neexistuje');
                }
            }

            // Pokud nejsou chyby, vytvorime trenink a pridame cviky
            if (empty($errors)) {
                try {
                    // Zacatek transakce
                    error_log('Zacinam transakci pro vytvoreni treninku');
                    $this->exerciseModel->beginTransaction();
                    
                    // Vytvoreni treninkove jednotky
                    $trainingSessionId = $this->exerciseModel->createTrainingSession(
                        $userId, 
                        $date, 
                        null, // total_duration
                        null, // total_calories_burned
                        $notes
                    );
                    
                    if (!$trainingSessionId) {
                        error_log('CHYBA: Nepodarilo se vytvorit treninkovou jednotku - ID: ' . var_export($trainingSessionId, true));
                        throw new Exception('Nepodarilo se vytvorit treninkovou jednotku');
                    }
                    
                    error_log('Vytvorena treninkova jednotka s ID: ' . $trainingSessionId);

                    // Pridani cviku do treninku
                    foreach ($exercises as $index => $exercise) {
                        error_log('Pridavani cviku #' . $index . ' (ID: ' . $exercise['exercise_id'] . ') do treninku');
                        $sets = (isset($exercise['sets']) && $exercise['sets'] !== '') ? $exercise['sets'] : 0;
                        $reps = (isset($exercise['reps']) && $exercise['reps'] !== '') ? $exercise['reps'] : 0;
                        $weight = (isset($exercise['weight']) && $exercise['weight'] !== '') ? $exercise['weight'] : 0;
                        $distance = (isset($exercise['distance']) && $exercise['distance'] !== '') ? $exercise['distance'] : 0;
                        
                        error_log('Parametry pro addExerciseToSession: sets=' . var_export($sets, true) . 
                                 ', reps=' . var_export($reps, true) . 
                                 ', weight=' . var_export($weight, true) . 
                                 ', distance=' . var_export($distance, true));
                        
                        $success = $this->exerciseModel->addExerciseToSession(
                            $trainingSessionId,
                            $exercise['exercise_id'],
                            $sets,
                            $reps,
                            $weight,
                            $distance
                        );

                        if (!$success) {
                            error_log('CHYBA: Nepodarilo se pridat cvik #' . $index . ' (ID cviku: ' . $exercise['exercise_id'] . ') do treninkove jednotky ID: ' . $trainingSessionId . '. Metoda addExerciseToSession vratila false.');
                            throw new Exception('Nepodarilo se pridat cvik #' . $index . ' do treninku');
                        }
                        error_log('Cvik #' . $index . ' (ID cviku: ' . $exercise['exercise_id'] . ') uspesne pridan do treninkove jednotky ID: ' . $trainingSessionId . '.');
                    }

                    // Commit transakce
                    $this->exerciseModel->commitTransaction();
                    error_log('Transakce uspesne dokoncena');

                    // Vycisteni session
                    unset($_SESSION['selected_exercises']);
                    unset($_SESSION['exercise_data']);

                    $_SESSION['training_added'] = true;
                    error_log('Trenink uspesne pridan, presmerovani na prehled');
                    header('Location: index.php?page=exercises');
                    exit;
                } catch (Exception $e) {
                    // Rollback v pripade chyby
                    $this->exerciseModel->rollbackTransaction();
                    error_log('CHYBA pri vytvareni treninku: ' . $e->getMessage());
                    $errors['general'] = 'Nastala chyba: ' . $e->getMessage();
                }
            }

            // Pokud doslo k chybe, ulozime chyby a data do session
            $_SESSION['exercise_errors'] = $errors;
            $_SESSION['exercise_data'] = [
                'date' => $date,
                'notes' => $notes,
                'exercises' => $exercises
            ];
            
            header('Location: index.php?page=add_exercise');
            exit;
        }
    }
}
?> 