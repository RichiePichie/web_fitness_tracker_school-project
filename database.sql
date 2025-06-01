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
    subtype ENUM('Distance', 'RepsSetsWeight', 'Reps') NOT NULL,
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
    individual_exercise_id INT DEFAULT NULL, -- Volitelné propojení s konkrétním cvikem
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (individual_exercise_id) REFERENCES individual_exercises(id) ON DELETE SET NULL
);

-- Přidání cvičení do tabulky individual_exercises
INSERT INTO individual_exercises (name, description, exercise_type) VALUES
-- Kardio cvičení
('Běh', 'Běh venku nebo na běžeckém pásu', 'cardio'),
('Jízda na kole', 'Cyklistika venku nebo na trenažeru', 'cardio'),
('Plavání', 'Plavání v bazénu nebo přírodním vodním zdroji', 'cardio'),
('Veslování', 'Cvičení na veslovacím trenažeru', 'cardio'),
('Skákání přes švihadlo', 'Kardio cvičení se švihadlem', 'cardio'),

-- Silová cvičení
('Klik', 'Klasický klik na zemi', 'strength'),
('Dřep', 'Dřep s vlastní vahou nebo se závažím', 'strength'),
('Mrtvý tah', 'Komplexní silové cvičení pro celé tělo', 'strength'),
('Bench press', 'Tlak na lavici s činkou', 'strength'),
('Přítahy', 'Přítahy na hrazdě nebo s expanderem', 'strength'),
('Výpady', 'Výpady vpřed nebo vzad', 'strength'),
('Prkno', 'Statické cvičení na střed těla', 'strength'),
('Shyby', 'Přítahy na hrazdě nadhmatem', 'strength'),

-- Flexibilní cvičení
('Pozdrav slunci', 'Základní sekvence jógy', 'flexibility'),
('Most', 'Záklon v leže na zádech', 'flexibility'),
('Předklon', 'Předklon v sedu nebo ve stoje', 'flexibility'),
('Kobra', 'Záklon v leže na břiše', 'flexibility'),
('Motýlek', 'Protahování vnitřních stehen', 'flexibility'),
('Kočka-kráva', 'Protahování páteře', 'flexibility'),

-- Cvičení na rovnováhu
('Stoj na jedné noze', 'Cvičení rovnováhy na jedné noze', 'balance'),
('Bojovník III', 'Jógová pozice pro rovnováhu', 'balance'),
('Chůze po čáře', 'Chůze po úzké čáře pro trénink rovnováhy', 'balance'),
('Stoj na špičkách', 'Stoj na špičkách s případným pohybem paží', 'balance'),
('Bosu balanční cvičení', 'Cvičení na balanční podložce', 'balance'),

-- Ostatní cvičení
('HIIT trénink', 'Vysoce intenzivní intervalový trénink', 'other'),
('Kruhový trénink', 'Kombinace různých cviků v kruzích', 'other'),
('Pilates', 'Cvičení zaměřené na střed těla a posturu', 'other'),
('Tai Chi', 'Pomalé pohybové sekvence pro zdraví a relaxaci', 'other'),
('Tanec', 'Různé taneční styly jako forma cvičení', 'other');

-- Přidání testovacích dat pro training_sessions (původní data upravena)
-- Předpokládáme, že uživatelé s id 1, 2, 3, 4, 5 existují
INSERT INTO training_sessions (user_id, date, total_duration, total_calories_burned, notes) VALUES
(1, '2025-03-15', 60, 550, 'Ranní trénink - běh a posilování'),
(1, '2025-03-16', 45, 250, 'Večerní jóga session'),
(2, '2025-03-15', 75, 400, 'Ranní kardio a posilování');

INSERT INTO users (username, email, password, user_type, gender, height, weight, date_of_birth) VALUES
('FreidY', 'precek.adam@seznam.cz','$2y$10$vd7.6b5KF/i4/OKJJIvnBO3QAsPDjzs25OEuXJcUE9778RtWHKtm.', 'admin', 'male', 188, 82, '2006-10-17');

-- Přidání testovacích dat pro training_exercise_entries
-- Předpokládáme, že individual_exercises s id 1 (Klik), 3 (Běh), 5 (Pozdrav slunci A) existují
-- Odkazujeme na training_sessions vytvořené výše
INSERT INTO training_exercise_entries (training_session_id, individual_exercise_id, sets, reps, duration, distance, calories_burned) VALUES
(1, 3, NULL, NULL, 30, 5.0, 300), -- Běh v tréninku 1
(1, 1, 3, 15, NULL, NULL, 100), -- Kliky v tréninku 1
(1, 6, 3, 60, NULL, NULL, 150), -- Prkno v tréninku 1
(2, 5, NULL, NULL, 45, NULL, 250), -- Pozdrav slunci A v tréninku 2
(3, 3, NULL, NULL, 40, 6.5, 350), -- Běh v tréninku 3
(3, 2, 4, 12, NULL, NULL, 50); -- Dřepy v tréninku 3

-- Přidání testovacích dat pro user_goals (původní data upravena)
-- Předpokládáme, že individual_exercises s id 1 (Klik), 3 (Běh) existují
INSERT INTO user_goals (user_id, title, description, goal_type, target_value, current_value, start_date, end_date, individual_exercise_id, status) VALUES
(1, 'Zhubnout 5 kg', 'Cílem je dosáhnout váhy 70 kg', 'weight', 70.00, 75.00, '2025-03-01', '2025-05-01', NULL, 'active'),
(1, 'Běhat 3x týdně', 'Pravidelně absolvovat 3 běhy týdně', 'exercise_frequency', 3.00, 1.00, '2025-03-01', '2025-04-01', 3, 'active'), -- Cíl vázaný na běh (id 3)
(2, 'Kardio 150 min/týden', 'Zlepšit kardio kondici celkovou délkou kardio aktivit', 'duration', 150.00, 60.00, '2025-03-01', '2025-04-15', NULL, 'active'),
(1, 'Udělat 50 kliků v kuse', 'Cíl vázaný na konkrétní výkon v cviku', 'specific_exercise', 50.00, 30.00, '2025-03-01', '2025-06-01', 1, 'active'); -- Cíl vázaný na kliky (id 1)