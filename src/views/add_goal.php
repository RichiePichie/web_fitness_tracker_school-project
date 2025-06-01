<?php
include 'header.php';

// Custom styling for the add goal form

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

<style>
    .form-container {
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        padding: 30px;
        margin-bottom: 40px;
    }
    
    h1 {
        font-weight: 700;
        color: #444;
        margin-bottom: 30px;
        position: relative;
    }
    
    h1:after {
        content: '';
        display: block;
        width: 80px;
        height: 4px;
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        margin: 15px auto 0;
        border-radius: 2px;
    }
    
    .form-label {
        font-weight: 600;
        color: #555;
        margin-bottom: 8px;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        padding: 12px 15px;
        border: 2px solid #e1e5eb;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #a777e3;
        box-shadow: 0 0 0 0.25rem rgba(167, 119, 227, 0.25);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #5c7de0, #9065ca);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .btn-secondary {
        background: #f3f4f6;
        color: #5a6169;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-secondary:hover {
        background: #e3e6ed;
        color: #3a3f45;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .alert {
        border-radius: 10px;
        padding: 15px 20px;
    }
    
    .required-field::after {
        content: ' *';
        color: #dc3545;
    }
    
    .form-buttons {
        margin-top: 30px;
    }
    
    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + 0.75rem);
        background-position: right calc(0.375em + 0.1875rem) center;
    }
</style>

<div class="container mt-5">
    <div class="page-header animate__animated animate__fadeIn text-center">
        <h1 class="mb-2"><i class="fas fa-bullseye me-2 text-primary"></i>Přidat nový cíl</h1>
        <p class="text-muted">Vytvořte nový cíl a sledujte svůj pokrok</p>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="form-container animate__animated animate__fadeIn shadow">
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errors['general']); ?></div>
                <?php endif; ?>

                <form action="index.php?action=add_goal" method="post" class="needs-validation card border-0" novalidate>
                    <div class="card-body">
                    <div class="mb-4">
                        <label for="title" class="form-label fw-bold required-field">
                            <i class="fas fa-heading text-primary me-2"></i>Název cíle
                        </label>
                        <input type="text" class="form-control form-control-lg <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>"
                               id="title" name="title" value="<?php echo htmlspecialchars($data['title']); ?>" 
                               placeholder="Např. 'Zhubnout 5kg' nebo 'Ujeběhnout 10km'" required>
                        <?php if (isset($errors['title'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['title']; ?></div>
                        <?php else: ?>
                            <div class="form-text text-muted">Zadejte výstižný název vašeho cíle</div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">
                            <i class="fas fa-align-left text-info me-2"></i>Popis
                        </label>
                        <textarea class="form-control" id="description" name="description" 
                                  placeholder="Detailnější popis vašeho cíle..."
                                  rows="3"><?php echo htmlspecialchars($data['description']); ?></textarea>
                        <div class="form-text text-muted">Nepoviné - Popište váš cíl podrobněji</div>
                    </div>

                    <div class="mb-4">
                        <label for="goal_type" class="form-label fw-bold required-field">
                            <i class="fas fa-tags text-success me-2"></i>Typ cíle
                        </label>
                        <select class="form-select form-select-lg <?php echo isset($errors['goal_type']) ? 'is-invalid' : ''; ?>"
                                id="goal_type" name="goal_type" required>
                            <option value="" <?php echo empty($data['goal_type']) ? 'selected' : ''; ?>>Vyberte typ cíle</option>
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
                            <div class="form-text text-muted">Vyberte kategorie, do které váš cíl spadá</div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="target_value" class="form-label fw-bold required-field">
                            <i class="fas fa-bullseye text-danger me-2"></i>Cílová hodnota
                        </label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control form-control-lg <?php echo isset($errors['target_value']) ? 'is-invalid' : ''; ?>" min="0"
                                   id="target_value" name="target_value" value="<?php echo htmlspecialchars($data['target_value']); ?>" required>
                            <span class="input-group-text">jednotek</span>
                        </div>
                        <?php if (isset($errors['target_value'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['target_value']; ?></div>
                        <?php else: ?>
                            <div class="form-text text-muted">Např. 5 (kg) nebo 10 (km) podle typu cíle</div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="current_value" class="form-label fw-bold">
                            <i class="fas fa-tachometer-alt text-info me-2"></i>Aktuální hodnota
                        </label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control <?php echo isset($errors['current_value']) ? 'is-invalid' : ''; ?>" min="0"
                                   id="current_value" name="current_value" value="<?php echo htmlspecialchars($data['current_value']); ?>">
                            <span class="input-group-text">jednotek</span>
                        </div>
                        <?php if (isset($errors['current_value'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['current_value']; ?></div>
                        <?php else: ?>
                            <div class="form-text text-muted">Nepoviné - Váš aktuální stav</div>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="start_date" class="form-label fw-bold required-field">
                                <i class="fas fa-calendar-plus text-success me-2"></i>Datum zahájení
                            </label>
                            <input type="date" class="form-control <?php echo isset($errors['start_date']) ? 'is-invalid' : ''; ?>"
                                   id="start_date" name="start_date" value="<?php echo htmlspecialchars($data['start_date']); ?>" required>
                            <?php if (isset($errors['start_date'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['start_date']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="end_date" class="form-label fw-bold required-field">
                                <i class="fas fa-calendar-check text-danger me-2"></i>Datum ukončení
                            </label>
                            <input type="date" class="form-control <?php echo isset($errors['end_date']) ? 'is-invalid' : ''; ?>"
                                   id="end_date" name="end_date" value="<?php echo htmlspecialchars($data['end_date']); ?>" required>
                            <?php if (isset($errors['end_date'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['end_date']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="index.php?page=goals" class="btn btn-outline-secondary">
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
        
        // Animate form controls on focus
        const formControls = document.querySelectorAll('.form-control, .form-select');
        formControls.forEach(control => {
            control.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            control.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
    })();
</script>