<?php
$pageTitle = 'Dashboard';
include 'src/views/header.php';

// Získání dat uživatele
$userModel = new User($pdo);
$exerciseModel = new Exercise($pdo);
$goalModel = new Goal($pdo);

$userId = $_SESSION['user_id'];
$user = $userModel->getById($userId);
$stats = $exerciseModel->getUserStats($userId);
$recentExercises = $exerciseModel->getRecentByUser($userId, 7);
$activeGoals = $goalModel->getActiveByUser($userId);

// Kontrola stavu cílů
$goalModel->checkGoalStatus($userId);
?>

<div class="dashboard">
    <div class="dashboard-welcome">
        <h3>Vítejte zpět, <?php echo htmlspecialchars($user['first_name'] ?: $user['username']); ?>!</h3>
        <p>Zde jsou vaše statistiky a přehled aktivit.</p>
    </div>
    
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-dumbbell"></i>
            </div>
            <div class="stat-info">
                <h4>Celkem cvičení</h4>
                <p class="stat-value"><?php echo $stats['total_exercises'] ?: 0; ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h4>Celkový čas</h4>
                <p class="stat-value"><?php echo $stats['total_duration'] ? floor($stats['total_duration'] / 60) . ' h ' . ($stats['total_duration'] % 60) . ' min' : '0 min'; ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-fire"></i>
            </div>
            <div class="stat-info">
                <h4>Spálené kalorie</h4>
                <p class="stat-value"><?php echo $stats['total_calories'] ?: 0; ?> kcal</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-info">
                <h4>Aktivních dní</h4>
                <p class="stat-value"><?php echo $stats['total_days'] ?: 0; ?></p>
            </div>
        </div>
    </div>
    
    <div class="dashboard-sections">
        <div class="dashboard-section">
            <div class="section-header">
                <h3>Nedávná cvičení</h3>
                <a href="index.php?page=exercises" class="btn small-btn">Zobrazit vše</a>
            </div>
            
            <?php if (empty($recentExercises)): ?>
                <div class="empty-state">
                    <p>Zatím nemáte žádná cvičení. <a href="index.php?page=add_exercise">Přidejte nové cvičení</a>.</p>
                </div>
            <?php else: ?>
                <div class="exercises-list">
                    <?php foreach ($recentExercises as $exercise): ?>
                        <div class="exercise-item">
                            <div class="exercise-icon">
                                <?php 
                                $icon = 'dumbbell';
                                if ($exercise['exercise_type'] === 'cardio') {
                                    $icon = 'running';
                                } elseif ($exercise['exercise_type'] === 'flexibility') {
                                    $icon = 'child';
                                } elseif ($exercise['exercise_type'] === 'balance') {
                                    $icon = 'balance-scale';
                                }
                                ?>
                                <i class="fas fa-<?php echo $icon; ?>"></i>
                            </div>
                            <div class="exercise-info">
                                <h4><?php echo htmlspecialchars($exercise['title']); ?></h4>
                                <p class="exercise-date"><?php echo date('d.m.Y', strtotime($exercise['date'])); ?></p>
                                <p>
                                    <span class="exercise-duration"><?php echo $exercise['duration']; ?> min</span>
                                    <span class="exercise-calories"><?php echo $exercise['calories_burned']; ?> kcal</span>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="section-footer">
                    <a href="index.php?page=add_exercise" class="btn primary-btn">Přidat cvičení</a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="dashboard-section">
            <div class="section-header">
                <h3>Aktivní cíle</h3>
                <a href="index.php?page=goals" class="btn small-btn">Zobrazit vše</a>
            </div>
            
            <?php if (empty($activeGoals)): ?>
                <div class="empty-state">
                    <p>Zatím nemáte žádné aktivní cíle. <a href="index.php?page=add_goal">Vytvořte nový cíl</a>.</p>
                </div>
            <?php else: ?>
                <div class="goals-list">
                    <?php foreach ($activeGoals as $goal): ?>
                        <?php
                        $progressPercent = 0;
                        if ($goal['target_value'] > 0) {
                            $progressPercent = min(100, ($goal['current_value'] / $goal['target_value']) * 100);
                        }
                        
                        $daysLeft = ceil((strtotime($goal['end_date']) - time()) / (60 * 60 * 24));
                        ?>
                        <div class="goal-item">
                            <div class="goal-info">
                                <h4><?php echo htmlspecialchars($goal['title']); ?></h4>
                                <p class="goal-dates">
                                    <span class="goal-date-start"><?php echo date('d.m.Y', strtotime($goal['start_date'])); ?></span> - 
                                    <span class="goal-date-end"><?php echo date('d.m.Y', strtotime($goal['end_date'])); ?></span>
                                    <span class="goal-days-left">(<?php echo $daysLeft; ?> dnů zbývá)</span>
                                </p>
                                <div class="goal-progress">
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo $progressPercent; ?>%"></div>
                                    </div>
                                    <div class="progress-info">
                                        <span class="progress-current"><?php echo $goal['current_value']; ?></span> / 
                                        <span class="progress-target"><?php echo $goal['target_value']; ?></span>
                                        <span class="progress-percent">(<?php echo round($progressPercent); ?>%)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="section-footer">
                    <a href="index.php?page=add_goal" class="btn primary-btn">Přidat cíl</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'src/views/footer.php'; ?> 