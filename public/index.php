<?php
/**
 * Fichier principal - index.php
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
}

// Analyser la route
$segments = explode('/', $path);
$controller = $segments[0] ?? 'dashboard';
$action = $segments[1] ?? 'index';
$param = $segments[2] ?? null;

// Routes spéciales
$routes = [
    // Authentification
    'auth/login' => ['AuthController', 'login'],
    'auth/authenticate' => ['AuthController', 'authenticate'],
    'auth/logout' => ['AuthController', 'logout'],
    
    // Dashboard
    'dashboard' => ['DashboardController', 'index'],
    
    // Utilisateurs (Admin seulement)
    'utilisateurs' => ['UtilisateurController', 'index'],
    'utilisateurs/create' => ['UtilisateurController', 'create'],
    'utilisateurs/store' => ['UtilisateurController', 'store'],
    
    // Thématiques (Admin seulement)
    'thematiques' => ['ThematiqueController', 'index'],
    'thematiques/create' => ['ThematiqueController', 'create'],
    'thematiques/store' => ['ThematiqueController', 'store'],
    
    // Idées
    'idees' => ['IdeeController', 'index'],
    'idees/create' => ['IdeeController', 'create'],
    'idees/store' => ['IdeeController', 'store'],
    
    // Évaluations
    'evaluations' => ['EvaluationController', 'index'],
    'evaluations/to-evaluate' => ['EvaluationController', 'toEvaluate'],
    'evaluations/store' => ['EvaluationController', 'store'],
    
    // API
    'api/stats' => ['ApiController', 'stats'],
    'api/change-statut' => ['ApiController', 'changeStatut'],
    'api/search-idees' => ['ApiController', 'searchIdees'],
    'api/validate-email' => ['ApiController', 'validateEmail']
];

// Construire la route complète
$fullRoute = $controller;
if ($action !== 'index') {
    $fullRoute .= '/' . $action;
}

try {
    // Vérifier si la route existe dans les routes spéciales
    if (isset($routes[$fullRoute])) {
        $controllerClass = $routes[$fullRoute][0];
        $method = $routes[$fullRoute][1];
      
        $controllerInstance = new $controllerClass();
        
        if ($param !== null) {
            $controllerInstance->$method($param);
        } else {
            $controllerInstance->$method();
        }
    }
    // Routes avec paramètres dynamiques
    elseif (isset($routes[$controller])) {
        $controllerClass = $routes[$controller][0];
        $method = $routes[$controller][1];
        
        $controllerInstance = new $controllerClass();
        
        // Méthodes avec ID (edit, update, delete, show)
        if (in_array($action, ['edit', 'update', 'delete', 'show']) && $param !== null) {
            $controllerInstance->$action($param);
        } elseif (in_array($action, ['edit', 'update', 'delete', 'show']) && $action !== 'index') {
            // ID dans $action pour les routes courtes
            $id = $action;
            $controllerInstance->show($id);
        } else {
            $controllerInstance->$method();
        }
    }
    // Routes RESTful dynamiques
    else {
        $controllerClass = ucfirst($controller) . 'Controller';
        
        if (class_exists($controllerClass)) {
            $controllerInstance = new $controllerClass();
            
            switch ($action) {
                case 'edit':
                case 'update':
                case 'delete':
                case 'show':
                    if ($param !== null) {
                        $controllerInstance->$action($param);
                    } else {
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
                        $controllerInstance->show($action);
                    } else {
                        throw new Exception("Action '$action' non trouvée");
                    }
            }
        } else {
            throw new Exception("Contrôleur '$controllerClass' non trouvé");
        }
    }
    
} catch (Exception $e) {
    // Gestion des erreurs
    http_response_code(404);
    
    if (isLoggedIn()) {
        require_once BASE_PATH . '/views/errors/404.php';
    } else {
        redirect('/auth/login');
    }
}