<?php
// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$host = 'localhost';
$db = 'fitness_tracker';
$user = 'root';
$pass = '';

$output = "Testing database connection...\n";

// Open file for writing
$file = fopen('db_test_results.txt', 'w');

// Test database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $output .= "Connection successful!\n";
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        $output .= "Users table exists.\n";
        
        // Check table structure
        $output .= "Users table structure:\n";
        $stmt = $pdo->query("DESCRIBE users");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $output .= $row['Field'] . " - " . $row['Type'] . " - " . ($row['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
        }
        
        // Test username collision
        $output .= "Testing username collision...\n";
        $username = "test.user";
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $count = $stmt->fetchColumn();
        $output .= "Username '$username' exists: " . ($count > 0 ? 'Yes' : 'No') . "\n";
        
        // Check existing entries
        $output .= "Existing users:\n";
        $stmt = $pdo->query("SELECT id, username, email FROM users LIMIT 5");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $output .= "ID: " . $row['id'] . ", Username: " . $row['username'] . ", Email: " . $row['email'] . "\n";
        }
    } else {
        $output .= "Users table does not exist.\n";
        $output .= "Creating users table...\n";
        
        // Create users table
        $sql = "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            first_name VARCHAR(50),
            last_name VARCHAR(50),
            gender VARCHAR(10),
            height DECIMAL(5,2) DEFAULT NULL,
            weight DECIMAL(5,2) DEFAULT NULL,
            date_of_birth DATE DEFAULT NULL,
            profile_image VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $pdo->exec($sql);
        $output .= "Users table created successfully.\n";
    }
    
} catch (PDOException $e) {
    $output .= "Connection failed: " . $e->getMessage() . "\n";
}

// Write output to file
fwrite($file, $output);
fclose($file);

// Also print to standard output
echo "Test completed, results saved to db_test_results.txt\n";
?> 