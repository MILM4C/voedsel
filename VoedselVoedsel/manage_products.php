<?php
session_start();
include 'config.php';

// Role checkeeeerrr
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['directie', 'magazijnmedewerker'])) {
    echo "Toegang geweigerd.";
    exit();
}

// Zoekfunctie
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $query = "SELECT * FROM producten WHERE 
              ProductNaam LIKE ? OR 
              Categorie LIKE ? OR 
              Voorraad LIKE ? OR 
              Streepjescode LIKE ? OR 
              EANnummer LIKE ?";
    $searchTerm = "%$search%";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
} else {
    $query = "SELECT * FROM producten";
    $stmt = $conn->prepare($query);
}

$stmt->execute();
$result = $stmt->get_result();

// Product verwijderen
if (isset($_POST['delete_product'])) {
    $productID = $_POST['delete_product_id'];
    $deleteQuery = "DELETE FROM producten WHERE ProductID = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $productID);
    if ($stmt->execute()) {
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
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }

        .button {
            display: inline-block;
            padding: 10px 15px;
            margin: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            background-color: #4CAF50; 
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #45a049;
        }

        .edit-button {
            background-color: #008CBA; 
        }

        .edit-button:hover {
            background-color: #007bb5;
        }

        .delete-button {
            background-color: #f44336; 
        }

        .delete-button:hover {
            background-color: #e60000;
        }

        .search-bar {
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 10px;
            font-size: 16px;
            width: 300px;
            margin-right: 10px;
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

        .back-button {
            background-color: #555; 
        }

        .back-button:hover {
            background-color: #333;
        }
    </style>
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
        <p><a href="add_product.php" class="button">Nieuw Product Toevoegen</a></p>
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
