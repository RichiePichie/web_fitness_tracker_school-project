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
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
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
        $setFields = [];
        $params = [':id' => $id];
        
        foreach ($data as $key => $value) {
            if ($key !== 'id' && $key !== 'password') {
                $setFields[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }
        
        if (isset($data['password']) && !empty($data['password'])) {
            $setFields[] = "password = :password";
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        $setClause = implode(', ', $setFields);
        $sql = "UPDATE users SET $setClause WHERE id = :id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Nahrání profilového obrázku
    public function uploadProfileImage($id, $imagePath) {
        $sql = "UPDATE users SET profile_image = :imagePath WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':imagePath' => $imagePath, ':id' => $id]);
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

    // Get last error
    public function getLastError() {
        return $this->lastError;
    }
}
?> 