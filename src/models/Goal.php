<?php
class Goal {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Vytvoření nového cíle
    public function create($userId, $title, $description, $goalType, $targetValue, $currentValue, $startDate, $endDate) {
        $sql = "INSERT INTO user_goals (user_id, title, description, goal_type, target_value, current_value, start_date, end_date) 
                VALUES (:userId, :title, :description, :goalType, :targetValue, :currentValue, :startDate, :endDate)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':userId' => $userId,
                ':title' => $title,
                ':description' => $description,
                ':goalType' => $goalType,
                ':targetValue' => $targetValue,
                ':currentValue' => $currentValue,
                ':startDate' => $startDate,
                ':endDate' => $endDate
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Získání cíle podle ID
    public function getById($id) {
        $sql = "SELECT * FROM user_goals WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Získání všech cílů uživatele
    public function getAllByUser($userId) {
        $sql = "SELECT * FROM user_goals WHERE user_id = :userId ORDER BY end_date ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Získání aktivních cílů uživatele
    public function getActiveByUser($userId) {
        $sql = "SELECT * FROM user_goals 
                WHERE user_id = :userId 
                AND status = 'active' 
                AND end_date >= CURDATE() 
                ORDER BY end_date ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Aktualizace cíle
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
        $sql = "UPDATE user_goals SET $setClause WHERE id = :id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Aktualizace aktuální hodnoty cíle
    public function updateCurrentValue($id, $currentValue) {
        $sql = "UPDATE user_goals SET current_value = :currentValue WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':currentValue' => $currentValue, ':id' => $id]);
    }

    // Změna stavu cíle
    public function updateStatus($id, $status) {
        $sql = "UPDATE user_goals SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    // Odstranění cíle
    public function delete($id) {
        $sql = "DELETE FROM user_goals WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // Kontrola a aktualizace stavu cílů
    public function checkGoalStatus($userId) {
        // Získání aktivních cílů
        $sql = "SELECT * FROM user_goals WHERE user_id = :userId AND status = 'active'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        $goals = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($goals as $goal) {
            // Pokud je cíl po termínu, označíme ho jako selhaný
            if (strtotime($goal['end_date']) < strtotime(date('Y-m-d'))) {
                $this->updateStatus($goal['id'], 'failed');
                continue;
            }
            
            // Kontrola, zda byl cíl splněn
            if ($goal['goal_type'] === 'weight') {
                // U váhových cílů záleží na směru (zhubnout / přibrat)
                if (($goal['target_value'] <= $goal['current_value'] && $goal['target_value'] > 0) ||
                    ($goal['target_value'] >= $goal['current_value'] && $goal['target_value'] < 0)) {
                    $this->updateStatus($goal['id'], 'completed');
                }
            } else {
                // Pro ostatní typy cílů
                if ($goal['current_value'] >= $goal['target_value']) {
                    $this->updateStatus($goal['id'], 'completed');
                }
            }
        }
        
        return true;
    }
}
?> 