<?php
/**
 * Modèle de base
 * models/BaseModel.php
 */

abstract class BaseModel {
    protected $db;
    protected $table;
    
    public function __construct() {
        // Utiliser le pattern Singleton pour obtenir l'instance de Database
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Trouver tous les enregistrements
     */
    public function findAll($where = '', $params = [], $orderBy = 'id ASC', $limit = null) {
    try {
        $sql = "SELECT * FROM {$this->table}";
        if (!empty($where)) {
            $sql .= " WHERE {$where}";
        }
        $sql .= " ORDER BY {$orderBy}";
        if ($limit !== null) {
            $sql .= " LIMIT " . intval($limit);
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erreur findAll dans {$this->table}: " . $e->getMessage());
        return [];
    }
}

    
    /**
     * Trouver un enregistrement par ID
     */
    public function findById($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erreur findById dans {$this->table}: " . $e->getMessage());
            return false;
        }
    }

    
    /**
     * Créer un nouvel enregistrement
     */
    public function create($data) {
        try {
            $fields = array_keys($data);
            $placeholders = ':' . implode(', :', $fields);
            $fieldsList = implode(', ', $fields);
            
            $sql = "INSERT INTO {$this->table} ({$fieldsList}) VALUES ({$placeholders})";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute($data);
        } catch (PDOException $e) {
            error_log("Erreur create dans {$this->table}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mettre à jour un enregistrement
     */
    public function update($id, $data) {
        try {
            $fields = [];
            foreach (array_keys($data) as $field) {
                $fields[] = "{$field} = :{$field}";
            }
            $fieldsList = implode(', ', $fields);
            
            $sql = "UPDATE {$this->table} SET {$fieldsList} WHERE id = :id";
            $data['id'] = $id;
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute($data);
        } catch (PDOException $e) {
            error_log("Erreur update dans {$this->table}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprimer un enregistrement
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Erreur delete dans {$this->table}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Compter les enregistrements
     */
    public function count($where = '') {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table}";
            if (!empty($where)) {
                $sql .= " WHERE {$where}";
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erreur count dans {$this->table}: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Trouver des enregistrements avec une condition
     */
    public function findWhere($where, $params = [], $orderBy = 'id ASC') {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$orderBy}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur findWhere dans {$this->table}: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Exécuter une requête personnalisée
     */
    protected function query($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Erreur query personnalisée: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtenir le dernier ID inséré
     */
    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }
    
    /**
     * Commencer une transaction
     */
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    
    /**
     * Valider une transaction
     */
    public function commit() {
        return $this->db->commit();
    }
    
    /**
     * Annuler une transaction
     */
    public function rollback() {
        return $this->db->rollback();
    }
}
?>