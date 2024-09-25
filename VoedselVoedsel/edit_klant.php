<?php
session_start();
include 'config.php';   // Zorg dat dit je databaseconfiguratie bevat
include 'autoload.php'; // Hiermee wordt de User-klasse automatisch geladen

// Vereist dat de gebruiker is ingelogd
User::requireLogin();

// Vereist dat de gebruiker een van de toegestane rollen heeft (directie of magazijnmedewerker)
User::requireRole(['directie']);

// Maak een nieuwe instantie van de Klant klasse
$klantClass = new Klant($conn);

// Controleer of er een klant ID is meegegeven
if (isset($_GET['klant_id'])) {
    $klantID = $_GET['klant_id'];
    // Haal de klantgegevens op
    $klant = $klantClass->getKlantById($klantID);
    
    // Als de klant niet gevonden is, terug naar de klantenlijst
    if (!$klant) {
        header("Location: klantenbeheer.php");
        exit();
    }
} else {
    header("Location: klantenbeheer.php");
    exit();
}

// Verwerk het formulier indien het is ingediend
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voornaam = $_POST['voornaam'];
    $achternaam = $_POST['achternaam'];
    $adres = $_POST['adres'];
    $telefoonnummer = $_POST['telefoonnummer'];
    $email = $_POST['email'];
    $aantal_volwassenen = $_POST['aantal_volwassenen'];
    $aantal_kinderen = $_POST['aantal_kinderen'];
    $aantal_babies = $_POST['aantal_babies'];

    // Update de klantgegevens
    $klantClass->updateKlant($klantID, $voornaam, $achternaam, $adres, $telefoonnummer, $email, $aantal_volwassenen, $aantal_kinderen, $aantal_babies);

    // Redirect naar de klantenlijst na succesvol bijwerken
    header("Location: klantenbeheer.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klant Bewerken</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 50%;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input[type="text"], input[type="email"], input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        .back-button {
            display: block;
            text-align: center;
            margin-top: 20px;
            background-color: #6c757d;
            color: white;
            padding: 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Klant Bewerken</h1>
        <form method="POST" action="">
            <label for="voornaam">Voornaam:</label>
            <input type="text" id="voornaam" name="voornaam" value="<?php echo htmlspecialchars($klant['Voornaam']); ?>" required>

            <label for="achternaam">Achternaam:</label>
            <input type="text" id="achternaam" name="achternaam" value="<?php echo htmlspecialchars($klant['Achternaam']); ?>" required>

            <label for="adres">Adres:</label>
            <input type="text" id="adres" name="adres" value="<?php echo htmlspecialchars($klant['Adres']); ?>" required>

            <label for="telefoonnummer">Telefoonnummer:</label>
            <input type="text" id="telefoonnummer" name="telefoonnummer" value="<?php echo htmlspecialchars($klant['Telefoonnummer']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($klant['Email']); ?>" required>

            <label for="aantal_volwassenen">Aantal Volwassenen:</label>
            <input type="number" id="aantal_volwassenen" name="aantal_volwassenen" value="<?php echo htmlspecialchars($klant['AantalVolwassenen']); ?>" required>

            <label for="aantal_kinderen">Aantal Kinderen:</label>
            <input type="number" id="aantal_kinderen" name="aantal_kinderen" value="<?php echo htmlspecialchars($klant['AantalKinderen']); ?>" required>

            <label for="aantal_babies">Aantal Baby's:</label>
            <input type="number" id="aantal_babies" name="aantal_babies" value="<?php echo htmlspecialchars($klant['AantalBabies']); ?>" required>

            <input type="submit" value="Opslaan">
        </form>
        <a href="klantenbeheer.php" class="back-button">Terug naar Klantenbeheer</a>
    </div>
</body>
</html>
