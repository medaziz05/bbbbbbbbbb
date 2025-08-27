<?php
class Evaluation extends BaseModel {
    protected $table = 'evaluations';
    
    // Obtenir les évaluations avec les informations jointes
    public function findAllWithJoins($conditions = '', $params = []) {
        $sql = "SELECT e.*, 
                       i.titre as idee_titre,
                       u.nom as evaluateur_nom, u.prenom as evaluateur_prenom
                FROM {$this->table} e
                LEFT JOIN idees i ON e.idee_id = i.id
                LEFT JOIN utilisateurs u ON e.evaluateur_id = u.id";
        
        if ($conditions) {
            $sql .= " WHERE " . $conditions;
        }
        $sql .= " ORDER BY e.date_creation DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    // Obtenir les évaluations d'une idée
    public function findByIdee($ideeId) {
        return $this->findAllWithJoins('e.idee_id = :idee_id', ['idee_id' => $ideeId]);
    }
    
    // Obtenir les évaluations d'un évaluateur
    public function findByEvaluateur($evaluateurId) {
        return $this->findAllWithJoins('e.evaluateur_id = :evaluateur_id', ['evaluateur_id' => $evaluateurId]);
    }
    
    // Vérifier si un évaluateur a déjà évalué une idée
    public function hasEvaluated($ideeId, $evaluateurId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE idee_id = :idee_id AND evaluateur_id = :evaluateur_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['idee_id' => $ideeId, 'evaluateur_id' => $evaluateurId]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    // Créer ou mettre à jour une évaluation
    public function createOrUpdate($data) {
        if ($this->hasEvaluated($data['idee_id'], $data['evaluateur_id'])) {
            // Mettre à jour
            $sql = "UPDATE {$this->table} SET note = :note, commentaire = :commentaire, date_modification = NOW() 
                    WHERE idee_id = :idee_id AND evaluateur_id = :evaluateur_id";
        } else {
            // Créer
            $sql = "INSERT INTO {$this->table} (idee_id, evaluateur_id, note, commentaire) 
                    VALUES (:idee_id, :evaluateur_id, :note, :commentaire)";
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
}
?>