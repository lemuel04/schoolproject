<?php
session_start();
require 'db.php';

// Vérification auth
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: index.php");
    exit();
}
$user_id = $_SESSION['utilisateur_id'];

// --- GESTION PROJET ---
// Recherche et récupération des projets
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT * FROM projets WHERE utilisateur_id = ?";
$params = [$user_id];

if ($search) {
    $sql .= " AND nom_projet LIKE ?";
    $params[] = "%$search%";
}
$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

// Sélection du projet actif (par défaut le dernier créé ou celui passé en GET)
$current_project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : (count($projects) > 0 ? $projects[0]['id'] : null);

// --- RÉCUPÉRATION DES TÂCHES ---
$tasks = ['todo' => [], 'inprogress' => [], 'done' => []];
if ($current_project_id) {
    $stmt = $pdo->prepare("SELECT * FROM taches WHERE projet_id = ? ORDER BY date_creation DESC");
    $stmt->execute([$current_project_id]);
    $all_tasks = $stmt->fetchAll();
    foreach ($all_tasks as $t) {
        $tasks[$t['statut']][] = $t;
    }
}
?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tableau de bord - SONGRE</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <nav>
      <div class="logo">SONGRE</div>
      <ul>
        <li><a href="nouveau_projet.php">Tableau de bord</a></li>
        <li><a href="Qui_somme_nous.html">Qui somme nous?</a></li>
        <li><a href="logout.php" class="btn btn-sm btn-outline-light text-white" style="border: 1px solid #cbd5f5;">Déconnexion</a></li>
      </ul>
    </nav>

    <!-- Section Kanban Board -->
    <div class="container-fluid mt-4 main-content">
      <div class="kanban-header">
        <h1 class="display-4 fw-bold text-primary">Tableau de Bord</h1>
        <p class="lead text-muted">Gestion simple des tâches</p>
        
        <!-- Contrôles (Recherche + Nouveau Projet) -->
        <div class="kanban-controls">
          <form action="nouveau_projet.php" method="GET" class="d-flex gap-2 search-box">
              <?php if($current_project_id): ?>
                  <input type="hidden" name="project_id" value="<?php echo $current_project_id; ?>">
              <?php endif; ?>
              <input type="text" name="search" class="form-control" placeholder="🔍 Rechercher un projet..." value="<?php echo htmlspecialchars($search); ?>">
              <button type="submit" class="btn btn-outline-primary">OK</button>
              <?php if($search): ?><a href="nouveau_projet.php" class="btn btn-outline-secondary">X</a><?php endif; ?>
          </form>
        </div>
        
        <!-- Formulaire Nouveau Projet (Remplacement Modal) -->
        <div class="mb-4 d-flex justify-content-center">
            <details class="w-100" style="max-width: 500px;">
                <summary class="btn btn-primary w-100">+ Nouveau Projet</summary>
                <div class="card card-body mt-2 shadow-sm text-start">
                    <form action="actions.php" method="POST">
                        <input type="hidden" name="action" value="add_project">
                        <div class="mb-3">
                            <label class="form-label">Titre du projet</label>
                            <input type="text" name="new_project_title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="new_project_desc" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Créer le projet</button>
                    </form>
                </div>
            </details>
        </div>

        <!-- Liste des projets (Navigation rapide) -->
        <?php if(count($projects) > 0): ?>
        <div class="mt-3 d-flex justify-content-center gap-2 flex-wrap">
            <span class="align-self-center text-muted small">Projets :</span>
            <?php foreach($projects as $proj): ?>
                <a href="nouveau_projet.php?project_id=<?php echo $proj['id']; ?>" 
                   class="btn btn-sm <?php echo ($current_project_id == $proj['id']) ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                   <?php echo htmlspecialchars($proj['nom_projet']); ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <?php if($search): ?>
                <div class="alert alert-warning mt-3 text-center">Aucun projet trouvé pour "<?php echo htmlspecialchars($search); ?>"</div>
            <?php endif; ?>
        <?php endif; ?>
      </div>

      <div class="kanban-board-wrapper">
        <div class="kanban-board">
          <div class="row">
            <!-- Colonne À FAIRE -->
            <div class="col-md-4">
              <div class="kanban-column">
                <div class="column-header bg-danger">
                  <h3>À FAIRE <span class="badge-count">0</span></h3>
                </div>
                <div class="column-content">
                  <?php renderTasks($tasks['todo'], $current_project_id); ?>
                </div>
              </div>
            </div>

            <!-- Colonne EN COURS -->
            <div class="col-md-4">
              <div class="kanban-column">
                <div class="column-header bg-warning">
                  <h3>EN COURS <span class="badge-count">0</span></h3>
                </div>
                <div class="column-content">
                  <?php renderTasks($tasks['inprogress'], $current_project_id); ?>
                </div>
              </div>
            </div>

            <!-- Colonne TERMINÉ -->
            <div class="col-md-4">
              <div class="kanban-column">
                <div class="column-header bg-success">
                  <h3>TERMINÉ <span class="badge-count">0</span></h3>
                </div>
                <div class="column-content">
                  <?php renderTasks($tasks['done'], $current_project_id); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <footer>
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3 footer-section">
            <h5>SONGRE</h5>
            <p class="footer-desc">
              Organisez vos projets avec un tableau Kanban moderne et intuitif.
            </p>
          </div>
          <div class="col-md-3 footer-section">
            <h5>Liens rapides</h5>
            <ul class="footer-links">
              <li><a href="index.php">Accueil</a></li>
              <li><a href="nouveau_projet.php">Nouveau Projet</a></li>
              <li><a href="Qui_somme_nous.html">Qui sommes nous?</a></li>
            </ul>
          </div>

          <div class="col-md-3 footer-section">
            <h5>Contact</h5>
            <div class="contact-info">
              <p><i class="phone">📞</i> +226 66 91 33 22</p>
              <p><i class="phone">📞</i> +226 66 95 14 97</p>
              <p><i class="email">✉️</i> info@songre.com</p>
            </div>
          </div>
          <div class="col-md-3 footer-section">
            <h5>Nous suivre</h5>
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
          <p>&copy; 2026 SONGRE. Tous droits réservés.</p>
        </div>
      </div>
    </footer>
  </body>
</html>

<?php
// Fonction helper pour afficher les tâches
function renderTasks($taskList, $projectId) {
    if (empty($taskList)) return;
    
    foreach ($taskList as $task) {
        $priorityClass = 'priority-medium';
        if ($task['priorite'] === 'high') $priorityClass = 'priority-high';
        if ($task['priorite'] === 'low') $priorityClass = 'priority-low';
        
        $completedClass = ($task['statut'] === 'done') ? 'completed' : '';
        
        echo '<div class="kanban-card ' . $completedClass . '">';
        echo '<div class="card-priority ' . $priorityClass . '" title="Priorité">!</div>';
        echo '<h5>' . htmlspecialchars($task['titre']) . '</h5>';
        if (!empty($task['description'])) {
            echo '<p>' . htmlspecialchars($task['description']) . '</p>';
        }
        
        // Barre de déplacement (Remplacement du Drag & Drop)
        echo '<div class="card-footer">';
        echo '<span class="card-date">' . date('d/m', strtotime($task['date_creation'])) . '</span>';
        echo '<div class="d-flex gap-2 align-items-center">';
            // Flèche retour
            if ($task['statut'] !== 'todo') {
                echo '<a href="actions.php?action=move_task&id=' . $task['id'] . '&direction=prev&project_id='.$projectId.'" class="text-secondary text-decoration-none" title="Précédent">⬅️</a>';
            }
            // Flèche avancer
            if ($task['statut'] !== 'done') {
                echo '<a href="actions.php?action=move_task&id=' . $task['id'] . '&direction=next&project_id='.$projectId.'" class="text-secondary text-decoration-none" title="Suivant">➡️</a>';
            }
            // Supprimer
        echo '<a href="actions.php?action=delete_task&id=' . $task['id'] . '&project_id='.$projectId.'" class="text-danger" style="text-decoration:none;" onclick="return confirm(\'Supprimer ?\')">🗑️</a>';
        echo '</div>'; // Fin div boutons
        echo '</div>';
        echo '</div>';
    }
}
