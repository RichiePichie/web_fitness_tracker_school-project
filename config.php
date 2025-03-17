<?php
$host = 'localhost';
$db = 'fitness_tracker';
$user = 'root';
$pass = '';

// Vytvoření připojení k databázi
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Připojení k databázi selhalo: " . $e->getMessage();
    exit;
}

// Autoload pro třídy
spl_autoload_register(function ($class_name) {
    $directories = array(
        'src/models/',
        'src/controllers/'
    );
    
    foreach ($directories as $directory) {
        $file = $directory . $class_name . '.php';
        if (file_exists($file)) {
            require_once($file);
            return;
        }
    }
});
?> 