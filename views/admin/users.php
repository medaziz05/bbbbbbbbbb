<?php
/**
 * Vue liste des utilisateurs (Admin seulement)
 * views/utilisateurs/index.php
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="fas fa-users me-2"></i>Gestion des utilisateurs
        </h1>
        <p class="text-muted mb-0">Gérer les comptes utilisateurs du système</p>
    </div>
    <a href="<?= BASE_URL ?>/utilisateurs/create" class="btn btn-primary">
        <i class="fas fa-user-plus me-2"></i>Nouvel utilisateur
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Liste des utilisateurs</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($utilisateurs)): ?>
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Date création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateurs as $utilisateur): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= e($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?></div>
                        </td>
                        <td><?= e($utilisateur['email']) ?></td>
                        <td>
                            <span class="badge bg-<?= $utilisateur['role'] === 'admin' ? 'danger' : ($utilisateur['role'] === 'evaluateur' ? 'warning' : 'info') ?>">
                                <?= getRoleLabel($utilisateur['role']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $utilisateur['actif'] ? 'success' : 'secondary' ?>">
                                <?= $utilisateur['actif'] ? 'Actif' : 'Inactif' ?>
                            </span>
                        </td>
                        <td><?= formatDate($utilisateur['date_creation']) ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= BASE_URL ?>/utilisateurs/edit/<?= $utilisateur['id'] ?>" 
                                   class="btn btn-outline-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($utilisateur['id'] != $_SESSION['user_id']): ?>
                                <button onclick="confirmDelete('<?= BASE_URL ?>/utilisateurs/delete/<?= $utilisateur['id'] ?>', 'Êtes-vous sûr de vouloir supprimer cet utilisateur ?')" 
                                        class="btn btn-outline-danger" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center text-muted py-5">
            <i class="fas fa-users fa-4x mb-3 opacity-25"></i>
            <h5>Aucun utilisateur trouvé</h5>
            <p>Commencez par créer des comptes utilisateurs.</p>
            <a href="<?= BASE_URL ?>/utilisateurs/create" class="btn btn-primary mt-3">
                <i class="fas fa-user-plus me-2"></i>Créer un utilisateur
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>