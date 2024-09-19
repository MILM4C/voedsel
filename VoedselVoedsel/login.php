<?php
session_start();
require 'config.php'; 

// Verwerk de inloggegevens
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verkrijg gebruiker uit de database
    $stmt = $conn->prepare("SELECT UserID, PasswordHash, Role FROM users WHERE Username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['PasswordHash'])) {
        
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['role'] = $user['Role']; 

        
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

    $stmt->close();
}

$conn->close(); 
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen</title>
    
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>Inloggen</h2>
        <form method="post" action="">
            <label for="username">Gebruikersnaam:</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="password">Wachtwoord:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <input type="submit" value="Inloggen">
        </form>
    </div>
</body>
</html>
