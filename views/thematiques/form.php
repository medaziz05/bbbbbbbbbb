<?php
/**
 * Vue formulaire de thématique (création/modification)
 * views/thematiques/form.php
 */
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-tags me-2"></i>
                    <?= $action === 'create' ? 'Nouvelle thématique' : 'Modifier la thématique' ?>
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
                
                <form method="POST" action="<?= BASE_URL ?>/thematiques/<?= $action === 'create' ? 'store' : 'update/' . $thematique['id'] ?>">
                    <div class="mb-3">
                        <label for="nom" class="form-label">
                            Nom de la thématique <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="nom" name="nom" 
                               value="<?= isset($thematique['nom']) ? e($thematique['nom']) : '' ?>" 
                               placeholder="Ex: Innovation Technologique" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">
                            Description <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="description" name="description" rows="4" 
                                  placeholder="Décrivez le domaine d'application de cette thématique..." required><?= isset($thematique['description']) ? e($thematique['description']) : '' ?></textarea>
                        <div class="form-text">
                            Cette description aidera les utilisateurs à comprendre le type d'idées attendues.
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="<?= BASE_URL ?>/thematiques" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            <?= $action === 'create' ? 'Créer la thématique' : 'Enregistrer les modifications' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>