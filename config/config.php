<?php
/**
 * Configuration principale de l'application
 * config/config.php
 */

// Définir le chemin de base
define('BASE_PATH', dirname(__DIR__));


// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'gestion_idees');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configuration générale
define('APP_NAME', 'Gestion d\'Idées');
define('BASE_URL', '/gestion_idees');
define('HASH_ALGO', PASSWORD_DEFAULT);

// Statuts des idées
define('STATUTS_IDEES', [
    'en_attente' => 'En attente',
    'en_evaluation' => 'En évaluation',
    'acceptee' => 'Acceptée',
    'refusee' => 'Refusée'
]);

// Rôles utilisateurs
define('ROLES', [
    'admin' => 'Administrateur',
    'salarie' => 'Salarié',
    'evaluateur' => 'Évaluateur'
]);

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoloader simple avec gestion d'erreurs
spl_autoload_register(function ($className) {
    $directories = [
       BASE_PATH . '/controllers',
        BASE_PATH . '/models',
        BASE_PATH . '/utils'
    ];
    foreach ($directories as $directory) {
        $file = $directory . '/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    error_log("Classe non trouvée: $className. Recherche dans: " . implode(', ', $directories));
});

// Inclure les utilitaires et helpers
require_once BASE_PATH . '/utils/functions.php';

/**
 * Fonctions helper essentielles (définies ici pour être toujours disponibles)
 */

/**
 * Échapper les données pour l'affichage HTML
 */
function e($data) {
    if (is_null($data)) {
        return '';
    }
    return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
}

/**
 * Nettoyer les données d'entrée
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirection
 */
function redirect($url) {
    if (strpos($url, 'http') !== 0) {
        $url = BASE_URL . $url;
    }
    header('Location: ' . $url);
    exit;
}

/**
 * Vérifier si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Vérifier si l'utilisateur a un rôle spécifique
 */
function hasRole($role) {
    return isLoggedIn() && $_SESSION['user_role'] === $role;
}

/**
 * Définir un message flash
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

/**
 * Récupérer et supprimer les messages flash
 */
function getFlashMessage() {
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

/**
 * Formater une date
 */
function formatDate($date, $format = 'd/m/Y H:i') {
    if (empty($date)) return '-';
    return date($format, strtotime($date));
}

/**
 * Formater une note - VERSION CORRIGÉE
 */
function formatNote($note) {
    if ($note === null || $note === '') return '-';
    
    // DEBUG: Ajouter un log pour voir la valeur reçue
    error_log("formatNote - Valeur reçue: " . var_export($note, true) . " (type: " . gettype($note) . ")");
    
    // S'assurer que c'est un nombre
    $noteFloat = floatval($note);
    error_log("formatNote - Valeur convertie: $noteFloat");
    
    // CORRECTION: Ne pas arrondir, juste formater avec 1 décimale
    return number_format($noteFloat, 1, '.', '') . '/20';
}

/**
 * Obtenir le label d'un statut
 */
function getStatutLabel($statut) {
    return STATUTS_IDEES[$statut] ?? $statut;
}

/**
 * Obtenir la classe CSS pour un statut
 */
function getStatutClass($statut) {
    $classes = [
        'en_attente' => 'warning',
        'en_evaluation' => 'info',
        'acceptee' => 'success',
        'refusee' => 'danger'
    ];
    
    return $classes[$statut] ?? 'secondary';
}

/**
 * Obtenir le label d'un rôle
 */
function getRoleLabel($role) {
    return ROLES[$role] ?? $role;
}

/**
 * Tronquer un texte
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $suffix;
}

// Test de la configuration au chargement
try {
    // Test simple de la base de données
    if (class_exists('Database')) {
        $db = Database::getInstance();
        if (!$db->testConnection()) {
            error_log("Attention: Problème de connexion à la base de données détecté");
        }
    }
} catch (Exception $e) {
    error_log("Erreur lors de l'initialisation: " . $e->getMessage());
}
?>