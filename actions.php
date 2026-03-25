<?php
session_start();
require 'db.php';

// Vérification sécurité : utilisateur connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: index.php");
    exit();
}
$user_id = $_SESSION['utilisateur_id'];

// --- TRAITEMENT DES FORMULAIRES ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Création de PROJET
    if (isset($_POST['action']) && $_POST['action'] === 'add_project') {
        $title = htmlspecialchars($_POST['new_project_title']);
        $desc = htmlspecialchars($_POST['new_project_desc']);
        
        // Note: On utilise 'nom_projet' conformément à votre structure SQL
        $stmt = $pdo->prepare("INSERT INTO projets (nom_projet, description, utilisateur_id) VALUES (?, ?, ?)");
        $stmt->execute([$title, $desc, $user_id]);
        
        // Redirection vers le nouveau projet
        header("Location: nouveau_projet.php?project_id=" . $pdo->lastInsertId());
        exit();
    }

    // 2. Création de TÂCHE (Carte)
    if (isset($_POST['action']) && $_POST['action'] === 'add_task') {
        $project_id = intval($_POST['project_id']);
        $title = htmlspecialchars($_POST['task_title']);
        $desc = htmlspecialchars($_POST['task_desc']);
        $priority = $_POST['task_priority'];
        $status = $_POST['task_status']; // 'todo', 'inprogress', 'done'

        if ($project_id > 0) {
            $stmt = $pdo->prepare("INSERT INTO taches (titre, description, priorite, statut, projet_id, utilisateur_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $desc, $priority, $status, $project_id, $user_id]);
            header("Location: nouveau_projet.php?project_id=" . $project_id);
            exit();
        }
    }
}

// --- DEPLACEMENT TACHE (Remplacement Drag & Drop) ---
if (isset($_GET['action']) && $_GET['action'] === 'move_task') {
    $task_id = intval($_GET['id']);
    $direction = $_GET['direction']; // 'next' ou 'prev'
    $project_id = intval($_GET['project_id']);
    
    // Récupérer le statut actuel
    $stmt = $pdo->prepare("SELECT statut FROM taches WHERE id = ? AND utilisateur_id = ?");
    $stmt->execute([$task_id, $user_id]);
    $task = $stmt->fetch();

    if ($task) {
        $status_map = ['todo', 'inprogress', 'done'];
        $current_index = array_search($task['statut'], $status_map);
        
        if ($direction === 'next' && $current_index < 2) $current_index++;
        if ($direction === 'prev' && $current_index > 0) $current_index--;
        
        $new_status = $status_map[$current_index];
        
        $stmt = $pdo->prepare("UPDATE taches SET statut = ? WHERE id = ?");
        $stmt->execute([$new_status, $task_id]);
    }
    header("Location: nouveau_projet.php?project_id=" . $project_id);
    exit();
}

// --- SUPPRESSIONS (via GET) ---
if (isset($_GET['action']) && $_GET['action'] === 'delete_task') {
    $task_id = intval($_GET['id']);
    $project_id = intval($_GET['project_id']);
    
    $stmt = $pdo->prepare("DELETE FROM taches WHERE id = ? AND utilisateur_id = ?");
    $stmt->execute([$task_id, $user_id]);
    
    header("Location: nouveau_projet.php?project_id=" . $project_id);
    exit();
}

// Si aucune action, retour au tableau de bord
header("Location: nouveau_projet.php");
exit();
?>