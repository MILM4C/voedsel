<?php
session_start();
include 'config.php';  

// Controleer of de gebruiker ingelogd is
if (!isset($_SESSION['user_id'])) {
    echo "Toegang geweigerd.";
    exit();
}

// Role checkereeeeeeeeeeeeeeer
if ($_SESSION['role'] !== 'directie' && $_SESSION['role'] !== 'magazijnmedewerker') {
    echo "Je hebt geen toegang tot deze pagina.";
    exit();
}

// Verkrijg leverancierID uit de GET-parameter
if (!isset($_GET['leverancierID']) || empty($_GET['leverancierID'])) {
    echo "Ongeldige parameters.";
    exit();
}

$leverancierID = intval($_GET['leverancierID']);

// Verkrijg de leveringen van de leverancier
$query = "SELECT * FROM leveringen WHERE LeverancierID = ?";
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
    <h1>Leveringen voor Leverancier ID: <?php echo htmlspecialchars($leverancierID); ?></h1>
    <a href="view_suppliers.php" class="button back-button">Terug naar Leveranciers</a>
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
