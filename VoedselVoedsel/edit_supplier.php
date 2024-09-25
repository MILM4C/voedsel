<?php
include 'config.php';

if (isset($_GET['id'])) {
    $supplier_id = $_GET['id'];

    $query = "SELECT LeverancierID, Bedrijfsnaam, Adres, Contactpersoon, Telefoonnummer, Emailadres FROM leveranciers WHERE LeverancierID = ?";
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

        // Update query om de leverancier bij te werken
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
    <style>
        :root {
            --primary-color: #007BFF;
            --primary-hover: #0056b3;
            --secondary-color: #6c757d;
            --secondary-hover: #5a6268;
            --border-color: #ddd;
            --error-color: #dc3545;
            --input-background: #fff;
            --border-radius: 5px;
            --padding: 12px;
            --font-family: 'Arial', sans-serif;
        }

        body {
            font-family: var(--font-family);
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column; 
            align-items: center; 
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .form-container {
            max-width: 600px;
            width: 100%; 
            padding: 20px;
            background-color: var(--input-background);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center; 
        }

        .form-container label {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
            text-align: center; 
        }

        .form-container input[type="text"], 
        .form-container input[type="email"] {
            width: 100%;
            padding: var(--padding);
            margin-bottom: 15px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            background-color: var(--input-background);
            font-size: 16px;
            text-align: center; 
        }

        .form-container input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .form-container input[type="submit"] {
            width: 100%;
            padding: var(--padding);
            font-size: 16px;
            color: white;
            background-color: var(--primary-color);
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-container input[type="submit"]:hover {
            background-color: var(--primary-hover);
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            padding: var(--padding);
            color: white;
            background-color: var(--secondary-color);
            border-radius: var(--border-radius);
            transition: background-color 0.3s;
            text-align: center; 
            width: 100%; 
            max-width: 600px; 
        }

        .back-link:hover {
            background-color: var(--secondary-hover);
        }

 
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            .form-container {
                padding: 15px;
            }

            .form-container input[type="submit"] {
                padding: 10px;
            }

            .back-link {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <h1>Leverancier Bewerken</h1>
    
    <div class="form-container">
        <form action="edit_supplier.php?id=<?php echo $supplier_id; ?>" method="post">
            <label for="bedrijfsnaam">Bedrijfsnaam:</label>
            <input type="text" id="bedrijfsnaam" name="bedrijfsnaam" value="<?php echo htmlspecialchars($supplier['Bedrijfsnaam']); ?>" required>

            <label for="adres">Adres:</label>
            <input type="text" id="adres" name="adres" value="<?php echo htmlspecialchars($supplier['Adres']); ?>">

            <label for="contactpersoon">Contactpersoon:</label>
            <input type="text" id="contactpersoon" name="contactpersoon" value="<?php echo htmlspecialchars($supplier['Contactpersoon']); ?>">

            <label for="telefoonnummer">Telefoonnummer:</label>
            <input type="text" id="telefoonnummer" name="telefoonnummer" value="<?php echo htmlspecialchars($supplier['Telefoonnummer']); ?>">

            <label for="emailadres">Emailadres:</label>
            <input type="email" id="emailadres" name="emailadres" value="<?php echo htmlspecialchars($supplier['Emailadres']); ?>">

            <input type="submit" value="Bijwerken">
        </form>
    </div>

    <a href="view_suppliers.php" class="back-link">Terug naar leveranciersoverzicht</a>
</body>
</html>
