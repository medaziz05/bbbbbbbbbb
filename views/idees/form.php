<?php
/**
 * Vue formulaire d'idée (création/modification)
 * views/idees/form.php
 */
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    <?= $action === 'create' ? 'Nouvelle idée' : 'Modifier l\'idée' ?>
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
                
                <form method="POST" action="<?= BASE_URL ?>/idees/<?= $action === 'create' ? 'store' : 'update/' . $idee['id'] ?>">
                    <div class="mb-3">
                        <label for="titre" class="form-label">
                            Titre de l'idée <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="titre" name="titre" 
                               value="<?= isset($idee['titre']) ? e($idee['titre']) : '' ?>" 
                               placeholder="Un titre accrocheur pour votre idée" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="thematique_id" class="form-label">
                            Thématique <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="thematique_id" name="thematique_id" required>
                            <option value="">Sélectionnez une thématique</option>
                            <?php foreach ($thematiques as $thematique): ?>
                            <option value="<?= $thematique['id'] ?>" 
                                    <?= (isset($idee['thematique_id']) && $idee['thematique_id'] == $thematique['id']) ? 'selected' : '' ?>>
                                <?= e($thematique['nom']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">
                            Description détaillée <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="description" name="description" rows="6" 
                                  placeholder="Décrivez votre idée en détail : problème identifié, solution proposée, bénéfices attendus..." required><?= isset($idee['description']) ? e($idee['description']) : '' ?></textarea>
                        <div class="form-text">
                            Expliquez clairement votre idée, son contexte et ses avantages potentiels.
                        </div>
                    </div>
                    
                    <?php if ($_SESSION['user_role'] === 'admin' && $action === 'edit'): ?>
                    <div class="mb-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select class="form-select" id="statut" name="statut">
                            <?php foreach (STATUTS_IDEES as $key => $label): ?>
                            <option value="<?= $key ?>" 
                                    <?= (isset($idee['statut']) && $idee['statut'] === $key) ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between">
                        <a href="<?= BASE_URL ?>/idees" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            <?= $action === 'create' ? 'Soumettre l\'idée' : 'Enregistrer les modifications' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
