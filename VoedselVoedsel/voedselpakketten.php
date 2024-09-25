<?php
session_start();
include 'config.php';  // Zorg voor correcte databaseverbinding
include 'autoload.php'; // Laad de benodigde klassen

// Controleer of de gebruiker is ingelogd en de juiste rol heeft
User::requireLogin();
$user_role = User::getUserRole();  // Haal de rol van de ingelogde gebruiker op

// Haal specifieke kolommen op
$query = "SELECT PakketID, Samenstelling, DatumAangemaakt FROM pakketten";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voedselpakketten Overzicht</title>
    <link rel="stylesheet" href="css/voedselpakket.css"> 
</head>
<body>
    <h1>Voedselpakketten Overzicht</h1>

    <div class="button-container">
        <a href="create_voedselpakket.php" class="button">Nieuw Voedselpakket Maken</a>
        <a href="user_dashboard.php" class="button">Terug naar Dashboard</a>
    </div>

    <table>
        <tr>
            <th>Pakket ID</th>
            <th>Beschrijving</th>
            <th>Datum Aangemaakt</th>
            <th>Acties</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['PakketID']); ?></td>
            <td><?php echo htmlspecialchars($row['Samenstelling']); ?></td>
            <td><?php echo htmlspecialchars($row['DatumAangemaakt']); ?></td>
            <td>
                <a href="view_pakket.php?pakket_id=<?php echo $row['PakketID']; ?>" class="button">Bekijk Inhoud</a>
                <a href="delete_voedselpakket.php?pakket_id=<?php echo $row['PakketID']; ?>" class="button delete-button">Verwijderen</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
