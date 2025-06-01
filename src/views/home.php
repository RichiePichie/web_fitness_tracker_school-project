<?php
$pageTitle = 'Domů';
include 'src/views/header.php';
?>

<div class="hero">
    <div class="hero-content glass-container">
        <h2>Vítejte ve Fitness Tracker</h2>
        <p>Sledujte své cvičení, nastavujte si cíle a sledujte svůj pokrok!</p>
        
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="cta-buttons">
                <a href="index.php?page=register" class="btn primary-btn">
                    <i class="fas fa-user-plus btn-icon"></i>
                    Registrovat se
                </a>
                <a href="index.php?page=login" class="btn primary-btn">
                    <i class="fas fa-sign-in-alt btn-icon"></i>
                    Přihlásit se
                </a>
            </div>
        <?php else: ?>
            <div class="cta-buttons">
                <a href="index.php?page=dashboard" class="btn primary-btn">
                    <i class="fas fa-tachometer-alt btn-icon"></i>
                    Přejít na dashboard
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Hero shapes -->
    <div class="shape shape1"></div>
    <div class="shape shape2"></div>
    <div class="shape shape3"></div>
</div>

<div class="features">
    <div class="feature">
        <div class="feature-icon">
            <i class="fas fa-dumbbell"></i>
        </div>
        <h3>Sledujte své cvičení</h3>
        <p>Zaznamenávejte různé typy cvičení, jejich trvání a spálené kalorie. Mějte přehled o své aktivitě.</p>
        <a href="<?php echo isset($_SESSION['user_id']) ? 'index.php?page=exercises' : 'index.php?page=register'; ?>" class="feature-link">
            Vyzkoušet
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    
    <div class="feature">
        <div class="feature-icon">
            <i class="fas fa-trophy"></i>
        </div>
        <h3>Nastavte si cíle</h3>
        <p>Vytvářejte si vlastní fitness cíle a sledujte svůj pokrok při jejich plnění. Motivujte se k lepším výsledkům.</p>
        <a href="<?php echo isset($_SESSION['user_id']) ? 'index.php?page=goals' : 'index.php?page=register'; ?>" class="feature-link">
            Vyzkoušet
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    
    <div class="feature">
        <div class="feature-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <h3>Sledujte své statistiky</h3>
        <p>Zobrazujte si statistiky svého cvičení a pokroku v čase pomocí přehledných grafů. Analyzujte své výsledky.</p>
        <a href="<?php echo isset($_SESSION['user_id']) ? 'index.php?page=dashboard' : 'index.php?page=register'; ?>" class="feature-link">
            Vyzkoušet
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>

<div class="how-it-works">
    <h2>Jak to funguje?</h2>
    <div class="steps">
        <div class="step">
            <div class="step-number">1</div>
            <h3>Vytvořte si účet</h3>
            <p>Registrujte se zdarma a vytvořte si svůj osobní profil. Zadejte základní informace a začněte používat aplikaci.</p>
        </div>
        
        <div class="step">
            <div class="step-number">2</div>
            <h3>Přidejte své cvičení</h3>
            <p>Zaznamenávejte různé typy cvičení, jejich trvání, spálené kalorie a další podrobnosti pro přesné sledování.</p>
        </div>
        
        <div class="step">
            <div class="step-number">3</div>
            <h3>Nastavte si cíle</h3>
            <p>Vytvářejte si osobní fitness cíle, kterých chcete dosáhnout. Stanovte si konkrétní plán pro jejich splnění.</p>
        </div>
        
        <div class="step">
            <div class="step-number">4</div>
            <h3>Sledujte svůj pokrok</h3>
            <p>Prohlížejte si podrobné statistiky a sledujte svůj pokrok v čase. Dosahujte svých cílů a překonávejte své limity.</p>
        </div>
    </div>
</div>

<!-- Testimonials section -->
<div class="testimonials">
    <h2>Co říkají naši uživatelé</h2>
    <div class="testimonials-container">
        <div class="testimonial">
            <div class="testimonial-quote">
                <i class="fas fa-quote-left"></i>
            </div>
            <p class="testimonial-text">Fitness Tracker mi pomohl pravidelně cvičit a sledovat svůj pokrok. Díky přehledným statistikám vím, na čem musím zapracovat.</p>
            <div class="testimonial-author">
                <img class="testimonial-avatar" src="public/images/Jan_novak.jpg" alt="Avatar">
                <div class="testimonial-info">
                    <h4>Jan Novák</h4>
                    <p>Aktivní uživatel, 3 měsíce</p>
                </div>
            </div>
        </div>
        
        <div class="testimonial">
            <div class="testimonial-quote">
                <i class="fas fa-quote-left"></i>
            </div>
            <p class="testimonial-text">Stanovení cílů a jejich sledování mě motivuje k lepším výkonům. Aplikace je jednoduchá a přehledná, přesně to jsem hledala.</p>
            <div class="testimonial-author">
                <img class="testimonial-avatar" src="public/images/Petra_svobodova.jpg" alt="Avatar">
                <div class="testimonial-info">
                    <h4>Petra Svobodová</h4>
                    <p>Aktivní uživatelka, 6 měsíců</p>
                </div>
            </div>
        </div>
        
        <div class="testimonial">
            <div class="testimonial-quote">
                <i class="fas fa-quote-left"></i>
            </div>
            <p class="testimonial-text">Nejlepší aplikace pro sledování cvičení, kterou jsem kdy používal. Díky ní jsem si vytvořil pravidelný návyk cvičení.</p>
            <div class="testimonial-author">
                <img class="testimonial-avatar" src="public/images/Martin_cerny.jpg" alt="Avatar">
                <div class="testimonial-info">
                    <h4>Martin Černý</h4>
                    <p>Aktivní uživatel, 1 rok</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Call to action -->
<div class="cta-section">
    <div class="cta-content">
        <h2>Začněte sledovat své fitness cíle ještě dnes</h2>
        <p>Registrujte se zdarma a získejte přístup ke všem funkcím aplikace Fitness Tracker</p>
        <a href="index.php?page=register" class="btn">
            <i class="fas fa-rocket btn-icon"></i>
            Začít zdarma
        </a>
    </div>
</div>

<?php include 'src/views/footer.php'; ?> 