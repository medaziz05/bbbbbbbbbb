<?php
/**
 * Contrôleur d'authentification
 * controllers/AuthController.php
 */
class AuthController extends BaseController {
    private $utilisateurModel;

    public function __construct() {
        $this->utilisateurModel = new Utilisateur();
    }
    
    // Afficher le formulaire de connexion
    public function login() {
        // Rediriger si déjà connecté
        if (isLoggedIn()) {
            redirect('/dashboard');
        }
        
        $this->loadView('auth/login', [
            'title' => 'Connexion'
        ], 'guest');
    }
    
    // Traiter la connexion
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/auth/login');
        }
        
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $errors = [];
        
        // Validation
        if (empty($email)) {
            $errors[] = "L'email est requis.";
        }
        
        if (empty($password)) {
            $errors[] = "Le mot de passe est requis.";
        }
        
        if (!empty($errors)) {
            $this->loadView('auth/login', [
                'title' => 'Connexion',
                'errors' => $errors,
                'email' => $email
            ], 'guest');
            return;
        }
        
        // Tentative d'authentification
        try {
            $user = $this->utilisateurModel->authenticate($email, $password);
            
            if ($user) {
                // Connexion réussie
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['login_time'] = time();
                
                // Log de la connexion
                error_log("Connexion réussie pour: " . $user['email']);
                
                // Redirection selon le rôle
                switch ($user['role']) {
                    case 'admin':
                    case 'salarie':
                    case 'evaluateur':
                        redirect('/dashboard');
                        break;
                    default:
                        redirect('/dashboard');
                }
            } else {
                // Échec de la connexion
                error_log("Échec de connexion pour: " . $email);
                $this->loadView('auth/login', [
                    'title' => 'Connexion',
                    'errors' => ['Email ou mot de passe incorrect.'],
                    'email' => $email
                ], 'guest');
            }
        } catch (Exception $e) {
            error_log("Erreur lors de l'authentification: " . $e->getMessage());
            $this->loadView('auth/login', [
                'title' => 'Connexion',
                'errors' => ['Erreur lors de la connexion. Veuillez réessayer.'],
                'email' => $email
            ], 'guest');
        }
    }
    
    // Déconnexion
    public function logout() {
        // Log de la déconnexion
        if (isLoggedIn()) {
            error_log("Déconnexion de: " . $_SESSION['user_email']);
        }
        
        // Détruire la session
        session_destroy();
        
        // Rediriger vers la page de connexion
        redirect('/auth/login');
    }
}
?>