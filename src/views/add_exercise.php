<?php
include 'header.php';

$errors = $_SESSION['exercise_errors'] ?? [];
$data = $_SESSION['exercise_data'] ?? [
    'title' => '',
    'description' => '',
    'exercise_type' => '',
    'duration' => '',
    'calories_burned' => '',
    'date' => date('Y-m-d')
];

unset($_SESSION['exercise_errors'], $_SESSION['exercise_data']);
?>

<div class="container mt-4">
    <h1>Přidat nové cvičení</h1>

    <form action="index.php?page=add_exercise" method="post" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="title" class="form-label">Název cvičení *</label>
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
            <label for="exercise_type" class="form-label">Typ cvičení *</label>
            <select class="form-select <?php echo isset($errors['exercise_type']) ? 'is-invalid' : ''; ?>"
                    id="exercise_type" name="exercise_type" required>
                <option value="">Vyberte typ cvičení</option>
                <option value="cardio" <?php echo $data['exercise_type'] === 'cardio' ? 'selected' : ''; ?>>Kardio</option>
                <option value="strength" <?php echo $data['exercise_type'] === 'strength' ? 'selected' : ''; ?>>Posilování</option>
                <option value="flexibility" <?php echo $data['exercise_type'] === 'flexibility' ? 'selected' : ''; ?>>Flexibilita</option>
                <option value="balance" <?php echo $data['exercise_type'] === 'balance' ? 'selected' : ''; ?>>Balance</option>
                <option value="other" <?php echo $data['exercise_type'] === 'other' ? 'selected' : ''; ?>>Ostatní</option>
            </select>
            <?php if (isset($errors['exercise_type'])): ?>
                <div class="invalid-feedback"><?php echo $errors['exercise_type']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="duration" class="form-label">Doba trvání (minuty) *</label>
            <input type="number" class="form-control <?php echo isset($errors['duration']) ? 'is-invalid' : ''; ?>"
                   id="duration" name="duration" value="<?php echo htmlspecialchars($data['duration']); ?>" required min="1">
            <?php if (isset($errors['duration'])): ?>
                <div class="invalid-feedback"><?php echo $errors['duration']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="calories_burned" class="form-label">Spálené kalorie</label>
            <input type="number" class="form-control" id="calories_burned" name="calories_burned"
                   value="<?php echo htmlspecialchars($data['calories_burned']); ?>" min="0">
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Datum *</label>
            <input type="date" class="form-control <?php echo isset($errors['date']) ? 'is-invalid' : ''; ?>"
                   id="date" name="date" value="<?php echo htmlspecialchars($data['date']); ?>" required>
            <?php if (isset($errors['date'])): ?>
                <div class="invalid-feedback"><?php echo $errors['date']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Přidat cvičení</button>
            <a href="index.php?page=exercises" class="btn btn-secondary">Zrušit</a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?> 