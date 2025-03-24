-- Vytvoření databáze
CREATE DATABASE IF NOT EXISTS fitness_tracker;
USE fitness_tracker;

-- Tabulka uživatelů
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    height DECIMAL(5,2) DEFAULT NULL,
    weight DECIMAL(5,2) DEFAULT NULL,
    date_of_birth DATE DEFAULT NULL,
    profile_image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabulka cvičení
CREATE TABLE IF NOT EXISTS exercises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    exercise_type ENUM('cardio', 'strength', 'flexibility', 'balance', 'other') NOT NULL,
    duration INT NOT NULL,
    calories_burned INT,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabulka cílů
CREATE TABLE IF NOT EXISTS goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    goal_type ENUM('weight', 'exercise_frequency', 'duration', 'distance', 'other') NOT NULL,
    target_value DECIMAL(10,2) NOT NULL,
    current_value DECIMAL(10,2) DEFAULT 0,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active', 'completed', 'failed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Přidání testovacích dat
INSERT INTO users (username, email, password, first_name, last_name, height, weight, date_of_birth) VALUES
('test_user', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test', 'User', 180.00, 75.00, NULL),
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 175.00, 70.00, NULL),
('john_doe', 'john.doe@example.com', '$2y$10$hashed_password', 'John', 'Doe', 180.00, 75.00, '1990-01-15'),
('jane_smith', 'jane.smith@example.com', '$2y$10$hashed_password', 'Jane', 'Smith', 165.00, 60.00, '1992-02-20'),
('michael_brown', 'michael.brown@example.com', '$2y$10$hashed_password', 'Michael', 'Brown', 175.00, 80.00, NULL),
('emily_jones', 'emily.jones@example.com', '$2y$10$hashed_password', 'Emily', 'Jones', 170.00, 65.00, NULL),
('david_wilson', 'david.wilson@example.com', '$2y$10$hashed_password', 'David', 'Wilson', 182.00, 90.00, NULL),
('sarah_davis', 'sarah.davis@example.com', '$2y$10$hashed_password', 'Sarah', 'Davis', 160.00, 55.00, NULL),
('james_miller', 'james.miller@example.com', '$2y$10$hashed_password', 'James', 'Miller', 178.00, 85.00, NULL),
('olivia_taylor', 'olivia.taylor@example.com', '$2y$10$hashed_password', 'Olivia', 'Taylor', 167.00, 62.00, NULL),
('william_anderson', 'william.anderson@example.com', '$2y$10$hashed_password', 'William', 'Anderson', 180.00, 78.00, NULL),
('isabella_thomas', 'isabella.thomas@example.com', '$2y$10$hashed_password', 'Isabella', 'Thomas', 172.00, 64.00, NULL),
('benjamin_jackson', 'benjamin.jackson@example.com', '$2y$10$hashed_password', 'Benjamin', 'Jackson', 176.00, 82.00, NULL),
('mia_white', 'mia.white@example.com', '$2y$10$hashed_password', 'Mia', 'White', 165.00, 58.00, NULL),
('elijah_harris', 'elijah.harris@example.com', '$2y$10$hashed_password', 'Elijah', 'Harris', 179.00, 88.00, NULL),
('ava_martin', 'ava.martin@example.com', '$2y$10$hashed_password', 'Ava', 'Martin', 168.00, 63.00, NULL),
('noah_thompson', 'noah.thompson@example.com', '$2y$10$hashed_password', 'Noah', 'Thompson', 181.00, 80.00, NULL),
('charlotte_garcia', 'charlotte.garcia@example.com', '$2y$10$hashed_password', 'Charlotte', 'Garcia', 170.00, 66.00, NULL),
('lucas_martinez', 'lucas.martinez@example.com', '$2y$10$hashed_password', 'Lucas', 'Martinez', 177.00, 84.00, NULL),
('sofia_rodriguez', 'sofia.rodriguez@example.com', '$2y$10$hashed_password', 'Sofia', 'Rodriguez', 165.00, 59.00, NULL),
('jack_wilson', 'jack.wilson@example.com', '$2y$10$hashed_password', 'Jack', 'Wilson', 182.00, 90.00, NULL),
('chloe_lee', 'chloe.lee@example.com', '$2y$10$hashed_password', 'Chloe', 'Lee', 169.00, 62.00, '1994-08-25');

INSERT INTO exercises (user_id, title, description, exercise_type, duration, calories_burned, date) VALUES
(1, 'Ranní běh', 'Běh v parku', 'cardio', 30, 300, '2025-03-15'),
(1, 'Posilování', 'Trénink horní části těla', 'strength', 45, 250, '2025-03-16'),
(2, 'Jóga', 'Relaxační jóga', 'flexibility', 60, 200, '2025-03-15'),
(1, 'Running', 'Morning run in the park', 'cardio', 30, 300, '2023-10-01'),
(1, 'Cycling', 'Cycling around the city', 'cardio', 45, 400, '2023-10-02'),
(2, 'Yoga', 'Yoga session for relaxation', 'flexibility', 60, 200, '2023-10-03'),
(2, 'Weightlifting', 'Upper body strength training', 'strength', 45, 350, '2023-10-04'),
(3, 'Swimming', 'Swimming in the pool', 'cardio', 30, 250, '2023-10-05'),
(3, 'Pilates', 'Pilates class for core strength', 'flexibility', 50, 220, '2023-10-06'),
(4, 'Hiking', 'Hiking in the mountains', 'cardio', 120, 600, '2023-10-07'),
(4, 'Dance', 'Dance class for fun', 'flexibility', 60, 300, '2023-10-08'),
(5, 'Boxing', 'Boxing training session', 'strength', 60, 500, '2023-10-09'),
(5, 'Zumba', 'Zumba class for cardio', 'cardio', 45, 400, '2023-10-10');

INSERT INTO goals (user_id, title, description, goal_type, target_value, current_value, start_date, end_date) VALUES
(1, 'Zhubnout', 'Chci zhubnout 5 kg', 'weight', 70.00, 75.00, '2025-03-01', '2025-05-01'),
(1, 'Běhat 3x týdně', 'Chci běhat pravidelně', 'exercise_frequency', 3.00, 1.00, '2025-03-01', '2025-04-01'),
(2, 'Kardio 150 minut týdně', 'Zlepšit kardio kondici', 'duration', 150.00, 60.00, '2025-03-01', '2025-04-15'),
(1, 'Lose Weight', 'Aim to lose 5 kg', 'weight', 70.00, 75.00, '2023-10-01', '2023-12-01'),
(1, 'Run 5 km', 'Complete a 5 km run', 'distance', 5.00, 2.00, '2023-10-01', '2023-11-01'),
(2, 'Increase Flexibility', 'Attend yoga classes regularly', 'exercise_frequency', 3.00, 1.00, '2023-10-01', '2023-11-01'),
(2, 'Strength Training', 'Complete 10 strength training sessions', 'exercise_frequency', 10.00, 4.00, '2023-10-01', '2023-12-01'),
(3, 'Swim 1000 m', 'Swim a total of 1000 meters', 'distance', 1000.00, 500.00, '2023-10-01', '2023-11-15'),
(3, 'Cardio 150 min/week', 'Achieve 150 minutes of cardio per week', 'duration', 150.00, 60.00, '2023-10-01', '2023-11-30'),
(4, 'Hike 3 times', 'Go hiking at least 3 times this month', 'exercise_frequency', 3.00, 1.00, '2023-10-01', '2023-10-31'),
(4, 'Dance Class', 'Attend 5 dance classes', 'exercise_frequency', 5.00, 2.00, '2023-10-01', '2023-11-15'),
(5, 'Boxing Match', 'Prepare for a boxing match', 'exercise_frequency', 1.00, 0.00, '2023-10-01', '2023-12-01'),
(5, 'Zumba Classes', 'Attend 8 Zumba classes', 'exercise_frequency', 8.00, 3.00, '2023-10-01', '2023-11-30');