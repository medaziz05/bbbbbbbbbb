<?php
/**
 * Layout pour les utilisateurs (Front Office)
 * views/layouts/user.php
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? e($title) . ' - ' : '' ?><?= APP_NAME ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --sidebar-width: 280px;
        }
        
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
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
        
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        .top-navbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 2rem;
            display: flex;
            justify-content: between;
            align-items: center;
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
        }
        
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
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
            <small class="text-light opacity-75"><?= getRoleLabel($_SESSION['user_role']) ?></small>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        Tableau de bord
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/idees">
                        <i class="fas fa-lightbulb"></i>
                        <?= $_SESSION['user_role'] === 'salarie' ? 'Mes idées' : 'Idées' ?>
                    </a>
                </li>
                
                <?php if ($_SESSION['user_role'] === 'salarie'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/idees/create">
                        <i class="fas fa-plus"></i>
                        Nouvelle idée
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if ($_SESSION['user_role'] === 'evaluateur'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/evaluations">
                        <i class="fas fa-star"></i>
                        Mes évaluations
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/evaluations/to-evaluate">
                        <i class="fas fa-clipboard-check"></i>
                        À évaluer
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        
        <!-- User Info -->
        <div class="mt-auto p-3 border-top border-light border-opacity-10">
            <div class="d-flex align-items-center">
                <div class="bg-white bg-opacity-20 rounded-circle p-2 me-2">
                    <i class="fas fa-user"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-semibold small"><?= e($_SESSION['user_name']) ?></div>
                    <small class="text-light opacity-75"><?= e($_SESSION['user_role']) ?></small>
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
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <div>
                <button class="btn btn-link d-md-none" type="button" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="mb-0"><?= isset($title) ? e($title) : 'Dashboard' ?></h5>
            </div>
            <div>
                <span class="text-muted small">
                    <i class="fas fa-clock me-1"></i>
                    <?= date('d/m/Y H:i') ?>
                </span>
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
                <?= e($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endforeach; ?>
            
            <?= $content ?>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }
        
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                if (alert.querySelector('.btn-close')) {
                    alert.querySelector('.btn-close').click();
                }
            });
        }, 5000);
    </script>
</body>
</html>
