<?php
session_start();
include 'db_connection.php';

// Controleer of de gebruiker ingelogd is en een admin is
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    echo "Toegang geweigerd.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $productName = $_POST['productName'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];

    // Voorbereide statement voor het toevoegen van het product
    $query = "INSERT INTO producten (ProductNaam, Categorie, Voorraad) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $productName, $category, $stock);

    if ($stmt->execute()) {
        echo "Product succesvol toegevoegd.";
    } else {
        echo "Fout bij toevoegen van product: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Toevoegen</title>
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
        <input type="submit" name="add_product" value="Toevoegen">
    </form>
    <p><a href="admin_page.php">Terug naar Admin Dashboard</a></p>
</body>
</html>
