        </div>
    </main>
    
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <i class="fas fa-heartbeat"></i>
                    <p>Fitness Tracker</p>
                </div>
                <div class="footer-links">
                    <a href="index.php">Domů</a>
                    <a href="index.php?page=login">Přihlášení</a>
                    <a href="index.php?page=register">Registrace</a>
                </div>
                <div class="footer-social">
                    <a href="#" class="social-btn" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-btn" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-btn" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Fitness Tracker. Všechna práva vyhrazena.</p>
            </div>
        </div>
    </footer>
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const mainNav = document.getElementById('main-nav');
            
            if (mobileMenuToggle && mainNav) {
                mobileMenuToggle.addEventListener('click', function() {
                    mainNav.classList.toggle('active');
                    this.querySelector('i').classList.toggle('fa-bars');
                    this.querySelector('i').classList.toggle('fa-times');
                });
            }
            
            // Dark mode toggle
            const themeToggle = document.getElementById('theme-toggle');
            const htmlElement = document.documentElement;
            const toggleText = document.querySelector('.theme-toggle-text');
            
            // Check for saved theme preference or default to light
            const savedTheme = localStorage.getItem('theme') || 'light';
            
            // Apply saved theme
            applyTheme(savedTheme);
            
            function applyTheme(theme) {
                htmlElement.setAttribute('data-theme', theme);
                
                // Update icon and text
                if (themeToggle) {
                    if (theme === 'dark') {
                        themeToggle.querySelector('i').classList.replace('fa-moon', 'fa-sun');
                        if (toggleText) {
                            toggleText.textContent = 'Světlý režim';
                        }
                    } else {
                        themeToggle.querySelector('i').classList.replace('fa-sun', 'fa-moon');
                        if (toggleText) {
                            toggleText.textContent = 'Tmavý režim';
                        }
                    }
                }
                
                localStorage.setItem('theme', theme);
            }
            
            // Toggle theme on button click
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    const currentTheme = htmlElement.getAttribute('data-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    
                    applyTheme(newTheme);
                });
            }
        });
    </script>
</body>
</html> 