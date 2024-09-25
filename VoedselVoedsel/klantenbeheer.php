<?php
session_start();
include 'config.php';  
include 'autoload.php';  // Zorg ervoor dat de Klant klasse wordt geladen

// Controleer of de gebruiker is ingelogd
User::requireLogin();

// Maak een nieuwe instantie van de Klant klasse
$klantClass = new Klant($conn);

// Haal alle klanten op
$klanten = $klantClass->getAllKlanten();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klantenbeheer</title>
    <link rel="stylesheet" href="css/klantbeheer.css"> 
    <script>
        function confirmDelete(klantID) {
            if (confirm("Weet je zeker dat je deze klant wilt verwijderen?")) {
                window.location.href = "delete_klant.php?klant_id=" + klantID;
            }
        }
    </script>
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
        <?php if ($klanten->num_rows > 0): ?>
            <?php while ($row = $klanten->fetch_assoc()): ?>
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
                    <td>
                        <a href="klant_pakketten.php?klant_id=<?php echo $row['KlantID']; ?>" class="pakket-button">Bekijk Pakketten</a>
                        <a href="edit_klant.php?klant_id=<?php echo $row['KlantID']; ?>" class="edit-button">Bewerken</a>
                        <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $row['KlantID']; ?>);" class="delete-button">Verwijderen</a>
                    </td>
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
