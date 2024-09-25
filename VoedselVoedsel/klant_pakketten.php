<?php
session_start();
require_once 'config.php'; 

// Controleer of de klant_id is doorgegeven
if (!isset($_GET['klant_id'])) {
    echo "Geen klant geselecteerd.";
    exit();
}

$klant_id = $_GET['klant_id'];

// Haal klantinformatie op 
$sql_klant = "SELECT Voornaam, Achternaam FROM klanten WHERE KlantID = ?";
$stmt_klant = $conn->prepare($sql_klant);
$stmt_klant->bind_param("i", $klant_id);
$stmt_klant->execute();
$result_klant = $stmt_klant->get_result();
$klant = $result_klant->fetch_assoc();

// Controleer of de klant bestaat
if (!$klant) {
    echo "Klant niet gevonden.";
    exit();
}

// Haal pakketten op van de geselecteerde klant
$sql = "SELECT p.PakketID, p.Samenstelling, p.DatumAangemaakt 
        FROM pakketten p
        JOIN klant_pakket kp ON p.PakketID = kp.PakketID
        WHERE kp.KlantID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $klant_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klant Pakketten</title>
    <link rel="stylesheet" href="css/klantpakket.css"> 
</head>
<body>
    <h1 style="text-align:center;">Pakketten van <?php echo htmlspecialchars($klant['Voornaam'] . ' ' . $klant['Achternaam']); ?></h1>

    <a href="klantenbeheer.php" class="back-button">Terug naar Klantenbeheer</a>

    <table>
        <tr>
            <th>Pakket ID</th>
            <th>Samenstelling</th>
            <th>Datum Aangemaakt</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['PakketID']); ?></td>
                    <td><?php echo htmlspecialchars($row['Samenstelling']); ?></td>
                    <td><?php echo htmlspecialchars($row['DatumAangemaakt']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" class="no-results">Geen pakketten gevonden voor deze klant.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>
