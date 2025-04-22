<?php
// Include the database connection file
include('db.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $token = bin2hex(random_bytes(50));  // Generate a token for email verification

    // Check if the email is already taken
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        die("Cet email est déjà utilisé.");
    }
    $stmt->close();

    // Insert user into the database
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, birth_date, address, phone, email, password, verified, token) VALUES (?, ?, ?, ?, ?, ?, ?, FALSE, ?)");
    $stmt->bind_param("ssssssss", $name, $surname, $dob, $address, $phone, $email, $password, $token);

    if ($stmt->execute()) {
        // Send verification email
        $verification_link = "http://yourdomain.com/verify.php?token=" . $token;
        $subject = "Vérification de votre compte";
        $message = "Cliquez sur ce lien pour vérifier votre compte: " . $verification_link;
        $headers = "From: no-reply@yourdomain.com";  // Use a valid email address

        // Send the email
        mail($email, $subject, $message, $headers);

        echo "Compte créé avec succès. Un email de vérification a été envoyé.";
    } else {
        echo "Erreur lors de la création du compte.";
    }

    $stmt->close();
    $conn->close();  // Close the database connection
}
?>

<!-- Registration Form -->
<form method="POST">
    <input type="text" name="name" placeholder="Nom" required>
    <input type="text" name="surname" placeholder="Prénom" required>
    <input type="date" name="dob" required>
    <input type="text" name="address" placeholder="Adresse" required>
    <input type="text" name="phone" placeholder="Numéro de téléphone" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <button type="submit">S'inscrire</button>
</form>

<!-- Button to return to the homepage -->
<a href="index.php" class="btn btn-secondary">Retour à l'accueil</a>
