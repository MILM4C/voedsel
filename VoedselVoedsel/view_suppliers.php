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

// Lijst leveranciers
$query = "SELECT * FROM leveranciers";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leveranciers Beheren</title>
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
        .delete-button {
            background-color: #dc3545;
        }
        .delete-button:hover {
            background-color: #c82333;
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
    <h1>Leveranciers Beheren</h1>
    <?php if ($_SESSION['role'] === 'directie' || $_SESSION['role'] === 'magazijnmedewerker'): ?>
        <a href="add_supplier.php" class="button">Leverancier Toevoegen</a>
        <a href="add_delivery.php" class="button">Nieuwe Levering Toevoegen</a>
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
                    <a href="edit_supplier.php?id=<?php echo $row['LeverancierID']; ?>" class="button">Bewerken</a>
                    <a href="view_suppliers.php?delete=<?php echo $row['LeverancierID']; ?>" class="button delete-button" onclick="return confirm('Weet je zeker dat je deze leverancier wilt verwijderen?');">Verwijderen</a>
                    <a href="view_delivery_contents.php?leverancierID=<?php echo $row['LeverancierID']; ?>" class="button">Bekijk Leveringen</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
