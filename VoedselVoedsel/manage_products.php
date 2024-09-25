<?php
session_start();
include 'config.php';   // Zorg dat dit je databaseconfiguratie bevat
include 'autoload.php'; // Hiermee wordt de User-klasse automatisch geladen
include 'classes/Product.php'; // Zorg ervoor dat de Product klasse wordt geladen

// Vereist dat de gebruiker is ingelogd
User::requireLogin();

// Vereist dat de gebruiker een van de toegestane rollen heeft (directie of magazijnmedewerker)
User::requireRole(['directie', 'magazijnmedewerker']);

// Maak een nieuwe instantie van de Product klasse
$productClass = new Product($conn);

// Zoekfunctie
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Haal alle producten op (met of zonder zoekterm)
$result = $productClass->getAllProducts($search);

// Product verwijderen
if (isset($_POST['delete_product'])) {
    $productID = $_POST['delete_product_id'];
    if ($productClass->deleteProduct($productID)) {
        echo "Product succesvol verwijderd.";
        header("Location: manage_products.php");
        exit();
    } else {
        echo "Fout bij verwijderen van product: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producten Beheren</title>
    <link rel="stylesheet" href="css/productlijst.css"> 
</head>
<body>
    <h1>Productenlijst</h1>

    <p><a href="user_dashboard.php" class="button back-button">Terug naar Dashboard</a></p>

    <div class="search-bar">
        <form method="get" action="manage_products.php">
            <input type="text" name="search" placeholder="Zoek op ProductNaam, Categorie, Voorraad, Streepjescode of EANnummer" value="<?php echo htmlspecialchars($search); ?>">
            <input type="submit" value="Zoeken">
        </form>
    </div>

    <?php if ($_SESSION['role'] === 'directie' || $_SESSION['role'] === 'magazijnmedewerker'): ?>
        <p><a href="add_product.php" class="button back-button">Nieuw Product Toevoegen</a></p>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>ProductNaam</th>
                <th>Categorie</th>
                <th>Voorraad</th>
                <th>Streepjescode</th>
                <th>EAN-nummer</th>
                <th>Acties</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['ProductNaam']); ?></td>
                    <td><?php echo htmlspecialchars($row['Categorie']); ?></td>
                    <td><?php echo htmlspecialchars($row['Voorraad']); ?></td>
                    <td><?php echo htmlspecialchars($row['Streepjescode']); ?></td>
                    <td><?php echo htmlspecialchars($row['EANnummer']); ?></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $row['ProductID']; ?>" class="button edit-button">Bijwerken</a>
                        <form method="post" action="manage_products.php" style="display:inline;">
                            <input type="hidden" name="delete_product_id" value="<?php echo $row['ProductID']; ?>">
                            <button type="submit" name="delete_product" class="button delete-button">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Geen producten gevonden.</p>
    <?php endif; ?>
</body>
</html>
