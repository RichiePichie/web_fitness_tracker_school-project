<?php
$pageTitle = 'Domů';
include 'src/views/header.php';
?>

<div class="hero">
    <div class="hero-content">
        <h2>Vítejte ve Fitness Tracker</h2>
        <p>Sledujte své cvičení, nastavujte si cíle a sledujte svůj pokrok!</p>
        
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="cta-buttons">
                <a href="index.php?page=register" class="btn primary-btn">Registrovat se</a>
                <a href="index.php?page=login" class="btn secondary-btn">Přihlásit se</a>
            </div>
        <?php else: ?>
            <div class="cta-buttons">
                <a href="index.php?page=dashboard" class="btn primary-btn">Přejít na dashboard</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="features">
    <div class="feature">
        <div class="feature-icon">
            <i class="fas fa-dumbbell"></i>
        </div>
        <h3>Sledujte své cvičení</h3>
        <p>Zaznamenávejte různé typy cvičení, jejich trvání a spálené kalorie.</p>
    </div>
    
    <div class="feature">
        <div class="feature-icon">
            <i class="fas fa-trophy"></i>
        </div>
        <h3>Nastavte si cíle</h3>
        <p>Vytvářejte si vlastní fitness cíle a sledujte svůj pokrok při jejich plnění.</p>
    </div>
    
    <div class="feature">
        <div class="feature-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <h3>Sledujte své statistiky</h3>
        <p>Zobrazujte si statistiky svého cvičení a pokroku v čase pomocí přehledných grafů.</p>
    </div>
</div>

<div class="how-it-works">
    <h2>Jak to funguje?</h2>
    <div class="steps">
        <div class="step">
            <div class="step-number">1</div>
            <h3>Vytvořte si účet</h3>
            <p>Registrujte se zdarma a vytvořte si svůj profil.</p>
        </div>
        
        <div class="step">
            <div class="step-number">2</div>
            <h3>Přidejte své cvičení</h3>
            <p>Zaznamenávejte různé typy cvičení a jejich podrobnosti.</p>
        </div>
        
        <div class="step">
            <div class="step-number">3</div>
            <h3>Nastavte si cíle</h3>
            <p>Vytvářejte si osobní cíle, kterých chcete dosáhnout.</p>
        </div>
        
        <div class="step">
            <div class="step-number">4</div>
            <h3>Sledujte svůj pokrok</h3>
            <p>Prohlížejte si statistiky a sledujte svůj pokrok v čase.</p>
        </div>
    </div>
</div>

<?php include 'src/views/footer.php'; ?> 