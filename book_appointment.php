<?php
require 'db.php'; // Connexion à la base de données
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver un Rendez-vous</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.css" rel="stylesheet" />
</head>
<body>

    <!-- Bouton de retour à l'accueil -->
    <a href="profile.php" class="btn btn-info">Retour au Profil</a>

    <h2>Réservez un Rendez-vous</h2>

    <!-- Affichage du calendrier -->
    <div id="calendar"></div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialiser le calendrier
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                events: function(start, end, timezone, callback) {
                    // Récupérer les créneaux horaires disponibles depuis la base de données
                    $.ajax({
                        url: 'get_slots.php', // Script PHP qui récupère les créneaux disponibles
                        dataType: 'json',
                        success: function(data) {
                            var events = [];
                            $(data).each(function() {
                                events.push({
                                    title: 'Disponible',
                                    start: $(this).date,
                                    end: $(this).end_time,
                                    slot_id: $(this).id // Ajouter l'ID du créneau pour chaque événement
                                });
                            });
                            callback(events);
                        }
                    });
                },
                dayClick: function(date, jsEvent, view) {
                    // Lorsque l'utilisateur clique sur une date, afficher les créneaux horaires disponibles
                    alert('Date sélectionnée: ' + date.format());
                }
            });
        });
    </script>
</body>
</html>
