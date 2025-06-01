<?php
include 'header.php';
?>

<div class="container mt-4">
    <h2>Moje cvičení</h2>
    
    <?php if (isset($_SESSION['exercise_added']) && $_SESSION['exercise_added']): ?>
        <div class="alert alert-success">
            Cvičení bylo úspěšně přidáno.
        </div>
        <?php unset($_SESSION['exercise_added']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['exercise_updated']) && $_SESSION['exercise_updated']): ?>
        <div class="alert alert-success">
            Cvičení bylo úspěšně aktualizováno.
        </div>
        <?php unset($_SESSION['exercise_updated']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['exercise_deleted']) && $_SESSION['exercise_deleted']): ?>
        <div class="alert alert-success">
            Cvičení bylo úspěšně odstraněno.
        </div>
        <?php unset($_SESSION['exercise_deleted']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['exercise_deleted']) && $_SESSION['exercise_deleted']): ?>
        <div class="alert alert-success">
            Cvičení bylo úspěšně odstraněno.
        </div>
        <?php unset($_SESSION['exercise_delete_error']); ?>
    <?php endif; ?>

    <?php if (empty($exercises)): ?>
        <div class="alert alert-info">
            Zatím nemáte žádná zaznamenaná cvičení.
        </div>
    <?php else: ?>
    <div class="exercises-table-container">
        <table class="exercises-table">
            <thead>
                <tr>
                    <th>Datum</th>
                    <th>Poznámky</th>
                    <th>Statistiky</th>
                    <th>Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exercises as $exercise): ?>
                    <tr>
                        <td>
                            <span class="exercise-date">
                                <?= date('d.m.Y H:i', strtotime($exercise['start_at'])) ?>
                            </span>
                        </td>
                        <td>
                            <span class="exercise-notes">
                                <?= htmlspecialchars($exercise['notes'] ?? '') ?>
                            </span>
                        </td>
                        <td>
                            <div class="exercise-stats">
                                <span class="exercise-stat">
                                    <i class="fas fa-clock"></i>
                                    <?= $exercise['total_duration'] ? $exercise['total_duration'] . ' min' : 'N/A' ?>
                                </span>
                                <span class="exercise-stat">
                                    <i class="fas fa-fire"></i>
                                    <?= $exercise['total_calories_burned'] ? $exercise['total_calories_burned'] . ' kcal' : 'N/A' ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="exercise-actions">
                                <form action="index.php?page=edit_exercise&id=<?= $exercise['id'] ?>" method="post">
                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Upravit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </form>
                                
                                <form action="index.php?page=exercises&action=delete_exercise&id=<?= $exercise['id'] ?>" method="post" onsubmit="return confirm('Opravdu chcete smazat toto cvičení?')">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Smazat">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    <div class="mb-3">
        <a href="index.php?page=add_exercise" class="btn primary-btn">Přidat nové cvičení</a>
    </div>
</div>

<?php
include 'footer.php';
?> 