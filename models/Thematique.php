<?php
/**
 * Modèle Thématique - CORRIGÉ
 * models/Thematique.php
 */

class Thematique extends BaseModel {
    protected $table = 'thematiques';
    
    // Obtenir les thématiques actives
    public function findActive() {
        return $this->findAll('actif = 1');
    }
    
    // CORRECTION: Obtenir toutes les thématiques avec le nombre d'idées
    public function findAllWithIdeeCount() {
        $sql = "SELECT t.*, 
                       COUNT(i.id) as nombre_idees
                FROM thematiques t 
                LEFT JOIN idees i ON t.id = i.thematique_id
                GROUP BY t.id
                ORDER BY t.nom ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Vérifier si une thématique a des idées associées
    public function hasIdees($id) {
        $sql = "SELECT COUNT(*) as count FROM idees WHERE thematique_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    // NOUVEAU: Obtenir le nombre d'idées pour une thématique spécifique
    public function getIdeeCount($id) {
        $sql = "SELECT COUNT(*) as count FROM idees WHERE thematique_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    // Obtenir les statistiques d'une thématique
    public function getStats($id) {
        $sql = "SELECT 
                    COUNT(*) as total_idees,
                    COUNT(CASE WHEN statut = 'acceptee' THEN 1 END) as idees_acceptees,
                    COUNT(CASE WHEN statut = 'en_evaluation' THEN 1 END) as idees_en_evaluation,
                    AVG(note_moyenne) as note_moyenne
                FROM idees 
                WHERE thematique_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    // Désactiver une thématique
    public function deactivate($id) {
        return $this->update($id, ['actif' => 0]);
    }
    
    // Activer une thématique
    public function activate($id) {
        return $this->update($id, ['actif' => 1]);
    }
}
?>