<?php
/**
 * Vue liste des thématiques (Admin seulement) - CORRIGÉE
 * views/admin/thematiques.php
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="fas fa-tags me-2"></i>Gestion des thématiques
        </h1>
        <p class="text-muted mb-0">Organiser les idées par domaines</p>
    </div>
    <a href="<?= BASE_URL ?>/thematiques/create" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nouvelle thématique
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Liste des thématiques</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($thematiques)): ?>
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Statut</th>
                        <th>Nb. idées</th>
                        <th>Date création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($thematiques as $thematique): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= e($thematique['nom']) ?></div>
                        </td>
                        <td>
                            <span class="text-muted"><?= truncate($thematique['description'], 60) ?></span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $thematique['actif'] ? 'success' : 'secondary' ?>">
                                <?= $thematique['actif'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-info"><?= $thematique['nombre_idees'] ?? 0 ?></span>
                        </td>
                        <td><?= formatDate($thematique['date_creation']) ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= BASE_URL ?>/thematiques/edit/<?= $thematique['id'] ?>" 
                                   class="btn btn-outline-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <!-- Bouton de suppression activé pour toutes les thématiques -->
                                <button onclick="confirmDelete('<?= BASE_URL ?>/thematiques/delete/<?= $thematique['id'] ?>', '<?= ($thematique['nombre_idees'] ?? 0) > 0 ? 'Cette thématique contient ' . $thematique['nombre_idees'] . ' idée(s). Êtes-vous sûr de vouloir la supprimer ?' : 'Êtes-vous sûr de vouloir supprimer cette thématique ?' ?>')" 
                                        class="btn btn-outline-danger" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center text-muted py-5">
            <i class="fas fa-tags fa-4x mb-3 opacity-25"></i>
            <h5>Aucune thématique trouvée</h5>
            <p>Commencez par créer des thématiques pour organiser les idées.</p>
            <a href="<?= BASE_URL ?>/thematiques/create" class="btn btn-primary mt-3">
                <i class="fas fa-plus me-2"></i>Créer une thématique
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>