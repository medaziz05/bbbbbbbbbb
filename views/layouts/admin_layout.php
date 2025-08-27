<?php
/**
 * Layout pour les administrateurs (Back Office)
 * views/layouts/admin.php
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? e($title) . ' - ' : '' ?><?= APP_NAME ?> - Administration</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #dc2626;
            --primary-dark: #b91c1c;
            --sidebar-width: 280px;
        }
        
        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.1);
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            margin: 0.25rem 1rem;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white !important;
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
        }
        
        .nav-section {
            padding: 0.5rem 1.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: rgba(255,255,255,0.6);
            margin-top: 1rem;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        .top-navbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        
        .content-wrapper {
            padding: 2rem;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            border-radius: 12px 12px 0 0 !important;
        }
        
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .stats-card {
            border-left: 4px solid var(--primary-color);
        }
        
        .stats-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .badge {
            border-radius: 6px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4 class="mb-1"><?= APP_NAME ?></h4>
            <small class="text-light opacity-75">Administration</small>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        Tableau de bord
                    </a>
                </li>
                
                <div class="nav-section">Gestion</div>
                
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/utilisateurs">
                        <i class="fas fa-users"></i>
                        Utilisateurs
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/thematiques">
                        <i class="fas fa-tags"></i>
                        Thématiques
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/idees">
                        <i class="fas fa-lightbulb"></i>
                        Idées
                    </a>
                </li>
                
                <div class="nav-section">Rapports</div>
                
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/rapports/idees">
                        <i class="fas fa-chart-bar"></i>
                        Statistiques idées
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/rapports/evaluations">
                        <i class="fas fa-star"></i>
                        Évaluations
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- User Info -->
        <div class="mt-auto p-3 border-top border-light border-opacity-10">
            <div class="d-flex align-items-center">
                <div class="bg-white bg-opacity-20 rounded-circle p-2 me-2">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-semibold small"><?= e($_SESSION['user_name']) ?></div>
                    <small class="text-light opacity-75">Administrateur</small>
                </div>
                <a href="<?= BASE_URL ?>/auth/logout" class="text-light" title="Déconnexion">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-link d-md-none me-2" type="button" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h5 class="mb-0"><?= isset($title) ? e($title) : 'Administration' ?></h5>
                    <?php if (isset($breadcrumb)): ?>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 small">
                            <?php foreach ($breadcrumb as $item): ?>
                            <li class="breadcrumb-item">
                                <?php if (isset($item['url'])): ?>
                                <a href="<?= $item['url'] ?>"><?= e($item['text']) ?></a>
                                <?php else: ?>
                                <?= e($item['text']) ?>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ol>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <span class="text-muted small me-3">
                    <i class="fas fa-clock me-1"></i>
                    <?= date('d/m/Y H:i') ?>
                </span>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-cog"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/auth/logout"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Content -->
        <div class="content-wrapper">
            <?php
            // Afficher les messages flash
            $messages = getFlashMessage();
            foreach ($messages as $type => $message):
                $alertClass = $type === 'error' ? 'danger' : $type;
            ?>
            <div class="alert alert-<?= $alertClass ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?= $type === 'success' ? 'check-circle' : ($type === 'error' ? 'exclamation-triangle' : 'info-circle') ?> me-2"></i>
                <?= e($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endforeach; ?>
            
            <?= $content ?>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }
        
        // Initialize DataTables
        $(document).ready(function() {
            $('.datatable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                },
                pageLength: 25,
                responsive: true
            });
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                if (alert.querySelector('.btn-close')) {
                    alert.querySelector('.btn-close').click();
                }
            });
        }, 5000);
        
        // Confirm delete actions
        function confirmDelete(url, message = 'Êtes-vous sûr de vouloir supprimer cet élément ?') {
            if (confirm(message)) {
                window.location.href = url;
            }
        }
    </script>
    
    <?php if (isset($extraJS)): ?>
    <?= $extraJS ?>
    <?php endif; ?>
</body>
</html>