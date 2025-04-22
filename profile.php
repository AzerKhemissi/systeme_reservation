<?php
session_start();
include('db.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur depuis la base de données
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);  // "i" pour entier
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();  // Récupérer le résultat sous forme de tableau associatif
} else {
    // Gérer l'erreur d'exécution de la requête
    die("Erreur de récupération des informations utilisateur.");
}

// Récupérer les rendez-vous de l'utilisateur
$appointment_stmt = $conn->prepare("SELECT * FROM appointments WHERE user_id = ? ORDER BY appointment_date DESC");
$appointment_stmt->bind_param("i", $user_id);
if ($appointment_stmt->execute()) {
    $appointment_result = $appointment_stmt->get_result();
} else {
    // Gérer l'erreur d'exécution de la requête
    die("Erreur de récupération des rendez-vous.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil</title>
    <link rel="stylesheet" href="style.css"> <!-- Si tu as un fichier CSS -->
    <style>
        /* Styles CSS pour rendre les boutons ergonomiques */
        .btn {
            padding: 10px 20px;
            margin: 10px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-info {
            background-color: #17a2b8;
            color: white;
        }

        .btn-info:hover {
            background-color: #138496;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-back {
            background-color: #f8f9fa;
            color: #343a40;
            border: 1px solid #ddd;
        }

        .btn-back:hover {
            background-color: #e2e6ea;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .alert {
            padding: 15px;
            margin: 10px 0;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            margin: 5px 0;
        }

        .appointments-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .appointments-table th, .appointments-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .appointments-table th {
            background-color: #f2f2f2;
        }

        .appointments-table td {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        // Affichage du message de succès ou d'erreur
        if (isset($_GET['message'])) {
            echo '<div class="alert">' . htmlspecialchars($_GET['message']) . '</div>';
        }
        ?>

        <h1>Bienvenue, <?php echo htmlspecialchars($user['first_name']); ?> <?php echo htmlspecialchars($user['last_name']); ?> !</h1>

        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
        <p>Date de naissance: <?php echo htmlspecialchars($user['birth_date']); ?></p>
        <p>Adresse: <?php echo htmlspecialchars($user['address']); ?></p>
        <p>Téléphone: <?php echo htmlspecialchars($user['phone']); ?></p>

        <!-- Bouton pour modifier le profil -->
        <a href="edit_profile.php" class="btn btn-info">Modifier mon profil</a>

        <!-- Lien vers la page de réservation -->
        <a href="book_appointment.php" class="btn btn-primary">Réserver un rendez-vous</a>

        <!-- Section des rendez-vous -->
        <h2>Mes Rendez-vous</h2>

        <?php if ($appointment_result->num_rows > 0): ?>
            <table class="appointments-table">
                <thead>
                    <tr>
                        <th>Date du Rendez-vous</th>
                        <th>Heure de début</th>
                        <th>Heure de fin</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($appointment = $appointment_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['start_time']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['end_time']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                            <td>
                                <!-- Bouton pour annuler un rendez-vous -->
                                <?php if ($appointment['status'] === 'scheduled'): ?>
                                    <form method="POST" action="cancel_appointment.php">
                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                        <button type="submit" class="btn btn-danger">Annuler</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun rendez-vous programmé.</p>
        <?php endif; ?>

        <!-- Bouton pour déconnecter et retourner à l'accueil -->
        <a href="logout.php" class="btn btn-back">Déconnecter</a>

        <!-- Bouton de suppression du compte -->
        <form method="POST" action="delete_account.php" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')">
            <button type="submit" class="btn btn-danger">Supprimer mon compte</button>
        </form>
    </div>
</body>
</html>
