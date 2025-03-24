<?php
include 'header.php';
?>

<div class="container mt-4">
    <h1>Můj profil</h1>
    
    <?php if (isset($_SESSION['profile_updated']) && $_SESSION['profile_updated']): ?>
        <div class="alert alert-success">
            Profil byl úspěšně aktualizován.
        </div>
        <?php unset($_SESSION['profile_updated']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['password_updated']) && $_SESSION['password_updated']): ?>
        <div class="alert alert-success">
            Heslo bylo úspěšně změněno.
        </div>
        <?php unset($_SESSION['password_updated']); ?>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <?php if (!empty($user['profile_image'])): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profilový obrázek" 
                             class="img-fluid rounded-circle mb-3" style="max-width: 150px; max-height: 150px;">
                    <?php else: ?>
                        <img src="public/img/default-avatar.png" alt="Výchozí profilový obrázek" 
                             class="img-fluid rounded-circle mb-3" style="max-width: 150px; max-height: 150px;">
                    <?php endif; ?>
                    
                    <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                    
                    <form action="index.php?page=upload_profile_image" method="post" enctype="multipart/form-data" class="mt-3">
                        <div class="mb-3">
                            <label for="profile_image" class="form-label">Nahrát nový profilový obrázek</label>
                            <input class="form-control" type="file" id="profile_image" name="profile_image" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">Nahrát</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Osobní údaje</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($errors) && !empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form action="index.php?page=update_profile" method="post">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">Jméno</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Příjmení</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="height" class="form-label">Výška (cm)</label>
                                <input type="number" step="0.01" class="form-control" id="height" name="height" 
                                       value="<?php echo htmlspecialchars($user['height'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="weight" class="form-label">Váha (kg)</label>
                                <input type="number" step="0.01" class="form-control" id="weight" name="weight" 
                                       value="<?php echo htmlspecialchars($user['weight'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="date_of_birth" class="form-label">Datum narození</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                   value="<?php echo htmlspecialchars($user['date_of_birth'] ?? ''); ?>">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Uložit změny</button>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Změna hesla</h5>
                </div>
                <div class="card-body">
                    <form action="index.php?page=change_password" method="post">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Současné heslo</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nové heslo</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Potvrzení nového hesla</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Změnit heslo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?> 