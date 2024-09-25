<?php
session_start();
include 'config.php';   // Zorg dat dit je databaseconfiguratie bevat
include 'autoload.php'; // Hiermee wordt de User-klasse automatisch geladen

// Vereist dat de gebruiker is ingelogd
User::requireLogin();

// Vereist dat de gebruiker een van de toegestane rollen heeft 
User::requireRole(['directie', 'magazijnmedewerker']);

// Verkrijg leveringID uit de GET-parameter
if (!isset($_GET['leveringID']) || empty($_GET['leveringID'])) {
    echo "Ongeldige parameters.";
    exit();
}

$leveringID = intval($_GET['leveringID']);

// Verwerk formulierinvoer voor de producten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productNaam = $_POST['productNaam'];
    $categorie = $_POST['categorie'];
    $streepjescode = $_POST['streepjescode'];
    $voorraad = intval($_POST['voorraad']);
    $omschrijving = $_POST['omschrijving'];
    $eannummer = $_POST['eannummer'];

    // Insert het  product in de tijdelijke tabel
    $query = "INSERT INTO levering_producten_temp (LeveringID, ProductNaam, Categorie, Streepjescode, Voorraad, Omschrijving, EANnummer) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issssss", $leveringID, $productNaam, $categorie, $streepjescode, $voorraad, $omschrijving, $eannummer);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producten Toevoegen aan Levering</title>
    <style>
        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-container input, .form-container textarea, .form-container select {
            width: 100%;
            padding: 10px;
            margin: 5px 0 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-container button {
            padding: 10px 15px;
            font-size: 16px;
            color: white;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-container button:hover {
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
    <h1>Producten Toevoegen aan Levering ID: <?php echo htmlspecialchars($leveringID); ?></h1>
    <a href="view_suppliers.php" class="button back-button">Terug naar Leveranciers</a>
    <div class="form-container">
        <form method="post" action="">
            <label for="productNaam">Product Naam</label>
            <input type="text" id="productNaam" name="productNaam" required>
            
            <label for="categorie">Categorie</label>
            <input type="text" id="categorie" name="categorie" required>
            
            <label for="streepjescode">Streepjescode</label>
            <input type="text" id="streepjescode" name="streepjescode" required>
            
            <label for="voorraad">Voorraad</label>
            <input type="number" id="voorraad" name="voorraad" required>
            
            <label for="omschrijving">Omschrijving</label>
            <textarea id="omschrijving" name="omschrijving" rows="4"></textarea>
            
            <label for="eannummer">EAN Nummer</label>
            <input type="text" id="eannummer" name="eannummer" required>
            
            <button type="submit">Opslaan</button>
        </form>
    </div>
</body>
</html>
