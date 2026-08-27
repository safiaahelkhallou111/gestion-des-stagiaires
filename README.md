# Plateforme de Gestion des Stagiaires OCP

> **Entité Digitalisation & Systèmes d'Information — OCP Group**  
> Plateforme centralisée pour la gestion complète des stagiaires (candidature, suivi, pointage, évaluation, messagerie)

---

## Stack Technique

| Composant | Technologie |
|-----------|-------------|
| Backend   | PHP 8.2+ (MVC natif) |
| Base de données | MySQL 8 (ou SQLite automatique en fallback) |
| Frontend  | Bootstrap 5.3 + CSS Design System OCP |
| Sécurité  | Sessions PHP, BCrypt (`password_hash`), PDO préparé |

---

## Démarrage Rapide (3 étapes)

### Prérequis
- [XAMPP](https://www.apachefriends.org/) (PHP 8+ + MySQL) **ou** juste PHP 8+ en ligne de commande
- PHP 8.x disponible dans votre PATH (ou utilisable via `C:\xampp\php\php.exe`)

### 1. Cloner / Placer le projet

```
c:\Users\HP\Desktop\gestion des stagiaires\
```

### 2. Lancer le serveur de développement PHP

```powershell
# Depuis le répertoire du projet
& "C:\xampp\php\php.exe" -S localhost:8000 -t public
```

**Ou via XAMPP :** Copiez le dossier dans `C:\xampp\htdocs\gestion-stagiaires\` et accédez via `http://localhost/gestion-stagiaires/`

### 3. Accéder à l'application

Ouvrez votre navigateur sur :  
**http://localhost:8000**

> La base de données est automatiquement créée et initialisée avec des données de démonstration dès le premier accès.

---

## Comptes de Démonstration

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| Encadrant OCP | `encadrant@ocp.ma` | `Password123!` |
| Stagiaire (PFE) | `stagiaire@ocp.ma` | `Password123!` |
| Stagiaire (PFA) | `youssef@ocp.ma` | `Password123!` |

---

## Arborescence du Projet

```
gestion des stagiaires/
├── config/
│   ├── config.php          # Constantes, helpers (url, redirect, sanitize...)
│   └── database.php        # Connexion PDO (MySQL → SQLite auto-fallback)
├── controllers/
│   ├── AuthController.php          # Auth (login, register, forgot/reset password)
│   ├── StagiaireController.php     # Module Stagiaire complet
│   ├── EncadrantController.php     # Module Encadrant complet
│   ├── MessageController.php       # API AJAX messagerie
│   └── NotificationController.php  # API AJAX notifications
├── models/
│   ├── User.php            # Authentification et gestion profil
│   ├── Stagiaire.php       # Profil et stage actif
│   ├── Encadrant.php       # Profil, dashboard stats, liste stagiaires
│   ├── Stage.php           # Création/clôture stage (RG2, RG6)
│   ├── SujetStage.php      # CRUD sujets par encadrant (RG4)
│   ├── Pointage.php        # Présence quotidienne unique (RG7)
│   ├── Tache.php           # Suivi avancement 0-100%
│   ├── Evaluation.php      # Notation (RG8), 3 critères
│   ├── Document.php        # Upload & gestion fichiers (RG10)
│   ├── Message.php         # Messagerie privée (RG9)
│   └── Notification.php    # Alertes & notifications
├── views/
│   ├── layouts/            # header, sidebar, navbar, footer
│   ├── auth/               # login, register, forgot/reset password
│   ├── stagiaire/          # Dashboard, Sujet, Tâches, Pointage, Documents, Messages, Notifications
│   ├── encadrant/          # Dashboard KPIs, Stagiaires, Fiche détaillée, Sujets, Évaluations, Pointages, Messages
│   └── errors/             # 403, 404
├── public/
│   ├── index.php           # Front Controller (routeur)
│   ├── css/style.css       # Design System OCP (Vert #008751, dark mode)
│   ├── js/
│   │   ├── app.js          # Interactions UI, dismiss alerts, tooltips
│   │   └── chat.js         # Messagerie temps réel (polling AJAX 3s)
│   └── uploads/
│       ├── documents/      # Fichiers déposés par les stagiaires
│       └── avatars/        # Photos de profil
└── database/
    ├── schema.sql                  # Schéma MySQL complet avec FK et seed data
    ├── init_db.php                 # Initialisation auto (MySQL + SQLite)
    └── ocp_stagiaires.sqlite       # BDD SQLite générée automatiquement (si MySQL absent)
```

---

## Règles de Gestion Implémentées

| # | Règle | Implémentation |
|---|-------|----------------|
| RG1 | Un utilisateur = un seul rôle | Colonne `role` ENUM, contrôle de session |
| RG2 | Un stagiaire = un seul stage actif | `Stage::create()` vérifie avant insertion |
| RG3 | Stage obligatoirement lié à stagiaire + encadrant + sujet | FK + NOT NULL sur `stages` |
| RG4 | Un sujet appartient à son encadrant | FK `encadrant_id` dans `sujets_stage` |
| RG5 | Encadrant ne consulte que ses stagiaires | Vérification `encadrant_id` dans chaque requête |
| RG6 | Statut stage : en_cours → terminé (irréversible) | `Stage::updateStatut()` refuse la régression |
| RG7 | Pointage unique par date + stagiaire | UNIQUE `(stagiaire_id, date_pointage)` |
| RG8 | Évaluation unique par stage, par son encadrant | UNIQUE `stage_id` dans `evaluations` |
| RG9 | Messagerie uniquement stagiaire ↔ son encadrant | `Message::envoyer()` vérifie les participants |
| RG10 | Document rattaché à un stagiaire + horodaté | FK + `TIMESTAMP DEFAULT CURRENT_TIMESTAMP` |

---

## Configuration MySQL (si MySQL disponible)

Éditez `config/database.php` :

```php
private static $host = '127.0.0.1';
private static $db_name = 'ocp_stagiaires';
private static $username = 'root';
private static $password = '';  // ← votre mot de passe root MySQL
```

Puis importez le schéma :

```bash
# Via mysql CLI
mysql -u root -p < database/schema.sql

# Ou importez database/schema.sql via phpMyAdmin
```

---

## Sécurité

- ✅ **PDO préparé** : Toutes les requêtes SQL utilisent des requêtes préparées (zero injection SQL)
- ✅ **BCrypt** : Mots de passe hashés avec `password_hash(PASSWORD_BCRYPT)` 
- ✅ **Sessions PHP** : Contrôle d'accès par rôle à chaque requête
- ✅ **XSS** : Toutes les sorties HTML passent par `sanitize()` (`htmlspecialchars`)
- ✅ **Upload sécurisé** : Vérification d'extension, renommage aléatoire
- ✅ **Contrôle cross-tenant** : Un encadrant ne peut jamais accéder aux données d'un stagiaire qui ne lui est pas affecté

---

## Évolutions Futures Planifiées

- 🔄 Profil Administrateur / RH (gestion globale)
- 📧 Envoi d'emails via SMTP (notifications réelles)
- 📊 Export Excel/PDF des listes et évaluations
- 🌐 Internationalisation (Français/Arabe/Anglais)
- 🚀 Migration vers Laravel ou Symfony
