<?php
/**
 * Vue liste des évaluations (pour les évaluateurs)
 * views/evaluations/index.php
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="fas fa-star me-2"></i>Mes évaluations
        </h1>
        <p class="text-muted mb-0">Historique de vos évaluations</p>
    </div>
    <a href="<?= BASE_URL ?>/evaluations/to-evaluate" class="btn btn-primary">
        <i class="fas fa-clipboard-check me-2"></i>Idées à évaluer
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Évaluations réalisées</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($evaluations)): ?>
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Idée</th>
                        <th>Note</th>
                        <th>Commentaire</th>
                        <th>Date d'évaluation</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($evaluations as $evaluation): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= e($evaluation['idee_titre']) ?></div>
                        </td>
                        <td>
                            <span class="badge bg-primary fs-6"><?= number_format($evaluation['note'], 1) ?>/20</span>
                        </td>
                        <td>
                            <?php if (!empty($evaluation['commentaire'])): ?>
                            <span class="text-truncate d-inline-block" style="max-width: 200px;" 
                                  title="<?= e($evaluation['commentaire']) ?>">
                                <?= e($evaluation['commentaire']) ?>
                            </span>
                            <?php else: ?>
                            <span class="text-muted">Aucun commentaire</span>
                            <?php endif; ?>
                        </td>
                        <td><?= formatDate($evaluation['date_creation']) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/idees/<?= $evaluation['idee_id'] ?>" 
                               class="btn btn-sm btn-outline-primary" title="Voir l'idée">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center text-muted py-5">
            <i class="fas fa-star fa-4x mb-3 opacity-25"></i>
            <h5>Aucune évaluation réalisée</h5>
            <p>Vous n'avez pas encore évalué d'idées.</p>
            <a href="<?= BASE_URL ?>/evaluations/to-evaluate" class="btn btn-primary mt-3">
                <i class="fas fa-clipboard-check me-2"></i>Évaluer des idées
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>