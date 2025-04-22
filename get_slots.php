<?php
// Connexion à la base de données
require 'db.php';

// Préparer la requête SQL pour récupérer les créneaux horaires disponibles
$query = "SELECT id, slot_date, start_time, end_time FROM slots WHERE available = 1";

// Exécuter la requête
if ($result = $conn->query($query)) {

    // Vérifier si des créneaux ont été trouvés
    if ($result->num_rows > 0) {
        $slots = [];

        // Récupérer les données et les formater pour le calendrier
        while ($row = $result->fetch_assoc()) {
            $slots[] = [
                'id' => $row['id'],
                'date' => $row['slot_date'],
                'start_time' => $row['start_time'],
                'end_time' => $row['end_time'],
                'end' => $row['slot_date'] . ' ' . $row['end_time'],  // Date + heure de fin pour l'événement
            ];
        }

        // Retourner les créneaux sous format JSON
        echo json_encode($slots);
    } else {
        // Si aucun créneau disponible
        echo json_encode([]);
    }

    // Libérer le résultat
    $result->free();

} else {
    // Si la requête échoue
    echo json_encode(['error' => 'Erreur lors de la récupération des créneaux.']);
}

// Fermer la connexion à la base de données
$conn->close();
?>
