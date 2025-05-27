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