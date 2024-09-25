<?php
session_start();
include 'config.php';   // Zorg dat dit je databaseconfiguratie bevat
include 'autoload.php'; // Hiermee wordt de User-klasse automatisch geladen

// Vereist dat de gebruiker is ingelogd
User::requireLogin();

// Vereist dat de gebruiker een van de toegestane rollen heeft (directie of magazijnmedewerker)
User::requireRole(['directie']);

// Zoekfunctie
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $query = "SELECT UserID, Username, Role FROM users WHERE 
              Username LIKE ? OR 
              Role LIKE ?";
    $searchTerm = "%$search%";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
} else {
    // Specifieke kolommen selecteren
    $query = "SELECT UserID, Username, Role FROM users";
    $stmt = $conn->prepare($query);
}

$stmt->execute();
$result = $stmt->get_result();

// Nieuwe gebruiker toevoegen
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $insertQuery = "INSERT INTO users (Username, PasswordHash, Role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sss", $username, $password, $role);
    if ($stmt->execute()) {
        echo "Nieuwe gebruiker succesvol toegevoegd.";
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Fout bij toevoegen van gebruiker: " . $conn->error;
    }
    $stmt->close();
}

// Verwijder gebruiker
if (isset($_POST['delete_user'])) {
    $userID = $_POST['delete_user_id'];
    $deleteQuery = "DELETE FROM users WHERE UserID = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $userID);
    if ($stmt->execute()) {
        echo "Gebruiker succesvol verwijderd.";
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Fout bij verwijderen van gebruiker: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beheer Gebruikers</title>
    <link rel="stylesheet" href="css/manageusers.css">
</head>
<body>
    <h1 style="text-align:center;">Gebruikerslijst</h1>

    <p><a href="user_dashboard.php" class="back-button">Terug naar Dashboard</a></p>

    <div class="search-bar">
        <form method="get" action="manage_users.php">
            <input type="text" name="search" placeholder="Zoek op Gebruikersnaam of Rol" value="<?php echo htmlspecialchars($search); ?>">
            <input type="submit" value="Zoeken">
        </form>
    </div>

    <p><a href="add_user.php" class="add-button">Nieuwe Gebruiker Toevoegen</a></p>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Gebruikersnaam</th>
                <th>Rol</th>
                <th>Acties</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['Username']); ?></td>
                    <td><?php echo htmlspecialchars($row['Role']); ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $row['UserID']; ?>" class="edit-button">Bewerken</a>
                        <form method="post" action="manage_users.php" style="display:inline;">
                            <input type="hidden" name="delete_user_id" value="<?php echo $row['UserID']; ?>">
                            <button type="submit" name="delete_user" class="delete-button">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p style="text-align: center;">Geen gebruikers gevonden.</p>
    <?php endif; ?>
</body>
</html>
