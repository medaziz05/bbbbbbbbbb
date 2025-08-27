<?php

class Utilisateur extends BaseModel {
    protected $table = 'utilisateurs';
    
    // Trouver un utilisateur par email
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email AND actif = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }
    
    // Vérifier si un email existe déjà
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";
        $params = ['email' => $email];
        
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    // Créer un utilisateur avec hash du mot de passe
    public function createUser($data) {
        $data['mot_de_passe'] = password_hash($data['mot_de_passe'], HASH_ALGO);
        return $this->create($data);
    }
    
    // Mettre à jour le mot de passe
    public function updatePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, HASH_ALGO);
        return $this->update($id, ['mot_de_passe' => $hashedPassword]);
    }
    
    // Obtenir les utilisateurs par rôle
    public function findByRole($role) {
        return $this->findAll('role = :role AND actif = 1', ['role' => $role]);
    }
    
    // Authentification
    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            return $user;
        }
        return false;
    }
    
    // Désactiver un utilisateur
    public function deactivate($id) {
        return $this->update($id, ['actif' => 0]);
    }
    
    // Activer un utilisateur
    public function activate($id) {
        return $this->update($id, ['actif' => 1]);
    }
}
?>