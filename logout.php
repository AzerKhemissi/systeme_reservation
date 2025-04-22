<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté (si une session existe)
if (isset($_SESSION['user_id'])) {
    // Détruire toutes les variables de session
    session_unset();

    // Détruire la session
    session_destroy();

    // Afficher un message de confirmation (optionnel)
    echo "Vous êtes maintenant déconnecté.";

    // Rediriger l'utilisateur vers la page de connexion
    header("Location: login.php");
    exit();
} else {
    // Si aucune session n'existe, rediriger directement vers la page de connexion
    header("Location: login.php");
    exit();
}
?>
