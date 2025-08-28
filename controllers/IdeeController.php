<?php
/**
 * Contrôleur des idées CORRIGÉ
 * controllers/IdeeController.php
 */

class IdeeController extends BaseController {
    private $ideeModel;
    private $thematiqueModel;
    private $evaluationModel;
    
    public function __construct() {
        if (!isLoggedIn()) {
            redirect('/auth/login');
        }
        
        $this->ideeModel = new Idee();
        $this->thematiqueModel = new Thematique();
        $this->evaluationModel = new Evaluation();
    }
    
    // Liste des idées
    public function index() {
        $userRole = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'];
        
        // Filtrer selon le rôle
        switch ($userRole) {
            case 'admin':
                $idees = $this->ideeModel->findAllWithJoins();
                $layout = 'admin_layout';
                break;
            case 'salarie':
                $idees = $this->ideeModel->findByUser($userId);
                $layout = 'user_layout';
                break;
            case 'evaluateur':
                $idees = $this->ideeModel->findAllWithJoins();
                $layout = 'user_layout';
                break;
            default:
                $idees = [];
                $layout = 'user_layout';
        }
        
        $this->loadView('idees/index', [
            'title' => 'Gestion des idées',
            'idees' => $idees,
            'canCreate' => $userRole === 'salarie' || $userRole === 'admin'
        ], $layout);
    }
    
    // Afficher une idée
    public function show($id) {
        $idee = $this->ideeModel->findByIdWithJoins($id);
        
        if (!$idee) {
            $this->redirectWithMessage('/idees', 'error', 'Idée introuvable.');
        }
        
        // Vérifier les permissions
        $userRole = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'];
        
        if ($userRole === 'salarie' && $idee['utilisateur_id'] != $userId) {
            $this->redirectWithMessage('/idees', 'error', 'Accès non autorisé.');
        }
        
        // Récupérer les évaluations si nécessaire
        $evaluations = [];
        $canEvaluate = false;
        
        if ($userRole === 'evaluateur') {
            $evaluations = $this->evaluationModel->findByIdee($id);
            $canEvaluate = !$this->evaluationModel->hasEvaluated($id, $userId);
        } elseif ($userRole === 'admin' || $idee['utilisateur_id'] == $userId) {
            $evaluations = $this->evaluationModel->findByIdee($id);
        }
        
        // Déterminer le layout
        $layout = ($userRole === 'admin') ? 'admin_layout' : 'user_layout';
        
        $this->loadView('idees/show', [
            'title' => 'Détail de l\'idée',
            'idee' => $idee,
            'evaluations' => $evaluations,
            'canEvaluate' => $canEvaluate,
            'canEdit' => $userRole === 'admin' || ($userRole === 'salarie' && $idee['utilisateur_id'] == $userId)
        ], $layout);
    }
    
    // Afficher le formulaire de création
    public function create() {
        // Vérifier les permissions
        if (!($_SESSION['user_role'] === 'salarie' || $_SESSION['user_role'] === 'admin')) {
            $this->redirectWithMessage('/dashboard', 'error', 'Accès non autorisé.');
        }
        
        $thematiques = $this->thematiqueModel->findActive();
        
        // Déterminer le layout
        $layout = ($_SESSION['user_role'] === 'admin') ? 'admin_layout' : 'user_layout';
        
        $this->loadView('idees/form', [
            'title' => 'Nouvelle idée',
            'action' => 'create',
            'idee' => null,
            'thematiques' => $thematiques
        ], $layout);
    }
    
    // Traiter la création
    public function store() {
        // Vérifier les permissions
        if (!($_SESSION['user_role'] === 'salarie' || $_SESSION['user_role'] === 'admin')) {
            $this->redirectWithMessage('/dashboard', 'error', 'Accès non autorisé.');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/idees');
        }
        
        $data = [
            'titre' => sanitize($_POST['titre'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'thematique_id' => intval($_POST['thematique_id'] ?? 0),
            'utilisateur_id' => $_SESSION['user_id']
        ];
        
        $errors = $this->validateIdee($data);
        
        if (!empty($errors)) {
            $thematiques = $this->thematiqueModel->findActive();
            $layout = ($_SESSION['user_role'] === 'admin') ? 'admin_layout' : 'user_layout';
            
            $this->loadView('idees/form', [
                'title' => 'Nouvelle idée',
                'action' => 'create',
                'idee' => $data,
                'thematiques' => $thematiques,
                'errors' => $errors
            ], $layout);
            return;
        }
        
        if ($this->ideeModel->create($data)) {
            $this->redirectWithMessage('/idees', 'success', 'Idée créée avec succès.');
        } else {
            $this->redirectWithMessage('/idees', 'error', 'Erreur lors de la création.');
        }
    }
    
    // Afficher le formulaire d'édition
    public function edit($id) {
        $idee = $this->ideeModel->findById($id);
        
        if (!$idee) {
            $this->redirectWithMessage('/idees', 'error', 'Idée introuvable.');
        }
        
        // Vérifier les permissions
        $userRole = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'];
        
        if ($userRole !== 'admin' && ($userRole !== 'salarie' || $idee['utilisateur_id'] != $userId)) {
            $this->redirectWithMessage('/idees', 'error', 'Accès non autorisé.');
        }
        
        $thematiques = $this->thematiqueModel->findActive();
        $layout = ($userRole === 'admin') ? 'admin_layout' : 'user_layout';
        
        $this->loadView('idees/form', [
            'title' => 'Modifier l\'idée',
            'action' => 'edit',
            'idee' => $idee,
            'thematiques' => $thematiques
        ], $layout);
    }
    
    // Traiter la modification
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/idees');
        }
        
        $idee = $this->ideeModel->findById($id);
        if (!$idee) {
            $this->redirectWithMessage('/idees', 'error', 'Idée introuvable.');
        }
        
        // Vérifier les permissions
        $userRole = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'];
        
        if ($userRole !== 'admin' && ($userRole !== 'salarie' || $idee['utilisateur_id'] != $userId)) {
            $this->redirectWithMessage('/idees', 'error', 'Accès non autorisé.');
        }
        
        $data = [
            'titre' => sanitize($_POST['titre'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'thematique_id' => intval($_POST['thematique_id'] ?? 0)
        ];
        
        // Admin peut modifier le statut
        if ($userRole === 'admin' && isset($_POST['statut'])) {
            $data['statut'] = sanitize($_POST['statut']);
        }
        
        $errors = $this->validateIdee($data);
        
        if (!empty($errors)) {
            $data['id'] = $id;
            $thematiques = $this->thematiqueModel->findActive();
            $layout = ($userRole === 'admin') ? 'admin_layout' : 'user_layout';
            
            $this->loadView('idees/form', [
                'title' => 'Modifier l\'idée',
                'action' => 'edit',
                'idee' => $data,
                'thematiques' => $thematiques,
                'errors' => $errors
            ], $layout);
            return;
        }
        
        if ($this->ideeModel->update($id, $data)) {
            $this->redirectWithMessage('/idees', 'success', 'Idée modifiée avec succès.');
        } else {
            $this->redirectWithMessage('/idees', 'error', 'Erreur lors de la modification.');
        }
    }
    
    // Supprimer une idée
    public function delete($id) {
        $idee = $this->ideeModel->findById($id);
        
        if (!$idee) {
            $this->redirectWithMessage('/idees', 'error', 'Idée introuvable.');
        }
        
        // Vérifier les permissions
        $userRole = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'];
        
        if ($userRole !== 'admin' && ($userRole !== 'salarie' || $idee['utilisateur_id'] != $userId)) {
            $this->redirectWithMessage('/idees', 'error', 'Accès non autorisé.');
        }
        
        if ($this->ideeModel->delete($id)) {
            $this->redirectWithMessage('/idees', 'success', 'Idée supprimée avec succès.');
        } else {
            $this->redirectWithMessage('/idees', 'error', 'Erreur lors de la suppression.');
        }
    }
    
    // Évaluer une idée
    public function evaluate($id) {
        $this->checkPermission('evaluateur');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/idees/' . $id);
        }
        
        $idee = $this->ideeModel->findById($id);
        if (!$idee) {
            $this->redirectWithMessage('/idees', 'error', 'Idée introuvable.');
        }
        
        $data = [
            'idee_id' => $id,
            'evaluateur_id' => $_SESSION['user_id'],
            'note' => floatval($_POST['note'] ?? 0),
            'commentaire' => sanitize($_POST['commentaire'] ?? '')
        ];
        
        $errors = $this->validateEvaluation($data);
        
        if (!empty($errors)) {
            foreach ($errors as $error) {
                setFlashMessage('error', $error);
            }
            redirect('/idees/' . $id);
            return;
        }
        
        if ($this->evaluationModel->createOrUpdate($data)) {
            // Mettre à jour le statut de l'idée si nécessaire
            if ($idee['statut'] === 'en_attente') {
                $this->ideeModel->updateStatut($id, 'en_evaluation');
            }
            
            $this->redirectWithMessage('/idees/' . $id, 'success', 'Évaluation enregistrée avec succès.');
        } else {
            $this->redirectWithMessage('/idees/' . $id, 'error', 'Erreur lors de l\'évaluation.');
        }
    }
    
    // Validation des données d'idée
    private function validateIdee($data) {
        $errors = $this->validateRequired([
            'titre' => 'Titre',
            'description' => 'Description',
            'thematique_id' => 'Thématique'
        ], $data);
        
        if ($data['thematique_id'] <= 0) {
            $errors[] = "Veuillez sélectionner une thématique valide.";
        } else {
            // Vérifier que la thématique existe
            $thematique = $this->thematiqueModel->findById($data['thematique_id']);
            if (!$thematique || !$thematique['actif']) {
                $errors[] = "La thématique sélectionnée n'est pas valide.";
            }
        }
        
        if (isset($data['statut']) && !array_key_exists($data['statut'], STATUTS_IDEES)) {
            $errors[] = "Statut invalide.";
        }
        
        return $errors;
    }
    
    // Validation des données d'évaluation
    private function validateEvaluation($data) {
        $errors = [];
        
        if ($data['note'] < 0 || $data['note'] > 20) {
            $errors[] = "La note doit être comprise entre 0 et 20.";
        }
        
        return $errors;
    }
}
?>