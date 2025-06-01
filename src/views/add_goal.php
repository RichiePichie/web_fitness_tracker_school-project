<?php
include 'header.php';

// Initialize variables
$errors = $_SESSION['goal_errors'] ?? [];
$data = $_SESSION['goal_data'] ?? [
    'title' => '',
    'description' => '',
    'goal_type' => '',
    'target_value' => '',
    'current_value' => '',
    'start_date' => date('Y-m-d'),
    'end_date' => date('Y-m-d', strtotime('+30 days'))
];

unset($_SESSION['goal_errors'], $_SESSION['goal_data']);
?>

<div class="container mt-5">
    <div class="page-header text-center">
        <h1 class="mb-2">
            Přidat nový cíl
        </h1>
        <p class="text-muted">Vytvořte nový cíl a sledujte svůj pokrok</p>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card">
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($errors['general']); ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?action=add_goal" method="post" class="needs-validation" novalidate>
                    <div class="card-body">
                        <div class="mb-4">
                            <label for="title" class="form-label required-field">
                                <i class="fas fa-heading text-primary me-2"></i>Název cíle
                            </label>
                            <input type="text" 
                                   class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>"
                                   id="title" 
                                   name="title" 
                                   value="<?php echo htmlspecialchars($data['title']); ?>" 
                                   placeholder="Např. 'Zhubnout 5kg' nebo 'Uběhnout 10km'" 
                                   required>
                            <?php if (isset($errors['title'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['title']; ?></div>
                            <?php else: ?>
                                <div class="form-text text-muted">Zadejte výstižný název vašeho cíle</div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left text-info me-2"></i>Popis
                            </label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      placeholder="Detailnější popis vašeho cíle..."
                                      rows="3"><?php echo htmlspecialchars($data['description']); ?></textarea>
                            <div class="form-text text-muted">Nepovinné - Popište váš cíl podrobněji</div>
                        </div>

                        <div class="mb-4">
                            <label for="goal_type" class="form-label required-field">
                                <i class="fas fa-tags text-success me-2"></i>Typ cíle
                            </label>
                            <select class="form-select <?php echo isset($errors['goal_type']) ? 'is-invalid' : ''; ?>"
                                    id="goal_type" 
                                    name="goal_type" 
                                    required>
                                <option value="" <?php echo empty($data['goal_type']) ? 'selected' : ''; ?>>
                                    Vyberte typ cíle
                                </option>
                                <option value="weight" <?php echo $data['goal_type'] === 'weight' ? 'selected' : ''; ?>>
                                    <i class="fas fa-weight"></i> Váha
                                </option>
                                <option value="distance" <?php echo $data['goal_type'] === 'distance' ? 'selected' : ''; ?>>
                                    <i class="fas fa-running"></i> Vzdálenost
                                </option>
                                <option value="workout_count" <?php echo $data['goal_type'] === 'workout_count' ? 'selected' : ''; ?>>
                                    <i class="fas fa-calendar-check"></i> Počet tréninků
                                </option>
                                <option value="strength" <?php echo $data['goal_type'] === 'strength' ? 'selected' : ''; ?>>
                                    <i class="fas fa-dumbbell"></i> Síla
                                </option>
                                <option value="other" <?php echo $data['goal_type'] === 'other' ? 'selected' : ''; ?>>
                                    <i class="fas fa-star"></i> Jiný
                                </option>
                            </select>
                            <?php if (isset($errors['goal_type'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['goal_type']; ?></div>
                            <?php else: ?>
                                <div class="form-text text-muted">Vyberte kategorii, do které váš cíl spadá</div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label for="target_value" class="form-label required-field">
                                <i class="fas fa-bullseye text-danger me-2"></i>Cílová hodnota
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       step="0.01" 
                                       class="form-control <?php echo isset($errors['target_value']) ? 'is-invalid' : ''; ?>" 
                                       min="0"
                                       id="target_value" 
                                       name="target_value" 
                                       value="<?php echo htmlspecialchars($data['target_value']); ?>" 
                                       required>
                                <span class="input-group-text">jednotek</span>
                            </div>
                            <?php if (isset($errors['target_value'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['target_value']; ?></div>
                            <?php else: ?>
                                <div class="form-text text-muted">Např. 5 (kg) nebo 10 (km) podle typu cíle</div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label for="current_value" class="form-label">
                                <i class="fas fa-tachometer-alt text-info me-2"></i>Aktuální hodnota
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       step="0.01" 
                                       class="form-control <?php echo isset($errors['current_value']) ? 'is-invalid' : ''; ?>" 
                                       min="0"
                                       id="current_value" 
                                       name="current_value" 
                                       value="<?php echo htmlspecialchars($data['current_value']); ?>">
                                <span class="input-group-text">jednotek</span>
                            </div>
                            <?php if (isset($errors['current_value'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['current_value']; ?></div>
                            <?php else: ?>
                                <div class="form-text text-muted">Nepovinné - Váš aktuální stav</div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="start_date" class="form-label required-field">
                                    <i class="fas fa-calendar-plus text-success me-2"></i>Datum zahájení
                                </label>
                                <input type="date" 
                                       class="form-control <?php echo isset($errors['start_date']) ? 'is-invalid' : ''; ?>"
                                       id="start_date" 
                                       name="start_date" 
                                       value="<?php echo htmlspecialchars($data['start_date']); ?>" 
                                       required>
                                <?php if (isset($errors['start_date'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['start_date']; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="end_date" class="form-label required-field">
                                    <i class="fas fa-calendar-check text-danger me-2"></i>Datum ukončení
                                </label>
                                <input type="date" 
                                       class="form-control <?php echo isset($errors['end_date']) ? 'is-invalid' : ''; ?>"
                                       id="end_date" 
                                       name="end_date" 
                                       value="<?php echo htmlspecialchars($data['end_date']); ?>" 
                                       required>
                                <?php if (isset($errors['end_date'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['end_date']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-between">
                        <a href="index.php?page=goals" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Zpět
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Přidat cíl
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
    // Form validation enhancement
    (function() {
        'use strict';
        
        // Fetch all forms we want to apply custom validation styles to
        var forms = document.querySelectorAll('.needs-validation');
        
        // Loop over them and prevent submission
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>