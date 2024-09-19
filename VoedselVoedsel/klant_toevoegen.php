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
    $voornaam = $conn->real_escape_string($_POST['voornaam']);
    $achternaam = $conn->real_escape_string($_POST['achternaam']);
    $adres = $conn->real_escape_string($_POST['adres']);
    $telefoonnummer = $conn->real_escape_string($_POST['telefoonnummer']);
    $email = $conn->real_escape_string($_POST['email']);
    $aantal_volwassenen = intval($_POST['aantal_volwassenen']);
    $aantal_kinderen = intval($_POST['aantal_kinderen']);
    $aantal_babies = intval($_POST['aantal_babies']);
    $wensen = isset($_POST['wensen']) ? $_POST['wensen'] : array();

    // Voeg klant toe
    $sql_klant = "INSERT INTO klanten (Voornaam, Achternaam, Adres, Telefoonnummer, Email) 
                  VALUES ('$voornaam', '$achternaam', '$adres', '$telefoonnummer', '$email')";

    if ($conn->query($sql_klant) === TRUE) {
        $klant_id = $conn->insert_id; // Haal het ID van de  toegevoegde klant op

        // Voeg gezinssamenstelling toe
        $sql_gezinssamenstelling = "INSERT INTO gezinssamenstelling (GezinID, AantalVolwassenen, AantalKinderen, AantalBabies) 
                                    VALUES ('$klant_id', $aantal_volwassenen, $aantal_kinderen, $aantal_babies)";

        if ($conn->query($sql_gezinssamenstelling) === TRUE) {
            // Voeg wensen toe
            foreach ($wensen as $wens_id) {
                $sql_wens = "INSERT INTO klantwensen (KlantID, WensID) VALUES ('$klant_id', '$wens_id')";
                $conn->query($sql_wens);
            }
            echo "Klant succesvol toegevoegd.";
        } else {
            echo "Fout bij het toevoegen van gezinssamenstelling: " . $conn->error;
        }
    } else {
        echo "Fout bij het toevoegen van klant: " . $conn->error;
    }
}

// dropdown
$sql_wensen = "SELECT WensID, WensOmschrijving FROM wensen";
$wensen_result = $conn->query($sql_wensen);

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe Klant Toevoegen</title>
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

        .wensen-container {
            margin-bottom: 20px;
        }

        .add-button {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            background-color: #007bff;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
        }

        .add-button:hover {
            background-color: #0056b3;
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
    <h1 style="text-align:center;">Nieuwe Klant Toevoegen</h1>

    <form method="POST" action="">
        <label for="voornaam">Voornaam:</label>
        <input type="text" id="voornaam" name="voornaam" required>

        <label for="achternaam">Achternaam:</label>
        <input type="text" id="achternaam" name="achternaam" required>

        <label for="adres">Adres:</label>
        <input type="text" id="adres" name="adres" required>

        <label for="telefoonnummer">Telefoonnummer:</label>
        <input type="text" id="telefoonnummer" name="telefoonnummer" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="aantal_volwassenen">Aantal Volwassenen:</label>
        <input type="number" id="aantal_volwassenen" name="aantal_volwassenen" min="0" required>

        <label for="aantal_kinderen">Aantal Kinderen:</label>
        <input type="number" id="aantal_kinderen" name="aantal_kinderen" min="0" required>

        <label for="aantal_babies">Aantal Babies:</label>
        <input type="number" id="aantal_babies" name="aantal_babies" min="0" required>

        <div class="wensen-container">
            <label for="wensen">Selecteer wensen:</label>
            <select id="wensen" name="wensen[]" multiple>
                <?php if ($wensen_result->num_rows > 0): ?>
                    <?php while ($wens = $wensen_result->fetch_assoc()): ?>
                        <option value="<?php echo $wens['WensID']; ?>">
                            <?php echo $wens['WensOmschrijving']; ?>
                        </option>
                    <?php endwhile; ?>
                <?php else: ?>
                    <option value="">Geen wensen beschikbaar</option>
                <?php endif; ?>
            </select>
        </div>

        <input type="submit" value="Klant Toevoegen">
    </form>

    <a href="nieuwe_wens.php" class="add-button">Nieuwe Wens Toevoegen</a>
    <a href="klantenbeheer.php" class="back-button">Terug naar Klantenbeheer</a>
</body>
</html>
