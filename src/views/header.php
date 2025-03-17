<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Tracker - <?php echo isset($pageTitle) ? $pageTitle : 'Sledování cvičení'; ?></title>
    <link rel="stylesheet" href="public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>Fitness Tracker</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Domů</a></li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="index.php?page=dashboard">Dashboard</a></li>
                    <li><a href="index.php?page=exercises">Cvičení</a></li>
                    <li><a href="index.php?page=goals">Cíle</a></li>
                    <li><a href="index.php?page=profile">Profil</a></li>
                    <li><a href="index.php?action=logout">Odhlásit se</a></li>
                <?php else: ?>
                    <li><a href="index.php?page=login">Přihlásit se</a></li>
                    <li><a href="index.php?page=register">Registrovat se</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <main>
        <div class="container">
            <?php if (isset($pageTitle)): ?>
                <h2><?php echo $pageTitle; ?></h2>
            <?php endif; ?> 