<?php
/**
 * Vue formulaire d'évaluation
 * views/evaluations/form.php
 */
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-star me-2"></i>
                    <?= $isEdit ? 'Modifier l\'évaluation' : 'Évaluer l\'idée' ?>
                </h5>
            </div>
            <div class="card-body">
                <?php if (isset($errors) && !empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <!-- Détails de l'idée -->
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-lightbulb me-2 text-warning"></i>
                            <?= e($idee['titre']) ?>
                        </h6>
                        <p class="card-text"><?= e($idee['description']) ?></p>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i>
                                    <strong>Auteur:</strong> <?= e($idee['utilisateur_prenom'] . ' ' . $idee['utilisateur_nom']) ?>
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-tag me-1"></i>
                                    <strong>Thématique:</strong> <?= e($idee['thematique_nom']) ?>
                                </small>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    <strong>Créée le:</strong> <?= formatDate($idee['date_creation']) ?>
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Statut:</strong> 
                                    <span class="badge bg-<?= getStatutClass($idee['statut']) ?>">
                                        <?= getStatutLabel($idee['statut']) ?>
                                    </span>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Formulaire d'évaluation -->
                <form method="POST" action="<?= BASE_URL ?>/evaluations/store" id="evaluationForm">
                    <input type="hidden" name="idee_id" value="<?= $idee['id'] ?>">
                    
                    <div class="mb-4">
                        <label for="note" class="form-label">
                            <i class="fas fa-star text-warning me-2"></i>
                            Note sur 20 <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-lg">
                            <input type="number" class="form-control" 
                                   id="note" name="note" min="0" max="20" step="0.5"
                                   value="<?= isset($evaluation['note']) ? $evaluation['note'] : '' ?>" 
                                   placeholder="16.5" required>
                            <span class="input-group-text">/20</span>
                        </div>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Évaluez l'idée selon sa <strong>faisabilité</strong>, son <strong>innovation</strong> et son <strong>impact potentiel</strong>
                        </div>
                        
                        <!-- Échelle d'aide -->
                        <div class="mt-3">
                            <small class="text-muted">Guide d'évaluation :</small>
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <div class="p-2 border rounded">
                                        <strong class="text-danger">0-7</strong> : Peu viable
                                        <br><small>Difficultés importantes</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-2 border rounded">
                                        <strong class="text-warning">8-14</strong> : Intéressant
                                        <br><small>Potentiel avec améliorations</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-2 border rounded">
                                        <strong class="text-success">15-20</strong> : Excellent
                                        <br><small>Très prometteuse</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="commentaire" class="form-label">
                            <i class="fas fa-comment me-2"></i>
                            Commentaire détaillé
                        </label>
                        <textarea class="form-control" id="commentaire" name="commentaire" 
                                  rows="6" placeholder="Expliquez votre évaluation en détail..."><?= isset($evaluation['commentaire']) ? e($evaluation['commentaire']) : '' ?></textarea>
                        <div class="form-text">
                            <i class="fas fa-lightbulb me-1"></i>
                            Expliquez les <strong>points forts</strong>, les <strong>points faibles</strong> et les <strong>suggestions d'amélioration</strong>
                        </div>
                    </div>
                    
                    <!-- Critères d'évaluation (aide visuelle) -->
                    <div class="card border-info mb-4">
                        <div class="card-header bg-info text-white">
                            <small><i class="fas fa-question-circle me-2"></i>Critères à considérer</small>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-primary">
                                        <i class="fas fa-cogs me-2"></i>Faisabilité
                                    </h6>
                                    <ul class="small text-muted">
                                        <li>Réalisabilité technique</li>
                                        <li>Ressources nécessaires</li>
                                        <li>Contraintes pratiques</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-success">
                                        <i class="fas fa-rocket me-2"></i>Innovation
                                    </h6>
                                    <ul class="small text-muted">
                                        <li>Originalité de l'approche</li>
                                        <li>Différenciation</li>
                                        <li>Créativité</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-warning">
                                        <i class="fas fa-chart-line me-2"></i>Impact
                                    </h6>
                                    <ul class="small text-muted">
                                        <li>Bénéfices attendus</li>
                                        <li>Portée du projet</li>
                                        <li>Valeur ajoutée</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="<?= BASE_URL ?>/evaluations/to-evaluate" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                            <i class="fas fa-save me-2"></i>
                            <?= $isEdit ? 'Mettre à jour l\'évaluation' : 'Enregistrer l\'évaluation' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('evaluationForm');
    const noteInput = document.getElementById('note');
    const submitBtn = document.getElementById('submitBtn');
    
    // Validation en temps réel
    noteInput.addEventListener('input', function() {
        const note = parseFloat(this.value);
        if (note >= 0 && note <= 20) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        }
    });
    
    // Confirmation avant soumission
    form.addEventListener('submit', function(e) {
        const note = parseFloat(noteInput.value);
        const commentaire = document.getElementById('commentaire').value.trim();
        
        if (note < 0 || note > 20) {
            e.preventDefault();
            alert('Veuillez saisir une note entre 0 et 20.');
            return;
        }
        
        if (commentaire.length === 0) {
            if (!confirm('Voulez-vous vraiment soumettre sans commentaire ?')) {
                e.preventDefault();
                return;
            }
        }
        
        // Désactiver le bouton pour éviter les doublons
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enregistrement...';
    });
});
</script>