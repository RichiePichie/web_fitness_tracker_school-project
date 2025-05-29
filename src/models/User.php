<?php
class User {
    private $pdo;
    private $lastError = [];

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Registrace nového uživatele
    public function register($username, $email, $password, $gender) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, email, password, gender) 
                VALUES (:username, :email, :password, :gender)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashedPassword,
                ':gender' => $gender
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            $this->lastError[] = $e->getMessage();
            error_log("Registration error: " . $e->getMessage());
            return false;
        }
    }

    // Přihlášení uživatele
    public function login($email, $password) {
        $sql = "SELECT id, username, email, password, user_type, gender, height, weight, 
                date_of_birth, profile_image, created_at 
                FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        
        return false;
    }

    // Získání uživatele podle ID
    public function getById($id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Aktualizace profilu uživatele
    public function updateProfile($id, $data) {
        $allowedFields = ['username', 'email', 'gender', 'height', 'weight', 'date_of_birth'];
        $setFields = [];
        $params = [':id' => $id];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $setFields[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }
        
        if (isset($data['password']) && !empty($data['password'])) {
            $setFields[] = "password = :password";
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (empty($setFields)) {
            return false;
        }
        
        $setClause = implode(', ', $setFields);
        error_log("Data, které se updatují: " . print_r($setClause, true));
        $sql = "UPDATE users SET $setClause, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return true;
            
        } catch (PDOException $e) {
            $this->lastError[] = $e->getMessage();
            error_log("Profile update error: " . $e->getMessage());
            return false;
        }
    }


    // Kontrola, zda uživatelské jméno již existuje
    public function usernameExists($username) {
        $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        return $stmt->fetchColumn() > 0;
    }

    // Kontrola, zda email již existuje
    public function emailExists($email) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    // Získání statistik uživatele
    public function getUserStats($userId) {
        $sql = "SELECT 
                (SELECT COUNT(*) FROM training_sessions WHERE user_id = :userId) as total_sessions,
                (SELECT COUNT(*) FROM user_goals WHERE user_id = :userId) as total_goals,
                (SELECT COUNT(*) FROM user_goals WHERE user_id = :userId AND status = 'completed') as completed_goals
                FROM dual";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Získání poslední chyby
    public function getLastError() {
        return $this->lastError;
    }
}
?> 