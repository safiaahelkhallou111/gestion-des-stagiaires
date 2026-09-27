# Gestion des Stagiaires OCP

Plateforme web de gestion des stagiaires pour l'entité Digitalisation & Systèmes d'Information de l'OCP Group.

Elle permet de centraliser la gestion des candidatures, des stages, des pointages, des tâches, des évaluations, des documents et de la communication entre stagiaires et encadrants.

## Aperçu

- Gestion des comptes stagiaires et encadrants
- Suivi des stages et affectations
- Pointage quotidien des stagiaires
- Suivi des tâches et progression
- Évaluations avec critères multiples
- Gestion des documents téléversés
- Messagerie interne et notifications
- Interface sécurisée avec contrôle d'accès selon le rôle

## Stack technique

- Backend : PHP 8+
- Framework MVC natif
- Base de données : MySQL / SQLite en fallback automatique
- Frontend : Bootstrap 5, CSS personnalisé
- Sécurité : sessions PHP, hashage BCrypt, PDO préparé

## Fonctionnalités principales

### Pour les stagiaires

- Consultation du tableau de bord
- Suivi de son stage et de son sujet
- Déclaration de pointage
- Gestion des tâches assignées
- Téléversement de documents
- Consultation des notifications et messages
- Soumission du rapport final

### Pour les encadrants

- Gestion des stagiaires affectés
- Consultation des fiches détaillées
- Suivi du pointage et des tâches
- Évaluation des stagiaires
- Gestion des sujets de stage
- Messagerie interne avec les stagiaires

## Prérequis

- PHP 8.x
- MySQL 8 ou SQLite
- XAMPP ou un serveur local PHP
- Navigateur moderne

## Installation

### 1. Cloner le projet

```bash
git clone https://github.com/safiaahelkhallou111/gestion-des-stagiaires.git
cd gestion-des-stagiaires
```

### 2. Démarrer le serveur

#### Option 1 : PHP intégré

```powershell
& "C:\xampp\php\php.exe" -S localhost:8000 -t public
```

#### Option 2 : XAMPP

Placez le projet dans le dossier `C:\xampp\htdocs\` puis ouvrez :

```text
http://localhost/gestion-des-stagiaires/
```

### 3. Accéder à l'application

Ouvrez votre navigateur sur :

```text
http://localhost:8000
```

> La base de données est créée automatiquement si nécessaire et les données de démonstration sont initialisées au premier accès.

## Comptes de démonstration

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Encadrant OCP | `encadrant@ocp.ma` | `Password123!` |
| Stagiaire (PFE) | `stagiaire@ocp.ma` | `Password123!` |
| Stagiaire (PFA) | `youssef@ocp.ma` | `Password123!` |

## Structure du projet

```text
gestion-des-stagiaires/
├── config/
│   ├── config.php
│   └── database.php
├── controllers/
├── models/
├── views/
├── public/
│   ├── index.php
│   ├── css/
│   ├── js/
│   └── uploads/
├── database/
│   ├── schema.sql
│   ├── init_db.php
│   └── ocp_stagiaires.sqlite
├── scripts/
├── README.md
├── .gitignore
└── LICENSE (si ajouté)
```

## Configuration de la base de données

Si vous utilisez MySQL, configurez les informations dans `config/database.php` :

```php
private static $host = '127.0.0.1';
private static $db_name = 'ocp_stagiaires';
private static $username = 'root';
private static $password = '';
```

Puis importer le schéma :

```bash
mysql -u root -p < database/schema.sql
```

## Sécurité

- Requêtes SQL préparées via PDO
- Hachage des mots de passe avec `password_hash`
- Contrôle d'accès par rôle
- Nettoyage des entrées/sorties avec `htmlspecialchars`
- Vérification des types et extensions de fichiers uploadés

## Développements futurs

- Profil administrateur / RH
- Notifications email via SMTP
- Export PDF / Excel
- Internationalisation
- Refactorisation vers un framework moderne

## Licence

Ce projet est destiné à un usage académique et de démonstration.

## Auteur

Projet développé dans le cadre de la gestion des stagiaires OCP.

## Contribution

Les contributions sont les bienvenues. Pour proposer une amélioration :

1. Fork le dépôt
2. Créez une branche
3. Effectuez vos changements
4. Ouvrez une pull request

---

Vous pouvez également visiter le dépôt GitHub ici :
https://github.com/safiaahelkhallou111/gestion-des-stagiaires
