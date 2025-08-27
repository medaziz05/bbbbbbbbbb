<?php
/**
 * Vue des idées à évaluer
 * views/evaluations/to_evaluate.php
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="fas fa-clipboard-check me-2"></i>Idées à évaluer
        </h1>
        <p class="text-muted mb-0">Idées en attente de votre évaluation</p>
    </div>
    <a href="<?= BASE_URL ?>/evaluations" class="btn btn-outline-primary">
        <i class="fas fa-history me-2"></i>Mes évaluations
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Idées en attente d'évaluation</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($idees)): ?>
        <div class="row">
            <?php foreach ($idees as $idee): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><?= e($idee['titre']) ?></h6>
                        <span class="badge bg-secondary"><?= e($idee['thematique_nom']) ?></span>
                    </div>
                    <div class="card-body">
                        <p class="card-text text-muted small mb-2">
                            Par <?= e($idee['utilisateur_prenom'] . ' ' . $idee['utilisateur_nom']) ?>
                            • <?= formatDate($idee['date_creation']) ?>
                        </p>
                        <p class="card-text"><?= truncate($idee['description'], 120) ?></p>
                        
                        <?php if ($idee['note_moyenne']): ?>
                        <div class="mb-2">
                            <small class="text-muted">Note actuelle : </small>
                            <span class="badge bg-primary"><?= formatNote($idee['note_moyenne']) ?></span>
                            <small class="text-muted">(<?= $idee['nombre_evaluations'] ?> évaluation(s))</small>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <a href="<?= BASE_URL ?>/idees/<?= $idee['id'] ?>" class="btn btn-primary">
                            <i class="fas fa-star me-2"></i>Évaluer cette idée
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center text-muted py-5">
            <i class="fas fa-clipboard-check fa-4x mb-3 opacity-25"></i>
            <h5>Aucune idée à évaluer</h5>
            <p>Toutes les idées disponibles ont été évaluées ou il n'y a pas encore d'idées soumises.</p>
            <a href="<?= BASE_URL ?>/evaluations" class="btn btn-outline-primary mt-3">
                <i class="fas fa-history me-2"></i>Voir mes évaluations
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
