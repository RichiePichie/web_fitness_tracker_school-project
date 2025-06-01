<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'config.php';

// Základní směrování
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Zpracování požadavků na kontrolery
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        
        // Akce uživatele
        if ($action === 'login') {
            $userController = new UserController(new User($pdo));
            $userController->login();
        } elseif ($action === 'register') {
            $userController = new UserController(new User($pdo));
            $userController->register();
        } elseif ($action === 'logout') {
            $userController = new UserController(new User($pdo));
            $userController->logout();
        } elseif ($action === 'update_profile') {
            $userController = new UserController(new User($pdo));
            $userController->updateProfile();
        }
        
        // Akce cvičení
        elseif ($action === 'add_exercise') {
            $exerciseController = new ExerciseController(new Exercise($pdo));
            $exerciseController->addExercise();
        } elseif ($action === 'update_exercise') {
            $exerciseController = new ExerciseController(new Exercise($pdo));
            $exerciseController->updateExercise();
        } elseif ($action === 'delete_exercise') {
            $exerciseController = new ExerciseController(new Exercise($pdo));
            $exerciseController->deleteExercise();
        }
        
        // Akce cílů
        elseif ($action === 'add_goal') {
            $goalController = new GoalController(new Goal($pdo));
            $goalController->addGoal();
        } elseif ($action === 'update_goal') {
            $goalController = new GoalController(new Goal($pdo));
            $goalController->updateGoal();
        } elseif ($action === 'update_goal_value') {
            $goalController = new GoalController(new Goal($pdo));
            $goalController->updateGoalValue();
        } elseif ($action === 'delete_goal') {
            $goalController = new GoalController(new Goal($pdo));
            $goalController->deleteGoal();
        }
    }
}

// Zobrazení stránek
if ($page === 'login' ) {
    if (isset($_SESSION['user_id'])) {
        header('Location: index.php?page=dashboard');
        exit();
    }
    include __DIR__ . '/src/views/login.php';
} elseif ($page === 'register') {
    if (isset($_SESSION['user_id'])) {
        header('Location: index.php?page=dashboard');
        exit();
    }
    include __DIR__ . '/src/views/register.php';
} elseif ($page === 'profile' && isset($_SESSION['user_id'])) {
    $userController = new UserController(new User($pdo));
    $userController->showProfile();
} elseif ($page === 'dashboard' && isset($_SESSION['user_id'])) {
    include __DIR__ . '/src/views/dashboard.php';
} 

// Cvičení
elseif ($page === 'exercises' && isset($_SESSION['user_id'])) {
    $exerciseController = new ExerciseController(new Exercise($pdo));
    $exerciseController->showExercises();
} elseif ($page === 'add_exercise' && isset($_SESSION['user_id'])) {
    include __DIR__ . '/src/views/add_exercise.php';
} elseif ($page === 'edit_exercise' && isset($_SESSION['user_id'])) {
    $exerciseController = new ExerciseController(new Exercise($pdo));
    $exerciseController->showEditExercise();
} elseif ($page === 'exercise_stats' && isset($_SESSION['user_id'])) {
    $exerciseController = new ExerciseController(new Exercise($pdo));
    $exerciseController->showStats();
}

// Cíle
elseif ($page === 'goals' && isset($_SESSION['user_id'])) {
    $goalController = new GoalController(new Goal($pdo));
    $goalController->showGoals();
} elseif ($page === 'add_goal' && isset($_SESSION['user_id'])) {
    include __DIR__ . '/src/views/add_goal.php';
} elseif ($page === 'edit_goal' && isset($_SESSION['user_id'])) {
    $goalController = new GoalController(new Goal($pdo));
    $goalController->showEditGoal();
} else {
    // Výchozí stránka
    include __DIR__ . '/src/views/home.php';
}
?> 