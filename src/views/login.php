<?php
$pageTitle = 'Přihlášení';
include 'src/views/header.php';

// Získání chybových zpráv a emailu, pokud existují
$errors = $_SESSION['login_errors'] ?? [];
$email = $_SESSION['login_email'] ?? '';

// Odstranění dat ze session
unset($_SESSION['login_errors']);
unset($_SESSION['login_email']);
?>

<div class="auth-form">
    <div class="card-header">
        <h3 class="card-title">Přihlášení</h3>
    </div>
    
    <form action="index.php?action=login" method="post">
        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $errors['general']; ?>
            </div>
        <?php endif; ?>
        
        <div class="form-group <?php echo isset($errors['email']) ? 'has-error' : ''; ?>">
            <label for="email">Email</label>
            <div class="input-icon-wrapper">
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Zadejte váš email" required>
            </div>
            <?php if (isset($errors['email'])): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $errors['email']; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-group <?php echo isset($errors['password']) ? 'has-error' : ''; ?>">
            <label for="password">Heslo</label>
            <div class="input-icon-wrapper">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="Zadejte vaše heslo" required>
                <button type="button" class="password-toggle" onclick="togglePasswordVisibility()">
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
        
        <div class="form-actions">
            <button type="submit" class="btn primary-btn">
                <i class="fas fa-sign-in-alt btn-icon"></i>
                Přihlásit se
            </button>
        </div>
        
        <div class="form-footer">
            <p>Nemáte účet? <a href="index.php?page=register">Registrujte se</a></p>
        </div>
    </form>
</div>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const icon = document.querySelector('.password-toggle i');
        
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