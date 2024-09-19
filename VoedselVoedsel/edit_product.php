<?php
session_start();
include 'config.php';

// zoveel role checkers man crazy
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'directie') {
    echo "Toegang geweigerd.";
    exit();
}

// Haal productgegevens op
if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    $query = "SELECT * FROM producten WHERE ProductID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    
    if (!$product) {
        echo "Product niet gevonden.";
        exit();
    }
}

// Product bijwerken
if (isset($_POST['update_product'])) {
    $productName = $_POST['productName'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $barcode = $_POST['barcode'];
    $ean = $_POST['ean'];
    $productId = $_POST['productId'];

    $query = "UPDATE producten SET ProductNaam = ?, Categorie = ?, Voorraad = ?, Streepjescode = ?, EANnummer = ? WHERE ProductID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssissi", $productName, $category, $stock, $barcode, $ean, $productId);
    if ($stmt->execute()) {
        echo "Product succesvol bijgewerkt.";
        header("Location: manage_products.php");
        exit();
    } else {
        echo "Fout bij het bijwerken van product: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Bijwerken</title>
</head>
<body>
    <h1>Product Bijwerken</h1>
    <form method="post" action="edit_product.php">
        <input type="hidden" name="productId" value="<?php echo htmlspecialchars($product['ProductID']); ?>">
        <label for="productName">Productnaam:</label>
        <input type="text" id="productName" name="productName" value="<?php echo htmlspecialchars($product['ProductNaam']); ?>" required><br>
        <label for="category">Categorie:</label>
        <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($product['Categorie']); ?>" required><br>
        <label for="stock">Voorraad:</label>
        <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($product['Voorraad']); ?>" required><br>
        <label for="barcode">Streepjescode:</label>
        <input type="text" id="barcode" name="barcode" value="<?php echo htmlspecialchars($product['Streepjescode']); ?>" required><br>
        <label for="ean">EAN-nummer:</label>
        <input type="text" id="ean" name="ean" value="<?php echo htmlspecialchars($product['EANnummer']); ?>" required><br>
        <input type="submit" name="update_product" value="Bijwerken">
    </form>
    <p><a href="manage_products.php" class="button">Terug naar Productenlijst</a></p>
</body>
</html>
