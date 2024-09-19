<?php
session_start();
include 'config.php';  

// Controleer of de gebruiker ingelogd is
if (!isset($_SESSION['user_id'])) {
    echo "Toegang geweigerd.";
    exit();
}

// role role
if ($_SESSION['role'] !== 'directie' && $_SESSION['role'] !== 'magazijnmedewerker') {
    echo "Je hebt geen toegang tot deze pagina.";
    exit();
}

// Voeg een nieuwe leverancier toe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bedrijfsnaam = $_POST['bedrijfsnaam'];
    $adres = $_POST['adres'];
    $contactpersoon = $_POST['contactpersoon'];
    $telefoonnummer = $_POST['telefoonnummer'];
    $emailadres = $_POST['emailadres'];

    $query = "INSERT INTO leveranciers (Bedrijfsnaam, Adres, Contactpersoon, Telefoonnummer, Emailadres) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $bedrijfsnaam, $adres, $contactpersoon, $telefoonnummer, $emailadres);
    $stmt->execute();
    $stmt->close();

    header("Location: view_suppliers.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leverancier Toevoegen</title>
</head>
<body>
    <h1>Leverancier Toevoegen</h1>
    <form action="add_supplier.php" method="post">
        <label for="bedrijfsnaam">Bedrijfsnaam:</label><br>
        <input type="text" id="bedrijfsnaam" name="bedrijfsnaam" required><br><br>
        <label for="adres">Adres:</label><br>
        <input type="text" id="adres" name="adres"><br><br>
        <label for="contactpersoon">Contactpersoon:</label><br>
        <input type="text" id="contactpersoon" name="contactpersoon"><br><br>
        <label for="telefoonnummer">Telefoonnummer:</label><br>
        <input type="text" id="telefoonnummer" name="telefoonnummer"><br><br>
        <label for="emailadres">Emailadres:</label><br>
        <input type="email" id="emailadres" name="emailadres"><br><br>
        <input type="submit" value="Leverancier Toevoegen">
    </form>
</body>
</html>
