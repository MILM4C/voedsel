<?php
session_start();
include 'config.php';  

// Checkerrol voor ingelogde gebruikers
if (!isset($_SESSION['user_id'])) {
    echo "Toegang geweigerd.";
    exit();
}

// Haal de lijst van klanten op
$query_klanten = "SELECT KlantID, CONCAT(Voornaam, ' ', Achternaam) AS Naam FROM klanten";
$result_klanten = $conn->query($query_klanten);

// Haal de lijst van producten op met een positieve voorraad
$query_producten = "SELECT ProductID, ProductNaam, Voorraad FROM producten WHERE Voorraad > 0";
$result_producten = $conn->query($query_producten);

// voegt het nieuwe pakket toe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $samenstelling = $_POST['samenstelling'];
    $datumAangemaakt = date('Y-m-d');
    $klantID = $_POST['klant'];  // Klant die het pakket ontvangt

    
    $conn->begin_transaction();

    try {
        // Voeg het nieuwe pakket toe aan de tabel
        $query = "INSERT INTO pakketten (Samenstelling, DatumAangemaakt, BestemdVoorKlantID) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $samenstelling, $datumAangemaakt, $klantID);
        $stmt->execute();
        $pakketID = $stmt->insert_id;  // Het ID van het  toegevoegde pakket
        $stmt->close();

        // Voeg producten toe aan het pakket
        foreach ($_POST['producten'] as $productID => $aantal) {
            if ($aantal > 0) {
                // Controleer de voorraad 
                $query = "SELECT Voorraad FROM producten WHERE ProductID = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $productID);
                $stmt->execute();
                $stmt->bind_result($voorraad);
                $stmt->fetch();
                $stmt->close();

                if ($aantal <= $voorraad) {
                    // Voeg het product toe aan het pakket_producten tabel
                    $query = "INSERT INTO pakketinhoud (PakketID, ProductID, Aantal) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("iii", $pakketID, $productID, $aantal);
                    $stmt->execute();
                    $stmt->close();

                    
                    $query = "UPDATE producten SET Voorraad = Voorraad - ? WHERE ProductID = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ii", $aantal, $productID);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }

        // Controleer of de combinatie KlantID en PakketID al bestaat
        $query = "SELECT COUNT(*) FROM klant_pakket WHERE KlantID = ? AND PakketID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $klantID, $pakketID);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count == 0) {
            // Voeg de klant-pakket relatie toe
            $query = "INSERT INTO klant_pakket (KlantID, PakketID, DatumToegewezen) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iis", $klantID, $pakketID, $datumAangemaakt);
            $stmt->execute();
            $stmt->close();
        }

        $conn->commit();
        echo "Voedselpakket succesvol aangemaakt en toegewezen aan klant.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Fout bij het aanmaken van het voedselpakket: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voedselpakket Maken</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .form-group {
            display: flex;
            gap: 20px;
        }
        .form-group .form-left, .form-group .form-right {
            flex: 1;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        textarea, select, input[type="number"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        textarea {
            resize: none;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
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
    </style>
</head>
<body>

<div class="form-container">
    <h1>Nieuw Voedselpakket Maken</h1>

    <form action="create_voedselpakket.php" method="post">
        <div class="form-group">
            <div class="form-left">
                <label for="samenstelling">Beschrijving van het pakket:</label>
                <textarea id="samenstelling" name="samenstelling" rows="4" required></textarea>

                <label for="producten">Producten toevoegen:</label>
                <table>
                    <tr>
                        <th>Product</th>
                        <th>Voorraad</th>
                        <th>Aantal</th>
                    </tr>
                    <?php while ($row_product = $result_producten->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row_product['ProductNaam']); ?></td>
                        <td><?php echo htmlspecialchars($row_product['Voorraad']); ?></td>
                        <td>
                            <input type="number" name="producten[<?php echo $row_product['ProductID']; ?>]" value="0" min="0" max="<?php echo $row_product['Voorraad']; ?>">
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>

            <div class="form-right">
                <label for="klant">Selecteer een klant:</label>
                <select id="klant" name="klant" required onchange="fetchKlantInfo()">
                    <option value="">Selecteer een klant</option>
                    <?php while ($row_klant = $result_klanten->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row_klant['KlantID']); ?>">
                        <?php echo htmlspecialchars($row_klant['Naam']); ?>
                    </option>
                    <?php endwhile; ?>
                </select>

                <div id="klant-info" style="margin-top: 20px;">
                    <p><strong>Wensen:</strong> <span id="wensen"></span></p>
                    <p><strong>Aantal volwassenen:</strong> <span id="aantal-volwassenen"></span></p>
                    <p><strong>Aantal kinderen:</strong> <span id="aantal-kinderen"></span></p>
                    <p><strong>Aantal baby's:</strong> <span id="aantal-babies"></span></p>
                </div>
            </div>
        </div>

        <input type="submit" value="Pakket Aanmaken">
    </form>

    <a href="voedselpakketten.php" class="button">Terug naar Overzicht</a>
</div>

<script>
    function fetchKlantInfo() {
        const klantID = document.getElementById('klant').value;
        if (klantID) {
            fetch(`get_klant_info.php?klant_id=${klantID}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                    } else {
                        document.getElementById('wensen').textContent = data.Wensen || 'Geen wensen beschikbaar';
                        document.getElementById('aantal-volwassenen').textContent = data.AantalVolwassenen || 'Niet gespecificeerd';
                        document.getElementById('aantal-kinderen').textContent = data.AantalKinderen || 'Niet gespecificeerd';
                        document.getElementById('aantal-babies').textContent = data.AantalBabies || 'Niet gespecificeerd';
                    }
                })
                .catch(error => console.error('Fout bij ophalen klantinformatie:', error));
        } else {
            // Leeg de klantinformatie als er geen klant is geselecteerd
            document.getElementById('wensen').textContent = '';
            document.getElementById('aantal-volwassenen').textContent = '';
            document.getElementById('aantal-kinderen').textContent = '';
            document.getElementById('aantal-babies').textContent = '';
        }
    }
</script>

</body>
</html>
