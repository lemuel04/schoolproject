# 🎯 SONGRE - Application Kanban

Une application de gestion de projets avec tableau Kanban développée en **PHP pur** et **MySQL**.

## 📋 Installation

### 1. Configuration de la base de données

1. Ouvrez `phpMyAdmin`.
2. Créez une base de données nommée `songre_db`.
3. Importez le fichier `songre_db.sql` situé à la racine du projet.
4. Vérifiez le fichier `db.php` pour les identifiants de connexion (par défaut: root, sans mot de passe).

### 2. Lancement

Accédez à l'application via votre navigateur :

```
http://localhost/SONGRE/index.php
```

## 🔑 Données de connexion démo

- 📧 **Email:** demo@songre.com
- 🔑 **Mot de passe:** demo

## 📁 Structure du projet

```
SONGRE/
├── index.php              # Page d'inscription
├── login.php              # Page de connexion
├── nouveau_projet.php     # Tableau Kanban
├── logout.php             # Déconnexion
├── Qui_somme_nous.php     # À Propos
├── config.php             # Configuration BD
├── install.php            # Installation automatique
├── style.css              # Styles
├── bootstrap/             # Framework Bootstrap
├── api/                   # API endpoints
│   ├── auth.php           # Authentification
│   └── tasks.php          # Gestion tâches
└── songre_kanban.sql      # Schéma base de données
```

## 🎨 Fonctionnalités

✅ **Authentification**

- Inscription sécurisée
- Connexion avec hachage bcrypt
- Gestion de sessions

✅ **Tableau Kanban**

- 3 colonnes: À Faire, En Cours, Terminé
- Ajouter/Supprimer tâches
- Organiser par priorité
- Persistance en base de données

✅ **Design responsif**

- Interface moderne avec Bootstrap
- Adapté mobile/tablette/desktop
- Thème clair épuré

## 🗄️ Base de données

**Base:** `songre_db`

### Tables

| Table           | Description                                  |
| --------------- | -------------------------------------------- |
| `users`         | Utilisateurs avec email et mot de passe      |
| `projects`      | Projets des utilisateurs                     |
| `columns_board` | Colonnes Kanban (À Faire, En Cours, Terminé) |
| `tasks`         | Tâches organisées par colonne                |

## ⚙️ Configuration

Modifiez `config.php` si nécessaire:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'songre_db');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_PORT', 3306);
```

## 🚀 Technologies utilisées

- **Backend:** PHP 7.4+
- **Serveur BD:** MySQL
- **Frontend:** HTML5, CSS3, Bootstrap 5
- **Sécurité:** PDO,

## 📞 Support

Pour toute question, rendez-vous sur la page **À Propos** de l'application.

---

**© 2026 SONGRE - Gestion simple et efficace de projets**
