<?php
/**
 * Contrôleur de base
 * controllers/BaseController.php
 */

abstract class BaseController {
    
    /**
     * Charger une vue avec layout
     */
    protected function loadView($view, $data = [], $layout = 'admin') {
        // Extraire les données pour les rendre disponibles dans les vues
        extract($data);
        
        // Commencer la capture de sortie
        ob_start();
        
        // Inclure la vue
        $viewFile = BASE_PATH . '/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new Exception("Vue non trouvée : $view");
        }
        
        // Récupérer le contenu
        $content = ob_get_clean();
        
        // Inclure le layout
        $layoutFile = BASE_PATH . '/views/layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            throw new Exception("Layout non trouvé : $layout");
        }
    }
    
    /**
     * Redirection avec message flash
     */
    protected function redirectWithMessage($url, $type, $message) {
        setFlashMessage($type, $message);
        redirect($url);
    }
    
    /**
     * Vérifier les permissions d'accès
     */
    protected function checkPermission($requiredRole = null) {
        if (!isLoggedIn()) {
            redirect('/auth/login');
        }
        
        if ($requiredRole && !hasRole($requiredRole)) {
            $this->redirectWithMessage('/dashboard', 'error', 'Accès non autorisé.');
        }
    }
    
    /**
     * Réponse JSON pour les API
     */
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Validation des champs requis
     */
    protected function validateRequired($fields, $data = null) {
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
     * Valider les données avec des règles personnalisées
     */
    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $fieldRules = is_array($fieldRules) ? $fieldRules : [$fieldRules];
            
            foreach ($fieldRules as $rule) {
                if (is_string($rule)) {
                    switch ($rule) {
                        case 'required':
                            if (empty($value)) {
                                $errors[] = "Le champ '$field' est requis.";
                            }
                            break;
                        case 'email':
                            if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                $errors[] = "Le champ '$field' doit être une adresse email valide.";
                            }
                            break;
                        case 'numeric':
                            if (!empty($value) && !is_numeric($value)) {
                                $errors[] = "Le champ '$field' doit être numérique.";
                            }
                            break;
                    }
                } elseif (is_array($rule)) {
                    $ruleName = $rule[0];
                    $ruleValue = $rule[1];
                    
                    switch ($ruleName) {
                        case 'min':
                            if (!empty($value) && strlen($value) < $ruleValue) {
                                $errors[] = "Le champ '$field' doit contenir au moins $ruleValue caractères.";
                            }
                            break;
                        case 'max':
                            if (!empty($value) && strlen($value) > $ruleValue) {
                                $errors[] = "Le champ '$field' ne peut pas dépasser $ruleValue caractères.";
                            }
                            break;
                        case 'in':
                            if (!empty($value) && !in_array($value, $ruleValue)) {
                                $errors[] = "La valeur du champ '$field' n'est pas valide.";
                            }
                            break;
                    }
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Paginer des résultats
     */
    protected function paginate($query, $page = 1, $perPage = 20) {
        $page = max(1, intval($page));
        $offset = ($page - 1) * $perPage;
        
        return [
            'data' => array_slice($query, $offset, $perPage),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => count($query),
                'total_pages' => ceil(count($query) / $perPage),
                'has_next' => $page < ceil(count($query) / $perPage),
                'has_prev' => $page > 1
            ]
        ];
    }
}