<?php
session_start();
include 'config.php';  

// Controleer of de gebruiker ingelogd is
if (!isset($_SESSION['user_id'])) {
    echo "Toegang geweigerd.";
    exit();
}

// Haal alle pakketten op
$query = "SELECT * FROM pakketten";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voedselpakketten Overzicht</title>
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
            margin-top: 10px;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .delete-button {
            background-color: #dc3545;
        }
        .delete-button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <h1>Voedselpakketten Overzicht</h1>
    <a href="create_voedselpakket.php" class="button">Nieuw Voedselpakket Maken</a>
    <a href="user_dashboard.php" class="button">Terug naar Dashboard</a>
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
