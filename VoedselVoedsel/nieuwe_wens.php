<?php
session_start();
require_once 'config.php'; 

// Controleer of de gebruiker ingelogd is
if (!isset($_SESSION['user_id'])) {
    echo "Toegang geweigerd.";
    exit();
}

// Verwerk formulier 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $wens = $conn->real_escape_string($_POST['wens']);

    // Voeg nieuwe wens toe
    $sql = "INSERT INTO wensen (WensOmschrijving) VALUES ('$wens')";

    if ($conn->query($sql) === TRUE) {
        echo "Nieuwe wens succesvol toegevoegd.";
    } else {
        echo "Fout bij het toevoegen van wens: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe Wens Toevoegen</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        form {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        .back-button {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            background-color: #6c757d;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Nieuwe Wens Toevoegen</h1>

    <form method="POST" action="">
        <label for="wens">Wens Beschrijving:</label>
        <input type="text" id="wens" name="wens" required>

        <input type="submit" value="Voeg Wens Toe">
    </form>

    <a href="klantenbeheer.php" class="back-button">Terug naar Klantenbeheer</a>
</body>
</html>
