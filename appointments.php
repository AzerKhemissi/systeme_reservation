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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
        }

        #calendar {
            max-width: 900px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .btn {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }

        .btn-info {
            background-color: #17a2b8;
        }

        .alert {
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

    <!-- Bouton de retour à l'accueil -->
    <a href="profile.php" class="btn btn-info">Retour au Profil</a>

    <h2>Réservez un Rendez-vous</h2>

    <!-- Affichage des messages -->
    <?php
    if (isset($_GET['message'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_GET['message']) . '</div>';
    }

    if (isset($_GET['error'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
    }
    ?>

    <!-- Affichage du calendrier -->
    <div id="calendar"></div>

    <!-- Script JS pour FullCalendar -->
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
                                    start: $(this).slot_date + 'T' + $(this).start_time,
                                    end: $(this).slot_date + 'T' + $(this).end_time,
                                    slot_id: $(this).id, // Ajouter l'ID du créneau pour chaque événement
                                    description: 'Réservez ce créneau !'
                                });
                            });
                            callback(events);
                        },
                        error: function(xhr, status, error) {
                            alert('Erreur de chargement des créneaux : ' + error);
                        }
                    });
                },
                dayClick: function(date, jsEvent, view) {
                    // Lorsque l'utilisateur clique sur une date
                    alert('Date sélectionnée: ' + date.format());
                },
                eventClick: function(event, jsEvent, view) {
                    // Lorsque l'utilisateur clique sur un créneau
                    if (confirm('Voulez-vous réserver ce créneau ?')) {
                        // Rediriger vers la page de réservation
                        window.location.href = 'book_appointment.php?slot_id=' + event.slot_id;
                    }
                },
                locale: 'fr', // Afficher le calendrier en français
                droppable: false, // Empêche de faire glisser des événements
                editable: false // Empêche l'édition des événements
            });
        });
    </script>
</body>
</html>
