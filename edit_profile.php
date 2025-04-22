<?php
require 'db.php';  // Database connection
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user's current information from the database
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);  // "i" denotes an integer parameter
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();  // Fetch the result as an associative array

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the updated form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    // Check if the email is already used by another user (not the current user)
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $user_id);  // "s" for string and "i" for integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error_message = "Cet email est déjà utilisé.";
    } else {
        // Update the user's information in the database
        $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, birth_date = ?, address = ?, phone = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $first_name, $last_name, $dob, $address, $phone, $email, $user_id);  // Binding parameters

        if ($stmt->execute()) {
            $success_message = "Informations mises à jour avec succès.";
        } else {
            $error_message = "Erreur lors de la mise à jour des informations.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mettre à jour votre profil</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Modifier votre profil</h1>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <!-- User Profile Update Form -->
        <form method="POST" class="mt-4">
            <div class="form-group">
                <label for="first_name">Prénom</label>
                <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="last_name">Nom</label>
                <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="dob">Date de naissance</label>
                <input type="date" class="form-control" name="dob" value="<?php echo htmlspecialchars($user['birth_date']); ?>" required>
            </div>

            <div class="form-group">
                <label for="address">Adresse</label>
                <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Numéro de téléphone</label>
                <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>

        <a href="profile.php" class="btn btn-secondary mt-3">Retour au profil</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
