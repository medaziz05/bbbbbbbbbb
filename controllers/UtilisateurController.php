<?php
/**
 * Contrôleur des utilisateurs - CORRIGÉ
 * controllers/UtilisateurController.php
 */

class UtilisateurController extends BaseController {
    private $utilisateurModel;
    
    public function __construct() {
        $this->checkPermission('admin');
        $this->utilisateurModel = new Utilisateur();
    }
    
    // Liste des utilisateurs - CORRECTION DU CHEMIN DE VUE
    public function index() {
        $utilisateurs = $this->utilisateurModel->findAll();
        
        // CORRECTION: Utiliser le bon chemin de vue existante
        $this->loadView('admin/users', [
            'title' => 'Gestion des utilisateurs',
            'utilisateurs' => $utilisateurs
        ], 'admin_layout');
    }
    
    // Afficher le formulaire de création
    public function create() {
        $this->loadView('utilisateurs/form', [
            'title' => 'Nouvel utilisateur',
            'action' => 'create',
            'utilisateur' => null
        ], 'admin_layout');
    }
    
    // Traiter la création
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/utilisateurs');
        }
        
        $data = [
            'nom' => sanitize($_POST['nom'] ?? ''),
            'prenom' => sanitize($_POST['prenom'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'mot_de_passe' => $_POST['mot_de_passe'] ?? '',
            'role' => sanitize($_POST['role'] ?? '')
        ];
        
        $errors = $this->validateUser($data);
        
        if (!empty($errors)) {
            $this->loadView('utilisateurs/form', [
                'title' => 'Nouvel utilisateur',
                'action' => 'create',
                'utilisateur' => $data,
                'errors' => $errors
            ], 'admin_layout');
            return;
        }
        
        if ($this->utilisateurModel->createUser($data)) {
            $this->redirectWithMessage('/utilisateurs', 'success', 'Utilisateur créé avec succès.');
        } else {
            $this->redirectWithMessage('/utilisateurs', 'error', 'Erreur lors de la création.');
        }
    }
    
    // Afficher le formulaire d'édition
    public function edit($id) {
        $utilisateur = $this->utilisateurModel->findById($id);
        
        if (!$utilisateur) {
            $this->redirectWithMessage('/utilisateurs', 'error', 'Utilisateur introuvable.');
        }
        
        $this->loadView('utilisateurs/form', [
            'title' => 'Modifier l\'utilisateur',
            'action' => 'edit',
            'utilisateur' => $utilisateur
        ], 'admin_layout');
    }
    
    // Traiter la modification
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/utilisateurs');
        }
        
        $utilisateur = $this->utilisateurModel->findById($id);
        if (!$utilisateur) {
            $this->redirectWithMessage('/utilisateurs', 'error', 'Utilisateur introuvable.');
        }
        
        $data = [
            'nom' => sanitize($_POST['nom'] ?? ''),
            'prenom' => sanitize($_POST['prenom'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'role' => sanitize($_POST['role'] ?? '')
        ];
        
        // Ajout du mot de passe si fourni
        if (!empty($_POST['mot_de_passe'])) {
            $data['mot_de_passe'] = $_POST['mot_de_passe'];
        }
        
        $errors = $this->validateUser($data, $id);
        
        if (!empty($errors)) {
            $data['id'] = $id;
            $this->loadView('utilisateurs/form', [
                'title' => 'Modifier l\'utilisateur',
                'action' => 'edit',
                'utilisateur' => $data,
                'errors' => $errors
            ], 'admin_layout');
            return;
        }
        
        // Mettre à jour avec ou sans mot de passe
        $success = false;
        if (isset($data['mot_de_passe'])) {
            $password = $data['mot_de_passe'];
            unset($data['mot_de_passe']);
            $success = $this->utilisateurModel->update($id, $data) && 
                      $this->utilisateurModel->updatePassword($id, $password);
        } else {
            $success = $this->utilisateurModel->update($id, $data);
        }
        
        if ($success) {
            $this->redirectWithMessage('/utilisateurs', 'success', 'Utilisateur modifié avec succès.');
        } else {
            $this->redirectWithMessage('/utilisateurs', 'error', 'Erreur lors de la modification.');
        }
    }
    
    // Supprimer un utilisateur
    public function delete($id) {
        if ($id == $_SESSION['user_id']) {
            $this->redirectWithMessage('/utilisateurs', 'error', 'Vous ne pouvez pas vous supprimer.');
        }
        
        if ($this->utilisateurModel->delete($id)) {
            $this->redirectWithMessage('/utilisateurs', 'success', 'Utilisateur supprimé avec succès.');
        } else {
            $this->redirectWithMessage('/utilisateurs', 'error', 'Erreur lors de la suppression.');
        }
    }
    
    // Validation des données utilisateur
    private function validateUser($data, $excludeId = null) {
        $errors = $this->validateRequired([
            'nom' => 'Nom',
            'prenom' => 'Prénom',
            'email' => 'Email',
            'role' => 'Rôle'
        ], $data);
        
        // Validation du mot de passe pour la création ou si fourni
        if ($excludeId === null || !empty($data['mot_de_passe'])) {
            if (empty($data['mot_de_passe'])) {
                $errors[] = "Le champ 'Mot de passe' est requis.";
            } elseif (strlen($data['mot_de_passe']) < 6) {
                $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
            }
        }
        
        // Validation de l'email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format d'email invalide.";
        } elseif ($this->utilisateurModel->emailExists($data['email'], $excludeId)) {
            $errors[] = "Cet email est déjà utilisé.";
        }
        
        // Validation du rôle
        if (!array_key_exists($data['role'], ROLES)) {
            $errors[] = "Rôle invalide.";
        }
        
        return $errors;
    }
}
