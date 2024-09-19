<?php
session_start();
require_once 'config.php'; 

// Controleer of de gebruiker ingelogd is
if (!isset($_SESSION['user_id'])) {
    echo "Toegang geweigerd.";
    exit();
}

// Haal klanten op uit de database, inclusief hun wensen
$sql = "SELECT k.KlantID, k.Voornaam, k.Achternaam, k.Adres, k.Telefoonnummer, k.Email, 
               g.AantalVolwassenen, g.AantalKinderen, g.AantalBabies, 
               GROUP_CONCAT(w.WensOmschrijving SEPARATOR ', ') AS Wensen
        FROM klanten k
        LEFT JOIN gezinssamenstelling g ON k.KlantID = g.GezinID
        LEFT JOIN klantwensen kw ON k.KlantID = kw.KlantID
        LEFT JOIN wensen w ON kw.WensID = w.WensID
        GROUP BY k.KlantID";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klantenbeheer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        .add-button, .back-button {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
        }

        .add-button {
            background-color: #28a745;
        }

        .add-button:hover {
            background-color: #218838;
        }

        .back-button {
            background-color: #6c757d;
        }

        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Klantenbeheer</h1>

    <a href="klant_toevoegen.php" class="add-button">Nieuwe Klant Toevoegen</a>
    <a href="user_dashboard.php" class="back-button">Terug naar Dashboard</a>

    <table>
        <tr>
            <th>Klant ID</th>
            <th>Naam</th>
            <th>Adres</th>
            <th>Telefoonnummer</th>
            <th>Email</th>
            <th>Aantal Volwassenen</th>
            <th>Aantal Kinderen</th>
            <th>Aantal Babies</th>
            <th>Wensen</th>
            <th>Acties</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['KlantID']; ?></td>
                    <td><?php echo $row['Voornaam'] . " " . $row['Achternaam']; ?></td>
                    <td><?php echo $row['Adres']; ?></td>
                    <td><?php echo $row['Telefoonnummer']; ?></td>
                    <td><?php echo $row['Email']; ?></td>
                    <td><?php echo $row['AantalVolwassenen']; ?></td>
                    <td><?php echo $row['AantalKinderen']; ?></td>
                    <td><?php echo $row['AantalBabies']; ?></td>
                    <td><?php echo !empty($row['Wensen']) ? $row['Wensen'] : 'Geen wensen'; ?></td>
                    <td><a href="klant_pakketten.php?klant_id=<?php echo $row['KlantID']; ?>">Bekijk Pakketten</a></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="10" style="text-align:center;">Geen klanten gevonden</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>
