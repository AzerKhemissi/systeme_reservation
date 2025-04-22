<?php
session_start();
include('db.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Vérifier si l'ID du rendez-vous est présent
if (isset($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id'];

    // Vérifier si le rendez-vous appartient à l'utilisateur
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $appointment_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Si aucun rendez-vous trouvé, on arrête
    if ($result->num_rows == 0) {
        die("Rendez-vous introuvable ou vous n'êtes pas autorisé à annuler ce rendez-vous.");
    }

    // Annuler le rendez-vous en mettant à jour son statut
    $stmt = $conn->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ?");
    $stmt->bind_param("i", $appointment_id);
    if ($stmt->execute()) {
        header("Location: profile.php?message=Rendez-vous annulé avec succès.");
    } else {
        header("Location: profile.php?message=Erreur lors de l'annulation du rendez-vous.");
    }
}
?>
