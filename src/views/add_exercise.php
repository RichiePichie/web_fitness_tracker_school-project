<?php
include 'header.php';

$errors = $_SESSION['exercise_errors'] ?? [];
$data = $_SESSION['exercise_data'] ?? [
    'date' => date('Y-m-d'),
    'notes' => '',
    'exercises' => [
        [
            'exercise_id' => '',
            'sets' => '',
            'reps' => '',
            'weight' => '',
            'distance' => ''
        ]
    ]
];

unset($_SESSION['exercise_errors'], $_SESSION['exercise_data']);
?>

<style>
.training-form {
    max-width: 800px;
    margin: 2rem auto;
    background-color: var(--card-bg);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-md);
    transition: var(--transition);
}

.training-form:hover {
    box-shadow: var(--shadow-lg);
}

.training-header {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 1.5rem;
    border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
    position: relative;
    overflow: hidden;
}

.training-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
    opacity: 0.5;
}

.training-header h4 {
    position: relative;
    z-index: 1;
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
}

.training-body {
    padding: 2rem;
}

.exercise-entry {
    background-color: var(--card-bg-2);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.exercise-entry:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.exercise-entry::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(to bottom, var(--primary-color), var(--secondary-color));
    opacity: 0;
    transition: var(--transition);
}

.exercise-entry:hover::before {
    opacity: 1;
}

.exercise-entry .card-body {
    padding: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: var(--text-color);
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    background-color: var(--light-color);
    color: var(--text-color);
    transition: var(--transition);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.15);
}

.form-control.is-invalid {
    border-color: var(--danger-color);
}

.invalid-feedback {
    color: var(--danger-color);
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.btn-group {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: var(--border-radius);
    font-weight: 500;
    cursor: pointer;
    transition: var(--transition);
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary {
    background-color: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background-color: var(--primary-hover);
    transform: translateY(-2px);
}

.btn-secondary {
    background-color: var(--card-bg-2);
    color: var(--text-color);
    border: 1px solid var(--border-color);
}

.btn-secondary:hover {
    background-color: var(--border-color);
    transform: translateY(-2px);
}

.btn-danger {
    background-color: var(--danger-color);
    color: white;
}

.btn-danger:hover {
    background-color: #dc2626;
    transform: translateY(-2px);
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

.add-exercise-btn {
    margin-top: 1rem;
    background-color: transparent;
    color: var(--primary-color);
    border: 1px solid var(--primary-color);
}

.add-exercise-btn:hover {
    background-color: rgba(var(--primary-rgb), 0.1);
}

.required-mark {
    color: var(--danger-color);
    margin-left: 0.25rem;
}

@media (max-width: 768px) {
    .training-form {
        margin: 1rem;
    }
    
    .training-body {
        padding: 1rem;
    }
    
    .exercise-entry .card-body {
        padding: 1rem;
    }
    
    .btn-group {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="training-form">
    <div class="training-header">
        <h4>Nový trénink</h4>
    </div>
    
    <div class="training-body">
        <form action="index.php?page=add_exercise" method="post">
            <!-- Datum tréninku -->
            <div class="form-group">
                <label for="date" class="form-label">
                    Datum tréninku
                    <span class="required-mark">*</span>
                </label>
                <input type="date" 
                       class="form-control <?php echo isset($errors['date']) ? 'is-invalid' : ''; ?>"
                       id="date" 
                       name="date" 
                       value="<?php echo htmlspecialchars($data['date']); ?>" 
                       required>
                <?php if (isset($errors['date'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['date']; ?></div>
                <?php endif; ?>
            </div>

            <!-- Seznam cviků -->
            <div class="form-group">
                <label class="form-label">
                    Cviky
                    <span class="required-mark">*</span>
                </label>
                <div id="exercises-container">
                    <?php foreach ($data['exercises'] as $index => $exercise): ?>
                    <div class="exercise-entry">
                        <div class="card-body">
                            <div class="form-row">
                                <!-- Výběr cviku -->
                                <div class="form-group">
                                    <label class="form-label">Cvik</label>
                                    <select class="form-control <?php echo isset($errors['exercises'][$index]['exercise_id']) ? 'is-invalid' : ''; ?>"
                                            name="exercises[<?php echo $index; ?>][exercise_id]" 
                                            required>
                                        <option value="">Vyberte cvik</option>
                                        <?php foreach ($availableExercises as $ex): ?>
                                            <option value="<?php echo $ex['id']; ?>" 
                                                    <?php echo $exercise['exercise_id'] == $ex['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($ex['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['exercises'][$index]['exercise_id'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['exercises'][$index]['exercise_id']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-row">
                                    <!-- Série -->
                                    <div class="form-group">
                                        <label class="form-label">Série</label>
                                        <input type="number" 
                                               class="form-control" 
                                               name="exercises[<?php echo $index; ?>][sets]"
                                               value="<?php echo htmlspecialchars($exercise['sets']); ?>"
                                               min="0">
                                    </div>

                                    <!-- Opakování -->
                                    <div class="form-group">
                                        <label class="form-label">Opakování</label>
                                        <input type="number" 
                                               class="form-control"
                                               name="exercises[<?php echo $index; ?>][reps]"
                                               value="<?php echo htmlspecialchars($exercise['reps']); ?>"
                                               min="0">
                                    </div>

                                    <!-- Váha -->
                                    <div class="form-group">
                                        <label class="form-label">Váha (kg)</label>
                                        <input type="number" 
                                               class="form-control"
                                               name="exercises[<?php echo $index; ?>][weight]"
                                               value="<?php echo htmlspecialchars($exercise['weight']); ?>"
                                               min="0" 
                                               step="0.1">
                                    </div>

                                    <!-- Vzdálenost -->
                                    <div class="form-group">
                                        <label class="form-label">Vzdálenost (km)</label>
                                        <input type="number" 
                                               class="form-control"
                                               name="exercises[<?php echo $index; ?>][distance]"
                                               value="<?php echo htmlspecialchars($exercise['distance']); ?>"
                                               min="0" 
                                               step="0.01">
                                    </div>
                                </div>

                                <?php if ($index > 0): ?>
                                <div class="form-group">
                                    <button type="button" class="btn btn-danger btn-sm remove-exercise">
                                        Odstranit cvik
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <button type="button" class="btn add-exercise-btn" id="add-exercise">
                    Přidat další cvik
                </button>
            </div>

            <!-- Poznámky -->
            <div class="form-group">
                <label for="notes" class="form-label">Poznámky</label>
                <textarea class="form-control" 
                          id="notes" 
                          name="notes" 
                          rows="2"><?php echo htmlspecialchars($data['notes']); ?></textarea>
            </div>

            <!-- Tlačítka -->
            <div class="btn-group">
                <a href="index.php?page=exercises" class="btn btn-secondary">Zrušit</a>
                <button type="submit" class="btn btn-primary">Uložit trénink</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('exercises-container');
    const addButton = document.getElementById('add-exercise');
    let exerciseCount = <?php echo count($data['exercises']); ?>;

    // Přidání nového cviku
    addButton.addEventListener('click', function() {
        const template = `
            <div class="exercise-entry">
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Cvik</label>
                            <select class="form-control" name="exercises[${exerciseCount}][exercise_id]" required>
                                <option value="">Vyberte cvik</option>
                                <?php foreach ($availableExercises as $ex): ?>
                                    <option value="<?php echo $ex['id']; ?>"><?php echo htmlspecialchars($ex['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Série</label>
                                <input type="number" class="form-control" name="exercises[${exerciseCount}][sets]" min="0">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Opakování</label>
                                <input type="number" class="form-control" name="exercises[${exerciseCount}][reps]" min="0">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Váha (kg)</label>
                                <input type="number" class="form-control" name="exercises[${exerciseCount}][weight]" min="0" step="0.1">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Vzdálenost (km)</label>
                                <input type="number" class="form-control" name="exercises[${exerciseCount}][distance]" min="0" step="0.01">
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="button" class="btn btn-danger btn-sm remove-exercise">
                                Odstranit cvik
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', template);
        exerciseCount++;
    });

    // Odstranění cviku
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-exercise')) {
            e.target.closest('.exercise-entry').remove();
        }
    });
});
</script>

<?php include 'footer.php'; ?> 