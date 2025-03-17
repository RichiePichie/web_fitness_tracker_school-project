<?php
class Exercise {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Přidání nového cvičení
    public function create($userId, $title, $description, $exerciseType, $duration, $caloriesBurned, $date) {
        $sql = "INSERT INTO exercises (user_id, title, description, exercise_type, duration, calories_burned, date) 
                VALUES (:userId, :title, :description, :exerciseType, :duration, :caloriesBurned, :date)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':userId' => $userId,
                ':title' => $title,
                ':description' => $description,
                ':exerciseType' => $exerciseType,
                ':duration' => $duration,
                ':caloriesBurned' => $caloriesBurned,
                ':date' => $date
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Získání cvičení podle ID
    public function getById($id) {
        $sql = "SELECT * FROM exercises WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Získání všech cvičení uživatele
    public function getAllByUser($userId, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM exercises WHERE user_id = :userId ORDER BY date DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        
        if ($limit !== null) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Aktualizace cvičení
    public function update($id, $data) {
        $setFields = [];
        $params = [':id' => $id];
        
        foreach ($data as $key => $value) {
            if ($key !== 'id' && $key !== 'user_id') {
                $setFields[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }
        
        $setClause = implode(', ', $setFields);
        $sql = "UPDATE exercises SET $setClause WHERE id = :id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Odstranění cvičení
    public function delete($id) {
        $sql = "DELETE FROM exercises WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // Získání statistik cvičení uživatele
    public function getUserStats($userId) {
        $sql = "SELECT 
                COUNT(*) as total_exercises, 
                SUM(duration) as total_duration, 
                SUM(calories_burned) as total_calories,
                COUNT(DISTINCT DATE(date)) as total_days
                FROM exercises 
                WHERE user_id = :userId";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Získání cvičení uživatele za posledních N dní
    public function getRecentByUser($userId, $days = 7) {
        $sql = "SELECT * FROM exercises 
                WHERE user_id = :userId 
                AND date >= DATE_SUB(CURDATE(), INTERVAL :days DAY) 
                ORDER BY date DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?> 