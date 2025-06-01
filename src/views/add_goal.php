<?php
include 'header.php';

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

<div class="container mt-4">
    <h1>Přidat nový cíl</h1>

    <form action="index.php?page=add_goal" method="post" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="title" class="form-label">Název cíle *</label>
            <input type="text" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>"
                   id="title" name="title" value="<?php echo htmlspecialchars($data['title']); ?>" required>
            <?php if (isset($errors['title'])): ?>
                <div class="invalid-feedback"><?php echo $errors['title']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Popis</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($data['description']); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="goal_type" class="form-label">Typ cíle *</label>
            <select class="form-select <?php echo isset($errors['goal_type']) ? 'is-invalid' : ''; ?>"
                    id="goal_type" name="goal_type" required>
                <option value="">Vyberte typ cíle</option>
                <option value="weight" <?php echo $data['goal_type'] === 'weight' ? 'selected' : ''; ?>>Váha</option>
                <option value="exercise_frequency" <?php echo $data['goal_type'] === 'exercise_frequency' ? 'selected' : ''; ?>>Frekvence cvičení</option>
                <option value="duration" <?php echo $data['goal_type'] === 'duration' ? 'selected' : ''; ?>>Doba trvání</option>
                <option value="distance" <?php echo $data['goal_type'] === 'distance' ? 'selected' : ''; ?>>Vzdálenost</option>
                <option value="other" <?php echo $data['goal_type'] === 'other' ? 'selected' : ''; ?>>Ostatní</option>
            </select>
            <?php if (isset($errors['goal_type'])): ?>
                <div class="invalid-feedback"><?php echo $errors['goal_type']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="target_value" class="form-label">Cílová hodnota *</label>
            <input type="number" step="0.01" class="form-control <?php echo isset($errors['target_value']) ? 'is-invalid' : ''; ?>" min="0"
                   id="target_value" name="target_value" value="<?php echo htmlspecialchars($data['target_value']); ?>" required>
            <?php if (isset($errors['target_value'])): ?>
                <div class="invalid-feedback"><?php echo $errors['target_value']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="current_value" class="form-label">Aktuální hodnota</label>
            <input type="number" step="0.01" class="form-control <?php echo isset($errors['current_value']) ? 'is-invalid' : ''; ?>" min="0"
                   id="current_value" name="current_value" value="<?php echo htmlspecialchars($data['current_value']); ?>">
            <?php if (isset($errors['current_value'])): ?>
                <div class="invalid-feedback"><?php echo $errors['current_value']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="start_date" class="form-label">Datum zahájení *</label>
            <input type="date" class="form-control <?php echo isset($errors['start_date']) ? 'is-invalid' : ''; ?>"
                   id="start_date" name="start_date" value="<?php echo htmlspecialchars($data['start_date']); ?>" required>
            <?php if (isset($errors['start_date'])): ?>
                <div class="invalid-feedback"><?php echo $errors['start_date']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="end_date" class="form-label">Datum ukončení *</label>
            <input type="date" class="form-control <?php echo isset($errors['end_date']) ? 'is-invalid' : ''; ?>"
                   id="end_date" name="end_date" value="<?php echo htmlspecialchars($data['end_date']); ?>" required>
            <?php if (isset($errors['end_date'])): ?>
                <div class="invalid-feedback"><?php echo $errors['end_date']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Přidat cíl</button>
            <a href="index.php?page=goals" class="btn btn-secondary">Zrušit</a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?> 