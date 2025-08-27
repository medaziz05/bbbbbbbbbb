<?php
/**
 * Modèle Idée
 * models/Idee.php
 */

class Idee extends BaseModel {
    protected $table = 'idees';
    
    // Obtenir les idées avec les informations jointes
    public function findAllWithJoins($conditions = '', $params = [], $limit = null) {
    $sql = "SELECT i.*, 
                   u.nom as utilisateur_nom, u.prenom as utilisateur_prenom,
                   t.nom as thematique_nom
            FROM {$this->table} i
            LEFT JOIN utilisateurs u ON i.utilisateur_id = u.id
            LEFT JOIN thematiques t ON i.thematique_id = t.id";
    
    if ($conditions) {
        $sql .= " WHERE " . $conditions;
    }
    $sql .= " ORDER BY i.date_creation DESC";
    if ($limit !== null) {
        $sql .= " LIMIT " . intval($limit);
    }
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
    
    // Obtenir une idée avec ses informations jointes
    public function findByIdWithJoins($id) {
        $sql = "SELECT i.*, 
                       u.nom as utilisateur_nom, u.prenom as utilisateur_prenom,
                       t.nom as thematique_nom
                FROM {$this->table} i
                LEFT JOIN utilisateurs u ON i.utilisateur_id = u.id
                LEFT JOIN thematiques t ON i.thematique_id = t.id
                WHERE i.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    // Obtenir les idées d'un utilisateur
    public function findByUser($userId) {
        return $this->findAllWithJoins('i.utilisateur_id = :user_id', ['user_id' => $userId]);
    }
    
    // Obtenir les idées par thématique
    public function findByThematique($thematiqueId) {
        return $this->findAllWithJoins('i.thematique_id = :thematique_id', ['thematique_id' => $thematiqueId]);
    }
    
    // Obtenir les idées par statut
    public function findByStatut($statut) {
        return $this->findAllWithJoins('i.statut = :statut', ['statut' => $statut]);
    }
    
    // Obtenir les meilleures idées (avec note)
    public function findBestIdees($limit = 10) {
        return $this->findAllWithJoins('i.note_moyenne IS NOT NULL ORDER BY i.note_moyenne DESC, i.nombre_evaluations DESC LIMIT ' . intval($limit));
    }
    
    // Mettre à jour le statut
    public function updateStatut($id, $statut) {
        return $this->update($id, ['statut' => $statut]);
    }
    
    // Obtenir les statistiques des idées
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    COUNT(CASE WHEN statut = 'en_attente' THEN 1 END) as en_attente,
                    COUNT(CASE WHEN statut = 'en_evaluation' THEN 1 END) as en_evaluation,
                    COUNT(CASE WHEN statut = 'acceptee' THEN 1 END) as acceptees,
                    COUNT(CASE WHEN statut = 'refusee' THEN 1 END) as refusees,
                    AVG(note_moyenne) as note_moyenne_globale
                FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>