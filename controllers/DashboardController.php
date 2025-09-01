<?php
/**
 * Contrôleur du tableau de bord CORRIGÉ
 * controllers/DashboardController.php
 */

class DashboardController extends BaseController {
    private $ideeModel;
    private $utilisateurModel;
    private $thematiqueModel;
    private $evaluationModel;
    
    public function __construct() {
        if (!isLoggedIn()) {
            redirect('/auth/login');
        }
        
        $this->ideeModel = new Idee();
        $this->utilisateurModel = new Utilisateur();
        $this->thematiqueModel = new Thematique();
        $this->evaluationModel = new Evaluation();
    }
    
    // Tableau de bord principal
    public function index() {
        $data = [
            'title' => 'Tableau de bord'
        ];

        $userRole = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'];

        switch ($userRole) {
            case 'admin':
                $data = array_merge($data, $this->getAdminStats());
                $layout = 'admin_layout';
                break;
            case 'salarie':
                $data = array_merge($data, $this->getSalarieStats($userId));
                $layout = 'user_layout';
                break;
            case 'evaluateur':
                $data = array_merge($data, $this->getEvaluateurStats($userId));
                $layout = 'user_layout';
                break;
            default:
                $layout = 'user_layout';
        }

        $this->loadView('admin/dashboard', $data, $layout);
    }
    
    private function getAdminStats() {
        try {
            $ideeStats = $this->ideeModel->getStats();
            $totalUsers = $this->utilisateurModel->count('actif = 1');
            $totalThematiques = $this->thematiqueModel->count('actif = 1');
            $recentIdees = $this->ideeModel->findAllWithJoins('', [], 5);
            
            return [
                'stats' => $ideeStats,
                'totalUsers' => $totalUsers,
                'totalThematiques' => $totalThematiques,
                'recentIdees' => $recentIdees
            ];
        } catch (Exception $e) {
            error_log("Erreur dans getAdminStats: " . $e->getMessage());
            return [
                'stats' => ['total' => 0, 'acceptees' => 0, 'note_moyenne_globale' => 0],
                'totalUsers' => 0,
                'totalThematiques' => 0,
                'recentIdees' => []
            ];
        }
    }
    
    private function getSalarieStats($userId) {
        try {
            $mesIdees = $this->ideeModel->findByUser($userId);
            $meilleuresIdees = $this->ideeModel->findBestIdees(5);
            
            return [
                'mesIdees' => $mesIdees ?: [],
                'meilleuresIdees' => $meilleuresIdees ?: []
            ];
        } catch (Exception $e) {
            error_log("Erreur dans getSalarieStats: " . $e->getMessage());
            return [
                'mesIdees' => [],
                'meilleuresIdees' => []
            ];
        }
    }
    
    private function getEvaluateurStats($userId) {
    try {
        $mesEvaluations = $this->evaluationModel->findByEvaluateur($userId);
        
        // CORRECTION: Récupérer les idées en attente ET en évaluation
        $ideesEnAttente = $this->ideeModel->findByStatut('en_attente');
        $ideesEnEvaluation = $this->ideeModel->findByStatut('en_evaluation');
        $allIdees = array_merge($ideesEnAttente ?? [], $ideesEnEvaluation ?? []);
        
        // Filtrer les idées déjà évaluées par cet utilisateur
        $ideesAEvaluerFiltered = [];
        if (!empty($allIdees)) {
            foreach ($allIdees as $idee) {
                if (!$this->evaluationModel->hasEvaluated($idee['id'], $userId)) {
                    $ideesAEvaluerFiltered[] = $idee;
                }
            }
        }
        
        return [
            'mesEvaluations' => $mesEvaluations ?: [],
            'ideesAEvaluer' => $ideesAEvaluerFiltered
        ];
    } catch (Exception $e) {
        error_log("Erreur dans getEvaluateurStats: " . $e->getMessage());
        return [
            'mesEvaluations' => [],
            'ideesAEvaluer' => []
        ];
    }
}
}
?>