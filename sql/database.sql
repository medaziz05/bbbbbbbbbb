-- Base de données pour le système de gestion d'idées et d'innovation

CREATE DATABASE IF NOT EXISTS gestion_idees CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_idees;

-- Table des utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'salarie', 'evaluateur') NOT NULL,
    actif BOOLEAN DEFAULT TRUE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des thématiques
CREATE TABLE thematiques (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    description TEXT,
    actif BOOLEAN DEFAULT TRUE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des idées
CREATE TABLE idees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    utilisateur_id INT NOT NULL,
    thematique_id INT NOT NULL,
    statut ENUM('en_attente', 'en_evaluation', 'acceptee', 'refusee') DEFAULT 'en_attente',
    note_moyenne DECIMAL(3,2) DEFAULT NULL,
    nombre_evaluations INT DEFAULT 0,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (thematique_id) REFERENCES thematiques(id) ON DELETE CASCADE
);

-- Table des évaluations
CREATE TABLE evaluations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idee_id INT NOT NULL,
    evaluateur_id INT NOT NULL,
    note DECIMAL(3,2) NOT NULL CHECK (note >= 0 AND note <= 20),
    commentaire TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (idee_id) REFERENCES idees(id) ON DELETE CASCADE,
    FOREIGN KEY (evaluateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_evaluation (idee_id, evaluateur_id)
);

-- Insertion des données de test
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES
('Admin', 'Super', 'admin@entreprise.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Dupont', 'Jean', 'jean.dupont@entreprise.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'salarie'),
('Martin', 'Marie', 'marie.martin@entreprise.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'evaluateur'),
('Bernard', 'Paul', 'paul.bernard@entreprise.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'salarie');

INSERT INTO thematiques (nom, description) VALUES
('Innovation Technologique', 'Idées liées aux nouvelles technologies et innovations'),
('Amélioration des Processus', 'Suggestions pour optimiser les processus internes'),
('Développement Durable', 'Initiatives écologiques et développement durable'),
('Communication Interne', 'Amélioration de la communication au sein de l\'entreprise');

INSERT INTO idees (titre, description, utilisateur_id, thematique_id, statut) VALUES
('Application mobile pour les employés', 'Développer une app mobile pour faciliter la communication interne', 2, 1, 'en_evaluation'),
('Réduction du papier au bureau', 'Mise en place d\'un système de dématérialisation complète', 4, 3, 'en_attente');

-- Trigger pour mettre à jour la note moyenne des idées
DELIMITER $$
CREATE TRIGGER update_note_moyenne AFTER INSERT ON evaluations
FOR EACH ROW
BEGIN
    UPDATE idees 
    SET 
        note_moyenne = (SELECT AVG(note) FROM evaluations WHERE idee_id = NEW.idee_id),
        nombre_evaluations = (SELECT COUNT(*) FROM evaluations WHERE idee_id = NEW.idee_id)
    WHERE id = NEW.idee_id;
END$$

CREATE TRIGGER update_note_moyenne_update AFTER UPDATE ON evaluations
FOR EACH ROW
BEGIN
    UPDATE idees 
    SET 
        note_moyenne = (SELECT AVG(note) FROM evaluations WHERE idee_id = NEW.idee_id),
        nombre_evaluations = (SELECT COUNT(*) FROM evaluations WHERE idee_id = NEW.idee_id)
    WHERE id = NEW.idee_id;
END$$

CREATE TRIGGER update_note_moyenne_delete AFTER DELETE ON evaluations
FOR EACH ROW
BEGIN
    UPDATE idees 
    SET 
        note_moyenne = (SELECT IFNULL(AVG(note), NULL) FROM evaluations WHERE idee_id = OLD.idee_id),
        nombre_evaluations = (SELECT COUNT(*) FROM evaluations WHERE idee_id = OLD.idee_id)
    WHERE id = OLD.idee_id;
END$$
DELIMITER ;