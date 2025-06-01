<?php
include 'header.php';
?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Moje cíle</h1>
    
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
                                <p class="mb-3"><?php echo htmlspecialchars($goal['description']); ?></p>
                            <?php endif; ?>
                            
                            <div class="goal-info-row mb-2">
                                <span class="goal-info-label"><i class="fas fa-tag me-2"></i>Typ:</span> 
                                <span class="goal-info-value"><?php echo htmlspecialchars($goal['goal_type']); ?></span>
                            </div>
                            
                            <div class="goal-info-row mb-2">
                                <span class="goal-info-label"><i class="fas fa-bullseye me-2"></i>Cílová hodnota:</span> 
                                <span class="goal-info-value"><?php echo htmlspecialchars($goal['target_value']); ?></span>
                            </div>
                            
                            <div class="goal-info-row mb-2">
                                <span class="goal-info-label"><i class="fas fa-chart-line me-2"></i>Aktuální hodnota:</span> 
                                <span class="goal-info-value"><?php echo htmlspecialchars($goal['current_value']); ?></span>
                            </div>
                            
                            <div class="goal-info-row mb-3">
                                <span class="goal-info-label"><i class="fas fa-calendar-alt me-2"></i>Období:</span> 
                                <span class="goal-info-value">
                                    <?php echo date('d.m.Y', strtotime($goal['start_date'])); ?> - 
                                    <?php echo date('d.m.Y', strtotime($goal['end_date'])); ?>
                                </span>
                            </div>
                            
                            <?php
                            $progress = ($goal['current_value'] / $goal['target_value']) * 100;
                            if ($goal['goal_type'] === 'weight' && $goal['current_value'] > $goal['target_value']) {
                                $progress = 100 - (($goal['current_value'] - $goal['target_value']) / ($goal['current_value'] - $goal['target_value'] + 0.01)) * 100;
                            }
                            $progress = min(100, max(0, $progress));
                            $roundedProgress = round($progress);
                            ?>
                            
                            <div class="progress-container mb-4">
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: <?php echo $progress; ?>%;" 
                                         aria-valuenow="<?php echo $progress; ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="progress-info mt-2">
                                    <span class="progress-percentage">
                                        <i class="fas fa-chart-pie me-1"></i> <?php echo $roundedProgress; ?>%
                                    </span>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="index.php?page=edit_goal&id=<?php echo $goal['id']; ?>" 
                                   class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i>Upravit
                                </a>
                                <form action="index.php?page=delete_goal" method="post" class="d-inline" 
                                      onsubmit="return confirm('Opravdu chcete smazat tento cíl?');">
                                    <input type="hidden" name="id" value="<?php echo $goal['id']; ?>">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash-alt me-2"></i>Smazat
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div class="mb-4 text-center">
        <button class="btn btn-primary btn-lg" onclick="window.location.href='index.php?page=add_goal'">
            <i class="fas fa-plus-circle me-2"></i>Přidat nový cíl
        </button>
    </div>
</div>

<?php
include 'footer.php';
?> 