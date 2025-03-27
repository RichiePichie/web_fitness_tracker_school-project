<?php
// Connect without specifying a database first
try {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if fitness_tracker database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'fitness_tracker'");
    if ($stmt->rowCount() == 0) {
        // Create database
        $pdo->exec("CREATE DATABASE fitness_tracker");
        echo "Database 'fitness_tracker' created successfully!<br>";
    }
    
    // Select the database
    $pdo->exec("USE fitness_tracker");
    echo "Using database: fitness_tracker<br>";
    
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function to check if a column exists in a table
function columnExists($pdo, $table, $column) {
    $sql = "SHOW COLUMNS FROM {$table} LIKE '{$column}'";
    $result = $pdo->query($sql);
    return $result->rowCount() > 0;
}

// Function to check if a table exists
function tableExists($pdo, $table) {
    $sql = "SHOW TABLES LIKE '{$table}'";
    $result = $pdo->query($sql);
    return $result->rowCount() > 0;
}

// Check if users table exists
if (!tableExists($pdo, 'users')) {
    // Create users table
    $sql = "CREATE TABLE users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        first_name VARCHAR(50) NULL,
        last_name VARCHAR(50) NULL,
        gender VARCHAR(10) NULL,
        height FLOAT NULL,
        weight FLOAT NULL,
        date_of_birth DATE NULL,
        profile_image VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Users table created successfully!<br>";
} else {
    echo "Users table already exists.<br>";
    
    // Add gender column if it doesn't exist
    if (!columnExists($pdo, 'users', 'gender')) {
        try {
            $sql = "ALTER TABLE users ADD COLUMN gender VARCHAR(10) NULL AFTER last_name";
            $pdo->exec($sql);
            echo "Added gender column to users table.<br>";
        } catch (PDOException $e) {
            echo "Error adding gender column: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "Gender column already exists in users table.<br>";
    }
}

// Show the current structure of the users table
try {
    $sql = "DESCRIBE users";
    $stmt = $pdo->query($sql);
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Current Users Table Structure:</h3>";
    echo "<pre>";
    foreach ($columns as $column) {
        echo $column['Field'] . " - " . $column['Type'] . " - " . ($column['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
    }
    echo "</pre>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

echo "<p>Database setup completed. <a href='index.php'>Go to homepage</a></p>"; 