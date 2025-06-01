document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('exercises-container');
    const addButton = document.getElementById('add-exercise');
    let exerciseCount = window.exerciseCount || 0;

    // Získání aktuální stránky z URL
    const currentPage = new URLSearchParams(window.location.search).get('page');

    // Funkce pro aktualizaci zobrazení polí podle typu cviku
    function updateExerciseFields(type, index) {
        const exerciseFields = document.getElementById(`exercise-fields-${index}`);
        const strengthFields = exerciseFields.querySelectorAll('.strength-fields');
        const cardioFields = exerciseFields.querySelectorAll('.cardio-fields');
        
        exerciseFields.classList.add('active');
        
        // Zobrazení/skrytí polí podle typu cviku
        strengthFields.forEach(field => {
            field.style.display = type === 'strength' ? 'block' : 'none';
        });
        cardioFields.forEach(field => {
            field.style.display = type === 'cardio' ? 'block' : 'none';
        });
    }

    // Funkce pro zobrazení vybraného cviku
    function displaySelectedExercise(exercise, index) {
        const selectedExerciseDiv = document.getElementById(`selected-exercise-${index}`);
        const selectBtn = selectedExerciseDiv.parentElement.querySelector('.select-exercise-btn');
        const exerciseIdInput = selectedExerciseDiv.parentElement.querySelector('.exercise-id-input');
        const exerciseFields = document.getElementById(`exercise-fields-${index}`);
        
        // Aktualizace obsahu karty cviku
        selectedExerciseDiv.querySelector('.exercise-card-title').textContent = exercise.name;
        
        // Získání českého názvu typu cviku
        const exerciseTypeMap = {
            'cardio': 'Kardio',
            'strength': 'Silové',
            'flexibility': 'Flexibilita',
            'balance': 'Rovnováha',
            'other': 'Ostatní'
        };
        
        const exerciseTypeBadge = selectedExerciseDiv.querySelector('.exercise-type-badge');
        exerciseTypeBadge.textContent = exerciseTypeMap[exercise.exercise_type] || exercise.exercise_type;
        exerciseTypeBadge.className = `exercise-type-badge exercise-type-${exercise.exercise_type}`;
        
        selectedExerciseDiv.querySelector('.exercise-description').textContent = exercise.description || 'Žádný popis';
        
        // Aktualizace skrytého inputu
        exerciseIdInput.value = exercise.id;
        
        // Aktualizace hodnot polí
        if (exerciseFields) {
            const setsInput = exerciseFields.querySelector('input[name$="[sets]"]');
            const repsInput = exerciseFields.querySelector('input[name$="[reps]"]');
            const weightInput = exerciseFields.querySelector('input[name$="[weight]"]');
            const distanceInput = exerciseFields.querySelector('input[name$="[distance]"]');
            
            if (setsInput) setsInput.value = exercise.sets || '';
            if (repsInput) repsInput.value = exercise.reps || '';
            if (weightInput) weightInput.value = exercise.weight || '';
            if (distanceInput) distanceInput.value = exercise.distance || '';
        }
        
        // Zobrazení karty a skrytí tlačítka
        selectedExerciseDiv.style.display = 'block';
        selectBtn.style.display = 'none';
        
        // Aktualizace polí podle typu cviku
        updateExerciseFields(exercise.exercise_type, index);
    }

    // Inicializace vybraných cviků ze sessionu
    if (typeof selectedExercises !== 'undefined') {
        selectedExercises.forEach((exercise, index) => {
            displaySelectedExercise(exercise, index);
        });
    }

    // Přidání nového cviku
    addButton.addEventListener('click', function() {
        const template = `
            <div class="exercise-entry">
                <div class="card-body">
                    <div class="form-column">
                        <div class="form-group">
                            <label class="form-label">Cvik</label>
                            <div class="exercise-select-wrapper">
                                <div class="selected-exercise" id="selected-exercise-${exerciseCount}" style="display: none;">
                                    <div class="exercise-card">
                                        <div class="exercise-card-header">
                                            <h3 class="exercise-card-title"></h3>
                                            <span class="exercise-type-badge"></span>
                                        </div>
                                        <div class="exercise-card-body">
                                            <p class="exercise-description"></p>
                                        </div>
                                    </div>
                                </div>
                                <a href="index.php?page=select_exercise&return_to=${currentPage}&index=${exerciseCount}"
                                   class="btn btn-secondary select-exercise-btn">
                                    Vybrat cvik
                                </a>
                                <input type="hidden" 
                                       name="exercises[${exerciseCount}][exercise_id]" 
                                       class="exercise-id-input"
                                       value="">
                            </div>
                        </div>

                        <div class="exercise-fields" id="exercise-fields-${exerciseCount}">
                            <div class="form-group strength-fields">
                                <label class="form-label">Série</label>
                                <input type="number" class="form-control" name="exercises[${exerciseCount}][sets]" min="0">
                            </div>

                            <div class="form-group strength-fields">
                                <label class="form-label">Opakování</label>
                                <input type="number" class="form-control" name="exercises[${exerciseCount}][reps]" min="0">
                            </div>

                            <div class="form-group strength-fields">
                                <label class="form-label">Váha (kg)</label>
                                <input type="number" class="form-control" name="exercises[${exerciseCount}][weight]" min="0" step="0.1">
                            </div>

                            <div class="form-group cardio-fields">
                                <label class="form-label">Vzdálenost (km)</label>
                                <input type="number" class="form-control" name="exercises[${exerciseCount}][distance]" min="0" step="0.01">
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="button" class="btn btn-danger btn-sm remove-exercise">
                                Odstranit cvik
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', template);
        exerciseCount++;
    });

    // Funkce pro odstranění cviku
    async function removeExercise(element) {
        const exerciseEntry = element.closest('.exercise-entry');
        const index = Array.from(container.children).indexOf(exerciseEntry);
        
        try {
            const response = await fetch('index.php?page=remove_exercise_from_session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `index=${index}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                exerciseEntry.remove();
                // Aktualizace indexů zbývajících cviků
                container.querySelectorAll('.exercise-entry').forEach((entry, newIndex) => {
                    const selectedExercise = entry.querySelector('.selected-exercise');
                    const selectBtn = entry.querySelector('.select-exercise-btn');
                    const exerciseIdInput = entry.querySelector('.exercise-id-input');
                    const exerciseFields = entry.querySelector('.exercise-fields');
                    
                    if (selectedExercise) {
                        selectedExercise.id = `selected-exercise-${newIndex}`;
                    }
                    if (selectBtn) {
                        selectBtn.href = selectBtn.href.replace(/index=\d+/, `index=${newIndex}`);
                    }
                    if (exerciseIdInput) {
                        exerciseIdInput.name = `exercises[${newIndex}][exercise_id]`;
                    }
                    if (exerciseFields) {
                        exerciseFields.id = `exercise-fields-${newIndex}`;
                        exerciseFields.querySelectorAll('input').forEach(input => {
                            input.name = input.name.replace(/exercises\[\d+\]/, `exercises[${newIndex}]`);
                        });
                    }
                });
            } else {
                console.error('Chyba při odstraňování cviku:', data.error);
                alert('Nepodařilo se odstranit cvik. Zkuste to prosím znovu.');
            }
        } catch (error) {
            console.error('Chyba při odstraňování cviku:', error);
            alert('Nepodařilo se odstranit cvik. Zkuste to prosím znovu.');
        }
    }

    // Event listener pro tlačítko odstranění cviku
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-exercise')) {
            e.preventDefault();
            removeExercise(e.target);
        }
    });

    // Funkce pro mazání cvičení
    async function deleteExercise(id) {
        if (confirm('Opravdu chcete smazat toto cvičení?')) {
            try {
                const response = await fetch('index.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_exercise&id=${id}`
                });
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Nepodařilo se smazat cvičení. Zkuste to prosím znovu.');
                }
            } catch (error) {
                console.error('Chyba při mazání cvičení:', error);
                alert('Nastala chyba při mazání cvičení. Zkuste to prosím znovu.');
            }
        }
    }
}); 