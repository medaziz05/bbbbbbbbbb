<?php
/**
 * Vue détail d'une idée
 * views/idees/show.php
 */
?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1"><?= e($idee['titre']) ?></h5>
                    <div class="d-flex gap-2 mt-2">
                        <span class="badge bg-<?= getStatutClass($idee['statut']) ?>">
                            <?= getStatutLabel($idee['statut']) ?>
                        </span>
                        <span class="badge bg-secondary"><?= e($idee['thematique_nom']) ?></span>
                        <?php if ($idee['note_moyenne']): ?>
                        <span class="badge bg-primary">
                            <i class="fas fa-star me-1"></i><?= formatNote($idee['note_moyenne']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if (isset($canEdit) && $canEdit): ?>
                <div class="btn-group">
                    <a href="<?= BASE_URL ?>/idees/edit/<?= $idee['id'] ?>" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <button onclick="confirmDelete('<?= BASE_URL ?>/idees/delete/<?= $idee['id'] ?>')" 
                            class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-3 fw-semibold">Auteur :</div>
                    <div class="col-sm-9"><?= e($idee['utilisateur_prenom'] . ' ' . $idee['utilisateur_nom']) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3 fw-semibold">Date de soumission :</div>
                    <div class="col-sm-9"><?= formatDate($idee['date_creation']) ?></div>
                </div>
                
                <div class="mb-4">
                    <h6 class="fw-semibold mb-2">Description :</h6>
                    <div class="border-start border-primary border-3 ps-3">
                        <?= nl2br(e($idee['description'])) ?>
                    </div>
                </div>
                
                <!-- Formulaire d'évaluation pour les évaluateurs -->
                <?php if (isset($canEvaluate) && $canEvaluate): ?>
                <div class="border-top pt-4">
                    <h6 class="fw-semibold mb-3">Évaluer cette idée</h6>
                    <form method="POST" action="<?= BASE_URL ?>/idees/evaluate/<?= $idee['id'] ?>">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="note" class="form-label">Note sur 20 <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="note" name="note" 
                                       min="0" max="20" step="0.5" required>
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="commentaire" class="form-label">Commentaire</label>
                            <textarea class="form-control" id="commentaire" name="commentaire" rows="3"
                                      placeholder="Vos observations et suggestions..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-star me-2"></i>Soumettre l'évaluation
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Informations supplémentaires -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Informations</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Nombre d'évaluations :</small>
                    <span class="fw-semibold"><?= $idee['nombre_evaluations'] ?? 0 ?></span>
                </div>
                <?php if ($idee['note_moyenne']): ?>
                <div class="mb-2">
                    <small class="text-muted">Note moyenne :</small>
                    <span class="fw-semibold text-primary"><?= formatNote($idee['note_moyenne']) ?></span>
                </div>
                <?php endif; ?>
                <div class="mb-2">
                    <small class="text-muted">Dernière modification :</small>
                    <span class="fw-semibold"><?= formatDate($idee['date_modification']) ?></span>
                </div>
            </div>
        </div>
        
        <!-- Évaluations -->
        <?php if (!empty($evaluations)): ?>
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Évaluations (<?= count($evaluations) ?>)</h6>
            </div>
            <div class="card-body">
                <?php foreach ($evaluations as $evaluation): ?>
                <div class="mb-3 pb-3 <?= !end($evaluations) === $evaluation ? 'border-bottom' : '' ?>">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="fw-semibold small">
                                <?= e($evaluation['evaluateur_prenom'] . ' ' . $evaluation['evaluateur_nom']) ?>
                            </div>
                            <small class="text-muted"><?= formatDate($evaluation['date_creation']) ?></small>
                        </div>
                        <span class="badge bg-primary"><?= number_format($evaluation['note'], 1) ?>/20</span>
                    </div>
                    <?php if (!empty($evaluation['commentaire'])): ?>
                    <div class="text-muted small">
                        <?= nl2br(e($evaluation['commentaire'])) ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="mt-3">
    <a href="<?= BASE_URL ?>/idees" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
    </a>
</div>
