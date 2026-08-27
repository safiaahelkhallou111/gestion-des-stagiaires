<?php
// Script d'initialisation automatique de la BDD OCP (MySQL & SQLite)
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

function initDatabase() {
    try {
        $pdo = Database::getConnection();
        $driver = Database::getDriver();

        if ($driver === 'mysql') {
            $check = $pdo->query("SHOW TABLES LIKE 'users'");
            if ($check->rowCount() > 0) {
                return "La base de données MySQL OCP est prête.";
            }

            $sql = file_get_contents(__DIR__ . '/schema.sql');
            $queries = array_filter(array_map('trim', explode(';', $sql)));
            foreach ($queries as $query) {
                if (empty($query) || strpos($query, '--') === 0) continue;
                $pdo->exec($query);
            }
            return "Base de données MySQL initialisée avec succès.";
        } else {
            // Initialisation SQLite
            $check = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
            if ($check->fetch()) {
                return "La base de données SQLite OCP est prête.";
            }

            // Exécution du schéma adapté SQLite
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    role TEXT NOT NULL,
                    nom TEXT NOT NULL,
                    prenom TEXT NOT NULL,
                    email TEXT NOT NULL UNIQUE,
                    password TEXT NOT NULL,
                    telephone TEXT DEFAULT NULL,
                    avatar TEXT DEFAULT 'default.png',
                    reset_token TEXT DEFAULT NULL,
                    reset_expires DATETIME DEFAULT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS encadrants (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER NOT NULL,
                    departement TEXT NOT NULL DEFAULT 'Digitalisation & SI',
                    fonction TEXT NOT NULL DEFAULT 'Chef de Projet Digital',
                    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
                );

                CREATE TABLE IF NOT EXISTS stagiaires (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER NOT NULL,
                    cin TEXT NOT NULL UNIQUE,
                    universite TEXT NOT NULL,
                    filiere TEXT NOT NULL,
                    niveau_etudes TEXT DEFAULT 'Bac +5',
                    type_stage TEXT NOT NULL DEFAULT 'PFE',
                    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
                );

                CREATE TABLE IF NOT EXISTS sujets_stage (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    encadrant_id INTEGER NOT NULL,
                    titre TEXT NOT NULL,
                    description TEXT NOT NULL,
                    domaine TEXT DEFAULT 'Informatique / Digital',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (encadrant_id) REFERENCES encadrants (id) ON DELETE CASCADE
                );

                CREATE TABLE IF NOT EXISTS stages (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    stagiaire_id INTEGER NOT NULL,
                    encadrant_id INTEGER NOT NULL,
                    sujet_id INTEGER NOT NULL,
                    date_debut DATE NOT NULL,
                    date_fin DATE NOT NULL,
                    statut TEXT NOT NULL DEFAULT 'en_cours',
                    date_validation DATETIME DEFAULT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (stagiaire_id) REFERENCES stagiaires (id) ON DELETE CASCADE,
                    FOREIGN KEY (encadrant_id) REFERENCES encadrants (id) ON DELETE CASCADE,
                    FOREIGN KEY (sujet_id) REFERENCES sujets_stage (id) ON DELETE CASCADE
                );

                CREATE TABLE IF NOT EXISTS pointages (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    stage_id INTEGER NOT NULL,
                    stagiaire_id INTEGER NOT NULL,
                    date_pointage DATE NOT NULL,
                    statut TEXT NOT NULL DEFAULT 'present',
                    remarque TEXT DEFAULT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE(stagiaire_id, date_pointage),
                    FOREIGN KEY (stage_id) REFERENCES stages (id) ON DELETE CASCADE,
                    FOREIGN KEY (stagiaire_id) REFERENCES stagiaires (id) ON DELETE CASCADE
                );

                CREATE TABLE IF NOT EXISTS taches (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    stage_id INTEGER NOT NULL,
                    titre TEXT NOT NULL,
                    description TEXT DEFAULT NULL,
                    date_limite DATE NOT NULL,
                    statut TEXT NOT NULL DEFAULT 'a_faire',
                    progression INTEGER NOT NULL DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (stage_id) REFERENCES stages (id) ON DELETE CASCADE
                );

                CREATE TABLE IF NOT EXISTS evaluations (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    stage_id INTEGER NOT NULL UNIQUE,
                    encadrant_id INTEGER NOT NULL,
                    note_technique INTEGER NOT NULL,
                    note_comportement INTEGER NOT NULL,
                    note_assiduite INTEGER NOT NULL,
                    note_globale REAL NOT NULL,
                    commentaire TEXT DEFAULT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (stage_id) REFERENCES stages (id) ON DELETE CASCADE,
                    FOREIGN KEY (encadrant_id) REFERENCES encadrants (id) ON DELETE CASCADE
                );

                CREATE TABLE IF NOT EXISTS documents (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    stagiaire_id INTEGER DEFAULT NULL,
                    stage_id INTEGER DEFAULT NULL,
                    titre TEXT NOT NULL,
                    type_document TEXT NOT NULL,
                    chemin_fichier TEXT NOT NULL,
                    taille_octets INTEGER NOT NULL DEFAULT 0,
                    permanent INTEGER NOT NULL DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (stagiaire_id) REFERENCES stagiaires (id) ON DELETE SET NULL,
                    FOREIGN KEY (stage_id) REFERENCES stages (id) ON DELETE SET NULL
                );

                CREATE TABLE IF NOT EXISTS messages (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    stage_id INTEGER NOT NULL,
                    expediteur_id INTEGER NOT NULL,
                    destinataire_id INTEGER NOT NULL,
                    message TEXT NOT NULL,
                    lu INTEGER DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (stage_id) REFERENCES stages (id) ON DELETE CASCADE,
                    FOREIGN KEY (expediteur_id) REFERENCES users (id) ON DELETE CASCADE,
                    FOREIGN KEY (destinataire_id) REFERENCES users (id) ON DELETE CASCADE
                );

                CREATE TABLE IF NOT EXISTS notifications (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER NOT NULL,
                    titre TEXT NOT NULL,
                    message TEXT NOT NULL,
                    lien TEXT DEFAULT NULL,
                    lu INTEGER DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
                );
            ");

            // Données démo SQLite
            $hash = '$2y$10$m017hMmP3WFhwx0UIUIhROAG3ZSb2YSuu5DKiGeVac9oVeTvxU7PW';
            $pdo->exec("
                INSERT INTO users (id, role, nom, prenom, email, password, telephone) VALUES
                (1, 'encadrant', 'ALAMI', 'Karim', 'encadrant@ocp.ma', '{$hash}', '0661123456'),
                (2, 'stagiaire', 'BENNANI', 'Sarah', 'stagiaire@ocp.ma', '{$hash}', '0665987654'),
                (3, 'stagiaire', 'EL AMRANI', 'Youssef', 'youssef@ocp.ma', '{$hash}', '0667112233'),
                (4, 'stagiaire', 'CHAOUKI', 'Yasmine', 'yasmine.chaouki@ocp.ma', '{$hash}', '0612349988');

                INSERT INTO encadrants (id, user_id, departement, fonction) VALUES
                (1, 1, 'Digitalisation OCP Group', 'Chef de Projet Senior DX');

                INSERT INTO stagiaires (id, user_id, cin, universite, filiere, niveau_etudes, type_stage) VALUES
                (1, 2, 'BJ543210', 'EMSI Casablanca', 'Génie Informatique & Web', 'Bac +5', 'PFE'),
                (2, 3, 'EE987654', 'UM6P Benguerir', 'Data Science & AI', 'Bac +4', 'PFA'),
                (3, 4, 'CD112233', 'ENSIAS Rabat', 'Génie Software & Cloud Computing', 'Bac +5', 'PFE');

                INSERT INTO sujets_stage (id, encadrant_id, titre, description, domaine) VALUES
                (1, 1, 'Conception & Déploiement de la Plateforme de Gestion des Stagiaires', 'Développement d’une solution web PHP/MySQL responsive centralisant le recrutement, le pointage, le suivi des tâches et l’évaluation des stagiaires pour l’entité Digitalisation OCP.', 'Développement Web Full-Stack'),
                (2, 1, 'Analyse Prédictive de la Maintenance Industrielle par Machine Learning', 'Mise en place d’algorithmes prédictifs pour optimiser la disponibilité des équipements de la chaîne logistique OCP.', 'IA & Data Science'),
                (3, 1, 'Supervision IoT & Monitoring Temps Réel des Pipelines OCP', 'Création d’un tableau de bord de supervision intelligente des capteurs IoT des pipelines OCP avec alertes temps réel.', 'IoT & Data Analytics');

                INSERT INTO stages (id, stagiaire_id, encadrant_id, sujet_id, date_debut, date_fin, statut) VALUES
                (1, 1, 1, 1, '2026-02-01', '2026-07-31', 'en_cours'),
                (2, 2, 1, 2, '2026-03-01', '2026-06-30', 'en_cours'),
                (3, 3, 1, 3, DATE('now', '-3 months'), DATE('now', '+3 days'), 'en_cours');

                INSERT INTO pointages (stage_id, stagiaire_id, date_pointage, statut, remarque) VALUES
                (1, 1, DATE('now', '-4 days'), 'present', 'Arrivée à 08h30 - Présence validée'),
                (1, 1, DATE('now', '-3 days'), 'present', 'Présence OK'),
                (1, 1, DATE('now', '-2 days'), 'retard', 'Arrivée à 09h15 - Retard transport'),
                (1, 1, DATE('now', '-1 days'), 'present', 'Travail sur le rapport intermédiaire'),
                (1, 1, DATE('now'), 'present', 'Présent aujourd’hui');

                INSERT INTO taches (stage_id, titre, description, date_limite, statut, progression) VALUES
                (1, 'Modélisation de la base de données MySQL', 'Concevoir le MCD/MLD respectant les 10 règles de gestion OCP.', DATE('now', '-5 days'), 'termine', 100),
                (1, 'Implémentation de l’architecture PHP MVC', 'Structure des répertoires, contrôleurs, modèles et système de routage.', DATE('now', '-2 days'), 'termine', 100),
                (1, 'Développement du Module Stagiaire & Encadrant', 'Création des interfaces Bootstrap 5 et intégration de la messagerie AJAX.', DATE('now', '+5 days'), 'en_cours', 75),
                (1, 'Rédaction & Dépôt du Rapport Final PDF', 'Finaliser la rédaction du document et soumettre la version PDF finale.', DATE('now', '+15 days'), 'a_faire', 0);

                INSERT INTO documents (stagiaire_id, stage_id, titre, type_document, chemin_fichier, taille_octets, permanent) VALUES
                (1, 1, 'Carte CIN', 'cin', 'sample_cin.pdf', 1024000, 0),
                (1, 1, 'Convention de Stage OCP 2026', 'convention', 'sample_convention.pdf', 2048000, 0),
                (1, 1, 'Rapport d’avancement Mi-Parcours', 'rapport_intermediaire', 'sample_rapport_inter.pdf', 3500000, 0);

                INSERT INTO messages (stage_id, expediteur_id, destinataire_id, message, lu) VALUES
                (1, 1, 2, 'Bonjour Sarah, bienvenue au sein de l’équipe Digitalisation OCP ! As-tu pu prendre connaissance du sujet de stage ?', 1),
                (1, 2, 1, 'Bonjour M. ALAMI, oui parfaitement. J’ai déjà démarré l’analyse du MCD et des règles de gestion.', 1),
                (1, 1, 2, 'Parfait. N’hésite pas à faire un premier pointage et à mettre à jour la progression de tes tâches.', 1);

                INSERT INTO notifications (user_id, titre, message, lien, lu) VALUES
                (2, 'Bienvenue sur la plateforme OCP', 'Votre stage a été activé sous la tutelle de M. Karim ALAMI.', 'stagiaire/me_sujet', 1),
                (2, 'Nouvelle tâche assignée', 'L’encadrant vous a assigné la tâche Développement du Module Stagiaire & Encadrant.', 'stagiaire/taches', 0),
                (1, 'Nouveau document déposé', 'Le stagiaire Sarah BENNANI a déposé son Rapport d’avancement Mi-Parcours.', 'encadrant/stagiaire_detail?id=1', 0);
            ");

            // Auto-migration : S'assurer que la colonne date_validation existe sur la table stages
            try {
                $pdo->exec("ALTER TABLE stages ADD COLUMN date_validation DATETIME DEFAULT NULL");
            } catch (Exception $e) {
                // La colonne existe déjà
            }

            return "Base de données SQLite initialisée avec succès.";
        }
    } catch (Exception $e) {
        return "Erreur d'initialisation BDD : " . $e->getMessage();
    }
}

// Appel automatique à l'inclusion (silencieux sur le web)
if (php_sapi_name() === 'cli') {
    echo initDatabase() . PHP_EOL;
}
