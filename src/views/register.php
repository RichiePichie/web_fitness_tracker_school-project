<?php
$pageTitle = 'Registrace';
include 'src/views/header.php';

// Získání chybových zpráv a dat, pokud existují
$errors = $_SESSION['register_errors'] ?? [];
$data = $_SESSION['register_data'] ?? [];

// Odstranění dat ze session
unset($_SESSION['register_errors']);
unset($_SESSION['register_data']);
?>

<div class="auth-form">
    <form action="index.php?action=register" method="post">
        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-error"><?php echo $errors['general']; ?></div>
        <?php endif; ?>
        
        <div class="form-group <?php echo isset($errors['username']) ? 'has-error' : ''; ?>">
            <label for="username">Uživatelské jméno</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($data['username'] ?? ''); ?>" required>
            <?php if (isset($errors['username'])): ?>
                <div class="error-message"><?php echo $errors['username']; ?></div>
            <?php endif; ?>
        </div>
        
        <div class="form-group <?php echo isset($errors['email']) ? 'has-error' : ''; ?>">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>" required>
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
        
        <div class="form-group <?php echo isset($errors['password_confirm']) ? 'has-error' : ''; ?>">
            <label for="password_confirm">Potvrzení hesla</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
            <?php if (isset($errors['password_confirm'])): ?>
                <div class="error-message"><?php echo $errors['password_confirm']; ?></div>
            <?php endif; ?>
        </div>
        
        <div class="form-row">
            <div class="form-group <?php echo isset($errors['first_name']) ? 'has-error' : ''; ?>">
                <label for="first_name">Jméno</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($data['first_name'] ?? ''); ?>">
                <?php if (isset($errors['first_name'])): ?>
                    <div class="error-message"><?php echo $errors['first_name']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group <?php echo isset($errors['last_name']) ? 'has-error' : ''; ?>">
                <label for="last_name">Příjmení</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($data['last_name'] ?? ''); ?>">
                <?php if (isset($errors['last_name'])): ?>
                    <div class="error-message"><?php echo $errors['last_name']; ?></div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn primary-btn">Registrovat se</button>
        </div>
        
        <div class="form-footer">
            <p>Již máte účet? <a href="index.php?page=login">Přihlaste se</a></p>
        </div>
    </form>
</div>

<?php include 'src/views/footer.php'; ?> 