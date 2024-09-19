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

// Verkrijg leveringID uit de GET-parameter
if (!isset($_GET['leveringID']) || empty($_GET['leveringID'])) {
    echo "Ongeldige parameters.";
    exit();
}

$leveringID = intval($_GET['leveringID']);

// Verkrijg leverancierID op basis van leveringID
$query = "SELECT leverancierID FROM leveringen WHERE leveringID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $leveringID);
$stmt->execute();
$stmt->bind_result($leverancierID);
$stmt->fetch();
$stmt->close();

// Verkrijg de producten van de levering
$query = "SELECT * FROM levering_producten_temp WHERE LeveringID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $leveringID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producten van Levering</title>
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
    <h1>Producten van Levering ID: <?php echo htmlspecialchars($leveringID); ?></h1>
    <a href="view_delivery_contents.php?leverancierID=<?php echo htmlspecialchars($leverancierID); ?>" class="button back-button">Terug naar Leveringen</a>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>LeveringProductTempID</th>
                <th>LeveringID</th>
                <th>ProductNaam</th>
                <th>Categorie</th>
                <th>Streepjescode</th>
                <th>Voorraad</th>
                <th>Omschrijving</th>
                <th>EANnummer</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['LeveringProductTempID']); ?></td>
                <td><?php echo htmlspecialchars($row['LeveringID']); ?></td>
                <td><?php echo htmlspecialchars($row['ProductNaam']); ?></td>
                <td><?php echo htmlspecialchars($row['Categorie']); ?></td>
                <td><?php echo htmlspecialchars($row['Streepjescode']); ?></td>
                <td><?php echo htmlspecialchars($row['Voorraad']); ?></td>
                <td><?php echo htmlspecialchars($row['Omschrijving']); ?></td>
                <td><?php echo htmlspecialchars($row['EANnummer']); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Geen producten gevonden voor deze levering.</p>
    <?php endif; ?>
    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
