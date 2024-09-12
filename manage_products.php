<?php
session_start();
include 'db_connection.php';

// Controleer of de gebruiker ingelogd is en een admin is
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    echo "Toegang geweigerd.";
    exit();
}

// Voeg een nieuw product toe
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $productName = $_POST['productName'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];

    // Voeg het product toe
    $query = "INSERT INTO producten (ProductNaam, Categorie, Voorraad) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $productName, $category, $stock);

    if ($stmt->execute()) {
        echo "Product succesvol toegevoegd.";
    } else {
        echo "Fout bij toevoegen van product: " . $conn->error;
    }
    $stmt->close();
}

// Verwijder een product
if (isset($_GET['delete'])) {
    $productID = $_GET['delete'];

    // Verwijder het product
    $query = "DELETE FROM producten WHERE ProductID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $productID);

    if ($stmt->execute()) {
        echo "Product succesvol verwijderd.";
    } else {
        echo "Fout bij verwijderen van product: " . $conn->error;
    }
    $stmt->close();
}

// Werk productgegevens bij
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $productID = $_POST['productID'];
    $productName = $_POST['productName'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];

    // Update het product
    $query = "UPDATE producten SET ProductNaam = ?, Categorie = ?, Voorraad = ? WHERE ProductID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssii", $productName, $category, $stock, $productID);

    if ($stmt->execute()) {
        echo "Product succesvol bijgewerkt.";
    } else {
        echo "Fout bij bijwerken van product: " . $conn->error;
    }
    $stmt->close();
}

// Verkrijg de lijst van producten
$query = "SELECT * FROM producten";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beheer Producten</title>
</head>
<body>
    <h1>Beheer Producten</h1>
    <p><a href="admin_dashboard.php">Terug naar dashboard</a></p>

    <!-- Formulier om een nieuw product toe te voegen -->
    <h2>Nieuw Product Toevoegen</h2>
    <form method="post" action="manage_products.php">
        <label for="productName">Productnaam:</label>
        <input type="text" id="productName" name="productName" required><br>
        <label for="category">Categorie:</label>
        <input type="text" id="category" name="category" required><br>
        <label for="stock">Voorraad:</label>
        <input type="number" id="stock" name="stock" required><br>
        <input type="submit" name="add_product" value="Toevoegen">
    </form>

    <!-- Tabel met bestaande producten -->
    <h2>Productenlijst</h2>
    <table border="1">
        <tr>
            <th>Productnaam</th>
            <th>Categorie</th>
            <th>Voorraad</th>
            <th>Acties</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <form method="post" action="manage_products.php">
                <td>
                    <input type="text" name="productName" value="<?php echo htmlspecialchars($row['ProductNaam']); ?>" required>
                </td>
                <td>
                    <input type="text" name="category" value="<?php echo htmlspecialchars($row['Categorie']); ?>" required>
                </td>
                <td>
                    <input type="number" name="stock" value="<?php echo htmlspecialchars($row['Voorraad']); ?>" required>
                </td>
                <td>
                    <input type="hidden" name="productID" value="<?php echo $row['ProductID']; ?>">
                    <input type="submit" name="update_product" value="Bijwerken">
                    <a href="manage_products.php?delete=<?php echo $row['ProductID']; ?>" onclick="return confirm('Weet je zeker dat je dit product wilt verwijderen?');">Verwijderen</a>
                </td>
            </form>
        </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>
