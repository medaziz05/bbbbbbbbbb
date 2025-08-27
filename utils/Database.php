<?php
/**
 * Classe de gestion de la base de données
 * utils/Database.php
 */
class Database {
    private $connection;
    private static $instance = null;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Erreur de connexion à la base de données: " . $e->getMessage());
            throw new Exception("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    // Méthode pour tester la connexion
    public function testConnection() {
        try {
            $stmt = $this->connection->query('SELECT 1');
            return $stmt !== false;
        } catch (PDOException $e) {
            error_log("Erreur lors du test de connexion: " . $e->getMessage());
            return false;
        }
    }
    
    // Méthode pour obtenir des informations sur la base
    public function getDatabaseInfo() {
        try {
            $stmt = $this->connection->query('SELECT DATABASE() as db_name, VERSION() as version');
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des infos DB: " . $e->getMessage());
            return null;
        }
    }
}
?>