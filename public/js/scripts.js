// Funkce pro inicializaci aplikace
document.addEventListener('DOMContentLoaded', function() {
    // Inicializace datepickeru pro formuláře s datem
    initDatePickers();
    
    // Inicializace validace formulářů
    initFormValidation();
    
    // Inicializace tlačítek pro odstranění
    initDeleteButtons();
});

// Inicializace datepickeru pro formuláře s datem
function initDatePickers() {
    // Pokud je k dispozici knihovna pro datepicker, inicializujte ji zde
    // Příklad pro nativní datepicker v HTML5
    const dateInputs = document.querySelectorAll('input[type="date"]');
    
    dateInputs.forEach(input => {
        // Nastavení dnešního data jako výchozí hodnoty, pokud není nastavena
        if (!input.value) {
            const today = new Date().toISOString().split('T')[0];
            input.value = today;
        }
    });
}

// Inicializace validace formulářů
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Validace povinných polí
            const requiredInputs = form.querySelectorAll('[required]');
            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid');
                    
                    // Vytvoření nebo aktualizace chybové zprávy
                    let errorMessage = input.parentNode.querySelector('.error-message');
                    if (!errorMessage) {
                        errorMessage = document.createElement('div');
                        errorMessage.className = 'error-message';
                        input.parentNode.appendChild(errorMessage);
                    }
                    errorMessage.textContent = 'Toto pole je povinné';
                } else {
                    input.classList.remove('is-invalid');
                    const errorMessage = input.parentNode.querySelector('.error-message');
                    if (errorMessage) {
                        errorMessage.remove();
                    }
                }
            });
            
            // Validace emailu
            const emailInputs = form.querySelectorAll('input[type="email"]');
            emailInputs.forEach(input => {
                if (input.value.trim() && !isValidEmail(input.value)) {
                    isValid = false;
                    input.classList.add('is-invalid');
                    
                    // Vytvoření nebo aktualizace chybové zprávy
                    let errorMessage = input.parentNode.querySelector('.error-message');
                    if (!errorMessage) {
                        errorMessage = document.createElement('div');
                        errorMessage.className = 'error-message';
                        input.parentNode.appendChild(errorMessage);
                    }
                    errorMessage.textContent = 'Zadejte platný email';
                }
            });
            
            // Validace hesla a potvrzení hesla
            const passwordInput = form.querySelector('input[name="password"]');
            const passwordConfirmInput = form.querySelector('input[name="password_confirm"]');
            
            if (passwordInput && passwordConfirmInput) {
                if (passwordInput.value !== passwordConfirmInput.value) {
                    isValid = false;
                    passwordConfirmInput.classList.add('is-invalid');
                    
                    // Vytvoření nebo aktualizace chybové zprávy
                    let errorMessage = passwordConfirmInput.parentNode.querySelector('.error-message');
                    if (!errorMessage) {
                        errorMessage = document.createElement('div');
                        errorMessage.className = 'error-message';
                        passwordConfirmInput.parentNode.appendChild(errorMessage);
                    }
                    errorMessage.textContent = 'Hesla se neshodují';
                }
            }
            
            if (!isValid) {
                event.preventDefault();
            }
        });
        
        // Odstranění chybových zpráv při změně hodnoty
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                input.classList.remove('is-invalid');
                const errorMessage = input.parentNode.querySelector('.error-message');
                if (errorMessage) {
                    errorMessage.remove();
                }
            });
        });
    });
}

// Inicializace tlačítek pro odstranění
function initDeleteButtons() {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            if (!confirm('Opravdu chcete odstranit tuto položku?')) {
                event.preventDefault();
            }
        });
    });
}

// Pomocná funkce pro validaci emailu
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Funkce pro aktualizaci hodnoty cíle
function updateGoalValue(goalId, currentValue, targetValue) {
    const progressBar = document.querySelector(`#goal-${goalId} .progress-fill`);
    const progressCurrent = document.querySelector(`#goal-${goalId} .progress-current`);
    const progressPercent = document.querySelector(`#goal-${goalId} .progress-percent`);
    
    if (progressBar && progressCurrent && progressPercent) {
        progressCurrent.textContent = currentValue;
        
        const percent = Math.min(100, (currentValue / targetValue) * 100);
        progressBar.style.width = `${percent}%`;
        progressPercent.textContent = `(${Math.round(percent)}%)`;
    }
} 