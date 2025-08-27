<?php
/**
 * Fonctions utilitaires supplémentaires
 * utils/functions.php
 */

/**
 * Générer un token CSRF
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifier un token CSRF
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Pagination simple
 */
function paginate($currentPage, $totalItems, $itemsPerPage, $baseUrl) {
    $totalPages = ceil($totalItems / $itemsPerPage);
    $pagination = [];
    
    if ($totalPages <= 1) {
        return [];
    }
    
    // Page précédente
    if ($currentPage > 1) {
        $pagination[] = [
            'url' => $baseUrl . '?page=' . ($currentPage - 1),
            'text' => '&laquo; Précédent',
            'active' => false
        ];
    }
    
    // Pages numérotées
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        $pagination[] = [
            'url' => $baseUrl . '?page=' . $i,
            'text' => $i,
            'active' => $i === $currentPage
        ];
    }
    
    // Page suivante
    if ($currentPage < $totalPages) {
        $pagination[] = [
            'url' => $baseUrl . '?page=' . ($currentPage + 1),
            'text' => 'Suivant &raquo;',
            'active' => false
        ];
    }
    
    return $pagination;
}

/**
 * Valider les champs requis
 */
function validateRequired($fields, $data = null) {
    $errors = [];
    $source = $data ?? $_POST;
    
    foreach ($fields as $field => $label) {
        if (empty($source[$field])) {
            $errors[] = "Le champ '$label' est requis.";
        }
    }
    
    return $errors;
}

/**
 * Nettoyer un nom de fichier
 */
function sanitizeFilename($filename) {
    // Remplacer les caractères dangereux
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    // Éviter les noms de fichiers vides
    if (empty($filename)) {
        $filename = 'file_' . time();
    }
    return $filename;
}

/**
 * Générer un mot de passe aléatoire
 */
function generateRandomPassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    return substr(str_shuffle(str_repeat($chars, ceil($length / strlen($chars)))), 0, $length);
}

/**
 * Valider une adresse email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Créer un slug à partir d'une chaîne
 */
function createSlug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

/**
 * Formater une taille de fichier
 */
function formatFileSize($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Obtenir l'adresse IP du client
 */
function getClientIP() {
    $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
    
    foreach ($ipKeys as $key) {
        if (array_key_exists($key, $_SERVER) && !empty($_SERVER[$key])) {
            $ips = explode(',', $_SERVER[$key]);
            return trim($ips[0]);
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Logger simple
 */
function logMessage($message, $level = 'INFO', $file = null) {
    if (!$file) {
        $file = BASE_PATH . '/logs/app.log';
    }
    
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = getClientIP();
    $logEntry = "[$timestamp] [$level] [$ip] $message" . PHP_EOL;
    
    file_put_contents($file, $logEntry, FILE_APPEND | LOCK_EX);
}