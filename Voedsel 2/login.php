<?php
session_start();
require 'config.php'; // Zorg ervoor dat je de databaseconfiguratie en verbinding hier hebt

// Verwerk de inloggegevens
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verkrijg gebruiker uit de database
    $stmt = $pdo->prepare("SELECT UserID, PasswordHash, Role FROM users WHERE Username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['PasswordHash'])) {
        // Inloggen geslaagd
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['role'] = $user['Role']; // Rol opslaan in sessie

        // Redirect naar het dashboard op basis van de rol
        if ($user['Role'] === 'admin') {
            header('Location: admin_dashboard.php');
        } else {
            header('Location: user_dashboard.php');
        }
        exit();
    } else {
        // Foutmelding als inloggen mislukt
        echo "Onjuiste gebruikersnaam of wachtwoord.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen</title>
</head>
<body>
    <h1>Inloggen</h1>
    <form method="post" action="">
        <label for="username">Gebruikersnaam:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Wachtwoord:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Inloggen</button>
    </form>
</body>
</html>
