    <?php
include 'header.php';
?>

<div class="container mt-4">
    <h1>Moje cíle</h1>
    
    <?php if (isset($_SESSION['goal_added']) && $_SESSION['goal_added']): ?>
        <div class="alert alert-success">
            Cíl byl úspěšně přidán.
        </div>
        <?php unset($_SESSION['goal_added']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['goal_updated']) && $_SESSION['goal_updated']): ?>
        <div class="alert alert-success">
            Cíl byl úspěšně aktualizován.
        </div>
        <?php unset($_SESSION['goal_updated']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['goal_deleted']) && $_SESSION['goal_deleted']): ?>
        <div class="alert alert-success">
            Cíl byl úspěšně odstraněn.
        </div>
        <?php unset($_SESSION['goal_deleted']); ?>
    <?php endif; ?>

    <div class="mb-3">
        <a href="index.php?page=add_goal" class="btn btn-primary">Přidat nový cíl</a>
    </div>

    <?php if (empty($goals)): ?>
        <div class="alert alert-info">
            Zatím nemáte žádné cíle.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($goals as $goal): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($goal['title']); ?></h5>
                            <span class="badge <?php echo $goal['status'] === 'completed' ? 'bg-success' : ($goal['status'] === 'failed' ? 'bg-danger' : 'bg-primary'); ?>">
                                <?php echo ucfirst($goal['status']); ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($goal['description'])): ?>
                                <p><?php echo htmlspecialchars($goal['description']); ?></p>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <strong>Typ:</strong> <?php echo htmlspecialchars($goal['goal_type']); ?>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Cílová hodnota:</strong> <?php echo htmlspecialchars($goal['target_value']); ?>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Aktuální hodnota:</strong> <?php echo htmlspecialchars($goal['current_value']); ?>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Období:</strong> <?php echo date('d.m.Y', strtotime($goal['start_date'])); ?> - <?php echo date('d.m.Y', strtotime($goal['end_date'])); ?>
                            </div>
                            
                            <?php
                            $progress = ($goal['current_value'] / $goal['target_value']) * 100;
                            if ($goal['goal_type'] === 'weight' && $goal['current_value'] > $goal['target_value']) {
                                // Pro váhové cíle, kde se snižuje váha
                                $progress = 100 - (($goal['current_value'] - $goal['target_value']) / ($goal['current_value'] - $goal['target_value'] + 0.01)) * 100;
                            }
                            $progress = min(100, max(0, $progress));
                            ?>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress; ?>%;" 
                                     aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100">
                                    <?php echo round($progress); ?>%
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="index.php?page=edit_goal&id=<?php echo $goal['id']; ?>" class="btn btn-sm btn-primary">Upravit</a>
                                <form action="index.php?page=delete_goal" method="post" class="d-inline" 
                                      onsubmit="return confirm('Opravdu chcete smazat tento cíl?');">
                                    <input type="hidden" name="id" value="<?php echo $goal['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Smazat</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
include 'footer.php';
?> 