<?php
include 'header.php';

$errors = $_SESSION['exercise_errors'] ?? [];
$data = $_SESSION['exercise_data'] ?? [
    'date' => $exercise['date'] ?? date('Y-m-d'),
    'notes' => $exercise['notes'] ?? '',
    'exercises' => []
];

// Získání cviků z existujícího tréninku
if (isset($exercise['exercises'])) {
    $exercisesData = explode('||', $exercise['exercises']);
    foreach ($exercisesData as $index => $exerciseData) {
        list($name, $sets, $reps, $weight, $distance) = explode('|', $exerciseData);
        $data['exercises'][$index] = [
            'exercise_id' => '', // Toto bude doplněno později
            'sets' => $sets,
            'reps' => $reps,
            'weight' => $weight,
            'distance' => $distance
        ];
    }
}

$exerciseTypes = [
    'cardio' => 'Kardio',
    'strength' => 'Silové',
    'flexibility' => 'Flexibilita',
    'balance' => 'Rovnováha',
    'other' => 'Ostatní'
];

// Získání vybraných cviků ze sessionu
$selectedExercises = $_SESSION['selected_exercises'] ?? [];

// Aktualizace exercise_id v data podle vybraných cviků
foreach ($selectedExercises as $index => $exercise) {
    if (!isset($data['exercises'][$index])) {
        $data['exercises'][$index] = [
            'exercise_id' => '',
            'sets' => '',
            'reps' => '',
            'weight' => '',
            'distance' => ''
        ];
    }
    $data['exercises'][$index]['exercise_id'] = $exercise['id'];
}

unset($_SESSION['exercise_errors']);
?>

<div class="training-form">
    <div class="training-header">
        <h4>Upravit trénink</h4>
    </div>
    
    <!-- Předání dat do JavaScriptu -->
    <script>
        const selectedExercises = <?php echo json_encode($selectedExercises); ?>;
        const exerciseCount = <?php echo count($data['exercises']); ?>;
    </script>
    
    <div class="training-body">
        <form action="index.php?action=update_exercise" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($exercise['id']); ?>">
            
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
                    <?php foreach ($data['exercises'] as $index => $exercise): 
                        $selectedExercise = $selectedExercises[$index] ?? null;
                    ?>
                    <div class="exercise-entry">
                        <div class="card-body">
                            <div class="form-column">
                                <!-- Výběr cviku -->
                                <div class="form-group">
                                    <div class="exercise-select-wrapper">
                                        <div class="selected-exercise" id="selected-exercise-<?php echo $index; ?>" 
                                             style="display: <?php echo $selectedExercise ? 'block' : 'none'; ?>;">
                                            <?php if ($selectedExercise): ?>
                                            <div class="exercise-card">
                                                <div class="exercise-card-header">
                                                    <h3 class="exercise-card-title"><?php echo htmlspecialchars($selectedExercise['name']); ?></h3>
                                                    <span class="exercise-type-badge exercise-type-<?php echo $selectedExercise['exercise_type']; ?>">
                                                        <?php echo $exerciseTypes[$selectedExercise['exercise_type']] ?? $selectedExercise['exercise_type']; ?>
                                                    </span>
                                                </div>
                                                <div class="exercise-card-body">
                                                    <p class="exercise-description"><?php echo htmlspecialchars($selectedExercise['description'] ?? 'Žádný popis'); ?></p>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <a href="index.php?page=select_exercise&return_to=edit_exercise&index=<?php echo $index; ?>" 
                                           class="btn btn-secondary select-exercise-btn"
                                           style="display: <?php echo $selectedExercise ? 'none' : 'inline-block'; ?>;">
                                            Vybrat cvik
                                        </a>
                                        <input type="hidden" 
                                               name="exercises[<?php echo $index; ?>][exercise_id]" 
                                               class="exercise-id-input"
                                               value="<?php echo htmlspecialchars($selectedExercise['id'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="exercise-fields <?php echo $selectedExercise ? 'active' : ''; ?>" 
                                     id="exercise-fields-<?php echo $index; ?>"
                                     style="<?php echo $selectedExercise ? 'display: block;' : ''; ?>">
                                    <!-- Série -->
                                    <div class="form-group strength-fields" 
                                         style="display: <?php echo ($selectedExercise && $selectedExercise['exercise_type'] === 'strength') ? 'block' : 'none'; ?>">
                                        <label class="form-label">Série</label>
                                        <input type="number" 
                                               class="form-control" 
                                               name="exercises[<?php echo $index; ?>][sets]"
                                               value="<?php echo htmlspecialchars($selectedExercise['sets'] ?? ''); ?>"
                                               min="0">
                                    </div>

                                    <!-- Opakování -->
                                    <div class="form-group strength-fields"
                                         style="display: <?php echo ($selectedExercise && $selectedExercise['exercise_type'] === 'strength') ? 'block' : 'none'; ?>">
                                        <label class="form-label">Opakování</label>
                                        <input type="number" 
                                               class="form-control"
                                               name="exercises[<?php echo $index; ?>][reps]"
                                               value="<?php echo htmlspecialchars($selectedExercise['reps'] ?? ''); ?>"
                                               min="0">
                                    </div>

                                    <!-- Váha -->
                                    <div class="form-group strength-fields"
                                         style="display: <?php echo ($selectedExercise && $selectedExercise['exercise_type'] === 'strength') ? 'block' : 'none'; ?>">
                                        <label class="form-label">Váha (kg)</label>
                                        <input type="number" 
                                               class="form-control"
                                               name="exercises[<?php echo $index; ?>][weight]"
                                               value="<?php echo htmlspecialchars($selectedExercise['weight'] ?? ''); ?>"
                                               min="0" 
                                               step="0.1">
                                    </div>

                                    <!-- Vzdálenost -->
                                    <div class="form-group cardio-fields"
                                         style="display: <?php echo ($selectedExercise && $selectedExercise['exercise_type'] === 'cardio') ? 'block' : 'none'; ?>">
                                        <label class="form-label">Vzdálenost (km)</label>
                                        <input type="number" 
                                               class="form-control"
                                               name="exercises[<?php echo $index; ?>][distance]"
                                               value="<?php echo htmlspecialchars($selectedExercise['distance'] ?? ''); ?>"
                                               min="0" 
                                               step="0.01">
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
                <button type="submit" class="btn btn-primary">Uložit změny</button>
            </div>
        </form>
    </div>
</div>

<script src="public/js/exercises.js"></script>
<?php include 'footer.php'; ?> 