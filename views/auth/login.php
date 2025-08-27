<?php
/**
 * Vue de connexion
 * views/auth/login.php
 */
?>

<div class="auth-header">
    <i class="fas fa-lightbulb fa-2x mb-3"></i>
    <h4>Connexion</h4>
    <p class="mb-0">Accédez à votre espace idées</p>
</div>

<div class="auth-body">
    <?php if (isset($errors) && !empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
            <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['timeout'])): ?>
    <div class="alert alert-warning">
        Votre session a expiré. Veuillez vous reconnecter.
    </div>
    <?php endif; ?>
    
    <form method="POST" action="<?= BASE_URL ?>/auth/authenticate">
        <div class="mb-3">
            <label for="email" class="form-label">
                <i class="fas fa-envelope me-1"></i>
                Adresse email
            </label>
            <input type="email" 
                   class="form-control" 
                   id="email" 
                   name="email" 
                   value="<?= isset($email) ? e($email) : '' ?>" 
                   required 
                   autocomplete="email"
                   placeholder="votre@email.com">
        </div>
        
        <div class="mb-3">
            <label for="password" class="form-label">
                <i class="fas fa-lock me-1"></i>
                Mot de passe
            </label>
            <input type="password" 
                   class="form-control" 
                   id="password" 
                   name="password" 
                   required 
                   autocomplete="current-password"
                   placeholder="••••••••">
        </div>
        
        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-sign-in-alt me-2"></i>
                Se connecter
            </button>
        </div>
    </form>
    
    <hr class="my-4">
    
    <div class="text-center">
        <small class="text-muted">
            <strong>Comptes de test :</strong><br>
            Admin: admin@entreprise.com<br>
            Salarié: jean.dupont@entreprise.com<br>
            Évaluateur: marie.martin@entreprise.com<br>
            <em>Mot de passe: password</em>
        </small>
    </div>
</div>

<script>
// Auto-focus sur le premier champ
document.getElementById('email').focus();
</script>