<?php
session_start();
include 'config.php';   // Zorg dat dit je databaseconfiguratie bevat
include 'autoload.php'; // Hiermee wordt de User-klasse automatisch geladen

// Vereist dat de gebruiker is ingelogd
User::requireLogin();

// Vereist dat de gebruiker een van de toegestane rollen heeft 
User::requireRole(['directie', 'magazijnmedewerker']);

// Voeg een nieuwe leverancier toe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bedrijfsnaam = $_POST['bedrijfsnaam'];
    $adres = $_POST['adres'];
    $contactpersoon = $_POST['contactpersoon'];
    $telefoonnummer = $_POST['telefoonnummer'];
    $emailadres = $_POST['emailadres'];

    $query = "INSERT INTO leveranciers (Bedrijfsnaam, Adres, Contactpersoon, Telefoonnummer, Emailadres) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $bedrijfsnaam, $adres, $contactpersoon, $telefoonnummer, $emailadres);
    $stmt->execute();
    $stmt->close();

    header("Location: view_suppliers.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leverancier Toevoegen</title>
    <style>
        :root {
            --primary-color: #007BFF;
            --primary-hover: #0056b3;
            --secondary-color: #6c757d;
            --secondary-hover: #5a6268;
            --border-color: #ddd;
            --error-color: #dc3545;
            --input-background: #fff;
            --border-radius: 5px;
            --padding: 12px;
            --font-family: 'Arial', sans-serif;
        }

        body {
            font-family: var(--font-family);
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column; 
            align-items: center; 
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .form-container {
            max-width: 600px;
            width: 100%; /
            padding: 20px;
            background-color: var(--input-background);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center; 
        }

        .form-container label {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
            text-align: center; 
        }

        .form-container input[type="text"], 
        .form-container input[type="email"] {
            width: 100%;
            padding: var(--padding);
            margin-bottom: 15px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            background-color: var(--input-background);
            font-size: 16px;
            text-align: center; 
        }

        .form-container input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .form-container input[type="submit"] {
            width: 100%;
            padding: var(--padding);
            font-size: 16px;
            color: white;
            background-color: var(--primary-color);
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-container input[type="submit"]:hover {
            background-color: var(--primary-hover);
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            padding: var(--padding);
            color: white;
            background-color: var(--secondary-color);
            border-radius: var(--border-radius);
            transition: background-color 0.3s;
            width: 100%;
            text-align: center; 
        }

        .back-button:hover {
            background-color: var(--secondary-hover);
        }


        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            .form-container {
                padding: 15px;
            }

            .form-container input[type="submit"] {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <h1>Leverancier Toevoegen</h1>
    <div class="form-container">
        <form action="add_supplier.php" method="post">
            <label for="bedrijfsnaam">Bedrijfsnaam:</label>
            <input type="text" id="bedrijfsnaam" name="bedrijfsnaam" required>

            <label for="adres">Adres:</label>
            <input type="text" id="adres" name="adres">

            <label for="contactpersoon">Contactpersoon:</label>
            <input type="text" id="contactpersoon" name="contactpersoon">

            <label for="telefoonnummer">Telefoonnummer:</label>
            <input type="text" id="telefoonnummer" name="telefoonnummer">

            <label for="emailadres">Emailadres:</label>
            <input type="email" id="emailadres" name="emailadres">

            <input type="submit" value="Leverancier Toevoegen">
        </form>
        <a href="view_suppliers.php" class="back-button">Terug naar leveranciersoverzicht</a>
    </div>
</body>
</html>
