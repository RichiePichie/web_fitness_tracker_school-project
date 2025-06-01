<?php
include 'header.php';

$errors = $_SESSION['goal_errors'] ?? [];
unset($_SESSION['goal_errors']);
?>

<div class="container mt-4">
    <h1>Upravit cíl</h1>

    <form action="index.php?page=update_goal" method="post" class="needs-validation" novalidate>
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($goal['id']); ?>">

        <div class="mb-3">
            <label for="title" class="form-label">Název cíle *</label>
            <input type="text" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>"
                   id="title" name="title" value="<?php echo htmlspecialchars($goal['title']); ?>" required>
            <?php if (isset($errors['title'])): ?>
                <div class="invalid-feedback"><?php echo $errors['title']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Popis</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($goal['description']); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="goal_type" class="form-label">Typ cíle *</label>
            <select class="form-select <?php echo isset($errors['goal_type']) ? 'is-invalid' : ''; ?>"
                    id="goal_type" name="goal_type" required>
                <option value="">Vyberte typ cíle</option>
                <option value="weight" <?php echo $goal['goal_type'] === 'weight' ? 'selected' : ''; ?>>Váha</option>
                <option value="exercise_frequency" <?php echo $goal['goal_type'] === 'exercise_frequency' ? 'selected' : ''; ?>>Frekvence cvičení</option>
                <option value="duration" <?php echo $goal['goal_type'] === 'duration' ? 'selected' : ''; ?>>Doba trvání</option>
                <option value="distance" <?php echo $goal['goal_type'] === 'distance' ? 'selected' : ''; ?>>Vzdálenost</option>
                <option value="other" <?php echo $goal['goal_type'] === 'other' ? 'selected' : ''; ?>>Ostatní</option>
            </select>
            <?php if (isset($errors['goal_type'])): ?>
                <div class="invalid-feedback"><?php echo $errors['goal_type']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="target_value" class="form-label">Cílová hodnota *</label>
            <input type="number" step="0.01" class="form-control <?php echo isset($errors['target_value']) ? 'is-invalid' : ''; ?>" min="0"
                   id="target_value" name="target_value" value="<?php echo htmlspecialchars($goal['target_value']); ?>" required>
            <?php if (isset($errors['target_value'])): ?>
                <div class="invalid-feedback"><?php echo $errors['target_value']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="current_value" class="form-label">Aktuální hodnota</label>
            <input type="number" step="0.01" class="form-control <?php echo isset($errors['current_value']) ? 'is-invalid' : ''; ?>" min="0"
                   id="current_value" name="current_value" value="<?php echo htmlspecialchars($goal['current_value']); ?>">
            <?php if (isset($errors['current_value'])): ?>
                <div class="invalid-feedback"><?php echo $errors['current_value']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="start_date" class="form-label">Datum zahájení *</label>
            <input type="date" class="form-control <?php echo isset($errors['start_date']) ? 'is-invalid' : ''; ?>"
                   id="start_date" name="start_date" value="<?php echo htmlspecialchars($goal['start_date']); ?>" required>
            <?php if (isset($errors['start_date'])): ?>
                <div class="invalid-feedback"><?php echo $errors['start_date']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="end_date" class="form-label">Datum ukončení *</label>
            <input type="date" class="form-control <?php echo isset($errors['end_date']) ? 'is-invalid' : ''; ?>"
                   id="end_date" name="end_date" value="<?php echo htmlspecialchars($goal['end_date']); ?>" required>
            <?php if (isset($errors['end_date'])): ?>
                <div class="invalid-feedback"><?php echo $errors['end_date']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="active" <?php echo $goal['status'] === 'active' ? 'selected' : ''; ?>>Aktivní</option>
                <option value="completed" <?php echo $goal['status'] === 'completed' ? 'selected' : ''; ?>>Dokončen</option>
                <option value="failed" <?php echo $goal['status'] === 'failed' ? 'selected' : ''; ?>>Nedokončen</option>
                <option value="cancelled" <?php echo $goal['status'] === 'cancelled' ? 'selected' : ''; ?>>Zrušen</option>
            </select>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Uložit změny</button>
            <a href="index.php?page=goals" class="btn btn-secondary">Zrušit</a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?> 