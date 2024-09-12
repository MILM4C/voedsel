<?php
session_start();
include 'db_connection.php';

// Controleer of de gebruiker ingelogd is en een admin is
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    echo "Toegang geweigerd.";
    exit();
}

$query = "SELECT * FROM producten";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producten Overzicht</title>
</head>
<body>
    <h1>Productenlijst</h1>
    <?php if ($result->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>ProductID</th>
                <th>ProductNaam</th>
                <th>Categorie</th>
                <th>Voorraad</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['ProductID']); ?></td>
                    <td><?php echo htmlspecialchars($row['ProductNaam']); ?></td>
                    <td><?php echo htmlspecialchars($row['Categorie']); ?></td>
                    <td><?php echo htmlspecialchars($row['Voorraad']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Geen producten gevonden.</p>
    <?php endif; ?>

    <p><a href="admin_page.php">Terug naar Admin Dashboard</a></p>

    <?php
    $result->free();
    $conn->close();
    ?>
</body>
</html>
