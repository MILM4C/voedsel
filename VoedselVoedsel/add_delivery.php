<?php
session_start();
include 'config.php';  

// Controleer of de gebruiker ingelogd is
if (!isset($_SESSION['user_id'])) {
    echo "Toegang geweigerd.";
    exit();
}

// Role checker zoals alltijd
if ($_SESSION['role'] !== 'directie' && $_SESSION['role'] !== 'magazijnmedewerker') {
    echo "Je hebt geen toegang tot deze pagina.";
    exit();
}

// Variabelen i
$leverancierID = $datum = $beschrijving = '';
$error_message = '';

// nieuwe levering + productent
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_delivery'])) {
        $leverancierID = intval($_POST['leverancierID']);
        $datum = $_POST['datum'];
        $beschrijving = $_POST['beschrijving'];

        // Controleer of de leverancier bestaat
        $checkQuery = "SELECT 1 FROM leveranciers WHERE LeverancierID = ?";
        $stmt = $conn->prepare($checkQuery);
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("i", $leverancierID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $stmt->close();
            $error_message = "De opgegeven leverancier bestaat niet.";
        } else {
            $stmt->close();

           
            $conn->begin_transaction();

            try {
                
                $query = "INSERT INTO leveringen (LeverancierID, Datum, Beschrijving) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($query);
                if ($stmt === false) {
                    throw new Exception("Error preparing statement: " . $conn->error);
                }
                $stmt->bind_param("iss", $leverancierID, $datum, $beschrijving);
                $stmt->execute();
                $leveringID = $stmt->insert_id; // Verkrijg de ID van de net toegevoegde levering

                // Voeg producten toe
                if (!empty($_POST['productNaam']) && !empty($_POST['categorie']) && !empty($_POST['streepjescode']) && !empty($_POST['voorraad']) && !empty($_POST['eannummer'])) {
                    $productNaam = $_POST['productNaam'];
                    $categorie = $_POST['categorie'];
                    $streepjescode = $_POST['streepjescode'];
                    $voorraad = intval($_POST['voorraad']);
                    $omschrijving = $_POST['omschrijving'];
                    $eannummer = $_POST['eannummer'];

                    $query = "INSERT INTO levering_producten_temp (LeveringID, ProductNaam, Categorie, Streepjescode, Voorraad, Omschrijving, EANnummer) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    if ($stmt === false) {
                        throw new Exception("Error preparing statement: " . $conn->error);
                    }
                    $stmt->bind_param("issssss", $leveringID, $productNaam, $categorie, $streepjescode, $voorraad, $omschrijving, $eannummer);
                    $stmt->execute();
                }

                
                $conn->commit();
                echo "Levering en producten succesvol toegevoegd!";
            } catch (Exception $e) {
                // Rollback bij een fout
                $conn->rollback();
                echo "Er is een fout opgetreden: " . htmlspecialchars($e->getMessage());
            }
            
            if (isset($stmt)) {
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe Levering Toevoegen</title>
    <style>
        .form-container {
            max-width: 800px;
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
        .error {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <h1>Nieuwe Levering Toevoegen</h1>
    <a href="view_suppliers.php" class="button back-button">Terug naar Leveranciers</a>

    <!-- Formulier om een nieuwe levering en producten toe te voegen -->
    <div class="form-container">
        <form method="post" action="">
            <h2>Levering Gegevens</h2>
            <label for="leverancierID">Leverancier ID</label>
            <input type="number" id="leverancierID" name="leverancierID" value="<?php echo htmlspecialchars($leverancierID); ?>" required>
            
            <label for="datum">Datum</label>
            <input type="date" id="datum" name="datum" value="<?php echo htmlspecialchars($datum); ?>" required>
            
            <label for="beschrijving">Beschrijving</label>
            <textarea id="beschrijving" name="beschrijving" rows="4"><?php echo htmlspecialchars($beschrijving); ?></textarea>

            <h2>Producten Toevoegen</h2>
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

            <button type="submit" name="add_delivery">Levering en Producten Toevoegen</button>
            <?php if (!empty($error_message)): ?>
                <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
