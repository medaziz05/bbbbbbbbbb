<?php
/**
 * Contrôleur des évaluations - VERSION AVEC DEBUG
 * controllers/EvaluationController.php
 */

class EvaluationController extends BaseController {
    private $evaluationModel;
    private $ideeModel;
    
    public function __construct() {
        // Debug: Log de l'utilisateur connecté
        error_log("EvaluationController - User role: " . ($_SESSION['user_role'] ?? 'non défini'));
        
        // Vérifier que l'utilisateur est connecté
        if (!isLoggedIn()) {
            error_log("EvaluationController - Utilisateur non connecté, redirection vers login");
            redirect('/auth/login');
            return;
        }
        
        // Vérifier le rôle évaluateur
        if ($_SESSION['user_role'] !== 'evaluateur') {
            error_log("EvaluationController - Rôle incorrect: " . $_SESSION['user_role']);
            setFlashMessage('error', 'Accès non autorisé. Vous devez être évaluateur.');
            redirect('/dashboard');
            return;
        }
        
        $this->evaluationModel = new Evaluation();
        $this->ideeModel = new Idee();
        
        error_log("EvaluationController - Initialisation réussie");
    }
    
    // Liste des évaluations
    public function index() {
        error_log("EvaluationController::index - Début");
        $userId = $_SESSION['user_id'];
        $evaluations = $this->evaluationModel->findByEvaluateur($userId);
        
        error_log("EvaluationController::index - Nombre d'évaluations: " . count($evaluations ?? []));
        
        $this->loadView('evaluations/index', [
            'title' => 'Mes évaluations',
            'evaluations' => $evaluations ?? []
        ], 'user_layout');
    }
    
    // Idées à évaluer
    public function toEvaluate() {
    error_log("EvaluationController::toEvaluate - Début");
    $userId = $_SESSION['user_id'];
    
    // CORRECTION: Récupérer les idées en attente ET en évaluation
    $ideesEnAttente = $this->ideeModel->findByStatut('en_attente');
    $ideesEnEvaluation = $this->ideeModel->findByStatut('en_evaluation');
    
    // Fusionner les deux tableaux
    $allIdees = array_merge($ideesEnAttente ?? [], $ideesEnEvaluation ?? []);
    
    error_log("EvaluationController::toEvaluate - Idées trouvées: " . count($allIdees));
    
    // Filtrer les idées non encore évaluées par cet évaluateur
    $ideesAEvaluer = [];
    if (!empty($allIdees)) {
        foreach ($allIdees as $idee) {
            if (!$this->evaluationModel->hasEvaluated($idee['id'], $userId)) {
                $ideesAEvaluer[] = $idee;
            }
        }
    }
    
    error_log("EvaluationController::toEvaluate - Idées à évaluer: " . count($ideesAEvaluer));
    
    $this->loadView('evaluations/to_evaluate', [
        'title' => 'Idées à évaluer',
        'idees' => $ideesAEvaluer
    ], 'user_layout');
}
    
    // Afficher le formulaire d'évaluation
    public function create($ideeId) {
        error_log("EvaluationController::create - ID: $ideeId");
        $userId = $_SESSION['user_id'];
        
        if (!$ideeId || !is_numeric($ideeId)) {
            error_log("EvaluationController::create - ID invalide: $ideeId");
            setFlashMessage('error', 'ID d\'idée invalide.');
            redirect('/evaluations/to-evaluate');
            return;
        }
        
        // Vérifier que l'idée existe
        $idee = $this->ideeModel->findByIdWithJoins($ideeId);
        if (!$idee) {
            error_log("EvaluationController::create - Idée non trouvée: $ideeId");
            setFlashMessage('error', 'Idée introuvable.');
            redirect('/evaluations/to-evaluate');
            return;
        }
        
        error_log("EvaluationController::create - Idée trouvée: " . $idee['titre']);
        
        // Vérifier que l'évaluateur n'a pas déjà évalué cette idée
        if ($this->evaluationModel->hasEvaluated($ideeId, $userId)) {
            error_log("EvaluationController::create - Déjà évaluée par user $userId");
            setFlashMessage('error', 'Vous avez déjà évalué cette idée.');
            redirect('/evaluations');
            return;
        }
        
        error_log("EvaluationController::create - Affichage du formulaire");
        
        $this->loadView('evaluations/form', [
            'title' => 'Évaluer l\'idée',
            'idee' => $idee,
            'evaluation' => null,
            'isEdit' => false
        ], 'user_layout');
    }
    
    // Traiter la soumission d'évaluation
    public function store() {
        error_log("EvaluationController::store - Début");
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("EvaluationController::store - Méthode non POST");
            redirect('/evaluations');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $ideeId = intval($_POST['idee_id'] ?? 0);
        
        error_log("EvaluationController::store - User: $userId, Idée: $ideeId");
        
        if (!$ideeId) {
            error_log("EvaluationController::store - ID idée manquant");
            setFlashMessage('error', 'ID de l\'idée manquant.');
            redirect('/evaluations/to-evaluate');
            return;
        }
        
        // Vérifier que l'idée existe
        $idee = $this->ideeModel->findByIdWithJoins($ideeId);
        if (!$idee) {
            error_log("EvaluationController::store - Idée non trouvée: $ideeId");
            setFlashMessage('error', 'Idée introuvable.');
            redirect('/evaluations/to-evaluate');
            return;
        }
        
        // Vérifier que l'évaluateur n'a pas déjà évalué cette idée
        if ($this->evaluationModel->hasEvaluated($ideeId, $userId)) {
            error_log("EvaluationController::store - Déjà évaluée");
            setFlashMessage('error', 'Vous avez déjà évalué cette idée.');
            redirect('/evaluations');
            return;
        }
        
        $data = [
            'idee_id' => $ideeId,
            'evaluateur_id' => $userId,
            'note' => floatval($_POST['note'] ?? 0),
            'commentaire' => sanitize($_POST['commentaire'] ?? '')
        ];
        
        error_log("EvaluationController::store - Note: " . $data['note']);
        
        $errors = $this->validateEvaluation($data);
        
        if (!empty($errors)) {
            error_log("EvaluationController::store - Erreurs de validation: " . implode(', ', $errors));
            $this->loadView('evaluations/form', [
                'title' => 'Évaluer l\'idée',
                'idee' => $idee,
                'evaluation' => $data,
                'errors' => $errors,
                'isEdit' => false
            ], 'user_layout');
            return;
        }
        
        if ($this->evaluationModel->createOrUpdate($data)) {
            error_log("EvaluationController::store - Évaluation créée avec succès");
            
            // Mettre à jour le statut de l'idée si nécessaire
            if ($idee['statut'] === 'en_attente') {
                $this->ideeModel->updateStatut($ideeId, 'en_evaluation');
                error_log("EvaluationController::store - Statut idée mis à jour");
            }
            
            setFlashMessage('success', 'Évaluation enregistrée avec succès.');
            redirect('/evaluations');
        } else {
            error_log("EvaluationController::store - Erreur lors de la création");
            setFlashMessage('error', 'Erreur lors de l\'évaluation.');
            redirect('/evaluations/to-evaluate');
        }
    }
    
    // Modifier une évaluation existante
    public function edit($ideeId) {
        error_log("EvaluationController::edit - ID: $ideeId");
        $userId = $_SESSION['user_id'];
        
        $idee = $this->ideeModel->findByIdWithJoins($ideeId);
        if (!$idee) {
            error_log("EvaluationController::edit - Idée non trouvée: $ideeId");
            setFlashMessage('error', 'Idée introuvable.');
            redirect('/evaluations');
            return;
        }
        
        $evaluations = $this->evaluationModel->findByIdee($ideeId);
        $evaluation = null;
        
        foreach ($evaluations ?? [] as $eval) {
            if ($eval['evaluateur_id'] == $userId) {
                $evaluation = $eval;
                break;
            }
        }
        
        if (!$evaluation) {
            error_log("EvaluationController::edit - Évaluation non trouvée pour user: $userId");
            setFlashMessage('error', 'Évaluation introuvable.');
            redirect('/evaluations');
            return;
        }
        
        error_log("EvaluationController::edit - Affichage formulaire édition");
        
        $this->loadView('evaluations/form', [
            'title' => 'Modifier l\'évaluation',
            'idee' => $idee,
            'evaluation' => $evaluation,
            'isEdit' => true
        ], 'user_layout');
    }
    
    // Validation des données d'évaluation
    private function validateEvaluation($data) {
        $errors = [];
        
        if (empty($data['note']) || $data['note'] < 0 || $data['note'] > 20) {
            $errors[] = "La note doit être comprise entre 0 et 20.";
        }
        
        if (empty($data['idee_id']) || !is_numeric($data['idee_id'])) {
            $errors[] = "ID de l'idée invalide.";
        }
        
        if (empty($data['evaluateur_id']) || !is_numeric($data['evaluateur_id'])) {
            $errors[] = "ID de l'évaluateur invalide.";
        }
        
        error_log("EvaluationController::validateEvaluation - " . count($errors) . " erreur(s)");
        
        return $errors;
    }
}