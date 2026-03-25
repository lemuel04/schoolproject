<?php
session_start();
require 'db.php';

// Si déjà connecté, redirection vers le tableau de bord
if (isset($_SESSION['utilisateur_id'])) {
    header("Location: nouveau_projet.php");
    exit();
}

$message = "";
// Détermine le mode du formulaire (login ou register) via GET, avec 'login' par défaut.
$form_mode = (isset($_GET['action']) && $_GET['action'] === 'register') ? 'register' : 'login';

// Traitement du formulaire (Inscription ou Connexion)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $action = $_POST['action']; // 'register' ou 'login'

    if ($action === 'register') {
        // Inscription
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (email, mot_de_passe) VALUES (?, ?)");
            $stmt->execute([$email, $hash]);
            $message = "<div class='alert alert-success'>Compte créé ! Connectez-vous maintenant.</div>";
            $form_mode = 'login'; // Après une inscription réussie, on affiche le formulaire de connexion.
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Cet email est déjà utilisé.</div>";
            $form_mode = 'register'; // En cas d'erreur, on reste sur le formulaire d'inscription.
        }
    } elseif ($action === 'login') {
        // Connexion
        $form_mode = 'login'; // En cas d'erreur, on reste sur le formulaire de connexion.
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['utilisateur_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            header("Location: nouveau_projet.php");
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Email ou mot de passe incorrect.</div>";
        }
    }
}
?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SONGRE-Gestion des projets</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <nav>
      <div class="logo">SONGRE</div>
      <ul>
        <li><a href="#">Accueil</a></li>
        <li><a href="nouveau_projet.php">Nouveau Projet</a></li>
        <li><a href="Qui_somme_nous.html">Qui somme nous?</a></li>
      </ul>
    </nav>
    <!-- Section Accueil -->
    <div class="container-fluid mt-4 section main-content" id="accueil">
      <div class="container">
        <div class="text-center mb-4">
          <h1 class="display-4 fw-bold text-primary">Bienvenue sur SONGRE</h1>
          <p class="lead text-muted">
            Organisez vos projets avec un tableau Kanban
          </p>
        </div>
        <div class="container mt-5">
          <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
              <?php echo $message; ?>
              <div class="card">
                <div class="card-header text-center">
                  <!-- Onglets simples pour basculer (visuel) -->
                  <h4>Espace Membre</h4>
                </div>
                <div class="card-body">
                  <form method="POST" action="index.php">
                    <input type="hidden" name="action" value="<?php echo $form_mode; ?>">
                    <div class="mb-3">
                      <label for="email" class="form-label"
                        >Adresse e-mail</label
                      >
                      <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        placeholder="Entrez votre e-mail"
                        required
                      />
                    </div>
                    <div class="mb-3">
                      <label for="password" class="form-label"
                        >Mot de passe</label
                      >
                      <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="<?php echo $form_mode === 'register' ? 'Créez un mot de passe' : 'Entrez votre mot de passe'; ?>"
                        required
                      />
                    </div>
                    <div class="d-grid gap-2">
                      <button type="submit" class="btn btn-primary">
                        <?php echo $form_mode === 'register' ? "S'inscrire" : 'Se connecter'; ?>
                      </button>
                      <?php if ($form_mode === 'login'): ?>
                        <a href="index.php?action=register" class="btn btn-outline-secondary btn-sm">Pas encore de compte ? S'inscrire</a>
                      <?php else: ?>
                        <a href="index.php?action=login" class="btn btn-outline-secondary btn-sm">Déjà un compte ? Se connecter</a>
                      <?php endif; ?>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Footer -->
    <footer>
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3 footer-section">
            <h5>SONGRE</h5>
            <p class="footer-desc small">
              Gestion simple et efficace de projets.
            </p>
          </div>
          <div class="col-md-3 footer-section">
            <h5>Liens</h5>
            <ul class="footer-links small">
              <li><a href="index.php">Accueil</a></li>
              <li><a href="nouveau_projet.php">Nouveau Projet</a></li>
              <li><a href="Qui_somme_nous.html">À Propos</a></li>
            </ul>
          </div>
          <div class="col-md-3 footer-section">
            <h5>Contact</h5>
            <div class="contact-info small">
              <p class="mb-1">📞 +226 66 91 33 22</p>
              <p class="mb-1">📞 +226 66 95 14 97</p>
              <p>✉️ info@songre.com</p>
            </div>
          </div>
          <div class="col-md-3 footer-section">
            <h5>Réseaux</h5>
            <div class="social-links">
              <a href="#" class="social-icon">f</a>
              <a href="#" class="social-icon">𝕏</a>
              <a href="#" class="social-icon">📷</a>
              <a href="#" class="social-icon">in</a>
            </div>
          </div>
        </div>
        <hr class="footer-divider" />
        <div class="footer-bottom text-center">
          <p class="small mb-0">&copy; 2026 SONGRE. Tous droits réservés.</p>
        </div>
      </div>
    </footer>
  </body>
</html>
