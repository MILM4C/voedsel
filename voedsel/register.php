<?php
session_start();
include 'db_connection.php';

// Verwerk het registratieformulier
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Rol van de gebruiker

    // Valideer rol
    if ($role !== 'admin' && $role !== 'user') {
        echo "Ongeldige rol. Alleen 'admin' of 'user' zijn toegestaan.";
        exit();
    }

    // Hash het wachtwoord
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Controleer of de gebruikersnaam al bestaat
    $query = "SELECT Username FROM users WHERE Username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Gebruikersnaam bestaat al.";
    } else {
        // Voeg nieuwe gebruiker toe
        $query = "INSERT INTO users (Username, PasswordHash, Role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $username, $passwordHash, $role);
        
        if ($stmt->execute()) {
            echo "Registratie succesvol. U kunt nu inloggen.";
        } else {
            echo "Fout bij registratie: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreren</title>
</head>
<body>
    <h1>Registreren</h1>
    <form method="post" action="register.php">
        <label for="username">Gebruikersnaam:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Wachtwoord:</label>
        <input type="password" id="password" name="password" required><br>
        <label for="role">Rol:</label>
        <select id="role" name="role" required>
            <option value="user">Gebruiker</option>
            <option value="admin">Admin</option>
        </select><br>
        <input type="submit" value="Registreren">
    </form>
</body>
</html>
