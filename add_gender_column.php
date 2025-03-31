<?php
// Database connection details
$host = 'localhost';
$db = 'fitness_tracker';
$user = 'root';
$pass = '';

echo "Adding gender column to users table...\n";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if gender column already exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'gender'");
    if ($stmt->rowCount() > 0) {
        echo "Gender column already exists.\n";
    } else {
        // Add gender column
        $sql = "ALTER TABLE users ADD COLUMN gender VARCHAR(10) NULL AFTER last_name";
        $pdo->exec($sql);
        echo "Gender column added successfully.\n";
    }
    
    // Verify the table structure
    echo "Current users table structure:\n";
    $stmt = $pdo->query("DESCRIBE users");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " - " . $row['Type'] . " - " . ($row['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 