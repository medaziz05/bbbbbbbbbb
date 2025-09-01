<?php
/**
 * Fichier principal - index.php - VERSION CORRIGÉE POUR ROUTING COMPLET
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/config.php';

// Récupérer l'URL demandée
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Supprimer le préfixe BASE_URL s'il existe
if (strpos($path, BASE_URL) === 0) {
    $path = substr($path, strlen(BASE_URL));
}

// Supprimer les slashes en début et fin
$path = trim($path, '/');

// Si pas de chemin, rediriger vers le dashboard ou login
if (empty($path)) {
    if (isLoggedIn()) {
        redirect('/dashboard');
    } else {
        redirect('/auth/login');
    }
    exit;
}

// Analyser la route
$segments = explode('/', $path);
$controller = $segments[0] ?? 'dashboard';
$action = $segments[1] ?? 'index';
$param = $segments[2] ?? null;

// Log pour debug
error_log("ROUTING DEBUG - Path: $path, Controller: $controller, Action: $action, Param: " . ($param ?? 'null'));

try {
    // GESTION SPÉCIALE DES ROUTES D'ÉVALUATION
    if ($controller === 'evaluations') {
        error_log("ROUTING - Gestion route évaluations: $action");
        
        $evalController = new EvaluationController();
        
        switch ($action) {
            case 'index':
                $evalController->index();
                exit;
                
            case 'to-evaluate':
                $evalController->toEvaluate();
                exit;
                
            case 'create':
                if ($param && is_numeric($param)) {
                    $evalController->create($param);
                } else {
                    error_log("ROUTING ERROR - ID manquant pour create: $param");
                    setFlashMessage('error', 'ID d\'idée requis pour créer une évaluation');
                    redirect('/evaluations/to-evaluate');
                }
                exit;
                
            case 'store':
                $evalController->store();
                exit;
                
            case 'edit':
                if ($param && is_numeric($param)) {
                    $evalController->edit($param);
                } else {
                    error_log("ROUTING ERROR - ID manquant pour edit: $param");
                    setFlashMessage('error', 'ID d\'idée requis pour modifier une évaluation');
                    redirect('/evaluations');
                }
                exit;
                
            default:
                error_log("ROUTING ERROR - Action d'évaluation inconnue: $action");
                setFlashMessage('error', 'Page non trouvée');
                redirect('/evaluations');
        }
    }
    
    // Routes spéciales pour les autres contrôleurs - VERSION COMPLÈTE
    $routes = [
        // Authentification
        'auth/login' => ['AuthController', 'login'],
        'auth/authenticate' => ['AuthController', 'authenticate'],
        'auth/logout' => ['AuthController', 'logout'],
        
        // Dashboard
        'dashboard' => ['DashboardController', 'index'],
        
        // Utilisateurs (Admin seulement) - AJOUT DES ROUTES MANQUANTES
        'utilisateurs' => ['UtilisateurController', 'index'],
        'utilisateurs/create' => ['UtilisateurController', 'create'],
        'utilisateurs/store' => ['UtilisateurController', 'store'],
        
        // Thématiques (Admin seulement) - AJOUT DES ROUTES MANQUANTES
        'thematiques' => ['ThematiqueController', 'index'],
        'thematiques/create' => ['ThematiqueController', 'create'],
        'thematiques/store' => ['ThematiqueController', 'store'],
     
        // Idées
        'idees' => ['IdeeController', 'index'],
        'idees/create' => ['IdeeController', 'create'],
        'idees/store' => ['IdeeController', 'store'],
        
        // API
        'api/stats' => ['ApiController', 'stats'],
        'api/change-statut' => ['ApiController', 'changeStatut'],
        'api/search-idees' => ['ApiController', 'searchIdees'],
        'api/validate-email' => ['ApiController', 'validateEmail'],
        'api/get-idee' => ['ApiController', 'getIdee'],
        'api/get-evaluations' => ['ApiController', 'getEvaluations']
    ];
    
    // Construire la route complète
    $fullRoute = $controller;
    if ($action !== 'index') {
        $fullRoute .= '/' . $action;
    }
    
    error_log("ROUTING - Vérification route complète: $fullRoute");
    
    // Vérifier si la route existe dans les routes spéciales
    if (isset($routes[$fullRoute])) {
        error_log("ROUTING - Route spéciale trouvée: $fullRoute");
        $controllerClass = $routes[$fullRoute][0];
        $method = $routes[$fullRoute][1];
      
        $controllerInstance = new $controllerClass();
        
        if ($param !== null) {
            $controllerInstance->$method($param);
        } else {
            $controllerInstance->$method();
        }
    }
    // Routes avec paramètres dynamiques - CORRECTION MAJEURE
    else {
        error_log("ROUTING - Tentative route dynamique: $controller -> $action");
        $controllerClass = ucfirst($controller) . 'Controller';
        
        if (class_exists($controllerClass)) {
            error_log("ROUTING - Classe contrôleur trouvée: $controllerClass");
            $controllerInstance = new $controllerClass();
            
            // Vérifier que la méthode existe
            if (method_exists($controllerInstance, $action)) {
                switch ($action) {
                    case 'edit':
                    case 'update':
                    case 'delete':
                    case 'show':
                        if ($param !== null && is_numeric($param)) {
                            $controllerInstance->$action($param);
                        } else {
                            error_log("ROUTING ERROR - ID requis pour $action, param reçu: " . ($param ?? 'null'));
                            throw new Exception("ID requis pour l'action $action");
                        }
                        break;
                        
                    case 'create':
                    case 'store':
                    case 'index':
                        $controllerInstance->$action();
                        break;
                        
                    default:
                        // Peut-être que $action est un ID pour show
                        if (is_numeric($action)) {
                            if (method_exists($controllerInstance, 'show')) {
                                $controllerInstance->show($action);
                            } else {
                                throw new Exception("Méthode 'show' non trouvée dans $controllerClass");
                            }
                        } else {
                            error_log("ROUTING ERROR - Action inconnue: $action pour $controllerClass");
                            throw new Exception("Action '$action' non trouvée dans $controllerClass");
                        }
                }
            } else {
                error_log("ROUTING ERROR - Méthode '$action' non trouvée dans $controllerClass");
                // Si l'action n'existe pas, peut-être que c'est un ID pour show
                if (is_numeric($action) && method_exists($controllerInstance, 'show')) {
                    $controllerInstance->show($action);
                } else {
                    throw new Exception("Méthode '$action' non trouvée dans le contrôleur $controllerClass");
                }
            }
        } else {
            error_log("ROUTING ERROR - Contrôleur non trouvé: $controllerClass");
            throw new Exception("Contrôleur '$controllerClass' non trouvé");
        }
    }
    
} catch (Exception $e) {
    // Log de l'erreur pour debug
    error_log("Erreur de routing: " . $e->getMessage() . " - Path: $path - Controller: $controller - Action: $action");
    
    // Gestion des erreurs
    http_response_code(404);
    
    // Affichage simple de l'erreur pour debug
    if (isLoggedIn()) {
        echo '<div style="padding: 20px; background: #f8f9fa; border: 1px solid #dee2e6; margin: 20px; border-radius: 8px;">';
        echo '<h3 style="color: #dc3545;">Erreur de navigation</h3>';
        echo '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><strong>Chemin demandé:</strong> ' . htmlspecialchars($path) . '</p>';
        echo '<p><strong>Contrôleur:</strong> ' . htmlspecialchars($controller) . '</p>';
        echo '<p><strong>Action:</strong> ' . htmlspecialchars($action) . '</p>';
        echo '<p><strong>Paramètre:</strong> ' . htmlspecialchars($param ?? 'aucun') . '</p>';
        echo '<p><a href="' . BASE_URL . '/dashboard" style="color: #007bff;">← Retour au dashboard</a></p>';
        echo '</div>';
    } else {
        redirect('/auth/login');
    }
}
?>