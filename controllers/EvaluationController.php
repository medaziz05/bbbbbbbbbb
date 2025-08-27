<?php
/**
 * Contrôleur des évaluations
 * controllers/EvaluationController.php
 */

class EvaluationController extends BaseController {
    private $evaluationModel;
    private $ideeModel;
    
    public function __construct() {
        $this->checkPermission('evaluateur');
        $this->evaluationModel = new Evaluation();
        $this->ideeModel = new Idee();
    }
    
    // Liste des évaluations
    public function index() {
        $userId = $_SESSION['user_id'];
        $evaluations = $this->evaluationModel->findByEvaluateur($userId);
        
        $this->loadView('evaluations/index', [
            'title' => 'Mes évaluations',
            'evaluations' => $evaluations
        ]);
    }
    
    // Idées à évaluer
    public function toEvaluate() {
        $userId = $_SESSION['user_id'];
        $allIdees = $this->ideeModel->findByStatut('en_evaluation');
        
        // Filtrer les idées non encore évaluées par cet évaluateur
        $ideesAEvaluer = array_filter($allIdees, function($idee) use ($userId) {
            return !$this->evaluationModel->hasEvaluated($idee['id'], $userId);
        });
        
        $this->loadView('evaluations/to_evaluate', [
            'title' => 'Idées à évaluer',
            'idees' => $ideesAEvaluer
        ]);
    }
    
    // Formulaire d'évaluation
    public function create($ideeId) {
        $userId = $_SESSION['user_id'];
        
        // Vérifier que l'idée existe
        $idee = $this->ideeModel->findByIdWithJoins($ideeId);
        if (!$idee) {
            $this->redirectWithMessage('/evaluations/to-evaluate', 'error', 'Idée introuvable.');
        }
        
        // Vérifier que l'évaluateur n'a pas déjà évalué cette idée
        if ($this->evaluationModel->hasEvaluated($ideeId, $userId)) {
            $this->redirectWithMessage('/evaluations/to-evaluate', 'error', 'Vous avez déjà évalué cette idée.');
        }
        
        $data = [
            'idee_id' => $ideeId,
            'evaluateur_id' => $userId,
            'note' => floatval($_POST['note'] ?? 0),
            'commentaire' => sanitize($_POST['commentaire'] ?? '')
        ];
        
        $errors = $this->validateEvaluation($data);
        
        if (!empty($errors)) {
            $idee = $this->ideeModel->findByIdWithJoins($ideeId);
            $this->loadView('evaluations/form', [
                'title' => 'Évaluer l\'idée',
                'idee' => $idee,
                'errors' => $errors,
                'data' => $data
            ]);
            return;
        }
        
        if ($this->evaluationModel->createOrUpdate($data)) {
            // Mettre à jour le statut de l'idée si nécessaire
            if ($idee['statut'] === 'en_attente') {
                $this->ideeModel->updateStatut($ideeId, 'en_evaluation');
            }
            
            $this->redirectWithMessage('/evaluations', 'success', 'Évaluation enregistrée avec succès.');
        } else {
            $this->redirectWithMessage('/evaluations/to-evaluate', 'error', 'Erreur lors de l\'évaluation.');
        }
    }
    
    // Modifier une évaluation existante
    public function edit($ideeId) {
        $userId = $_SESSION['user_id'];
        
        $idee = $this->ideeModel->findByIdWithJoins($ideeId);
        if (!$idee) {
            $this->redirectWithMessage('/evaluations', 'error', 'Idée introuvable.');
        }
        
        $evaluations = $this->evaluationModel->findByIdee($ideeId);
        $evaluation = null;
        
        foreach ($evaluations as $eval) {
            if ($eval['evaluateur_id'] == $userId) {
                $evaluation = $eval;
                break;
            }
        }
        
        if (!$evaluation) {
            $this->redirectWithMessage('/evaluations', 'error', 'Évaluation introuvable.');
        }
        
        $this->loadView('evaluations/form', [
            'title' => 'Modifier l\'évaluation',
            'idee' => $idee,
            'evaluation' => $evaluation,
            'isEdit' => true
        ]);
    }
    
    // Validation des données d'évaluation
    private function validateEvaluation($data) {
        $errors = [];
        
        if (empty($data['note']) || $data['note'] < 0 || $data['note'] > 20) {
            $errors[] = "La note doit être comprise entre 0 et 20.";
        }
        
        return $errors;
    }
}
?>