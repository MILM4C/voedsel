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

// Lijst productleveringen
$query = "SELECT * FROM product_leveringen";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Leveringen Bekijken</title>
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
    <h1>Product Leveringen Bekijken</h1>
    <a href="user_dashboard.php" class="button back-button">Terug naar Dashboard</a> 
    <table>
        <tr>
            <th>Levering ID</th>
            <th>Leverancier ID</th>
            <th>Datum</th>
            <th>Beschrijving</th>
            <th>Acties</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['ProductLeveringID']); ?></td>
            <td><?php echo htmlspecialchars($row['LeverancierID']); ?></td>
            <td><?php echo htmlspecialchars($row['Datum']); ?></td>
            <td><?php echo htmlspecialchars($row['Beschrijving']); ?></td>
            <td>
                <a href="view_product_delivery_details.php?id=<?php echo $row['ProductLeveringID']; ?>" class="button">Bekijk Details</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
