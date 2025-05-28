<?php include 'header.php'; ?>

<div class="container">
    <section class="card">
        <h2 class="mb-4">Můj profil</h2>

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

        <div class="form-row">
            <div class="form-group text-center">
                <img src="<?= !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'public/img/default-avatar.png'; ?>" 
                     alt="Profilový obrázek" 
                     class="img-fluid rounded-circle mb-2" style="max-width: 120px;">
                <h3><?= htmlspecialchars($user['username']) ?></h3>
                <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
            </div>

            <form action="index.php?page=update_profile" method="post" enctype="multipart/form-data" class="flex-1">
                <div class="form-group">
                    <label for="profile_image">Změnit profilový obrázek</label>
                    <input type="file" name="profile_image" id="profile_image" accept="image/*">
                </div>

                <div class="form-row">
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
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="height">Výška (cm)</label>
                        <input type="number" step="0.01" name="height" id="height"
                               value="<?= htmlspecialchars($user['height'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="weight">Váha (kg)</label>
                        <input type="number" step="0.01" name="weight" id="weight"
                               value="<?= htmlspecialchars($user['weight'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="date_of_birth">Datum narození</label>
                    <input type="date" name="date_of_birth" id="date_of_birth"
                           value="<?= htmlspecialchars($user['date_of_birth'] ?? '') ?>">
                </div>

                <hr>

                <h3>Změna hesla</h3>

                <div class="form-group">
                    <label for="current_password">Současné heslo</label>
                    <input type="password" name="current_password" id="current_password" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="new_password">Nové heslo</label>
                        <input type="password" name="new_password" id="new_password" required minlength="8">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Potvrzení hesla</label>
                        <input type="password" name="confirm_password" id="confirm_password" required minlength="8">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn primary-btn">Uložit změny</button>
                </div>
            </form>
        </div>
    </section>
</div>

<?php include 'footer.php'; ?>
