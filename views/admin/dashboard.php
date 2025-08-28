<?php
/**
 * Vue dashboard principal - VERSION FINALE COMPLÈTE
 * views/admin/dashboard.php
 */

$userRole = $_SESSION['user_role'] ?? '';
?>

<div class="row mb-4">
    <div class="col">
        <h1 class="h2 mb-0">
            <i class="fas fa-tachometer-alt me-2"></i>
            Tableau de bord
            <?php if ($userRole === 'admin'): ?>
                <small class="text-muted">- Administrateur</small>
            <?php elseif ($userRole === 'evaluateur'): ?>
                <small class="text-muted">- Évaluateur</small>
            <?php else: ?>
                <small class="text-muted">- Salarié</small>
            <?php endif; ?>
        </h1>
        <p class="text-muted mb-0">
            Bienvenue <?= e($_SESSION['user_prenom'] ?? '') ?> <?= e($_SESSION['user_nom'] ?? '') ?>
        </p>
    </div>
</div>

<?php if ($userRole === 'admin'): ?>
<!-- Dashboard Administrateur -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-primary mb-2">
                    <i class="fas fa-lightbulb fa-2x"></i>
                </div>
                <h3 class="mb-1"><?= $stats['total'] ?? 0 ?></h3>
                <small class="text-muted">Idées soumises</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-success mb-2">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <h3 class="mb-1"><?= $stats['acceptees'] ?? 0 ?></h3>
                <small class="text-muted">Idées acceptées</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-info mb-2">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <h3 class="mb-1"><?= $totalUsers ?? 0 ?></h3>
                <small class="text-muted">Utilisateurs actifs</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-warning mb-2">
                    <i class="fas fa-tags fa-2x"></i>
                </div>
                <h3 class="mb-1"><?= $totalThematiques ?? 0 ?></h3>
                <small class="text-muted">Thématiques</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Idées récentes
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($recentIdees)): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Auteur</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentIdees as $idee): ?>
                            <tr>
                                <td><?= e($idee['titre']) ?></td>
                                <td><?= e($idee['utilisateur_prenom'] . ' ' . $idee['utilisateur_nom']) ?></td>
                                <td>
                                    <span class="badge bg-<?= getStatutClass($idee['statut']) ?>">
                                        <?= getStatutLabel($idee['statut']) ?>
                                    </span>
                                </td>
                                <td><?= formatDate($idee['date_creation']) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/idees/<?= $idee['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        Voir
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">Aucune idée récente</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php elseif ($userRole === 'evaluateur'): ?>
<!-- Dashboard Évaluateur - CORRIGÉ -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-primary mb-2">
                    <i class="fas fa-star fa-2x"></i>
                </div>
                <h3 class="mb-1"><?= count($mesEvaluations ?? []) ?></h3>
                <small class="text-muted">Mes évaluations</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-warning mb-2">
                    <i class="fas fa-clipboard-check fa-2x"></i>
                </div>
                <h3 class="mb-1"><?= count($ideesAEvaluer ?? []) ?></h3>
                <small class="text-muted">Idées à évaluer</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-success mb-2">
                    <i class="fas fa-chart-line fa-2x"></i>
                </div>
                <h3 class="mb-1">
                    <?php 
                    $totalNotes = 0;
                    $countNotes = 0;
                    foreach ($mesEvaluations ?? [] as $eval) {
                        if ($eval['note']) {
                            $totalNotes += $eval['note'];
                            $countNotes++;
                        }
                    }
                    echo $countNotes > 0 ? number_format($totalNotes / $countNotes, 1) : '0';
                    ?>/20
                </h3>
                <small class="text-muted">Note moyenne donnée</small>
            </div>
        </div>
    </div>
</div>

<!-- Actions rapides pour évaluateur -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="text-white">Évaluer des idées</h5>
                        <p class="text-white-50 mb-0">
                            <?= count($ideesAEvaluer ?? []) ?> idée<?= count($ideesAEvaluer ?? []) > 1 ? 's' : '' ?> en attente
                        </p>
                    </div>
                    <div>
                        <a href="<?= BASE_URL ?>/evaluations/to-evaluate" class="btn btn-light">
                            <i class="fas fa-star me-2"></i>Évaluer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="text-white">Mes évaluations</h5>
                        <p class="text-white-50 mb-0">
                            Voir l'historique complet
                        </p>
                    </div>
                    <div>
                        <a href="<?= BASE_URL ?>/evaluations" class="btn btn-light">
                            <i class="fas fa-list me-2"></i>Voir tout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Idées à évaluer -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-check me-2"></i>Idées à évaluer
                </h5>
                <?php if (!empty($ideesAEvaluer)): ?>
                <a href="<?= BASE_URL ?>/evaluations/to-evaluate" class="btn btn-sm btn-outline-primary">
                    Voir toutes
                </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (!empty($ideesAEvaluer)): ?>
                <div class="list-group list-group-flush">
                    <?php 
                    $displayIdees = array_slice($ideesAEvaluer, 0, 3); // Afficher seulement 3 idées
                    foreach ($displayIdees as $idee): 
                    ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <h6 class="mb-1"><?= e($idee['titre']) ?></h6>
                            <small class="text-muted">
                                Par <?= e($idee['utilisateur_prenom'] . ' ' . $idee['utilisateur_nom']) ?>
                                • <?= formatDate($idee['date_creation'], 'd/m/Y') ?>
                            </small>
                        </div>
                        <div>
                            <a href="<?= BASE_URL ?>/evaluations/create/<?= $idee['id'] ?>" 
                               class="btn btn-sm btn-success" title="Évaluer">
                                <i class="fas fa-star"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (count($ideesAEvaluer) > 3): ?>
                <div class="text-center mt-3">
                    <small class="text-muted">
                        Et <?= count($ideesAEvaluer) - 3 ?> autre<?= count($ideesAEvaluer) - 3 > 1 ? 's' : '' ?> idée<?= count($ideesAEvaluer) - 3 > 1 ? 's' : '' ?>...
                    </small>
                </div>
                <?php endif; ?>
                
                <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-clipboard-check fa-3x mb-3 opacity-25"></i>
                    <p>Aucune idée à évaluer pour le moment</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Mes dernières évaluations -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-star me-2"></i>Mes dernières évaluations
                </h5>
                <?php if (!empty($mesEvaluations)): ?>
                <a href="<?= BASE_URL ?>/evaluations" class="btn btn-sm btn-outline-primary">
                    Voir toutes
                </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (!empty($mesEvaluations)): ?>
                <div class="list-group list-group-flush">
                    <?php 
                    $displayEvaluations = array_slice($mesEvaluations, 0, 3); // Afficher seulement 3 évaluations
                    foreach ($displayEvaluations as $eval): 
                    ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <h6 class="mb-1"><?= e($eval['idee_titre']) ?></h6>
                            <small class="text-muted">
                                Évaluée le <?= formatDate($eval['date_creation'], 'd/m/Y') ?>
                            </small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-<?php 
                                $note = floatval($eval['note']);
                                if ($note >= 15) echo 'success';
                                elseif ($note >= 10) echo 'warning';
                                else echo 'danger';
                            ?>">
                                <?= number_format($eval['note'], 1) ?>/20
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (count($mesEvaluations) > 3): ?>
                <div class="text-center mt-3">
                    <small class="text-muted">
                        Et <?= count($mesEvaluations) - 3 ?> autre<?= count($mesEvaluations) - 3 > 1 ? 's' : '' ?> évaluation<?= count($mesEvaluations) - 3 > 1 ? 's' : '' ?>...
                    </small>
                </div>
                <?php endif; ?>
                
                <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-star fa-3x mb-3 opacity-25"></i>
                    <p>Aucune évaluation effectuée</p>
                    <a href="<?= BASE_URL ?>/evaluations/to-evaluate" class="btn btn-primary btn-sm">
                        <i class="fas fa-star me-2"></i>Commencer à évaluer
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Dashboard Salarié -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-primary mb-2">
                    <i class="fas fa-lightbulb fa-2x"></i>
                </div>
                <h3 class="mb-1"><?= count($mesIdees ?? []) ?></h3>
                <small class="text-muted">Mes idées soumises</small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-success mb-2">
                    <i class="fas fa-trophy fa-2x"></i>
                </div>
                <h3 class="mb-1">
                    <?php
                    $acceptees = 0;
                    foreach ($mesIdees ?? [] as $idee) {
                        if ($idee['statut'] === 'acceptee') $acceptees++;
                    }
                    echo $acceptees;
                    ?>
                </h3>
                <small class="text-muted">Idées acceptées</small>
            </div>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card bg-gradient-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="text-white">Soumettre une nouvelle idée</h5>
                        <p class="text-white-50 mb-0">Partagez votre innovation avec l'équipe</p>
                    </div>
                    <div class="col-auto">
                        <a href="<?= BASE_URL ?>/idees/create" class="btn btn-light btn-lg">
                            <i class="fas fa-plus me-2"></i>Nouvelle idée
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Mes idées -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>Mes idées récentes
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($mesIdees)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach (array_slice($mesIdees, 0, 5) as $idee): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <h6 class="mb-1"><?= e($idee['titre']) ?></h6>
                            <small class="text-muted">
                                <?= formatDate($idee['date_creation'], 'd/m/Y') ?>
                            </small>
                        </div>
                        <span class="badge bg-<?= getStatutClass($idee['statut']) ?>">
                            <?= getStatutLabel($idee['statut']) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-center mt-3">
                    <a href="<?= BASE_URL ?>/idees" class="btn btn-outline-primary btn-sm">
                        Voir toutes mes idées
                    </a>
                </div>
                <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-lightbulb fa-3x mb-3 opacity-25"></i>
                    <p>Aucune idée soumise</p>
                    <a href="<?= BASE_URL ?>/idees/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Première idée
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Meilleures idées -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-trophy me-2"></i>Meilleures idées
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($meilleuresIdees)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($meilleuresIdees as $idee): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <h6 class="mb-1"><?= e($idee['titre']) ?></h6>
                            <small class="text-muted">
                                Par <?= e($idee['utilisateur_prenom'] . ' ' . $idee['utilisateur_nom']) ?>
                            </small>
                        </div>
                        <span class="badge bg-warning">
                            <?= formatNote($idee['note_moyenne']) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-trophy fa-3x mb-3 opacity-25"></i>
                    <p>Aucune idée évaluée</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.list-group-item {
    border: none;
    border-bottom: 1px solid rgba(0,0,0,.125);
}

.list-group-item:last-child {
    border-bottom: none;
}

.opacity-25 {
    opacity: 0.25;
}
</style>