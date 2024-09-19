<?php

include 'config.php';


if (isset($_GET['id'])) {
    $supplier_id = $_GET['id'];

    
    $query = "SELECT * FROM leveranciers WHERE LeverancierID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $supplier_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $supplier = $result->fetch_assoc();

    if (!$supplier) {
        echo "Leverancier niet gevonden!";
        exit();
    }

    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $bedrijfsnaam = $_POST['bedrijfsnaam'];
        $adres = $_POST['adres'];
        $contactpersoon = $_POST['contactpersoon'];
        $telefoonnummer = $_POST['telefoonnummer'];
        $emailadres = $_POST['emailadres'];

       
        $update_query = "UPDATE leveranciers 
                         SET Bedrijfsnaam = ?, Adres = ?, Contactpersoon = ?, Telefoonnummer = ?, Emailadres = ? 
                         WHERE LeverancierID = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('sssssi', $bedrijfsnaam, $adres, $contactpersoon, $telefoonnummer, $emailadres, $supplier_id);

        if ($update_stmt->execute()) {
            echo "Leverancier succesvol bijgewerkt!";
        } else {
            echo "Fout bij het bijwerken van de leverancier!";
        }
    }
} else {
    echo "Geen leverancier ID opgegeven!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leverancier Bewerken</title>
</head>
<body>
    <h1>Leverancier Bewerken</h1>
    
    
    <form action="edit_supplier.php?id=<?php echo $supplier_id; ?>" method="post">
        <label for="bedrijfsnaam">Bedrijfsnaam:</label><br>
        <input type="text" id="bedrijfsnaam" name="bedrijfsnaam" value="<?php echo htmlspecialchars($supplier['Bedrijfsnaam']); ?>" required><br><br>

        <label for="adres">Adres:</label><br>
        <input type="text" id="adres" name="adres" value="<?php echo htmlspecialchars($supplier['Adres']); ?>"><br><br>

        <label for="contactpersoon">Contactpersoon:</label><br>
        <input type="text" id="contactpersoon" name="contactpersoon" value="<?php echo htmlspecialchars($supplier['Contactpersoon']); ?>"><br><br>

        <label for="telefoonnummer">Telefoonnummer:</label><br>
        <input type="text" id="telefoonnummer" name="telefoonnummer" value="<?php echo htmlspecialchars($supplier['Telefoonnummer']); ?>"><br><br>

        <label for="emailadres">Emailadres:</label><br>
        <input type="email" id="emailadres" name="emailadres" value="<?php echo htmlspecialchars($supplier['Emailadres']); ?>"><br><br>

        <input type="submit" value="Bijwerken">
    </form>

    
    <br>
    <a href="view_suppliers.php">Terug naar leveranciersoverzicht</a>
</body>
</html>
