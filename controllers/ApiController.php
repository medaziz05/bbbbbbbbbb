<?php
/**
 * Contrôleur API pour les requêtes AJAX
 * controllers/ApiController.php
 */
class ApiController extends BaseController {
    
    public function __construct() {
        if (!isLoggedIn()) {
            $this->jsonResponse(['error' => 'Non authentifié'], 401);
        }
        
        // Vérifier que la requête est en AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            $this->jsonResponse(['error' => 'Requête invalide'], 400);
        }
    }
    
    // Obtenir les statistiques pour le dashboard
    public function stats() {
        $userRole = $_SESSION['user_role'];
        
        if ($userRole !== 'admin') {
            $this->jsonResponse(['error' => 'Accès non autorisé'], 403);
        }
        
        try {
            $ideeModel = new Idee();
            $utilisateurModel = new Utilisateur();
            $thematiqueModel = new Thematique();
            
            $stats = [
                'idees' => $ideeModel->getStats(),
                'utilisateurs' => [
                    'total' => $utilisateurModel->count('actif = 1'),
                    'admins' => $utilisateurModel->count('role = "admin" AND actif = 1'),
                    'salaries' => $utilisateurModel->count('role = "salarie" AND actif = 1'),
                    'evaluateurs' => $utilisateurModel->count('role = "evaluateur" AND actif = 1')
                ],
                'thematiques' => $thematiqueModel->count('actif = 1')
            ];
            
            $this->jsonResponse($stats);
        } catch (Exception $e) {
            error_log("Erreur dans stats API: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Erreur lors de la récupération des statistiques'], 500);
        }
    }
    
    // Changer le statut d'une idée
    public function changeStatut() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Méthode non autorisée'], 405);
        }
        
        if (!hasRole('admin')) {
            $this->jsonResponse(['error' => 'Accès non autorisé'], 403);
        }
        
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $ideeId = intval($data['idee_id'] ?? 0);
            $statut = sanitize($data['statut'] ?? '');
            
            if (!$ideeId || !array_key_exists($statut, STATUTS_IDEES)) {
                $this->jsonResponse(['error' => 'Données invalides'], 400);
            }
            
            $ideeModel = new Idee();
            $idee = $ideeModel->findById($ideeId);
            
            if (!$idee) {
                $this->jsonResponse(['error' => 'Idée introuvable'], 404);
            }
            
            if ($ideeModel->updateStatut($ideeId, $statut)) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Statut mis à jour avec succès',
                    'statut' => $statut,
                    'statut_label' => STATUTS_IDEES[$statut]
                ]);
            } else {
                $this->jsonResponse(['error' => 'Erreur lors de la mise à jour'], 500);
            }
        } catch (Exception $e) {
            error_log("Erreur dans changeStatut API: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Erreur lors de la mise à jour du statut'], 500);
        }
    }
    
    // Rechercher des idées
    public function searchIdees() {
        try {
            $query = sanitize($_GET['q'] ?? '');
            $thematique = intval($_GET['thematique'] ?? 0);
            $statut = sanitize($_GET['statut'] ?? '');
            
            $ideeModel = new Idee();
            $conditions = [];
            $params = [];
            
            if (!empty($query)) {
                $conditions[] = "(i.titre LIKE :query OR i.description LIKE :query)";
                $params['query'] = "%$query%";
            }
            
            if ($thematique > 0) {
                $conditions[] = "i.thematique_id = :thematique";
                $params['thematique'] = $thematique;
            }
            
            if (!empty($statut) && array_key_exists($statut, STATUTS_IDEES)) {
                $conditions[] = "i.statut = :statut";
                $params['statut'] = $statut;
            }
            
            // Filtrer selon le rôle
            $userRole = $_SESSION['user_role'];
            $userId = $_SESSION['user_id'];
            
            if ($userRole === 'salarie') {
                $conditions[] = "i.utilisateur_id = :user_id";
                $params['user_id'] = $userId;
            }
            
            $whereClause = !empty($conditions) ? implode(' AND ', $conditions) : '';
            $idees = $ideeModel->findAllWithJoins($whereClause, $params);
            
            $this->jsonResponse([
                'success' => true,
                'idees' => $idees ?: [],
                'count' => count($idees ?: [])
            ]);
        } catch (Exception $e) {
            error_log("Erreur dans searchIdees API: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Erreur lors de la recherche'], 500);
        }
    }
    
    // Valider un email en temps réel
    public function validateEmail() {
        try {
            $email = sanitize($_GET['email'] ?? '');
            $excludeId = intval($_GET['exclude_id'] ?? 0);
            
            if (empty($email)) {
                $this->jsonResponse(['valid' => false, 'message' => 'Email requis']);
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->jsonResponse(['valid' => false, 'message' => 'Format d\'email invalide']);
            }
            
            $utilisateurModel = new Utilisateur();
            $exists = $utilisateurModel->emailExists($email, $excludeId ?: null);
            
            if ($exists) {
                $this->jsonResponse(['valid' => false, 'message' => 'Cet email est déjà utilisé']);
            }
            
            $this->jsonResponse(['valid' => true, 'message' => 'Email disponible']);
        } catch (Exception $e) {
            error_log("Erreur dans validateEmail API: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Erreur lors de la validation de l\'email'], 500);
        }
    }
    
    // Obtenir les données d'une idée pour modal/popup
    public function getIdee() {
        try {
            $ideeId = intval($_GET['id'] ?? 0);
            
            if (!$ideeId) {
                $this->jsonResponse(['error' => 'ID manquant'], 400);
            }
            
            $ideeModel = new Idee();
            $idee = $ideeModel->findByIdWithJoins($ideeId);
            
            if (!$idee) {
                $this->jsonResponse(['error' => 'Idée introuvable'], 404);
            }
            
            // Vérifier les permissions
            $userRole = $_SESSION['user_role'];
            $userId = $_SESSION['user_id'];
            
            if ($userRole === 'salarie' && $idee['utilisateur_id'] != $userId) {
                $this->jsonResponse(['error' => 'Accès non autorisé'], 403);
            }
            
            $this->jsonResponse([
                'success' => true,
                'idee' => $idee
            ]);
        } catch (Exception $e) {
            error_log("Erreur dans getIdee API: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Erreur lors de la récupération de l\'idée'], 500);
        }
    }
    
    // Obtenir les évaluations d'une idée
    public function getEvaluations() {
        try {
            $ideeId = intval($_GET['idee_id'] ?? 0);
            
            if (!$ideeId) {
                $this->jsonResponse(['error' => 'ID d\'idée manquant'], 400);
            }
            
            $evaluationModel = new Evaluation();
            $evaluations = $evaluationModel->findByIdee($ideeId);
            
            $this->jsonResponse([
                'success' => true,
                'evaluations' => $evaluations ?: [],
                'count' => count($evaluations ?: [])
            ]);
        } catch (Exception $e) {
            error_log("Erreur dans getEvaluations API: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Erreur lors de la récupération des évaluations'], 500);
        }
    }
}
?>