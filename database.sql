-- Vytvoření databáze
CREATE DATABASE IF NOT EXISTS fitness_tracker;
USE fitness_tracker;

-- Tabulka uživatelů
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    gender ENUM('male', 'female','other') NOT NULL,
    height DECIMAL(5,2) DEFAULT NULL,
    weight DECIMAL(5,2) DEFAULT NULL,
    date_of_birth DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabulka jednotlivých cviků (definice)
CREATE TABLE IF NOT EXISTS individual_exercises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    exercise_type ENUM('cardio', 'strength', 'flexibility', 'balance', 'other') NOT NULL,
    subtype ENUM('Distance', 'RepsSetsWeight', 'Reps', 'Time', 'None') NOT NULL DEFAULT 'None'
);

-- Tabulka tréninkových jednotek (konkrétní trénink)
CREATE TABLE IF NOT EXISTS training_sessions (
    id INT AUTO_INCREMENT NOT NULL primary key,
    user_id INT NOT NULL,
    date DATE NOT NULL,
    total_duration INT,
    total_calories_burned INT,
    notes TEXT,
    start_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Propojovací tabulka mezi tréninkovými jednotkami a jednotlivými cviky (konkrétní provedení cviku v tréninku)
CREATE TABLE IF NOT EXISTS training_exercise_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    training_session_id INT NOT NULL,
    individual_exercise_id INT NOT NULL,
    sets INT DEFAULT 0,
    reps INT DEFAULT 0,
    weight DECIMAL(5,2) DEFAULT 0,
    distance DECIMAL(10,2) DEFAULT 0,
    time TIME DEFAULT NULL,
    FOREIGN KEY (training_session_id) REFERENCES training_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (individual_exercise_id) REFERENCES individual_exercises(id) ON DELETE CASCADE
);

-- Tabulka cílů uživatele
CREATE TABLE IF NOT EXISTS user_goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    goal_type ENUM('weight', 'exercise_frequency', 'duration', 'distance', 'other') NOT NULL,
    target_value DECIMAL(10,2) NOT NULL,
    current_value DECIMAL(10,2) DEFAULT 0,
    start_date DATE NOT NULL,
    end_date DATE,
    status ENUM('active', 'completed', 'failed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Přidání cvičení do tabulky individual_exercises
INSERT INTO individual_exercises (name, description, exercise_type, subtype) VALUES
('Běh', 'Běh venku nebo na běžeckém pásu', 'cardio', 'Distance'),
('Jízda na kole', 'Cyklistika venku nebo na trenažeru', 'cardio', 'Distance'),
('Plavání', 'Plavání v bazénu nebo přírodním vodním zdroji', 'cardio', 'Distance'),
('Veslování', 'Cvičení na veslovacím trenažeru', 'cardio', 'Distance'),
('Skákání přes švihadlo', 'Kardio cvičení se švihadlem', 'cardio', 'Time'),

-- Silová cvičení
('Klik', 'Klasický klik na zemi', 'strength', 'RepsSetsWeight'),
('Dřep', 'Dřep s vlastní vahou nebo se závažím', 'strength', 'RepsSetsWeight'),
('Mrtvý tah', 'Komplexní silové cvičení pro celé tělo', 'strength', 'RepsSetsWeight'),
('Bench press', 'Tlak na lavici s činkou', 'strength', 'RepsSetsWeight'),
('Přítahy', 'Přítahy na hrazdě nebo s expanderem', 'strength', 'RepsSetsWeight'),
('Výpady', 'Výpady vpřed nebo vzad', 'strength', 'RepsSetsWeight'),

-- Flexibilní cvičení
('Pozdrav slunci', 'Základní sekvence jógy', 'flexibility', 'Time'),
('Most', 'Záklon v leže na zádech', 'flexibility', 'Time'),
('Předklon', 'Předklon v sedu nebo ve stoje', 'flexibility', 'Time'),
('Kobra', 'Záklon v leže na břiše', 'flexibility', 'Time'),
('Motýlek', 'Protahování vnitřních stehen', 'flexibility', 'Time'),
('Kočka-kráva', 'Protahování páteře', 'flexibility', 'Time'),

-- Cvičení na rovnováhu
('Stoj na jedné noze', 'Cvičení rovnováhy na jedné noze', 'balance', 'Time'),
('Bojovník III', 'Jógová pozice pro rovnováhu', 'balance', 'Time'),
('Chůze po čáře', 'Chůze po úzké čáře pro trénink rovnováhy', 'balance', 'Time'),
('Stoj na špičkách', 'Stoj na špičkách s případným pohybem paží', 'balance', 'Time'),
('Bosu balanční cvičení', 'Cvičení na balanční podložce', 'balance', 'Time'),

-- Ostatní cvičení
('HIIT trénink', 'Vysoce intenzivní intervalový trénink', 'other', 'Time'),
('Kruhový trénink', 'Kombinace různých cviků v kruzích', 'other', 'Time'),
('Pilates', 'Cvičení zaměřené na střed těla a posturu', 'other', 'Time'),
('Tai Chi', 'Pomalé pohybové sekvence pro zdraví a relaxaci', 'other', 'Time'),
('Tanec', 'Různé taneční styly jako forma cvičení', 'other', 'Time');

INSERT INTO users (username, email, password, user_type, gender, height, weight, date_of_birth) VALUES
('FreidY', 'precek.adam@seznam.cz','$2y$10$vd7.6b5KF/i4/OKJJIvnBO3QAsPDjzs25OEuXJcUE9778RtWHKtm.', 'admin', 'male', 188, 82, '2006-10-17');
