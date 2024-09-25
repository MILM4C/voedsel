<?php
session_start();
include 'config.php';  


if (!isset($_SESSION['user_id'])) {
    echo "Toegang geweigerd.";
    exit();
}

if (isset($_GET['pakket_id'])) {
    $pakketID = intval($_GET['pakket_id']);

  
    $query = "
        SELECT p.ProductNaam, pi.Aantal
        FROM pakketinhoud pi
        JOIN producten p ON pi.ProductID = p.ProductID
        WHERE pi.PakketID = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pakketID);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    echo "Geen pakket ID opgegeven.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inhoud van Pakket</title>
    <link rel="stylesheet" href="css/voedselpakket.css"> 
</head>
<body>
    <h1>Inhoud van Pakket ID: <?php echo htmlspecialchars($pakketID); ?></h1>
    <table>
        <tr>
            <th>Product Naam</th>
            <th>Aantal</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['ProductNaam']); ?></td>
            <td><?php echo htmlspecialchars($row['Aantal']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <a href="voedselpakketten.php" class="button">Terug naar Overzicht</a>
</body>
</html>
