<?php
class Exercise {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Vytvoření nové tréninkové jednotky
    public function createTrainingSession($userId, $date, $totalDuration = null, $totalCaloriesBurned = null, $notes = null) {
        $sql = "INSERT INTO training_sessions (user_id, date, total_duration, total_calories_burned, notes) 
                VALUES (:userId, :date, :totalDuration, :totalCaloriesBurned, :notes)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':userId' => $userId,
                ':date' => $date,
                ':totalDuration' => $totalDuration,
                ':totalCaloriesBurned' => $totalCaloriesBurned,
                ':notes' => $notes
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Chyba při vytváření tréninkové jednotky: " . $e->getMessage());
            return false;
        }
    }

    // Přidání cviku do tréninkové jednotky
    public function addExerciseToSession($trainingSessionId, $individualExerciseId, $sets = null, $reps = null, $weight = null, $distance = null) {
        $sql = "INSERT INTO training_exercise_entries 
                (training_session_id, individual_exercise_id, sets, reps, weight, distance) 
                VALUES (:trainingSessionId, :individualExerciseId, :sets, :reps, :weight, :distance)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $executionSuccess = $stmt->execute([
                ':trainingSessionId' => $trainingSessionId,
                ':individualExerciseId' => $individualExerciseId,
                ':sets' => $sets,
                ':reps' => $reps,
                ':weight' => $weight,
                ':distance' => $distance
            ]);

            if ($executionSuccess) {
                $rowCount = $stmt->rowCount();
                if ($rowCount > 0) {
                    error_log("ÚSPĚCH: Cvik (ID: $individualExerciseId) úspěšně přidán do tréninkové jednotky (ID: $trainingSessionId). Počet ovlivněných řádků: $rowCount");
                    return true;
                } else {
                    error_log("VAROVÁNÍ: Metoda addExerciseToSession sice proběhla bez PDO výjimky, ale nebyly vloženy žádné řádky pro cvik (ID: $individualExerciseId) do tréninkové jednotky (ID: $trainingSessionId).");
                    return false;
                }
            } else {
                // Toto by se teoreticky nemělo stát, pokud je ERRMODE_EXCEPTION zapnutý a execute selže, měla by být vyvolána výjimka.
                // Ale pro jistotu logujeme.
                $errorInfo = $stmt->errorInfo();
                error_log("CHYBA: $stmt->execute() v addExerciseToSession vrátilo false. PDO error info: " . print_r($errorInfo, true));
                return false;
            }
        } catch (PDOException $e) {
            error_log("PDO CHYBA při přidávání cviku (ID: $individualExerciseId) do tréninkové jednotky (ID: $trainingSessionId): " . $e->getMessage());
            return false;
        }
    }

    // Získání tréninkové jednotky podle ID
    public function getTrainingSessionById($id) {
        $sql = "SELECT ts.*, 
                GROUP_CONCAT(
                    CONCAT(ie.name, '|', tee.sets, '|', tee.reps, '|', tee.weight, '|', tee.distance, '|', ie.exercise_type, '|', tee.individual_exercise_id)
                    SEPARATOR '||'
                ) as exercises
                FROM training_sessions ts
                LEFT JOIN training_exercise_entries tee ON ts.id = tee.training_session_id
                LEFT JOIN individual_exercises ie ON tee.individual_exercise_id = ie.id
                WHERE ts.id = :id
                GROUP BY ts.id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Získání všech tréninkových jednotek uživatele
    public function getAllTrainingSessionsByUser($userId, $limit = null, $offset = 0) {
        
        $sql = "SELECT ts.*, 
                GROUP_CONCAT(
                    CONCAT(ie.name, '|', tee.sets, '|', tee.reps, '|', tee.weight, '|', tee.distance, '|', ie.exercise_type)
                    SEPARATOR '||'
                ) as exercises
                FROM training_sessions ts
                LEFT JOIN training_exercise_entries tee ON ts.id = tee.training_session_id
                LEFT JOIN individual_exercises ie ON tee.individual_exercise_id = ie.id
                WHERE ts.user_id = :userId
                GROUP BY ts.id
                ORDER BY ts.date DESC";
        
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

    // Aktualizace tréninkové jednotky
    public function updateTrainingSession($id, $data) {
        try {
            $this->beginTransaction();
            
            // 1. Aktualizace základních údajů tréninkové jednotky
            $setFields = [];
            $params = [':id' => $id];
            
            foreach ($data as $key => $value) {
                if ($key !== 'id' && $key !== 'user_id' && $key !== 'exercises') {
                    $setFields[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }
            
            if (!empty($setFields)) {
                $setClause = implode(', ', $setFields);
                $sql = "UPDATE training_sessions SET $setClause WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
            }
            
            // 2. Aktualizace cviků
            if (isset($data['exercises'])) {
                // Nejprve smažeme všechny existující cviky pro tuto tréninkovou jednotku
                $deleteSql = "DELETE FROM training_exercise_entries WHERE training_session_id = :id";
                $stmt = $this->pdo->prepare($deleteSql);
                $stmt->execute([':id' => $id]);
                
                // Pak přidáme nové cviky
                foreach ($data['exercises'] as $exercise) {
                    if (!empty($exercise['exercise_id'])) {
                        $this->addExerciseToSession(
                            $id,
                            $exercise['exercise_id'],
                            $exercise['sets'] ?? null,
                            $exercise['reps'] ?? null,
                            $exercise['weight'] ?? null,
                            $exercise['distance'] ?? null
                        );
                    }
                }
            }
            
            $this->commitTransaction();
            return true;
            
        } catch (PDOException $e) {
            $this->rollbackTransaction();
            error_log("Chyba při aktualizaci tréninkové jednotky: " . $e->getMessage());
            return false;
        }
    }

    // Odstranění tréninkové jednotky
    public function deleteTrainingSession($id) {
        $sql = "DELETE FROM training_sessions WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // Získání statistik tréninků uživatele
    public function getUserStats($userId) {
        $sql = "SELECT 
                COUNT(*) as total_sessions,
                SUM(total_duration) as total_duration,
                SUM(total_calories_burned) as total_calories,
                COUNT(DISTINCT DATE(date)) as total_days
                FROM training_sessions 
                WHERE user_id = :userId";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Získání tréninků uživatele za posledních N dní
    public function getRecentTrainingSessions($userId, $days = 7) {
        $sql = "SELECT ts.*, 
                GROUP_CONCAT(
                    CONCAT(ie.name, '|', tee.sets, '|', tee.reps, '|', tee.weight, '|', tee.distance)
                    SEPARATOR '||'
                ) as exercises
                FROM training_sessions ts
                LEFT JOIN training_exercise_entries tee ON ts.id = tee.training_session_id
                LEFT JOIN individual_exercises ie ON tee.individual_exercise_id = ie.id
                WHERE ts.user_id = :userId 
                AND ts.date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                GROUP BY ts.id
                ORDER BY ts.date DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Získání všech individuálních cvičení
    public function getAllExercises() {
        $sql = "SELECT *
                FROM individual_exercises 
                ORDER BY exercise_type, name";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Chyba při získávání seznamu cvičení: " . $e->getMessage());
            return [];
        }
    }

    // Získání individuálního cvičení podle ID
    public function getExerciseById($id) {
        $sql = "SELECT id, name, description, exercise_type 
                FROM individual_exercises 
                WHERE id = :id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Chyba při získávání cvičení podle ID: " . $e->getMessage());
            return false;
        }
    }

    // Metody pro práci s transakcemi
    public function beginTransaction() {
        $this->pdo->beginTransaction();
    }

    public function commitTransaction() {
        $this->pdo->commit();
    }

    public function rollbackTransaction() {
        $this->pdo->rollBack();
    }
}
?> 