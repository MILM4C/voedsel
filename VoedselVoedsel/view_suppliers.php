<?php
session_start();
include 'config.php';   // Zorg dat dit je databaseconfiguratie bevat
include 'autoload.php'; // Hiermee wordt de User-klasse automatisch geladen

// Vereist dat de gebruiker is ingelogd
User::requireLogin();

// Vereist dat de gebruiker een van de toegestane rollen heeft (directie of magazijnmedewerker)
User::requireRole(['directie', 'magazijnmedewerker']);

// Verwijder een leverancier en alle gerelateerde leveringen en producten
if (isset($_GET['delete'])) {
    $leverancierID = intval($_GET['delete']);

    // Start transactie
    $conn->begin_transaction();

    try {
        // Verwijder alle producten die aan leveringen zijn gekoppeld
        $query = "DELETE FROM levering_producten_temp WHERE LeveringID IN (SELECT LeveringID FROM leveringen WHERE LeverancierID = ?)";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("i", $leverancierID);
        $stmt->execute();
        $stmt->close();

        // Verwijder alle leveringen die aan de leverancier zijn gekoppeld
        $query = "DELETE FROM leveringen WHERE LeverancierID = ?";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("i", $leverancierID);
        $stmt->execute();
        $stmt->close();

        // Verwijder de leverancier
        $query = "DELETE FROM leveranciers WHERE LeverancierID = ?";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("i", $leverancierID);
        $stmt->execute();
        $stmt->close();

        // Commit transactie
        $conn->commit();
        header("Location: view_suppliers.php");
        exit();
    } catch (Exception $e) {
        // Rollback bij een fout
        $conn->rollback();
        echo "Er is een fout opgetreden: " . htmlspecialchars($e->getMessage());
    }
}

// Lijst leveranciers met specifieke kolommen
$query = "SELECT LeverancierID, Bedrijfsnaam, Adres, Contactpersoon, Telefoonnummer, Emailadres FROM leveranciers";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leveranciers Beheren</title>
    <link rel="stylesheet" href="css/view_suppliers.css"> 
</head>
<body>
    <h1>Leveranciers Beheren</h1>
    <?php if ($_SESSION['role'] === 'directie' || $_SESSION['role'] === 'magazijnmedewerker'): ?>
        <a href="add_supplier.php" class="button add-button">Leverancier Toevoegen</a>
        <a href="add_delivery.php" class="button add-button">Nieuwe Levering Toevoegen</a>
    <?php endif; ?>
    <a href="user_dashboard.php" class="button back-button">Terug naar Dashboard</a> 
    <table>
        <tr>
            <th>Leverancier ID</th>
            <th>Bedrijfsnaam</th>
            <th>Adres</th>
            <th>Contactpersoon</th>
            <th>Telefoonnummer</th>
            <th>Emailadres</th>
            <th>Acties</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['LeverancierID']); ?></td>
            <td><?php echo htmlspecialchars($row['Bedrijfsnaam']); ?></td>
            <td><?php echo htmlspecialchars($row['Adres']); ?></td>
            <td><?php echo htmlspecialchars($row['Contactpersoon']); ?></td>
            <td><?php echo htmlspecialchars($row['Telefoonnummer']); ?></td>
            <td><?php echo htmlspecialchars($row['Emailadres']); ?></td>
            <td>
                <?php if ($_SESSION['role'] === 'directie' || $_SESSION['role'] === 'magazijnmedewerker'): ?>
                    <a href="edit_supplier.php?id=<?php echo $row['LeverancierID']; ?>" class="button add-button">Bewerken</a>
                    <a href="view_suppliers.php?delete=<?php echo $row['LeverancierID']; ?>" class="button delete-button" onclick="return confirm('Weet je zeker dat je deze leverancier wilt verwijderen?');">Verwijderen</a>
                    <a href="view_delivery_contents.php?leverancierID=<?php echo $row['LeverancierID']; ?>" class="button add-button">Bekijk Leveringen</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
