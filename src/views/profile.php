<?php
$pageTitle = 'Profil';
include 'src/views/header.php';

// Get error messages and form data if they exist
$errors = $_SESSION['profile_errors'] ?? [];

unset($_SESSION['register_errors']);
?>

<div class="container profile">
    <section class="card">
        <h2>Můj profil</h2>

        <?php if (!empty($_SESSION['profile_updated'])): ?>
            <div class="alert alert-success">Profil byl úspěšně aktualizován.</div>
            <?php unset($_SESSION['profile_updated']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['password_updated'])): ?>
            <div class="alert alert-success">Heslo bylo úspěšně změněno.</div>
            <?php unset($_SESSION['password_updated']); ?>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="index.php?action=update_profile&page=profile" method="post" enctype="multipart/form-data" class="profile-grid">
            <div class="profile-section">
                <h3>Osobní informace</h3>
                <div class="form-group">
                    <label for="height">Výška (cm)</label>
                    <input type="number" step="0.01" name="height" id="height" value="<?= htmlspecialchars($user['height'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="weight">Váha (kg)</label>
                    <input type="number" step="0.01" name="weight" id="weight"
                           value="<?= htmlspecialchars($user['weight'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="date_of_birth">Datum narození</label>
                    <input type="date" name="date_of_birth" id="date_of_birth"
                           value="<?= htmlspecialchars($user['date_of_birth'] ?? '') ?>">
                </div>
            </div>

            <div class="profile-section">
                <h3>Přihlašovací údaje</h3>
                <div class="form-group">
                    <label for="username">Uživatelské jméno</label>
                    <input type="text" name="username" id="username" required
                           value="<?= htmlspecialchars($user['username']) ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required
                           value="<?= htmlspecialchars($user['email']) ?>">
                </div>

                <h3 class="mt-4">Změna hesla</h3>
                <div class="form-group">
                    <label for="current_password">Současné heslo</label>
                    <input type="password" name="current_password" id="current_password">
                </div>
                <div class="form-group">
                    <label for="new_password">Nové heslo</label>
                    <input type="password" name="new_password" id="new_password" minlength="6">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Potvrzení hesla</label>
                    <input type="password" name="confirm_password" id="confirm_password" minlength="6">
                </div>
            </div>

            <div class="form-actions" style="grid-column: 1 / -1;">
                <button type="submit" class="btn primary-btn w-full">Uložit změny</button>
            </div>
        </form>
    </section>
</div>

<style>

.profile-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    padding: 1rem;
}

.profile-section {
    background: var(--card-bg);
    padding: 1.5rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
    transition: var(--transition);
}

.profile-section:hover {
    box-shadow: var(--shadow-md);
}

.profile-section h3 {
    margin-bottom: 1.5rem;
    color: var(--primary-color);
    font-size: 1.2rem;
    font-weight: 600;
    position: relative;
    padding-bottom: 0.5rem;
}

.profile-section h3::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    border-radius: 2px;
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-color);
    font-weight: 500;
    font-size: 0.875rem;
}

.form-group input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    background-color: var(--light-color);
    color: var(--text-color);
    transition: var(--transition);
}

.form-group input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.15);
    outline: none;
}

.form-actions {
    margin-top: 2rem;
    text-align: center;
}

@media (max-width: 768px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'footer.php'; ?>
