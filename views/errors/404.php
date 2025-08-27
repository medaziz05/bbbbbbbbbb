<?php
/**
 * Page d'erreur 404
 * views/errors/404.php
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page non trouvée - <?= APP_NAME ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .error-container {
            text-align: center;
            color: white;
            padding: 2rem;
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: 300;
            margin: 0;
            text-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }
        
        .error-message {
            font-size: 1.5rem;
            margin: 1rem 0;
            opacity: 0.9;
        }
        
        .error-description {
            font-size: 1rem;
            margin: 2rem 0;
            opacity: 0.8;
        }
        
        .btn-home {
            background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.3);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-home:hover {
            background: rgba(255,255,255,0.3);
            color: white;
            transform: translateY(-2px);
        }
        
        .floating-icon {
            font-size: 3rem;
            margin-bottom: 2rem;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="floating-icon">
            <i class="fas fa-search"></i>
        </div>
        <h1 class="error-code">404</h1>
        <p class="error-message">Page non trouvée</p>
        <p class="error-description">
            La page que vous recherchez n'existe pas ou a été déplacée.
        </p>
        <a href="<?= BASE_URL ?>/dashboard" class="btn-home">
            <i class="fas fa-home me-2"></i>
            Retourner à l'accueil
        </a>
    </div>
</body>
</html>