<?php
include 'header.php';

$availableExercises = $exerciseModel->getAllExercises();
$exerciseTypes = [
    'cardio' => 'Kardio',
    'strength' => 'Silové',
    'flexibility' => 'Flexibilita',
    'balance' => 'Rovnováha',
    'other' => 'Ostatní'
];

$returnTo = $_GET['return_to'] ?? 'add_exercise';
$index = $_GET['index'] ?? 0;
?>

<div class="exercises-container">
    <a href="index.php?page=<?php echo $returnTo; ?>" class="back-button">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
        Zpět na formulář
    </a>

    <div class="exercises-header">
        <h2>Výběr cviku</h2>
        <div class="exercises-filters">
            <div class="search-box">
                <input type="text" id="exercise-search" placeholder="Hledat cvik...">
            </div>
            <div class="type-filters">
                <button class="type-filter active" data-type="all">Vše</button>
                <?php foreach ($exerciseTypes as $type => $label): ?>
                    <button class="type-filter" data-type="<?php echo $type; ?>"><?php echo $label; ?></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="exercises-grid" id="exercises-grid">
        <?php foreach ($availableExercises as $exercise): ?>
            <div class="exercise-card" 
                 data-type="<?php echo $exercise['exercise_type']; ?>"
                 data-name="<?php echo htmlspecialchars(strtolower($exercise['name'])); ?>">
                <div class="exercise-card-header">
                    <h3 class="exercise-card-title"><?php echo htmlspecialchars($exercise['name']); ?></h3>
                    <span class="exercise-type-badge exercise-type-<?php echo $exercise['exercise_type']; ?>">
                        <?php echo $exerciseTypes[$exercise['exercise_type']]; ?>
                    </span>
                </div>
                <div class="exercise-card-body">
                    <p class="exercise-description"><?php echo htmlspecialchars($exercise['description']); ?></p>
                </div>
                <div class="exercise-card-footer">
                    <a href="index.php?page=handle_selected_exercise&selected_exercise=<?php echo $exercise['id']; ?>&index=<?php echo $index; ?>&return_to=<?php echo $returnTo; ?>" 
                       class="btn btn-secondary select-exercise-btn">
                        Vybrat cvik
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="no-exercises" style="display: none;">
        Žádné cviky nebyly nalezeny.
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('exercise-search');
    const typeFilters = document.querySelectorAll('.type-filter');
    const exerciseCards = document.querySelectorAll('.exercise-card');
    const noExercises = document.querySelector('.no-exercises');

    // Funkce pro filtrování cviků
    function filterExercises() {
        const searchTerm = searchInput.value.toLowerCase();
        const activeType = document.querySelector('.type-filter.active').dataset.type;
        let visibleCount = 0;

        exerciseCards.forEach(card => {
            const cardType = card.dataset.type;
            const cardName = card.dataset.name;
            const matchesSearch = cardName.includes(searchTerm);
            const matchesType = activeType === 'all' || cardType === activeType;

            if (matchesSearch && matchesType) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Zobrazení/skrytí zprávy o nenalezených cvicích
        noExercises.style.display = visibleCount === 0 ? 'block' : 'none';
    }

    // Event listenery pro vyhledávání a filtry
    searchInput.addEventListener('input', filterExercises);

    typeFilters.forEach(filter => {
        filter.addEventListener('click', function() {
            typeFilters.forEach(f => f.classList.remove('active'));
            this.classList.add('active');
            filterExercises();
        });
    });
});
</script>

<?php include 'footer.php'; ?> 