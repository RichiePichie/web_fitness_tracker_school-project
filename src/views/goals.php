    <?php
include 'header.php';
?>

<style>
    .goal-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        overflow: hidden;
        height: 100%;
        border: none;
    }
    
    .goal-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    }
    
    .goal-card .card-header {
        border-bottom: none;
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        color: white;
        padding: 15px 20px;
    }
    
    .goal-card .card-body {
        padding: 20px;
    }
    
    .goal-card .card-title {
        font-weight: 600;
    }
    
    .goal-card .badge {
        font-size: 0.8rem;
        padding: 6px 10px;
        border-radius: 20px;
    }
    
    .goal-card .progress-container {
        position: relative;
        margin-bottom: 30px;
    }
    
    .goal-card .progress {
        height: 16px;
        border-radius: 10px;
        margin-bottom: 5px;
        background-color: rgba(0,0,0,0.05);
    }
    
    .goal-card .progress-bar {
        border-radius: 10px;
        background: linear-gradient(90deg, #47c0b6, #2b8be3);
    }
    
    .goal-card .progress-percentage {
        position: absolute;
        right: 0;
        top: -25px;
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        color: white;
        font-weight: bold;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 14px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #5c7de0, #9065ca);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .btn-danger {
        background: linear-gradient(135deg, #ff6b6b, #cc5656);
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .btn-danger:hover {
        background: linear-gradient(135deg, #e95e5e, #b54a4a);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .goal-info-row {
        margin-bottom: 12px;
        display: flex;
        align-items: flex-start;
    }
    
    .goal-info-label {
        font-weight: 600;
        margin-right: 8px;
        min-width: 120px;
    }
    
    .goal-info-value {
        flex-grow: 1;
    }
    
    h1 {
        font-weight: 700;
        color: #444;
        margin-bottom: 30px;
    }
    
    .goal-action-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }
    
    .alert {
        border-radius: 10px;
        padding: 15px 20px;
    }
    
    .add-goal-btn {
        margin-bottom: 30px;
    }
</style>

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

    <div class="mb-4 text-center add-goal-btn">
        <a href="index.php?page=add_goal" class="btn btn-primary btn-lg"><i class="fas fa-plus-circle me-2"></i>Přidat nový cíl</a>
    </div>

    <?php if (empty($goals)): ?>
        <div class="alert alert-info">
            Zatím nemáte žádné cíle.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($goals as $goal): ?>
                <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up">
                    <div class="card goal-card">
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
                            
                            <div class="goal-info-row">
                                <span class="goal-info-label"><i class="fas fa-tag me-2"></i>Typ:</span> 
                                <span class="goal-info-value"><?php echo htmlspecialchars($goal['goal_type']); ?></span>
                            </div>
                            
                            <div class="goal-info-row">
                                <span class="goal-info-label"><i class="fas fa-bullseye me-2"></i>Cílová hodnota:</span> 
                                <span class="goal-info-value"><?php echo htmlspecialchars($goal['target_value']); ?></span>
                            </div>
                            
                            <div class="goal-info-row">
                                <span class="goal-info-label"><i class="fas fa-chart-line me-2"></i>Aktuální hodnota:</span> 
                                <span class="goal-info-value"><?php echo htmlspecialchars($goal['current_value']); ?></span>
                            </div>
                            
                            <div class="goal-info-row">
                                <span class="goal-info-label"><i class="fas fa-calendar-alt me-2"></i>Období:</span> 
                                <span class="goal-info-value"><?php echo date('d.m.Y', strtotime($goal['start_date'])); ?> - <?php echo date('d.m.Y', strtotime($goal['end_date'])); ?></span>
                            </div>
                            
                            <?php
                            $progress = ($goal['current_value'] / $goal['target_value']) * 100;
                            if ($goal['goal_type'] === 'weight' && $goal['current_value'] > $goal['target_value']) {
                                // Pro váhové cíle, kde se snižuje váha
                                $progress = 100 - (($goal['current_value'] - $goal['target_value']) / ($goal['current_value'] - $goal['target_value'] + 0.01)) * 100;
                            }
                            $progress = min(100, max(0, $progress));
                            $roundedProgress = round($progress);
                            ?>
                            <div class="progress-container">
                                <span class="progress-percentage">
                                    <i class="fas fa-chart-pie me-1"></i> <?php echo $roundedProgress; ?>%
                                </span>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $progress; ?>%;" 
                                         aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="goal-action-buttons">
                                <a href="index.php?page=edit_goal&id=<?php echo $goal['id']; ?>" class="btn btn-primary"><i class="fas fa-edit me-2"></i>Upravit</a>
                                <form action="index.php?page=delete_goal" method="post" class="d-inline" 
                                      onsubmit="return confirm('Opravdu chcete smazat tento cíl?');">
                                    <input type="hidden" name="id" value="<?php echo $goal['id']; ?>">
                                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt me-2"></i>Smazat</button>
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