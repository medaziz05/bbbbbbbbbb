<?php
/**
 * Vue liste des idées
 * views/idees/index.php
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="fas fa-lightbulb me-2"></i>
            <?= $_SESSION['user_role'] === 'salarie' ? 'Mes idées' : 'Gestion des idées' ?>
        </h1>
        <p class="text-muted mb-0">
            <?= $_SESSION['user_role'] === 'salarie' ? 'Vos propositions d\'amélioration' : 'Toutes les idées soumises' ?>
        </p>
    </div>
    
    <?php if (isset($canCreate) && $canCreate): ?>
    <a href="<?= BASE_URL ?>/idees/create" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nouvelle idée
    </a>
    <?php endif; ?>
</div>

<!-- Filtres -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Rechercher</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Titre ou description..." value="<?= isset($_GET['search']) ? e($_GET['search']) : '' ?>">
            </div>
            <div class="col-md-3">
                <label for="statut" class="form-label">Statut</label>
                <select class="form-select" id="statut" name="statut">
                    <option value="">Tous les statuts</option>
                    <?php foreach (STATUTS_IDEES as $key => $label): ?>
                    <option value="<?= $key ?>" <?= (isset($_GET['statut']) && $_GET['statut'] === $key) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="thematique" class="form-label">Thématique</label>
                <select class="form-select" id="thematique" name="thematique">
                    <option value="">Toutes les thématiques</option>
                    <!-- Les thématiques seraient chargées ici -->
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Liste des idées</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($idees)): ?>
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <?php if ($_SESSION['user_role'] !== 'salarie'): ?>
                        <th>Auteur</th>
                        <?php endif; ?>
                        <th>Thématique</th>
                        <th>Statut</th>
                        <th>Note</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($idees as $idee): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= e($idee['titre']) ?></div>
                            <small class="text-muted"><?= truncate($idee['description'], 60) ?></small>
                        </td>
                        <?php if ($_SESSION['user_role'] !== 'salarie'): ?>
                        <td><?= e($idee['utilisateur_prenom'] . ' ' . $idee['utilisateur_nom']) ?></td>
                        <?php endif; ?>
                        <td>
                            <span class="badge bg-secondary"><?= e($idee['thematique_nom']) ?></span>
                        </td>
                        <td>
                            <span class="badge bg-<?= getStatutClass($idee['statut']) ?>">
                                <?= getStatutLabel($idee['statut']) ?>
                            </span>
                        </td>
                        <td><?= formatNote($idee['note_moyenne']) ?></td>
                        <td><?= formatDate($idee['date_creation']) ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= BASE_URL ?>/idees/<?= $idee['id'] ?>" class="btn btn-outline-primary" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($_SESSION['user_role'] === 'admin' || 
                                         ($_SESSION['user_role'] === 'salarie' && $idee['utilisateur_id'] == $_SESSION['user_id'])): ?>
                                <a href="<?= BASE_URL ?>/idees/edit/<?= $idee['id'] ?>" class="btn btn-outline-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="confirmDelete('<?= BASE_URL ?>/idees/delete/<?= $idee['id'] ?>')" 
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
            <i class="fas fa-lightbulb fa-4x mb-3 opacity-25"></i>
            <h5>Aucune idée trouvée</h5>
            <p>
                <?php if ($_SESSION['user_role'] === 'salarie'): ?>
                    Vous n'avez pas encore proposé d'idées.
                <?php else: ?>
                    Aucune idée n'a été soumise pour le moment.
                <?php endif; ?>
            </p>
            <?php if (isset($canCreate) && $canCreate): ?>
            <a href="<?= BASE_URL ?>/idees/create" class="btn btn-primary mt-3">
                <i class="fas fa-plus me-2"></i>Proposer une idée
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
