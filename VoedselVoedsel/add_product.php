<?php
session_start();
include 'config.php';
include 'autoload.php';
include 'classes/Product.php'; // Voeg de Product-klasse toe

// Vereist dat de gebruiker is ingelogd
User::requireLogin();

// Vereist dat de gebruiker een van de toegestane rollen heeft 
User::requireRole(['directie', 'magazijnmedewerker']);

// Maak een nieuwe instantie van de Product klasse
$productClass = new Product($conn);

// Product toevoegen
if (isset($_POST['add_product'])) {
    $productName = $_POST['productName'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $barcode = $_POST['barcode'];
    $ean = $_POST['ean'];

    if ($productClass->addProduct($productName, $category, $stock, $barcode, $ean)) {
        echo "Product succesvol toegevoegd.";
        header("Location: manage_products.php");
        exit();
    } else {
        echo "Fout bij het toevoegen van product: " . $conn->error;
    }
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
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /
            background-color: #f4f4f4; 
        }

        .container {
            max-width: 600px;
            width: 100%;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

        input[type="submit"], .back-button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%; 
            margin-top: 20px; 
        }

        input[type="submit"]:hover, .back-button:hover {
            background-color: #45a049;
        }

        .back-button {
            background-color: #007BFF; 
            margin-top: 30px; 
        }

        .back-button:hover {
            background-color: #0056b3; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="text-align:center;">Nieuw Product Toevoegen</h1>
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
        <a href="manage_products.php" class="back-button">Terug</a> 
    </div>
</body>
</html>
