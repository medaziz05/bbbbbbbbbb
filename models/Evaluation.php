<?php
/**
 * Modèle Evaluation - VERSION CORRIGÉE
 * models/Evaluation.php
 */

class Evaluation extends BaseModel {
    protected $table = 'evaluations';
    
    // Obtenir les évaluations avec les informations jointes
    public function findAllWithJoins($conditions = '', $params = []) {
        try {
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
            return $stmt->fetchAll() ?: [];
        } catch (PDOException $e) {
            error_log("Erreur findAllWithJoins Evaluation: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtenir les évaluations d'une idée
    public function findByIdee($ideeId) {
        try {
            return $this->findAllWithJoins('e.idee_id = :idee_id', ['idee_id' => $ideeId]);
        } catch (Exception $e) {
            error_log("Erreur findByIdee: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtenir les évaluations d'un évaluateur
    public function findByEvaluateur($evaluateurId) {
        try {
            return $this->findAllWithJoins('e.evaluateur_id = :evaluateur_id', ['evaluateur_id' => $evaluateurId]);
        } catch (Exception $e) {
            error_log("Erreur findByEvaluateur: " . $e->getMessage());
            return [];
        }
    }
    
    // Vérifier si un évaluateur a déjà évalué une idée
    public function hasEvaluated($ideeId, $evaluateurId) {
        try {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE idee_id = :idee_id AND evaluateur_id = :evaluateur_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['idee_id' => $ideeId, 'evaluateur_id' => $evaluateurId]);
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Erreur hasEvaluated: " . $e->getMessage());
            return false;
        }
    }
    
    // Créer ou mettre à jour une évaluation
    public function createOrUpdate($data) {
        try {
            if ($this->hasEvaluated($data['idee_id'], $data['evaluateur_id'])) {
                // Mettre à jour
                $sql = "UPDATE {$this->table} SET note = :note, commentaire = :commentaire, date_modification = NOW() 
                        WHERE idee_id = :idee_id AND evaluateur_id = :evaluateur_id";
                error_log("Evaluation::createOrUpdate - Mise à jour existante");
            } else {
                // Créer
                $sql = "INSERT INTO {$this->table} (idee_id, evaluateur_id, note, commentaire, date_creation) 
                        VALUES (:idee_id, :evaluateur_id, :note, :commentaire, NOW())";
                error_log("Evaluation::createOrUpdate - Création nouvelle");
            }
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($data);
            
            if ($result) {
                error_log("Evaluation::createOrUpdate - Succès");
                $this->updateIdeeNoyenneNote($data['idee_id']);
            } else {
                error_log("Evaluation::createOrUpdate - Échec");
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur createOrUpdate Evaluation: " . $e->getMessage());
            return false;
        }
    }
    
    // Mettre à jour la note moyenne d'une idée
    private function updateIdeeNoyenneNote($ideeId) {
        try {
            $sql = "UPDATE idees SET 
                        note_moyenne = (SELECT AVG(note) FROM evaluations WHERE idee_id = :idee_id),
                        nombre_evaluations = (SELECT COUNT(*) FROM evaluations WHERE idee_id = :idee_id2)
                    WHERE id = :idee_id3";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'idee_id' => $ideeId, 
                'idee_id2' => $ideeId, 
                'idee_id3' => $ideeId
            ]);
            
            error_log("Evaluation::updateIdeeNoyenneNote - Note moyenne mise à jour pour idée $ideeId");
        } catch (PDOException $e) {
            error_log("Erreur updateIdeeNoyenneNote: " . $e->getMessage());
        }
    }
}