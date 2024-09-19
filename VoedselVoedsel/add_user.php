<?php
session_start();
include 'config.php';

// the main role checker baby
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'directie') {
    echo "Toegang geweigerd.";
    exit();
}

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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .form-group button:hover {
            background-color: #45a049;
        }
        .back-button {
            background-color: #555;
            text-decoration: none;
            padding: 10px 20px;
            color: white;
            border-radius: 4px;
            display: inline-block;
            margin-top: 20px;
        }
        .back-button:hover {
            background-color: #333;
        }
    </style>
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
