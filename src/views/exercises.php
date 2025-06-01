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
    
    <?php if (empty($exercises)): ?>
        <div class="alert alert-info">
            Zatím nemáte žádná zaznamenaná cvičení.
        </div>
    <?php else: ?>

    

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Název</th>
                        <th>Typ</th>
                        <th>Doba trvání (min)</th>
                        <th>Spálené kalorie</th>
                        <th>Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exercises as $exercise): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(date('d.m.Y', strtotime($exercise['date']))); ?></td>
                            <td><?php echo htmlspecialchars($exercise['title']); ?></td>
                            <td><?php echo htmlspecialchars($exercise['exercise_type']); ?></td>
                            <td><?php echo htmlspecialchars($exercise['duration']); ?></td>
                            <td><?php echo htmlspecialchars($exercise['calories_burned']); ?></td>
                            <td>
                                <a href="index.php?page=edit_exercise&id=<?php echo $exercise['id']; ?>" 
                                   class="btn btn-sm btn-primary">Upravit</a>
                                <form action="index.php?page=delete_exercise" method="post" class="d-inline" 
                                      onsubmit="return confirm('Opravdu chcete smazat toto cvičení?');">
                                    <input type="hidden" name="id" value="<?php echo $exercise['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Smazat</button>
                                </form>
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