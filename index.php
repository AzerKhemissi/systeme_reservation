<?php
session_start();
require 'db.php';  // Connexion à la base de données

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue au Système de Réservation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Bienvenue dans notre Système de Réservation</h1>
        <p class="lead">Vous pouvez créer un compte ou vous connecter pour commencer à prendre des rendez-vous.</p>

        <div class="mt-4">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="btn btn-primary">Créer un Compte</a>
                <a href="login.php" class="btn btn-secondary">Se Connecter</a>
            <?php else: ?>
                <?php
                // Récupérer l'ID utilisateur à partir de la session
                $user_id = $_SESSION['user_id'];

                // Préparer la requête pour récupérer le prénom et le nom de l'utilisateur
                $stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
                $stmt->bind_param("i", $user_id);  // Lier l'ID utilisateur
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();

                // Si l'utilisateur existe, afficher son prénom et nom
                if ($user) {
                    echo "<p>Bonjour, " . htmlspecialchars($user['first_name']) . " " . htmlspecialchars($user['last_name']) . "!</p>";
                } else {
                    echo "<p>Utilisateur non trouvé.</p>";
                }
                ?>
                <a href="profile.php" class="btn btn-info">Voir Mon Profil</a>
                <a href="logout.php" class="btn btn-danger">Se Déconnecter</a>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
