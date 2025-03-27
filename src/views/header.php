<!DOCTYPE html>
<html lang="cs" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Tracker - <?php echo isset($pageTitle) ? $pageTitle : 'Sledování cvičení'; ?></title>
    <link rel="stylesheet" href="public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <i class="fas fa-heartbeat" style="font-size: 1.5rem; margin-right: 0.75rem; color: var(--primary-color);"></i>
                <h1>Fitness Tracker</h1>
            </div>
            
            <div class="header-controls">
                <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark mode">
                    <i class="fas fa-moon"></i>
                    <span class="theme-toggle-text">Tmavý režim</span>
                </button>
                
                <button class="mobile-menu-toggle" id="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <nav id="main-nav">
                <ul>
                    <li><a href="index.php" <?php echo !isset($_GET['page']) || $_GET['page'] === 'home' ? 'class="active"' : ''; ?>>
                        <i class="fas fa-home"></i> Domů
                    </a></li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="index.php?page=dashboard" <?php echo isset($_GET['page']) && $_GET['page'] === 'dashboard' ? 'class="active"' : ''; ?>>
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a></li>
                        <li><a href="index.php?page=exercises" <?php echo isset($_GET['page']) && $_GET['page'] === 'exercises' ? 'class="active"' : ''; ?>>
                            <i class="fas fa-dumbbell"></i> Cvičení
                        </a></li>
                        <li><a href="index.php?page=goals" <?php echo isset($_GET['page']) && $_GET['page'] === 'goals' ? 'class="active"' : ''; ?>>
                            <i class="fas fa-trophy"></i> Cíle
                        </a></li>
                        <li><a href="index.php?page=profile" <?php echo isset($_GET['page']) && $_GET['page'] === 'profile' ? 'class="active"' : ''; ?>>
                            <i class="fas fa-user"></i> Profil
                        </a></li>
                        <li>
                            <form action="index.php?action=logout" method="post" class="logout-form">
                                <button type="submit" class="logout-btn">
                                    <i class="fas fa-sign-out-alt"></i> Odhlásit se
                                </button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li><a href="index.php?page=login" <?php echo isset($_GET['page']) && $_GET['page'] === 'login' ? 'class="active"' : ''; ?>>
                            <i class="fas fa-sign-in-alt"></i> Přihlásit se
                        </a></li>
                        <li><a href="index.php?page=register" <?php echo isset($_GET['page']) && $_GET['page'] === 'register' ? 'class="active"' : ''; ?>>
                            <i class="fas fa-user-plus"></i> Registrovat se
                        </a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    
    <main>
        <div class="container">
            <?php if (isset($pageTitle) && (!isset($_GET['page']) || $_GET['page'] !== 'home')): ?>
                <h2><?php echo $pageTitle; ?></h2>
            <?php endif; ?> 