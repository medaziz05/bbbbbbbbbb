<?php
/**
 * Vue formulaire d'utilisateur (création/modification)
 * views/utilisateurs/form.php
 */
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    <?= $action === 'create' ? 'Nouvel utilisateur' : 'Modifier l\'utilisateur' ?>
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
                
                <form method="POST" action="<?= BASE_URL ?>/utilisateurs/<?= $action === 'create' ? 'store' : 'update/' . $utilisateur['id'] ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prenom" class="form-label">
                                    Prénom <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="prenom" name="prenom" 
                                       value="<?= isset($utilisateur['prenom']) ? e($utilisateur['prenom']) : '' ?>" 
                                       placeholder="Jean" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom" class="form-label">
                                    Nom <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="nom" name="nom" 
                                       value="<?= isset($utilisateur['nom']) ? e($utilisateur['nom']) : '' ?>" 
                                       placeholder="Dupont" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            Adresse email <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= isset($utilisateur['email']) ? e($utilisateur['email']) : '' ?>" 
                               placeholder="jean.dupont@entreprise.com" required>
                        <div class="form-text">
                            L'email servira d'identifiant de connexion.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">
                            Rôle <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Sélectionnez un rôle</option>
                            <?php foreach (ROLES as $key => $label): ?>
                            <option value="<?= $key ?>" 
                                    <?= (isset($utilisateur['role']) && $utilisateur['role'] === $key) ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">
                            <strong>Admin:</strong> Accès complet au système<br>
                            <strong>Salarié:</strong> Peut proposer des idées<br>
                            <strong>Évaluateur:</strong> Peut évaluer les idées soumises
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mot_de_passe" class="form-label">
                            Mot de passe <?= $action === 'create' ? '<span class="text-danger">*</span>' : '' ?>
                        </label>
                        <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" 
                               placeholder="••••••••" <?= $action === 'create' ? 'required' : '' ?>>
                        <div class="form-text">
                            <?= $action === 'create' ? 'Minimum 6 caractères.' : 'Laissez vide pour conserver le mot de passe actuel.' ?>
                        </div>
                    </div>
                    
                    <?php if ($action === 'create'): ?>
                    <div class="mb-3">
                        <label for="mot_de_passe_confirm" class="form-label">
                            Confirmer le mot de passe <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control" id="mot_de_passe_confirm" name="mot_de_passe_confirm" 
                               placeholder="••••••••" required>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between">
                        <a href="<?= BASE_URL ?>/utilisateurs" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            <?= $action === 'create' ? 'Créer l\'utilisateur' : 'Enregistrer les modifications' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Validation côté client pour la confirmation du mot de passe
<?php if ($action === 'create'): ?>
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('mot_de_passe');
    const confirmPassword = document.getElementById('mot_de_passe_confirm');
    
    function validatePassword() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Les mots de passe ne correspondent pas');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    password.addEventListener('input', validatePassword);
    confirmPassword.addEventListener('input', validatePassword);
});
<?php endif; ?>
</script>