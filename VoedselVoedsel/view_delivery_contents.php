<?php
session_start();
include 'config.php';   // Zorg dat dit je databaseconfiguratie bevat
include 'autoload.php'; // Hiermee wordt de User-klasse automatisch geladen

// Vereist dat de gebruiker is ingelogd
User::requireLogin();

// Vereist dat de gebruiker een van de toegestane rollen heeft (directie of magazijnmedewerker)
User::requireRole(['directie', 'magazijnmedewerker']);

// Verkrijg leverancierID uit de GET-parameter
if (!isset($_GET['leverancierID']) || empty($_GET['leverancierID'])) {
    echo "Ongeldige parameters.";
    exit();
}

$leverancierID = intval($_GET['leverancierID']);

// Verkrijg de leveringen van de leverancier, specifiek voor bepaalde kolommen
$query = "SELECT LeveringID, Datum, Beschrijving FROM leveringen WHERE LeverancierID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $leverancierID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leveringen voor Leverancier</title>
    <link rel="stylesheet" href="css/leverancier.css"> 
</head>
<body>
    <h1>Leveringen voor Leverancier ID: <?php echo htmlspecialchars($leverancierID); ?></h1>
    <a href="view_suppliers.php" class="button">Terug naar Leveranciers</a>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Levering ID</th>
                <th>Datum</th>
                <th>Beschrijving</th>
                <th>Acties</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['LeveringID']); ?></td>
                <td><?php echo htmlspecialchars($row['Datum']); ?></td>
                <td><?php echo htmlspecialchars($row['Beschrijving']); ?></td>
                <td>
                    <a href="view_delivery_products.php?leveringID=<?php echo $row['LeveringID']; ?>" class="button">Bekijk Producten</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Geen leveringen gevonden voor deze leverancier.</p>
    <?php endif; ?>
    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
