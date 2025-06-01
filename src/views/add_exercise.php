<?php
include 'header.php';

// Načtení chyb a dat z session
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
$selectedExercises = $_SESSION['selected_exercises'] ?? [];

// Vyčištění session po načtení (ale zachováme selected_exercises)
unset($_SESSION['exercise_errors']);
unset($_SESSION['exercise_data']);

// Pro účely inicializace JavaScript počítadla
$exerciseCount = count($selectedExercises) > 0 ? max(array_keys($selectedExercises)) + 1 : 0;

// Získání vybraných cviků ze sessionu
$exerciseTypes = [
    'cardio' => 'Kardio',
    'strength' => 'Silové',
    'flexibility' => 'Flexibilita',
    'balance' => 'Rovnováha',
    'other' => 'Ostatní'
];

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

?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="page-header animate__animated animate__fadeIn">
                <h4><i class="fas fa-dumbbell me-2"></i>Nový trénink</h4>
                <p class="text-muted">Vytvořte nový trénink a přidejte cviky</p>
            </div>
        </div>
        
        <!-- Předání dat do JavaScriptu -->
        <script>
            const selectedExercises = <?php echo json_encode($selectedExercises); ?>;
            const exerciseCount = <?php echo count($data['exercises']); ?>;
        </script>
        
        <div class="training-body">
            <form action="index.php?action=save_training" method="post" id="training-form" class="animate__animated animate__fadeIn">
                <!-- Zobrazení obecných chyb -->
                <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo $errors['general']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Zobrazení obecných chyb u cviků -->
                <?php if (isset($errors['exercises']) && is_string($errors['exercises'])): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo $errors['exercises']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                <!-- Datum tréninku -->
                <div class="form-group mb-4">
                    <label for="date" class="form-label fw-bold">
                        <i class="fas fa-calendar-alt text-primary me-2"></i>Datum tréninku
                        <span class="required-mark text-danger">*</span>
                    </label>
                    <input type="date" 
                           class="form-control form-control-lg <?php echo isset($errors['date']) ? 'is-invalid' : ''; ?>"
                           id="date" 
                           name="date" 
                           value="<?php echo htmlspecialchars($data['date']); ?>" 
                           required>
                    <?php if (isset($errors['date'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['date']; ?></div>
                    <?php else: ?>
                        <div class="form-text text-muted">Vyberte datum, kdy trénink proběhl</div>
                    <?php endif; ?>
                </div>

                <!-- Seznam cviků -->
                <div class="form-group mb-4">
                    <label class="form-label fw-bold">
                        <i class="fas fa-list-ol text-success me-2"></i>Cviky
                        <span class="required-mark text-danger">*</span>
                    </label>
                    <div class="form-text text-muted mb-2">Přidejte alespoň jeden cvik pro váš trénink</div>
                    <div id="exercises-container">
                        <?php foreach ($data['exercises'] as $index => $exercise): 
                            $selectedExercise = $selectedExercises[$index] ?? null;
                        ?>
                        <div class="exercise-entry card shadow-sm mb-3 border-left-primary animate__animated animate__fadeIn">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 card-title">Cvik #<?php echo $index + 1; ?></h5>
                                <button type="button" class="btn btn-danger btn-sm remove-exercise">
                                    <i class="fas fa-trash me-1"></i>Odstranit
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="form-column">
                                    <!-- Výběr cviku -->
                                    <div class="form-group mb-4">
                                        <label class="form-label fw-bold">Vyberte cvik</label>
                                        <div class="exercise-select-wrapper">
                                            <div class="selected-exercise" id="selected-exercise-<?php echo $index; ?>" 
                                                 style="display: <?php echo $selectedExercise ? 'block' : 'none'; ?>;">
                                                <?php if ($selectedExercise): ?>
                                                <div class="exercise-card card border-0">
                                                    <div class="exercise-card-header d-flex justify-content-between align-items-center">
                                                        <h5 class="exercise-card-title mb-0"><?php echo htmlspecialchars($selectedExercise['name']); ?></h5>
                                                        <span class="exercise-type-badge badge bg-<?php echo $selectedExercise['exercise_type'] === 'cardio' ? 'danger' : ($selectedExercise['exercise_type'] === 'strength' ? 'primary' : 'success'); ?> text-white">
                                                            <?php echo $exerciseTypes[$selectedExercise['exercise_type']] ?? $selectedExercise['exercise_type']; ?>
                                                        </span>
                                                    </div>
                                                    <div class="exercise-card-body mt-2">
                                                        <p class="exercise-description mb-0"><i class="fas fa-info-circle text-info me-2"></i><?php echo htmlspecialchars($selectedExercise['description'] ?? 'Žádný popis'); ?></p>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <a href="index.php?page=select_exercise&return_to=add_exercise&index=<?php echo $index; ?>" 
                                               class="btn btn-primary select-exercise-btn w-100"
                                               style="display: <?php echo $selectedExercise ? 'none' : 'inline-block'; ?>;">
                                                <i class="fas fa-dumbbell me-2"></i>Vybrat cvik
                                            </a>
                                            <input type="hidden" 
                                                   name="exercises[<?php echo $index; ?>][exercise_id]" 
                                                   class="exercise-id-input"
                                                   value="<?php echo htmlspecialchars($selectedExercise['id'] ?? ''); ?>">
                                        </div>
                                    </div>

                                    <div class="exercise-fields card mt-3 mb-3 border-0 bg-light <?php echo $selectedExercise ? 'active' : ''; ?>" 
                                         id="exercise-fields-<?php echo $index; ?>"
                                         style="<?php echo $selectedExercise ? 'display: block;' : ''; ?>">                                         
                                        <div class="card-body">
                                        <h6 class="mb-3"><i class="fas fa-sliders-h me-2"></i>Podrobnosti cviku</h6>
                                        <!-- Série -->
                                        <div class="row strength-fields" 
                                             style="display: <?php echo ($selectedExercise && $selectedExercise['exercise_type'] === 'strength') ? 'flex' : 'none'; ?>">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label"><i class="fas fa-layer-group text-primary me-1"></i>Série</label>
                                                <div class="input-group">
                                                    <input type="number" 
                                                           class="form-control" 
                                                           name="exercises[<?php echo $index; ?>][sets]"
                                                           value="<?php echo htmlspecialchars($selectedExercise['sets'] ?? ''); ?>"
                                                           min="0">
                                                    <span class="input-group-text">sérií</span>
                                                </div>
                                            </div>

                                            <!-- Opakování -->
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label"><i class="fas fa-redo text-success me-1"></i>Opakování</label>
                                                <div class="input-group">
                                                    <input type="number" 
                                                           class="form-control"
                                                           name="exercises[<?php echo $index; ?>][reps]"
                                                           value="<?php echo htmlspecialchars($selectedExercise['reps'] ?? ''); ?>"
                                                           min="0">
                                                    <span class="input-group-text">opakování</span>
                                                </div>
                                            </div>

                                            <!-- Váha -->
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label"><i class="fas fa-weight-hanging text-danger me-1"></i>Váha</label>
                                                <div class="input-group">
                                                    <input type="number" 
                                                           class="form-control"
                                                           name="exercises[<?php echo $index; ?>][weight]"
                                                           value="<?php echo htmlspecialchars($selectedExercise['weight'] ?? ''); ?>"
                                                           min="0" 
                                                           step="0.1">
                                                    <span class="input-group-text">kg</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Vzdálenost -->
                                        <div class="row cardio-fields"
                                             style="display: <?php echo ($selectedExercise && $selectedExercise['exercise_type'] === 'cardio') ? 'flex' : 'none'; ?>">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label"><i class="fas fa-route text-info me-1"></i>Vzdálenost</label>
                                                <div class="input-group">
                                                    <input type="number" 
                                                           class="form-control"
                                                           name="exercises[<?php echo $index; ?>][distance]"
                                                           value="<?php echo htmlspecialchars($selectedExercise['distance'] ?? ''); ?>"
                                                           min="0" 
                                                           step="0.01">
                                                    <span class="input-group-text">km</span>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" class="btn btn-success mb-4" id="add-exercise">
                        <i class="fas fa-plus-circle me-2"></i>Přidat další cvik
                    </button>
                </div>

                    <button type="button" class="btn btn-success mb-4" id="add-exercise">
                        <i class="fas fa-plus-circle me-2"></i>Přidat další cvik
                    </button>
                </div>

                <!-- Poznámky -->
                <div class="form-group mb-4">
                    <label for="notes" class="form-label fw-bold">
                        <i class="fas fa-sticky-note text-info me-2"></i>Poznámky
                    </label>
                    <textarea class="form-control" 
                              id="notes" 
                              name="notes" 
                              placeholder="Doplňte poznámky k tréninku..."
                              rows="3"><?php echo htmlspecialchars($data['notes']); ?></textarea>
                    <div class="form-text text-muted">Nepoviné - vaše osobní poznámky k tréninku</div>
                </div>

                <!-- Tlačítka -->
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="index.php?page=exercises" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Zrušit
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Uložit trénink
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Inicializace počítadla cviků pro JavaScript
        window.exerciseCount = <?php echo $exerciseCount; ?>;
        
        // Inicializace vybraných cviků pro JavaScript
        const selectedExercises = <?php echo json_encode(array_values($selectedExercises)); ?>;
    </script>

<script src="public/js/exercises.js"></script>
<?php include 'footer.php'; ?>