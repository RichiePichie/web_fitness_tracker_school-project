-- Create individual_exercises table
CREATE TABLE IF NOT EXISTS individual_exercises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    exercise_type ENUM('cardio', 'strength', 'flexibility', 'balance', 'other') NOT NULL,
    subtype ENUM('Distance', 'RepsSetsWeight', 'Reps') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add some sample exercises
INSERT INTO individual_exercises (name, description, exercise_type, subtype) VALUES
('Running', 'Outdoor or treadmill running', 'cardio', 'Distance'),
('Walking', 'Brisk walking for cardio', 'cardio', 'Distance'),
('Cycling', 'Indoor or outdoor cycling', 'cardio', 'Distance'),
('Swimming', 'Swimming laps in pool', 'cardio', 'Distance'),
('Push-ups', 'Basic bodyweight exercise for upper body strength', 'strength', 'Reps'),
('Bench Press', 'Barbell chest press exercise', 'strength', 'RepsSetsWeight'),
('Squats', 'Lower body compound exercise', 'strength', 'RepsSetsWeight'),
('Deadlifts', 'Full body compound exercise', 'strength', 'RepsSetsWeight'),
('Yoga', 'Basic yoga poses and stretches', 'flexibility', 'Reps'),
('Planks', 'Core stability exercise', 'balance', 'Reps'),
('Jumping Jacks', 'Full body cardio exercise', 'cardio', 'Reps'),
('Pull-ups', 'Upper body pulling exercise', 'strength', 'Reps'),
('Lunges', 'Lower body exercise', 'strength', 'Reps'),
('Shoulder Press', 'Overhead pressing movement', 'strength', 'RepsSetsWeight'),
('Rowing', 'Cardio exercise on rowing machine', 'cardio', 'Distance');

-- Add foreign key constraint to training_exercise_entries table
ALTER TABLE training_exercise_entries
ADD CONSTRAINT fk_individual_exercise
FOREIGN KEY (individual_exercise_id)
REFERENCES individual_exercises(id)
ON DELETE RESTRICT
ON UPDATE CASCADE; 