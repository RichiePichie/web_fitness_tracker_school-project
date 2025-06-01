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
    $exerciseItems = explode('||', $exercise['exercises']);
    foreach ($exerciseItems as $index => $item) {
        $parts = explode('|', $item);
        $name = $parts[0] ?? '';
        $sets = $parts[1] ?? '';
        $reps = $parts[2] ?? '';
        $weight = $parts[3] ?? '';
        $distance = $parts[4] ?? '';
        $exerciseType = $parts[5] ?? '';
        $individualExerciseId = $parts[6] ?? '';

        $data['exercises'][$index] = [
            'exercise_id' => $individualExerciseId, // ID konkrétního cviku z individual_exercises
            'name' => $name,         // Název cviku pro informaci
            'sets' => $sets,
            'reps' => $reps,
            'weight' => $weight,
            'distance' => $distance,
            'exercise_type' => $exerciseType // Typ cviku pro logiku zobrazení
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
print_r($_SESSION);

unset($_SESSION['exercise_errors']);
?>

<div class="training-form">
    <div class="training-header">
        <h4>Upravit trénink</h4>
    </div>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($errors['general'])): ?>
        <div class="alert alert-error">
            <?php echo $errors['general']; ?>
        </div>
    <?php endif; ?>
    
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

            <!-- Délka tréninku a spálené kalorie -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-clock text-primary me-1"></i>Délka tréninku - min
                    </label>
                    <div class="input-group">
                        <input type="number" 
                                class="form-control <?php echo isset($errors['duration']) ? 'is-invalid' : ''; ?>" 
                                name="duration"
                                value="<?php echo htmlspecialchars($data['duration'] ?? $exercise['total_duration'] ?? ''); ?>"
                                min="0"
                                step="1">
                    </div>
                    <?php if (isset($errors['duration'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['duration']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-fire text-danger me-1"></i>Spálené kalorie - kcal
                    </label>
                    <div class="input-group">
                        <input type="number" 
                                class="form-control <?php echo isset($errors['calories_burned']) ? 'is-invalid' : ''; ?>"
                                name="calories_burned"
                                value="<?php echo htmlspecialchars($data['calories_burned'] ?? $exercise['total_calories_burned'] ?? ''); ?>"
                                min="0"
                                step="1">
                    </div>
                    <?php if (isset($errors['calories_burned'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['calories_burned']; ?></div>
                    <?php endif; ?>
                    <div class="form-text text-muted">Odhadovaný počet spálených kalorií</div>
                </div>
            </div>

            <!-- Seznam cviků -->
            <div class="form-group">
                <label class="form-label">
                    Cviky
                    <span class="required-mark">*</span>
                </label>
                <?php if (isset($errors['exercises'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['exercises']; ?></div>
                <?php endif; ?>
                <div id="exercises-container">
                    <?php foreach ($data['exercises'] as $index => $currentDbExercise): 
                        // Pokud máme data z databáze pro tento cvik (první načtení editačního formuláře)
                        // a zároveň nemáme novější data ze session (po výběru cviku na select_exercise.php),
                        // použijeme data z databáze.
                        if (isset($currentDbExercise['exercise_id']) && !empty($currentDbExercise['exercise_id']) && !isset($selectedExercises[$index])) {
                            $selectedExercise = [
                                'id' => $currentDbExercise['exercise_id'],
                                'name' => $currentDbExercise['name'] ?? 'Neznámý cvik',
                                'description' => '', // Popis není v GROUP_CONCAT, můžeme ho případně načíst zvlášť
                                'exercise_type' => $currentDbExercise['exercise_type'] ?? 'other',
                                'sets' => $currentDbExercise['sets'] ?? '',
                                'reps' => $currentDbExercise['reps'] ?? '',
                                'weight' => $currentDbExercise['weight'] ?? '',
                                'distance' => $currentDbExercise['distance'] ?? ''
                            ];
                        } elseif (isset($selectedExercises[$index])) {
                            // Jinak použijeme data ze session (pokud existují) - typicky po výběru z select_exercise.php
                            $selectedExercise = $selectedExercises[$index];
                        } else {
                            // Pojistka, pokud nemáme ani data z DB ani ze session pro tento index
                            $selectedExercise = null;
                        }
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
                    <i class="fas fa-plus"></i> Přidat další cvik
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
                <a href="index.php?page=exercises" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Zrušit
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Uložit změny
                </button>
            </div>
        </form>
    </div>
</div>

<script src="public/js/exercises.js"></script>
<?php include 'footer.php'; ?> 