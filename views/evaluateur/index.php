<?php
/**
 * Vue liste des évaluations de l'évaluateur
 * views/evaluations/index.php
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="fas fa-star me-2"></i>Mes évaluations
        </h1>
        <p class="text-muted mb-0">Historique de vos évaluations d'idées</p>
    </div>
    <div>
        <a href="<?= BASE_URL ?>/evaluations/to-evaluate" class="btn btn-primary me-2">
            <i class="fas fa-plus me-2"></i>Évaluer des idées
        </a>
        <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline-secondary">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
        </a>
    </div>
</div>

<!-- Statistiques personnelles -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-primary mb-2">
                    <i class="fas fa-star fa-2x"></i>
                </div>
                <h4 class="mb-1"><?= count($evaluations ?? []) ?></h4>
                <small class="text-muted">Évaluations effectuées</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-success mb-2">
                    <i class="fas fa-chart-line fa-2x"></i>
                </div>
                <h4 class="mb-1">
                    <?php 
                    $totalNotes = 0;
                    $countNotes = 0;
                    foreach ($evaluations ?? [] as $eval) {
                        if ($eval['note']) {
                            $totalNotes += $eval['note'];
                            $countNotes++;
                        }
                    }
                    echo $countNotes > 0 ? number_format($totalNotes / $countNotes, 1) : '0';
                    ?>/20
                </h4>
                <small class="text-muted">Note moyenne donnée</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-info mb-2">
                    <i class="fas fa-calendar fa-2x"></i>
                </div>
                <h4 class="mb-1">
                    <?php
                    $thisMonth = 0;
                    $currentMonth = date('Y-m');
                    foreach ($evaluations ?? [] as $eval) {
                        if (substr($eval['date_creation'], 0, 7) === $currentMonth) {
                            $thisMonth++;
                        }
                    }
                    echo $thisMonth;
                    ?>
                </h4>
                <small class="text-muted">Ce mois-ci</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">Historique des évaluations</h5>
            </div>
            <div class="col-auto">
                <!-- Tri et filtres -->
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary active" data-sort="date">
                        <i class="fas fa-calendar me-1"></i>Par date
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-sort="note">
                        <i class="fas fa-star me-1"></i>Par note
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($evaluations)): ?>
        <div class="table-responsive">
            <table class="table table-hover" id="evaluationsTable">
                <thead class="table-light">
                    <tr>
                        <th>
                            <i class="fas fa-lightbulb me-1"></i>Idée évaluée
                        </th>
                        <th>
                            <i class="fas fa-star me-1"></i>Note donnée
                        </th>
                        <th>
                            <i class="fas fa-comment me-1"></i>Commentaire
                        </th>
                        <th>
                            <i class="fas fa-calendar me-1"></i>Date d'évaluation
                        </th>
                        <th class="text-center">
                            <i class="fas fa-cogs me-1"></i>Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($evaluations as $evaluation): ?>
                    <tr data-note="<?= $evaluation['note'] ?>" data-date="<?= $evaluation['date_creation'] ?>">
                        <td>
                            <div>
                                <div class="fw-semibold">
                                    <a href="<?= BASE_URL ?>/idees/<?= $evaluation['idee_id'] ?>" 
                                       class="text-decoration-none">
                                        <?= e($evaluation['idee_titre']) ?>
                                    </a>
                                </div>
                                <small class="text-muted">
                                    ID: #<?= $evaluation['idee_id'] ?>
                                </small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-<?php 
                                    $note = floatval($evaluation['note']);
                                    if ($note >= 15) echo 'success';
                                    elseif ($note >= 10) echo 'warning';
                                    else echo 'danger';
                                ?> me-2">
                                    <?= number_format($evaluation['note'], 1) ?>/20
                                </span>
                                <div class="flex-grow-1">
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar bg-<?php 
                                            if ($note >= 15) echo 'success';
                                            elseif ($note >= 10) echo 'warning';
                                            else echo 'danger';
                                        ?>" 
                                             style="width: <?= ($evaluation['note'] / 20) * 100 ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($evaluation['commentaire'])): ?>
                                <div class="small">
                                    <?= truncate($evaluation['commentaire'], 80) ?>
                                    <?php if (strlen($evaluation['commentaire']) > 80): ?>
                                        <button class="btn btn-link btn-sm p-0" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#commentModal<?= $evaluation['id'] ?>">
                                            <small>Voir plus</small>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">
                                    <i class="fas fa-minus"></i> Pas de commentaire
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="small">
                                <?= formatDate($evaluation['date_creation'], 'd/m/Y') ?>
                                <br>
                                <span class="text-muted">
                                    <?= formatDate($evaluation['date_creation'], 'H:i') ?>
                                </span>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="<?= BASE_URL ?>/idees/<?= $evaluation['idee_id'] ?>" 
                                   class="btn btn-outline-primary" 
                                   title="Voir l'idée"
                                   data-bs-toggle="tooltip">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/evaluations/edit/<?= $evaluation['idee_id'] ?>" 
                                   class="btn btn-outline-warning" 
                                   title="Modifier l'évaluation"
                                   data-bs-toggle="tooltip">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Modal pour commentaire complet -->
                    <?php if (!empty($evaluation['commentaire']) && strlen($evaluation['commentaire']) > 80): ?>
                    <div class="modal fade" id="commentModal<?= $evaluation['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-comment me-2"></i>
                                        Commentaire complet
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <h6 class="text-primary">
                                        <?= e($evaluation['idee_titre']) ?>
                                    </h6>
                                    <div class="border-start border-primary border-3 ps-3 mt-3">
                                        <?= nl2br(e($evaluation['commentaire'])) ?>
                                    </div>
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-star me-1"></i>
                                            Note donnée: <strong><?= number_format($evaluation['note'], 1) ?>/20</strong>
                                        </small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        Fermer
                                    </button>
                                    <a href="<?= BASE_URL ?>/evaluations/edit/<?= $evaluation['idee_id'] ?>" 
                                       class="btn btn-primary">
                                        <i class="fas fa-edit me-2"></i>Modifier
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php else: ?>
        <!-- État vide -->
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-star fa-5x text-muted opacity-25"></i>
            </div>
            <h4 class="text-muted">Aucune évaluation effectuée</h4>
            <p class="text-muted mb-4">
                Vous n'avez pas encore évalué d'idées. Commencez dès maintenant !
            </p>
            <a href="<?= BASE_URL ?>/evaluations/to-evaluate" class="btn btn-primary btn-lg">
                <i class="fas fa-star me-2"></i>Commencer à évaluer
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($evaluations)): ?>
<!-- JavaScript pour les interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Fonction de tri
    const sortButtons = document.querySelectorAll('[data-sort]');
    const tableBody = document.querySelector('#evaluationsTable tbody');
    const rows = Array.from(tableBody.querySelectorAll('tr:not([id*="commentModal"])'));
    
    sortButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Mettre à jour les boutons actifs
            sortButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const sortType = this.getAttribute('data-sort');
            
            rows.sort((a, b) => {
                let aValue, bValue;
                
                if (sortType === 'date') {
                    aValue = new Date(a.getAttribute('data-date'));
                    bValue = new Date(b.getAttribute('data-date'));
                    return bValue - aValue; // Plus récent en premier
                } else if (sortType === 'note') {
                    aValue = parseFloat(a.getAttribute('data-note'));
                    bValue = parseFloat(b.getAttribute('data-note'));
                    return bValue - aValue; // Note la plus élevée en premier
                }
                
                return 0;
            });
            
            // Réorganiser les lignes
            rows.forEach(row => tableBody.appendChild(row));
        });
    });
    
    // Animation des barres de progression
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.transition = 'width 1s ease-in-out';
            bar.style.width = width;
        }, 100);
    });
    
    // Effet de survol sur les lignes
    rows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
            this.style.transform = 'scale(1.01)';
            this.style.transition = 'all 0.2s ease';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
            this.style.transform = '';
        });
    });
});
</script>
<?php endif; ?>