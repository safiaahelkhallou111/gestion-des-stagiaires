-- ==============================================================================
-- BDD : Plateforme de Gestion des Stagiaires OCP (Entité Digitalisation)
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS `ocp_stagiaires` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ocp_stagiaires`;

-- Supprimer les tables si elles existent (ordre inverse des dépendances FK)
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `documents`;
DROP TABLE IF EXISTS `evaluations`;
DROP TABLE IF EXISTS `taches`;
DROP TABLE IF EXISTS `pointages`;
DROP TABLE IF EXISTS `stages`;
DROP TABLE IF EXISTS `sujets_stage`;
DROP TABLE IF EXISTS `stagiaires`;
DROP TABLE IF EXISTS `encadrants`;
DROP TABLE IF EXISTS `users`;

-- 1. Table Utilisateurs (Authentification et profil de base)
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `role` ENUM('stagiaire', 'encadrant') NOT NULL,
  `nom` VARCHAR(100) NOT NULL,
  `prenom` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `telephone` VARCHAR(20) DEFAULT NULL,
  `avatar` VARCHAR(255) DEFAULT 'default.png',
  `reset_token` VARCHAR(255) DEFAULT NULL,
  `reset_expires` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Table Encadrants (Spécifique OCP)
CREATE TABLE `encadrants` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `departement` VARCHAR(100) NOT NULL DEFAULT 'Digitalisation & SI',
  `fonction` VARCHAR(100) NOT NULL DEFAULT 'Chef de Projet Digital',
  CONSTRAINT `fk_encadrants_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table Stagiaires
CREATE TABLE `stagiaires` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `cin` VARCHAR(20) NOT NULL UNIQUE,
  `universite` VARCHAR(150) NOT NULL,
  `filiere` VARCHAR(150) NOT NULL,
  `niveau_etudes` VARCHAR(50) DEFAULT 'Bac +5',
  `type_stage` ENUM('PFE', 'PFA', 'Observation', 'Initiation') NOT NULL DEFAULT 'PFE',
  CONSTRAINT `fk_stagiaires_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Table Sujets de Stage (RG4 : Appartient à un seul encadrant)
CREATE TABLE `sujets_stage` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `encadrant_id` INT NOT NULL,
  `titre` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `domaine` VARCHAR(100) DEFAULT 'Informatique / Digital',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_sujets_encadrant` FOREIGN KEY (`encadrant_id`) REFERENCES `encadrants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Table Stages (RG2: Stagiaire a un seul stage actif | RG3: Lié obligatoirement à stagiaire, encadrant, sujet)
CREATE TABLE `stages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `stagiaire_id` INT NOT NULL,
  `encadrant_id` INT NOT NULL,
  `sujet_id` INT NOT NULL,
  `date_debut` DATE NOT NULL,
  `date_fin` DATE NOT NULL,
  `statut` ENUM('en_cours', 'termine') NOT NULL DEFAULT 'en_cours',
  `date_validation` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_stages_stagiaire` FOREIGN KEY (`stagiaire_id`) REFERENCES `stagiaires` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_stages_encadrant` FOREIGN KEY (`encadrant_id`) REFERENCES `encadrants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_stages_sujet` FOREIGN KEY (`sujet_id`) REFERENCES `sujets_stage` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Table Pointages (RG7: Un pointage = 1 date + 1 stagiaire unique)
CREATE TABLE `pointages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `stage_id` INT NOT NULL,
  `stagiaire_id` INT NOT NULL,
  `date_pointage` DATE NOT NULL,
  `statut` ENUM('present', 'absent', 'retard') NOT NULL DEFAULT 'present',
  `remarque` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_pointage_stagiaire_date` (`stagiaire_id`, `date_pointage`),
  CONSTRAINT `fk_pointages_stage` FOREIGN KEY (`stage_id`) REFERENCES `stages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pointages_stagiaire` FOREIGN KEY (`stagiaire_id`) REFERENCES `stagiaires` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Table Tâches (Progression assignée par l'encadrant, mise à jour par le stagiaire)
CREATE TABLE `taches` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `stage_id` INT NOT NULL,
  `titre` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `date_limite` DATE NOT NULL,
  `statut` ENUM('a_faire', 'en_cours', 'termine') NOT NULL DEFAULT 'a_faire',
  `progression` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_taches_stage` FOREIGN KEY (`stage_id`) REFERENCES `stages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Table Évaluations (RG8: Liée à un stage précis, 1 évaluation unique par stage)
CREATE TABLE `evaluations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `stage_id` INT NOT NULL UNIQUE,
  `encadrant_id` INT NOT NULL,
  `note_technique` INT NOT NULL CHECK (`note_technique` BETWEEN 1 AND 10),
  `note_comportement` INT NOT NULL CHECK (`note_comportement` BETWEEN 1 AND 10),
  `note_assiduite` INT NOT NULL CHECK (`note_assiduite` BETWEEN 1 AND 10),
  `note_globale` FLOAT NOT NULL,
  `commentaire` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_evaluations_stage` FOREIGN KEY (`stage_id`) REFERENCES `stages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_evaluations_encadrant` FOREIGN KEY (`encadrant_id`) REFERENCES `encadrants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Table Documents (RG10: Rattaché à un stagiaire & stage, horodaté)
CREATE TABLE `documents` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `stagiaire_id` INT NULL,
  `stage_id` INT NULL,
  `titre` VARCHAR(255) NOT NULL,
  `type_document` ENUM('cin', 'cv', 'convention', 'rapport_intermediaire', 'rapport_final', 'autre') NOT NULL,
  `chemin_fichier` VARCHAR(255) NOT NULL,
  `taille_octets` INT NOT NULL DEFAULT 0,
  `permanent` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_documents_stagiaire` FOREIGN KEY (`stagiaire_id`) REFERENCES `stagiaires` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_documents_stage` FOREIGN KEY (`stage_id`) REFERENCES `stages` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Table Messages (RG9: Messagerie uniquement entre stagiaire et son encadrant assigné)
CREATE TABLE `messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `stage_id` INT NOT NULL,
  `expediteur_id` INT NOT NULL,
  `destinataire_id` INT NOT NULL,
  `message` TEXT NOT NULL,
  `lu` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_messages_stage` FOREIGN KEY (`stage_id`) REFERENCES `stages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_messages_expediteur` FOREIGN KEY (`expediteur_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_messages_destinataire` FOREIGN KEY (`destinataire_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Table Notifications
CREATE TABLE `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `titre` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `lien` VARCHAR(255) DEFAULT NULL,
  `lu` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- JEU DE DONNÉES DE DÉMONSTRATION (DEMO DATA OCP)
-- Mot de passe par défaut pour tous les comptes : Password123!
-- ==============================================================================

-- Compte 1: Encadrant (Karim ALAMI)
INSERT INTO `users` (`id`, `role`, `nom`, `prenom`, `email`, `password`, `telephone`) VALUES
(1, 'encadrant', 'ALAMI', 'Karim', 'encadrant@ocp.ma', '$2y$10$m017hMmP3WFhwx0UIUIhROAG3ZSb2YSuu5DKiGeVac9oVeTvxU7PW', '0661123456');

INSERT INTO `encadrants` (`id`, `user_id`, `departement`, `fonction`) VALUES
(1, 1, 'Digitalisation OCP Group', 'Chef de Projet Senior DX');

-- Compte 2: Stagiaire 1 (Sarah BENNANI - PFE en cours)
INSERT INTO `users` (`id`, `role`, `nom`, `prenom`, `email`, `password`, `telephone`) VALUES
(2, 'stagiaire', 'BENNANI', 'Sarah', 'stagiaire@ocp.ma', '$2y$10$m017hMmP3WFhwx0UIUIhROAG3ZSb2YSuu5DKiGeVac9oVeTvxU7PW', '0665987654');

INSERT INTO `stagiaires` (`id`, `user_id`, `cin`, `universite`, `filiere`, `niveau_etudes`, `type_stage`) VALUES
(1, 2, 'BJ543210', 'EMSI Casablanca', 'Génie Informatique & Web', 'Bac +5', 'PFE');

-- Compte 3: Stagiaire 2 (Youssef EL AMRANI - PFA)
INSERT INTO `users` (`id`, `role`, `nom`, `prenom`, `email`, `password`, `telephone`) VALUES
(3, 'stagiaire', 'EL AMRANI', 'Youssef', 'youssef@ocp.ma', '$2y$10$m017hMmP3WFhwx0UIUIhROAG3ZSb2YSuu5DKiGeVac9oVeTvxU7PW', '0667112233');

INSERT INTO `stagiaires` (`id`, `user_id`, `cin`, `universite`, `filiere`, `niveau_etudes`, `type_stage`) VALUES
(2, 3, 'EE987654', 'UM6P Benguerir', 'Data Science & AI', 'Bac +4', 'PFA');

-- Sujets de Stage OCP
INSERT INTO `sujets_stage` (`id`, `encadrant_id`, `titre`, `description`, `domaine`) VALUES
(1, 1, 'Conception & Déploiement de la Plateforme de Gestion des Stagiaires', 'Développement d’une solution web PHP/MySQL responsive centralisant le recrutement, le pointage, le suivi des tâches et l’évaluation des stagiaires pour l’entité Digitalisation OCP.', 'Développement Web Full-Stack'),
(2, 1, 'Analyse Prédictive de la Maintenance Industrielle par Machine Learning', 'Mise en place d’algorithmes prédictifs pour optimiser la disponibilité des équipements de la chaîne logistique OCP.', 'IA & Data Science');

-- Stages
INSERT INTO `stages` (`id`, `stagiaire_id`, `encadrant_id`, `sujet_id`, `date_debut`, `date_fin`, `statut`) VALUES
(1, 1, 1, 1, '2026-02-01', '2026-07-31', 'en_cours'),
(2, 2, 1, 2, '2026-03-01', '2026-06-30', 'en_cours');

-- Pointages pour Stagiaire 1 (Sarah)
INSERT INTO `pointages` (`stage_id`, `stagiaire_id`, `date_pointage`, `statut`, `remarque`) VALUES
(1, 1, CURDATE() - INTERVAL 4 DAY, 'present', 'Arrivée à 08h30 - Présence validée'),
(1, 1, CURDATE() - INTERVAL 3 DAY, 'present', 'Présence OK'),
(1, 1, CURDATE() - INTERVAL 2 DAY, 'retard', 'Arrivée à 09h15 - Retard transport'),
(1, 1, CURDATE() - INTERVAL 1 DAY, 'present', 'Travail sur le rapport intermédiaire'),
(1, 1, CURDATE(), 'present', 'Présent aujourd’hui');

-- Tâches pour Stage 1
INSERT INTO `taches` (`stage_id`, `titre`, `description`, `date_limite`, `statut`, `progression`) VALUES
(1, 'Modélisation de la base de données MySQL', 'Concevoir le MCD/MLD respectant les 10 règles de gestion OCP.', CURDATE() - INTERVAL 5 DAY, 'termine', 100),
(1, 'Implémentation de l’architecture PHP MVC', 'Structure des répertoires, contrôleurs, modèles et système de routage.', CURDATE() - INTERVAL 2 DAY, 'termine', 100),
(1, 'Développement du Module Stagiaire & Encadrant', 'Création des interfaces Bootstrap 5 et intégration de la messagerie AJAX.', CURDATE() + INTERVAL 5 DAY, 'en_cours', 75),
(1, 'Rédaction & Dépôt du Rapport Final PDF', 'Finaliser la rédaction du document et soumettre la version PDF finale.', CURDATE() + INTERVAL 15 DAY, 'a_faire', 0);

-- Documents déposés par Sarah BENNANI
INSERT INTO `documents` (`stagiaire_id`, `stage_id`, `titre`, `type_document`, `chemin_fichier`, `taille_octets`, `permanent`) VALUES
(1, 1, 'Carte d’Identité Nationale (CIN)', 'cin', 'sample_cin.pdf', 1024000, 0),
(1, 1, 'Convention de Stage OCP 2026', 'convention', 'sample_convention.pdf', 2048000, 0),
(1, 1, 'Rapport d’avancement Mi-Parcours', 'rapport_intermediaire', 'sample_rapport_inter.pdf', 3500000, 0);

-- Messages de test entre Encadrant (Karim) et Stagiaire (Sarah)
INSERT INTO `messages` (`stage_id`, `expediteur_id`, `destinataire_id`, `message`, `lu`, `created_at`) VALUES
(1, 1, 2, 'Bonjour Sarah, bienvenue au sein de l’équipe Digitalisation OCP ! As-tu pu prendre connaissance du sujet de stage ?', 1, NOW() - INTERVAL 2 DAY),
(1, 2, 1, 'Bonjour M. ALAMI, oui parfaitement. J’ai déjà démarré l’analyse du MCD et des règles de gestion.', 1, NOW() - INTERVAL 2 DAY + INTERVAL 30 MINUTE),
(1, 1, 2, 'Parfait. N’hésite pas à faire un premier pointage et à mettre à jour la progression de tes tâches.', 1, NOW() - INTERVAL 1 DAY);

-- Notifications
INSERT INTO `notifications` (`user_id`, `titre`, `message`, `lien`, `lu`) VALUES
(2, 'Bienvenue sur la plateforme OCP', 'Votre stage a été activé sous la tutelle de M. Karim ALAMI.', 'stagiaire/me_sujet', 1),
(2, 'Nouvelle tâche assignée', 'L’encadrant vous a assigné la tâche "Développement du Module Stagiaire & Encadrant".', 'stagiaire/me_taches', 0),
(1, 'Nouveau document déposé', 'Le stagiaire Sarah BENNANI a déposé son Rapport d’avancement Mi-Parcours.', 'encadrant/stagiaire_detail?id=1', 0);
