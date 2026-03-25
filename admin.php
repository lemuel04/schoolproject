<?php 
session_start();
require 'db.php';

// Récupération des utilisateurs (adaptation pour utiliser PDO)
// Note : J'utilise la table 'utilisateurs' car 'messages' n'existe pas dans la structure fournie

// --- ACTIONS ---
// 1. Suppression
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?")->execute([$id]);
    header("Location: admin.php");
    exit();
}

// 2. Ajout
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_user'])) {
    $email = htmlspecialchars($_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    try {
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (email, mot_de_passe) VALUES (?, ?)");
        $stmt->execute([$email, $pass]);
        $msg = "<div class='alert alert-success my-3'>Utilisateur ajouté avec succès !</div>";
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger my-3'>Erreur : Cet email existe déjà.</div>";
    }
}

$users = $pdo->query("SELECT * FROM utilisateurs ORDER BY id DESC")->fetchAll();
?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Administration - SONGRE</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <nav>
      <div class="logo">SONGRE Admin</div>
      <ul>
        <li><a href="index.php">Accueil</a></li>
        <li><a href="nouveau_projet.php">Tableau de bord</a></li>
        <li><a href="logout.php">Déconnexion</a></li>
      </ul>
    </nav>

    <div class="container mt-5 main-content">
      <div class="kanban-header">
        <h1 class="display-4 fw-bold text-primary">Administration</h1>
        <p class="lead text-muted">Gestion des utilisateurs inscrits</p>
        <?php echo $msg; ?>
        
        <!-- Formulaire Ajouter (Remplacement Modal) -->
        <details class="mb-4">
            <summary class="btn btn-primary"> + Ajouter un utilisateur </summary>
            <div class="card card-body mt-2" style="max-width: 500px; margin: 0 auto;">
                <form method="POST" action="admin.php">
                    <input type="hidden" name="new_user" value="1">
                    <div class="mb-3 text-start">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required placeholder="nom@exemple.com">
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" name="password" class="form-control" required placeholder="Mot de passe">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Enregistrer</button>
                </form>
            </div>
        </details>
      </div>

      <div class="card shadow-sm">
        <div class="card-header text-white" style="background: linear-gradient(135deg, #026aa7 0%, #005588 100%);">
            <h4 class="mb-0 fs-5">Liste des comptes</h4>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Email</th>
                  <th>Mot de passe (Hash)</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td class="text-muted small"><?php echo htmlspecialchars($row['mot_de_passe']); ?></td>
                        <td>
                            <a href="admin.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center py-3">Aucun utilisateur trouvé</td>
                    </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>


    <footer>
      <div class="container-fluid">
        <div class="footer-bottom text-center">
          <p>&copy; 2026 SONGRE. Tous droits réservés.</p>
        </div>
      </div>
    </footer>
  </body>
</html>