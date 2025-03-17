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
    <form action="index.php?action=login" method="post">
        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-error"><?php echo $errors['general']; ?></div>
        <?php endif; ?>
        
        <div class="form-group <?php echo isset($errors['email']) ? 'has-error' : ''; ?>">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            <?php if (isset($errors['email'])): ?>
                <div class="error-message"><?php echo $errors['email']; ?></div>
            <?php endif; ?>
        </div>
        
        <div class="form-group <?php echo isset($errors['password']) ? 'has-error' : ''; ?>">
            <label for="password">Heslo</label>
            <input type="password" id="password" name="password" required>
            <?php if (isset($errors['password'])): ?>
                <div class="error-message"><?php echo $errors['password']; ?></div>
            <?php endif; ?>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn primary-btn">Přihlásit se</button>
        </div>
        
        <div class="form-footer">
            <p>Nemáte účet? <a href="index.php?page=register">Registrujte se</a></p>
        </div>
    </form>
</div>

<?php include 'src/views/footer.php'; ?> 