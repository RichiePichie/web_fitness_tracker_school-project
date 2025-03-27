<?php
$pageTitle = 'Registrace';
include 'src/views/header.php';

// Get error messages and form data if they exist
$errors = $_SESSION['register_errors'] ?? [];
$formData = $_SESSION['register_form_data'] ?? [];

// Remove data from session
unset($_SESSION['register_errors']);
unset($_SESSION['register_form_data']);
?>

<div class="auth-form">
    <div class="card-header">
        <h3 class="card-title">Vytvořit nový účet</h3>
    </div>
    
    <form action="index.php?action=register" method="post">
        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $errors['general']; ?>
            </div>
        <?php endif; ?>
        
        <div class="form-row">
            <div class="form-group <?php echo isset($errors['first_name']) ? 'has-error' : ''; ?>">
                <label for="first_name">Jméno</label>
                <div class="input-icon-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($formData['first_name'] ?? ''); ?>" placeholder="Zadejte jméno" required>
                </div>
                <?php if (isset($errors['first_name'])): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $errors['first_name']; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group <?php echo isset($errors['last_name']) ? 'has-error' : ''; ?>">
                <label for="last_name">Příjmení</label>
                <div class="input-icon-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($formData['last_name'] ?? ''); ?>" placeholder="Zadejte příjmení" required>
                </div>
                <?php if (isset($errors['last_name'])): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $errors['last_name']; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-group <?php echo isset($errors['email']) ? 'has-error' : ''; ?>">
            <label for="email">Email</label>
            <div class="input-icon-wrapper">
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" placeholder="Zadejte email" required>
            </div>
            <?php if (isset($errors['email'])): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $errors['email']; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-row">
            <div class="form-group <?php echo isset($errors['password']) ? 'has-error' : ''; ?>">
                <label for="password">Heslo</label>
                <div class="input-icon-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Zadejte heslo" required>
                    <button type="button" class="password-toggle" onclick="togglePasswordVisibility('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <?php if (isset($errors['password'])): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $errors['password']; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group <?php echo isset($errors['password_confirm']) ? 'has-error' : ''; ?>">
                <label for="password_confirm">Potvrdit heslo</label>
                <div class="input-icon-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password_confirm" name="password_confirm" placeholder="Potvrďte heslo" required>
                    <button type="button" class="password-toggle" onclick="togglePasswordVisibility('password_confirm')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <?php if (isset($errors['password_confirm'])): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $errors['password_confirm']; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-group">
            <label for="gender">Pohlaví</label>
            <div class="select-wrapper">
                <select id="gender" name="gender" required>
                    <option value="" disabled <?php echo !isset($formData['gender']) ? 'selected' : ''; ?>>Vyberte pohlaví</option>
                    <option value="male" <?php echo (isset($formData['gender']) && $formData['gender'] === 'male') ? 'selected' : ''; ?>>Muž</option>
                    <option value="female" <?php echo (isset($formData['gender']) && $formData['gender'] === 'female') ? 'selected' : ''; ?>>Žena</option>
                    <option value="other" <?php echo (isset($formData['gender']) && $formData['gender'] === 'other') ? 'selected' : ''; ?>>Jiné</option>
                </select>
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
        
        <div class="form-group checkbox-group">
            <div class="checkbox-wrapper">
                <input type="checkbox" id="terms" name="terms" required <?php echo isset($formData['terms']) ? 'checked' : ''; ?>>
                <label for="terms" class="checkbox-label">Souhlasím s <a href="#" class="link">podmínkami použití</a> a <a href="#" class="link">ochranou osobních údajů</a></label>
            </div>
            <?php if (isset($errors['terms'])): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $errors['terms']; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn primary-btn">
                <i class="fas fa-user-plus btn-icon"></i>
                Vytvořit účet
            </button>
        </div>
        
        <div class="form-footer">
            <p>Již máte účet? <a href="index.php?page=login">Přihlaste se</a></p>
        </div>
    </form>
</div>

<script>
    function togglePasswordVisibility(fieldId) {
        const passwordInput = document.getElementById(fieldId);
        const icon = document.querySelector(`#${fieldId} ~ .password-toggle i`);
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>

<?php include 'src/views/footer.php'; ?> 