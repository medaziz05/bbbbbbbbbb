<?php
/**
 * Contrôleur des thématiques - CORRIGÉ
 * controllers/ThematiqueController.php
 */

class ThematiqueController extends BaseController {
    private $thematiqueModel;
    
    public function __construct() {
        $this->checkPermission('admin');
        $this->thematiqueModel = new Thematique();
    }
    
    // Liste des thématiques - CORRECTION COMPLÈTE
    public function index() {
        // CORRECTION: Utiliser la méthode qui compte les idées
        $thematiques = $this->thematiqueModel->findAllWithIdeeCount();
        
        // CORRECTION: Utiliser le bon chemin de vue existante
        $this->loadView('admin/thematiques', [
            'title' => 'Gestion des thématiques',
            'thematiques' => $thematiques
        ], 'admin_layout');
    }
    
    // Afficher le formulaire de création
    public function create() {
        $this->loadView('thematiques/form', [
            'title' => 'Nouvelle thématique',
            'action' => 'create',
            'thematique' => null
        ], 'admin_layout');
    }
    
    // Traiter la création
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/thematiques');
        }
        
        $data = [
            'nom' => sanitize($_POST['nom'] ?? ''),
            'description' => sanitize($_POST['description'] ?? '')
        ];
        
        $errors = $this->validateThematique($data);
        
        if (!empty($errors)) {
            $this->loadView('thematiques/form', [
                'title' => 'Nouvelle thématique',
                'action' => 'create',
                'thematique' => $data,
                'errors' => $errors
            ], 'admin_layout');
            return;
        }
        
        if ($this->thematiqueModel->create($data)) {
            $this->redirectWithMessage('/thematiques', 'success', 'Thématique créée avec succès.');
        } else {
            $this->redirectWithMessage('/thematiques', 'error', 'Erreur lors de la création.');
        }
    }
    
    // Afficher le formulaire d'édition
    public function edit($id) {
        $thematique = $this->thematiqueModel->findById($id);
        
        if (!$thematique) {
            $this->redirectWithMessage('/thematiques', 'error', 'Thématique introuvable.');
        }
        
        $this->loadView('thematiques/form', [
            'title' => 'Modifier la thématique',
            'action' => 'edit',
            'thematique' => $thematique
        ], 'admin_layout');
    }
    
    // Traiter la modification
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/thematiques');
        }
        
        $thematique = $this->thematiqueModel->findById($id);
        if (!$thematique) {
            $this->redirectWithMessage('/thematiques', 'error', 'Thématique introuvable.');
        }
        
        $data = [
            'nom' => sanitize($_POST['nom'] ?? ''),
            'description' => sanitize($_POST['description'] ?? '')
        ];
        
        $errors = $this->validateThematique($data);
        
        if (!empty($errors)) {
            $data['id'] = $id;
            $this->loadView('thematiques/form', [
                'title' => 'Modifier la thématique',
                'action' => 'edit',
                'thematique' => $data,
                'errors' => $errors
            ], 'admin_layout');
            return;
        }
        
        if ($this->thematiqueModel->update($id, $data)) {
            $this->redirectWithMessage('/thematiques', 'success', 'Thématique modifiée avec succès.');
        } else {
            $this->redirectWithMessage('/thematiques', 'error', 'Erreur lors de la modification.');
        }
    }
    
    // Supprimer une thématique
    public function delete($id) {
        if ($this->thematiqueModel->hasIdees($id)) {
            $this->redirectWithMessage('/thematiques', 'error', 'Impossible de supprimer une thématique qui contient des idées.');
        }
        
        if ($this->thematiqueModel->delete($id)) {
            $this->redirectWithMessage('/thematiques', 'success', 'Thématique supprimée avec succès.');
        } else {
            $this->redirectWithMessage('/thematiques', 'error', 'Erreur lors de la suppression.');
        }
    }
    
    // Validation des données thématique
    private function validateThematique($data) {
        return $this->validateRequired([
            'nom' => 'Nom',
            'description' => 'Description'
        ], $data);
    }
}
?>