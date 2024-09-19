<?php
session_start();
include 'config.php';  

// Controleer of de gebruiker ingelogd is
if (!isset($_SESSION['user_id'])) {
    echo "Toegang geweigerd.";
    exit();
}

// Role checkerers
if ($_SESSION['role'] !== 'directie' && $_SESSION['role'] !== 'magazijnmedewerker') {
    echo "Je hebt geen toegang tot deze pagina.";
    exit();
}

// Verkrijg de leverancierID van de querystring
$leverancierID = intval($_GET['leverancierID']);

// Verkrijg de leveringen voor de leverancier
$deliveryQuery = "SELECT * FROM leveringen WHERE LeverancierID = ?";
$deliveryStmt = $conn->prepare($deliveryQuery);
$deliveryStmt->bind_param("i", $leverancierID);
$deliveryStmt->execute();
$deliveryResult = $deliveryStmt->get_result();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leverancier Details</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .button {
            display: inline-block;
            padding: 10px 15px;
            font-size: 16px;
            color: white;
            background-color: #007BFF;
            text-align: center;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
        .button:hover {
            background-color: #0056b3;
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
    <h1>Leverancier Details</h1>
    <a href="view_suppliers.php" class="button back-button">Terug naar Leveranciers</a> 
    <h2>Leveringen</h2>
    <?php if ($deliveryResult->num_rows > 0): ?>
        <table>
            <tr>
                <th>Levering ID</th>
                <th>Datum</th>
                <th>Beschrijving</th>
                <th>Inhoud</th>
            </tr>
            <?php while ($deliveryRow = $deliveryResult->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($deliveryRow['LeveringID']); ?></td>
                <td><?php echo htmlspecialchars($deliveryRow['Datum']); ?></td>
                <td><?php echo htmlspecialchars($deliveryRow['Beschrijving']); ?></td>
                <td><a href="view_delivery_contents.php?leveringID=<?php echo $deliveryRow['LeveringID']; ?>" class="button">Bekijk Inhoud</a></td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Geen leveringen gevonden voor deze leverancier.</p>
    <?php endif; ?>
    <?php $deliveryStmt->close(); ?>
</body>
</html>
