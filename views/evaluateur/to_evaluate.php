<?php
/**
 * Vue liste des idées à évaluer
 * views/evaluations/to_evaluate.php
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="fas fa-clipboard-check me-2"></i>Idées à évaluer
        </h1>
        <p class="text-muted mb-0">Évaluez les idées soumises par les collaborateurs</p>
    </div>
    <div>
        <a href="<?= BASE_URL ?>/evaluations" class="btn btn-outline-primary me-2">
            <i class="fas fa-star me-2"></i>Mes évaluations
        </a>
        <a href="<?= BASE_URL ?>/dashboard" class="btn btn-secondary">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
        </a>
    </div>
</div>

<!-- Statistiques rapides -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-gradient-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="text-white mb-0">
                            <i class="fas fa-tasks me-2"></i>
                            <?= count($idees ?? []) ?> idée<?= count($idees ?? []) > 1 ? 's' : '' ?> en attente d'évaluation
                        </h5>
                        <small class="text-white-50">Votre expertise est précieuse pour l'innovation</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">Liste des idées</h5>
            </div>
            <div class="col-auto">
                <!-- Filtres si nécessaire -->
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary active" data-filter="all">
                        <i class="fas fa-list me-1"></i>Toutes
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-filter="recent">
                        <i class="fas fa-clock me-1"></i>Récentes
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($idees)): ?>
        <div class="table-responsive">
            <table class="table table-hover" id="ideesTable">
                <thead class="table-light">
                    <tr>
                        <th>
                            <i class="fas fa-lightbulb me-1"></i>Idée
                        </th>
                        <th>
                            <i class="fas fa-user me-1"></i>Auteur
                        </th>
                        <th>
                            <i class="fas fa-tag me-1"></i>Thématique
                        </th>
                        <th>
                            <i class="fas fa-calendar me-1"></i>Date
                        </th>
                        <th>
                            <i class="fas fa-info-circle me-1"></i>Statut
                        </th>
                        <th class="text-center">
                            <i class="fas fa-cogs me-1"></i>Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($idees as $idee): ?>
                    <tr data-date="<?= $idee['date_creation'] ?>">
                        <td>
                            <div>
                                <div class="fw-semibold">
                                    <?= e($idee['titre']) ?>
                                </div>
                                <small class="text-muted">
                                    <?= truncate($idee['description'], 80) ?>
                                </small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                     style="width: 32px; height: 32px;">
                                    <small class="fw-bold">
                                        <?= strtoupper(substr($idee['utilisateur_prenom'], 0, 1)) ?>
                                    </small>
                                </div>
                                <div>
                                    <div class="small fw-semibold">
                                        <?= e($idee['utilisateur_prenom'] . ' ' . $idee['utilisateur_nom']) ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary rounded-pill">
                                <?= e($idee['thematique_nom']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="small">
                                <?= formatDate($idee['date_creation'], 'd/m/Y') ?>
                                <br>
                                <span class="text-muted">
                                    <?= formatDate($idee['date_creation'], 'H:i') ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-<?= getStatutClass($idee['statut']) ?>">
                                <?= getStatutLabel($idee['statut']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="<?= BASE_URL ?>/idees/<?= $idee['id'] ?>" 
                                   class="btn btn-outline-info" 
                                   title="Voir les détails"
                                   data-bs-toggle="tooltip">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/evaluations/create/<?= $idee['id'] ?>" 
                                   class="btn btn-success" 
                                   title="Évaluer cette idée"
                                   data-bs-toggle="tooltip">
                                    <i class="fas fa-star"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination si nécessaire -->
        <?php if (count($idees) > 10): ?>
        <nav aria-label="Pagination des idées" class="mt-3">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <span class="page-link">Précédent</span>
                </li>
                <li class="page-item active">
                    <span class="page-link">1</span>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#">2</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#">Suivant</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
        
        <?php else: ?>
        <!-- État vide -->
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-clipboard-check fa-5x text-muted opacity-25"></i>
            </div>
            <h4 class="text-muted">Aucune idée à évaluer</h4>
            <p class="text-muted mb-4">
                Toutes les idées soumises ont déjà été évaluées ou sont en attente de soumission.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="<?= BASE_URL ?>/evaluations" class="btn btn-primary">
                    <i class="fas fa-star me-2"></i>Voir mes évaluations
                </a>
                <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline-secondary">
                    <i class="fas fa-tachometer-alt me-2"></i>Retour au dashboard
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($idees)): ?>
<!-- JavaScript pour les interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Filtres
    const filterButtons = document.querySelectorAll('[data-filter]');
    const tableRows = document.querySelectorAll('#ideesTable tbody tr');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Mettre à jour les boutons actifs
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            
            if (filter === 'all') {
                tableRows.forEach(row => row.style.display = '');
            } else if (filter === 'recent') {
                const now = new Date();
                const sevenDaysAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                
                tableRows.forEach(row => {
                    const dateStr = row.getAttribute('data-date');
                    const rowDate = new Date(dateStr);
                    
                    if (rowDate >= sevenDaysAgo) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        });
    });
    
    // Animation au survol des lignes
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
            this.style.transform = 'translateX(5px)';
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