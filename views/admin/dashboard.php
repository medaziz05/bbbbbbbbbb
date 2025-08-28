<?php
/**
 * Vue du tableau de bord
 * views/dashboard/index.php
 */
?>
<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0">
            <i class="fas fa-tachometer-alt me-2"></i>
            Tableau de bord
        </h1>
        <p class="text-muted">Bienvenue, <?= e($_SESSION['user_name']) ?></p>
    </div>
</div>

<?php if ($_SESSION['user_role'] === 'admin'): ?>
<!-- Statistiques Admin -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div>
                        <div class="h4 mb-1"><?= $stats['total'] ?? 0 ?></div>
                        <div class="text-muted small">Idées totales</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="h4 mb-1"><?= $totalUsers ?? 0 ?></div>
                        <div class="text-muted small">Utilisateurs actifs</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div class="h4 mb-1"><?= $stats['acceptees'] ?? 0 ?></div>
                        <div class="text-muted small">Idées acceptées</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-star"></i>
                    </div>
                    <div>
                        <div class="h4 mb-1"><?= number_format($stats['note_moyenne_globale'] ?? 0, 1) ?>/20</div>
                        <div class="text-muted small">Note moyenne</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Idées récentes</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($recentIdees)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Auteur</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentIdees as $idee): ?>
                            <tr>
                                <td>
                                    <a href="<?= BASE_URL ?>/idees/<?= $idee['id'] ?>" class="text-decoration-none">
                                        <?= e($idee['titre']) ?>
                                    </a>
                                </td>
                                <td><?= e($idee['utilisateur_prenom'] . ' ' . $idee['utilisateur_nom']) ?></td>
                                <td>
                                    <span class="badge bg-<?= getStatutClass($idee['statut']) ?>">
                                        <?= getStatutLabel($idee['statut']) ?>
                                    </span>
                                </td>
                                <td><?= formatDate($idee['date_creation']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                    <p>Aucune idée pour le moment</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Actions rapides</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= BASE_URL ?>/utilisateurs" class="btn btn-outline-primary">
                        <i class="fas fa-users me-2"></i>Gérer les utilisateurs
                    </a>
                    <a href="<?= BASE_URL ?>/thematiques" class="btn btn-outline-primary">
                        <i class="fas fa-tags me-2"></i>Gérer les thématiques
                    </a>
                    <a href="<?= BASE_URL ?>/idees" class="btn btn-outline-primary">
                        <i class="fas fa-lightbulb me-2"></i>Voir toutes les idées
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php elseif ($_SESSION['user_role'] === 'salarie'): ?>
<!-- Dashboard Salarié -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Mes idées</h5>
                <a href="<?= BASE_URL ?>/idees/create" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-2"></i>Nouvelle idée
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($mesIdees)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Thématique</th>
                                <th>Statut</th>
                                <th>Note</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mesIdees as $idee): ?>
                            <tr>
                                <td><?= e($idee['titre']) ?></td>
                                <td><?= e($idee['thematique_nom']) ?></td>
                                <td>
                                    <span class="badge bg-<?= getStatutClass($idee['statut']) ?>">
                                        <?= getStatutLabel($idee['statut']) ?>
                                    </span>
                                </td>
                                <td><?= formatNote($idee['note_moyenne']) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/idees/<?= $idee['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-lightbulb fa-3x mb-3 opacity-50"></i>
                    <p>Vous n'avez pas encore proposé d'idées</p>
                    <a href="<?= BASE_URL ?>/idees/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Proposer ma première idée
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Meilleures idées</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($meilleuresIdees)): ?>
                <?php foreach (array_slice($meilleuresIdees, 0, 5) as $idee): ?>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <div class="fw-semibold small"><?= truncate($idee['titre'], 30) ?></div>
                        <small class="text-muted"><?= e($idee['utilisateur_prenom'] . ' ' . $idee['utilisateur_nom']) ?></small>
                    </div>
                    <span class="badge bg-success"><?= formatNote($idee['note_moyenne']) ?></span>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <div class="text-center text-muted py-3">
                    <i class="fas fa-star opacity-50"></i>
                    <p class="mb-0 small">Aucune idée évaluée</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php elseif ($_SESSION['user_role'] === 'evaluateur'): ?>
<!-- Dashboard Évaluateur - SECTION CORRIGÉE -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-check me-2"></i>Idées à évaluer
                </h5>
                <a href="<?= BASE_URL ?>/evaluations/to-evaluate" class="btn btn-primary btn-sm">
                    <i class="fas fa-list me-2"></i>Voir toutes
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($ideesAEvaluer)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Auteur</th>
                                <th>Thématique</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($ideesAEvaluer, 0, 10) as $idee): ?>
                            <tr>
                                <td>
                                    <a href="<?= BASE_URL ?>/idees/<?= $idee['id'] ?>" class="text-decoration-none fw-semibold">
                                        <?= e($idee['titre']) ?>
                                    </a>
                                </td>
                                <td><?= e($idee['utilisateur_prenom'] . ' ' . $idee['utilisateur_nom']) ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?= e($idee['thematique_nom']) ?></span>
                                </td>
                                <td><?= formatDate($idee['date_creation']) ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= BASE_URL ?>/idees/<?= $idee['id'] ?>" 
                                           class="btn btn-outline-primary" title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>/evaluations/create/<?= $idee['id'] ?>" 
                                           class="btn btn-success" title="Évaluer cette idée">
                                            <i class="fas fa-star me-1"></i>Évaluer
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="<?= BASE_URL ?>/evaluations/to-evaluate" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i>Voir toutes les idées à évaluer
                    </a>
                </div>
                <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-clipboard-check fa-3x mb-3 opacity-50"></i>
                    <h5>Aucune idée à évaluer</h5>
                    <p>Toutes les idées en évaluation ont été traitées.</p>
                    <a href="<?= BASE_URL ?>/evaluations" class="btn btn-outline-primary">
                        <i class="fas fa-star me-2"></i>Voir mes évaluations
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-star me-2"></i>Mes dernières évaluations
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($mesEvaluations)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach (array_slice($mesEvaluations, 0, 5) as $evaluation): ?>
                    <div class="list-group-item border-0 px-0 py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <a href="<?= BASE_URL ?>/idees/<?= $evaluation['idee_id'] ?? '#' ?>" 
                                   class="fw-semibold text-decoration-none small">
                                    <?= truncate($evaluation['idee_titre'] ?? 'Titre non disponible', 35) ?>
                                </a>
                                <div class="text-muted small mt-1">
                                    <i class="fas fa-clock me-1"></i>
                                    <?= formatDate($evaluation['date_creation']) ?>
                                </div>
                            </div>
                            <span class="badge bg-primary ms-2">
                                <?= number_format($evaluation['note'], 1) ?>/20
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-center mt-3 pt-2 border-top">
                    <a href="<?= BASE_URL ?>/evaluations" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-list me-2"></i>Toutes mes évaluations
                    </a>
                </div>
                <?php else: ?>
                <div class="text-center text-muted py-3">
                    <i class="fas fa-star fa-2x mb-2 opacity-50"></i>
                    <p class="mb-2">Aucune évaluation effectuée</p>
                    <a href="<?= BASE_URL ?>/evaluations/to-evaluate" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>Commencer à évaluer
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Statistiques rapides -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Mes statistiques
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <div class="h4 mb-0 text-primary"><?= count($mesEvaluations ?? []) ?></div>
                            <small class="text-muted">Évaluations</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="h4 mb-0 text-warning"><?= count($ideesAEvaluer ?? []) ?></div>
                        <small class="text-muted">En attente</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>