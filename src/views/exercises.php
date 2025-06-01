<?php
include 'header.php';
?>

<div class="container mt-4">
    <div class="page-header animate__animated animate__fadeIn">
        <h2>Moje cvičení</h2>
        <p class="text-muted">Přehled všech vašich zaznamenaných tréninků a cvičení</p>
    </div>
    
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
    <div class="exercises-table-container animate__animated animate__fadeIn">
        <table class="exercises-table table table-hover shadow-sm">
            <thead class="bg-light">
                <tr>
                    <th>Datum</th>
                    <th>Poznámky</th>
                    <th>Statistiky</th>
                    <th>Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exercises as $exercise): ?>
                    <tr class="exercise-row">
                        <td>
                            <span class="exercise-date fw-bold">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                <?= date('d.m.Y', strtotime($exercise['start_at'])) ?>
                            </span>
                            <div class="text-muted small">
                                <i class="fas fa-clock me-1"></i>
                                <?= date('H:i', strtotime($exercise['start_at'])) ?>
                            </div>
                        </td>
                        <td>
                            <span class="exercise-notes">
                                <?php if (!empty($exercise['notes'])): ?>
                                    <i class="fas fa-sticky-note text-info me-2"></i>
                                    <?= htmlspecialchars($exercise['notes']) ?>
                                <?php else: ?>
                                    <span class="text-muted"><i class="fas fa-sticky-note me-2"></i>Žádné poznámky</span>
                                <?php endif; ?>
                            </span>
                        </td>
                        <td>
                            <div class="exercise-stats d-flex flex-column gap-1">
                                <span class="exercise-stat badge bg-light text-dark">
                                    <i class="fas fa-clock text-success me-1"></i>
                                    <?= $exercise['total_duration'] ? $exercise['total_duration'] . ' min' : 'N/A' ?>
                                </span>
                                <span class="exercise-stat badge bg-light text-dark">
                                    <i class="fas fa-fire text-danger me-1"></i>
                                    <?= $exercise['total_calories_burned'] ? $exercise['total_calories_burned'] . ' kcal' : 'N/A' ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="exercise-actions d-flex gap-2">
                                <a href="index.php?page=edit_exercise&id=<?= $exercise['id'] ?>" class="btn btn-sm btn-primary" title="Upravit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="index.php?page=exercises&action=delete_exercise&id=<?= $exercise['id'] ?>" method="post" onsubmit="return confirm('Opravdu chcete smazat toto cvičení?')">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Smazat">
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
    <div class="mb-3 mt-4 animate__animated animate__fadeInUp">
        <a href="index.php?page=add_exercise" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Přidat nové cvičení
        </a>
    </div>
</div>

<?php
include 'footer.php';
?> 