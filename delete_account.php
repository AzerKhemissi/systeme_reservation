<?php
session_start();
include('db.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Supprimer l'utilisateur de la base de données
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);  // "i" pour entier
if ($stmt->execute()) {
    // Déconnexion de l'utilisateur
    session_destroy();  // Détruire la session

    // Rediriger vers la page d'accueil
    header("Location: index.php");
    exit();
} else {
    echo "Erreur lors de la suppression du compte.";
}

$stmt->close();
$conn->close();
