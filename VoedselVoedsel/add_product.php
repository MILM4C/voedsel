<?php
session_start();
include 'config.php';

// Role checker lkldskdlskdls
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['directie', 'magazijnmedewerker'])) {
    echo "Toegang geweigerd.";
    exit();
}

// Product toevoegen
if (isset($_POST['add_product'])) {
    $productName = $_POST['productName'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $barcode = $_POST['barcode'];
    $ean = $_POST['ean'];

    $query = "INSERT INTO producten (ProductNaam, Categorie, Voorraad, Streepjescode, EANnummer) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssiss", $productName, $category, $stock, $barcode, $ean);
    if ($stmt->execute()) {
        echo "Product succesvol toegevoegd.";
        header("Location: manage_products.php");
        exit();
    } else {
        echo "Fout bij het toevoegen van product: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuw Product Toevoegen</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Nieuw Product Toevoegen</h1>
    <form method="post" action="add_product.php">
        <label for="productName">Productnaam:</label>
        <input type="text" id="productName" name="productName" required><br>
        <label for="category">Categorie:</label>
        <input type="text" id="category" name="category" required><br>
        <label for="stock">Voorraad:</label>
        <input type="number" id="stock" name="stock" required><br>
        <label for="barcode">Streepjescode:</label>
        <input type="text" id="barcode" name="barcode" required><br>
        <label for="ean">EAN-nummer:</label>
        <input type="text" id="ean" name="ean" required><br>
        <input type="submit" name="add_product" value="Toevoegen">
    </form>
</body>
</html>
