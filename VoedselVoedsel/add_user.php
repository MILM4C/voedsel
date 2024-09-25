<?php
session_start();
include 'config.php';   // Zorg dat dit je databaseconfiguratie bevat
include 'autoload.php'; // Hiermee wordt de User-klasse automatisch geladen

// Vereist dat de gebruiker is ingelogd
User::requireLogin();

// Vereist dat de gebruiker een van de toegestane rollen heeft 
User::requireRole(['directie']);

// Verwerken van het formulier om een nieuwe gebruiker toe te voegen
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Invoegen van de nieuwe gebruiker in de database
    $insertQuery = "INSERT INTO users (Username, PasswordHash, Role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sss", $username, $password, $role);
    if ($stmt->execute()) {
        echo "Nieuwe gebruiker succesvol toegevoegd.";
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Fout bij toevoegen van gebruiker: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe Gebruiker Toevoegen</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <div class="container">
        <h1>Nieuwe Gebruiker Toevoegen</h1>
        <form method="post" action="add_user.php">
            <div class="form-group">
                <label for="username">Gebruikersnaam</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Wachtwoord</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Rol</label>
                <select id="role" name="role" required>
                    <option value="directie">Directie</option>
                    <option value="magazijnmedewerker">Magazijnmedewerker</option>
                    <option value="vrijwilliger">Vrijwilliger</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" name="add_user">Toevoegen</button>
            </div>
        </form>
        <a href="manage_users.php" class="back-button">Terug naar Gebruikerslijst</a>
    </div>
</body>
</html>
