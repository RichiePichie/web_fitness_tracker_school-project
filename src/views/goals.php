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
                    <div class="card goal-card">
                        <div class="card-header goal-card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-bullseye me-2"></i>
                                <?php echo htmlspecialchars($goal['title']); ?>
                            </h5>
                            <span class="goal-status-badge <?php echo $goal['status'] === 'completed' ? 'bg-success' : ($goal['status'] === 'failed' ? 'bg-danger' : 'bg-primary'); ?>">
                                <i class="fas <?php echo $goal['status'] === 'completed' ? 'fa-check-circle' : ($goal['status'] === 'failed' ? 'fa-times-circle' : 'fa-clock'); ?> me-1"></i>
                                <?php echo ucfirst($goal['status']); ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($goal['description'])): ?>
                                <div class="goal-description mb-4">
                                    <i class="fas fa-align-left text-muted me-2"></i>
                                    <?php echo htmlspecialchars($goal['description']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="goal-info-grid">
                                <div class="goal-info-item">
                                    <div class="goal-info-label">
                                        <i class="fas fa-tag text-primary me-2"></i>Typ cíle
                                    </div>
                                    <div class="goal-info-value">
                                        <?php 
                                        $typeIcons = [
                                            'weight' => 'fa-weight',
                                            'distance' => 'fa-running',
                                            'workout_count' => 'fa-calendar-check',
                                            'strength' => 'fa-dumbbell',
                                            'other' => 'fa-star'
                                        ];
                                        $icon = $typeIcons[$goal['goal_type']] ?? 'fa-tag';
                                        ?>
                                        <i class="fas <?php echo $icon; ?> me-2"></i>
                                        <?php echo htmlspecialchars($goal['goal_type']); ?>
                                    </div>
                                </div>
                                
                                <div class="goal-info-item">
                                    <div class="goal-info-label">
                                        <i class="fas fa-bullseye text-danger me-2"></i>Cílová hodnota
                                    </div>
                                    <div class="goal-info-value">
                                        <?php echo htmlspecialchars($goal['target_value']); ?> jednotek
                                    </div>
                                </div>
                                
                                <div class="goal-info-item">
                                    <div class="goal-info-label">
                                        <i class="fas fa-chart-line text-success me-2"></i>Aktuální hodnota
                                    </div>
                                    <div class="goal-info-value">
                                        <?php echo htmlspecialchars($goal['current_value']); ?> jednotek
                                    </div>
                                </div>
                                
                                <div class="goal-info-item">
                                    <div class="goal-info-label">
                                        <i class="fas fa-calendar-alt text-info me-2"></i>Období
                                    </div>
                                    <div class="goal-info-value">
                                        <?php echo date('d.m.Y', strtotime($goal['start_date'])); ?> - 
                                        <?php echo date('d.m.Y', strtotime($goal['end_date'])); ?>
                                    </div>
                                </div>
                            </div>
                            
                            <?php
                            $progress = ($goal['current_value'] / $goal['target_value']) * 100;
                            if ($goal['goal_type'] === 'weight' && $goal['current_value'] > $goal['target_value']) {
                                $progress = 100 - (($goal['current_value'] - $goal['target_value']) / ($goal['current_value'] - $goal['target_value'] + 0.01)) * 100;
                            }
                            $progress = min(100, max(0, $progress));
                            $roundedProgress = round($progress);
                            $progressColor = $progress >= 100 ? 'success' : ($progress >= 75 ? 'primary' : ($progress >= 50 ? 'info' : ($progress >= 25 ? 'warning' : 'danger')));
                            ?>
                            
                            <div class="progress-container mb-4">
                                <div class="progress-label d-flex justify-content-between align-items-center mb-2">
                                    <span class="progress-title">
                                        <i class="fas fa-chart-pie me-2"></i>Pokrok
                                    </span>
                                    <span class="progress-percentage">
                                        <?php echo $roundedProgress; ?>%
                                    </span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-<?php echo $progressColor; ?>" 
                                         role="progressbar" 
                                         style="width: <?php echo $progress; ?>%;" 
                                         aria-valuenow="<?php echo $progress; ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="goal-actions d-flex justify-content-between">
                                <a href="index.php?page=edit_goal&id=<?php echo $goal['id']; ?>" 
                                   class="btn btn-primary btn-edit">
                                    <i class="fas fa-edit me-2"></i>Upravit
                                </a>
                                <form action="index.php?action=delete_goal" method="post" class="d-inline" 
                                      onsubmit="return confirm('Opravdu chcete smazat tento cíl?');">
                                    <input type="hidden" name="id" value="<?php echo $goal['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-delete">
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