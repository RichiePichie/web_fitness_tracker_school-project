<?php
class GoalController {
    private $goalModel;
    
    public function __construct($goalModel) {
        $this->goalModel = $goalModel;
    }
    
    // Zobrazení přehledu cílů
    public function showGoals() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $goals = $this->goalModel->getAllByUser($userId);
        
        // Kontrola stavu cílů
        $this->goalModel->checkGoalStatus($userId);
        
        include __DIR__ . '/../views/goals.php';
    }
    
    // Zobrazení formuláře pro přidání cíle
    public function showAddGoal() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        include __DIR__ . '/../views/add_goal.php';
    }
    
    // Zpracování přidání cíle
    public function addGoal() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $goalType = $_POST['goal_type'] ?? '';
            $targetValue = (float)($_POST['target_value'] ?? 0);
            $currentValue = (float)($_POST['current_value'] ?? 0);
            $startDate = $_POST['start_date'] ?? date('Y-m-d');
            $endDate = $_POST['end_date'] ?? date('Y-m-d', strtotime('+30 days'));
            
            $errors = [];
            
            // Validace vstupu
            if (empty($title)) {
                $errors['title'] = 'Název cíle je povinný';
            }
            
            if (empty($goalType)) {
                $errors['goal_type'] = 'Typ cíle je povinný';
            }
            
            if ($targetValue <= 0 && $goalType !== 'weight') {
                $errors['target_value'] = 'Cílová hodnota musí být větší než 0';
            }
            
            if (empty($startDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
                $errors['start_date'] = 'Neplatné datum začátku';
            }
            
            if (empty($endDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
                $errors['end_date'] = 'Neplatné datum konce';
            }
            
            if (strtotime($endDate) <= strtotime($startDate)) {
                $errors['end_date'] = 'Datum konce musí být po datu začátku';
            }
            
            if (empty($errors)) {
                $goalId = $this->goalModel->create(
                    $userId,
                    $title,
                    $description,
                    $goalType,
                    $targetValue,
                    $currentValue,
                    $startDate,
                    $endDate
                );
                
                if ($goalId) {
                    $_SESSION['goal_added'] = true;
                    header('Location: index.php?page=goals');
                    exit;
                } else {
                    $errors['general'] = 'Nastala chyba při přidávání cíle';
                }
            }
            
            // Pokud došlo k chybě, uložíme chyby a data do session
            $_SESSION['goal_errors'] = $errors;
            $_SESSION['goal_data'] = [
                'title' => $title,
                'description' => $description,
                'goal_type' => $goalType,
                'target_value' => $targetValue,
                'current_value' => $currentValue,
                'start_date' => $startDate,
                'end_date' => $endDate
            ];
            
            header('Location: index.php?page=add_goal');
            exit;
        }
    }
    
    // Zobrazení formuláře pro editaci cíle
    public function showEditGoal() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $goalId = $_GET['id'] ?? 0;
        
        $goal = $this->goalModel->getById($goalId);
        
        if (!$goal || $goal['user_id'] != $userId) {
            header('Location: index.php?page=goals');
            exit;
        }
        
        include __DIR__ . '/../views/edit_goal.php';
    }
    
    // Zpracování editace cíle
    public function updateGoal() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $goalId = $_POST['id'] ?? 0;
            
            $goal = $this->goalModel->getById($goalId);
            
            if (!$goal || $goal['user_id'] != $userId) {
                header('Location: index.php?page=goals');
                exit;
            }
            
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $goalType = $_POST['goal_type'] ?? '';
            $targetValue = (float)($_POST['target_value'] ?? 0);
            $currentValue = (float)($_POST['current_value'] ?? 0);
            $startDate = $_POST['start_date'] ?? date('Y-m-d');
            $endDate = $_POST['end_date'] ?? date('Y-m-d', strtotime('+30 days'));
            $status = $_POST['status'] ?? 'active';
            
            $errors = [];
            
            // Validace vstupu
            if (empty($title)) {
                $errors['title'] = 'Název cíle je povinný';
            }
            
            if (empty($goalType)) {
                $errors['goal_type'] = 'Typ cíle je povinný';
            }
            
            if ($targetValue <= 0 && $goalType !== 'weight') {
                $errors['target_value'] = 'Cílová hodnota musí být větší než 0';
            }
            
            if (empty($startDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
                $errors['start_date'] = 'Neplatné datum začátku';
            }
            
            if (empty($endDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
                $errors['end_date'] = 'Neplatné datum konce';
            }
            
            if (strtotime($endDate) <= strtotime($startDate)) {
                $errors['end_date'] = 'Datum konce musí být po datu začátku';
            }
            
            if (empty($errors)) {
                $data = [
                    'title' => $title,
                    'description' => $description,
                    'goal_type' => $goalType,
                    'target_value' => $targetValue,
                    'current_value' => $currentValue,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => $status
                ];
                
                $result = $this->goalModel->update($goalId, $data);
                
                if ($result) {
                    $_SESSION['goal_updated'] = true;
                    header('Location: index.php?page=goals');
                    exit;
                } else {
                    $errors['general'] = 'Nastala chyba při aktualizaci cíle';
                }
            }
            
            // Pokud došlo k chybě, uložíme chyby do session
            $_SESSION['goal_errors'] = $errors;
            header('Location: index.php?page=edit_goal&id=' . $goalId);
            exit;
        }
    }
    
    // Zpracování aktualizace hodnoty cíle
    public function updateGoalValue() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $goalId = $_POST['id'] ?? 0;
            $currentValue = (float)($_POST['current_value'] ?? 0);
            
            $goal = $this->goalModel->getById($goalId);
            
            if (!$goal || $goal['user_id'] != $userId) {
                header('Location: index.php?page=goals');
                exit;
            }
            
            $result = $this->goalModel->updateCurrentValue($goalId, $currentValue);
            
            if ($result) {
                // Po aktualizaci hodnoty zkontrolujeme stav cíle
                $this->goalModel->checkGoalStatus($userId);
                $_SESSION['goal_value_updated'] = true;
            } else {
                $_SESSION['goal_value_update_error'] = true;
            }
            
            header('Location: index.php?page=goals');
            exit;
        }
    }
    
    // Zpracování odstranění cíle
    public function deleteGoal() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $goalId = $_POST['id'] ?? 0;
            
            $goal = $this->goalModel->getById($goalId);
            
            if (!$goal || $goal['user_id'] != $userId) {
                header('Location: index.php?page=goals');
                exit;
            }
            
            $result = $this->goalModel->delete($goalId);
            
            if ($result) {
                $_SESSION['goal_deleted'] = true;
            } else {
                $_SESSION['goal_delete_error'] = true;
            }
            
            header('Location: index.php?page=goals');
            exit;
        }
    }
}
?> 