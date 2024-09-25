<?php
session_start();
include 'config.php';   // Zorg dat dit je databaseconfiguratie bevat
include 'autoload.php'; // Hiermee wordt de User-klasse automatisch geladen

// Vereist dat de gebruiker is ingelogd
User::requireLogin();

// Vereist dat de gebruiker een van de toegestane rollen heeft (directie of magazijnmedewerker)
User::requireRole(['directie']);

$userID = $_GET['id'];


$query = "SELECT UserID, Username, Role, PasswordHash FROM users WHERE UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Gebruiker niet gevonden.";
    exit();
}

$user = $result->fetch_assoc();

// Update gebruiker als het formulier is ingediend
if (isset($_POST['update_user'])) {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $password = isset($_POST['password']) && !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['PasswordHash'];

    // Query om gebruiker bij te werken
    $updateQuery = "UPDATE users SET Username = ?, Role = ?, PasswordHash = ? WHERE UserID = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssi", $username, $role, $password, $userID);

    if ($stmt->execute()) {
        echo "Gebruiker succesvol bijgewerkt.";
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Fout bij het bijwerken van gebruiker: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gebruiker Bewerken</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <h1>Gebruiker Bewerken</h1>

    <p><a href="manage_users.php" class="back-button">Terug naar Beheer Gebruikers</a></p>

    <form method="post" action="edit_user.php?id=<?php echo $userID; ?>">
        <label for="username">Gebruikersnaam:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['Username']); ?>" required><br>

        <label for="role">Rol:</label>
        <select id="role" name="role" required>
            <option value="directie" <?php echo $user['Role'] === 'directie' ? 'selected' : ''; ?>>Directie</option>
            <option value="magazijnmedewerker" <?php echo $user['Role'] === 'magazijnmedewerker' ? 'selected' : ''; ?>>Magazijnmedewerker</option>
            <option value="vrijwilliger" <?php echo $user['Role'] === 'vrijwilliger' ? 'selected' : ''; ?>>Vrijwilliger</option>
        </select><br>

        <label for="password">Wachtwoord (laat leeg om te behouden):</label>
        <input type="password" id="password" name="password"><br>

        <input type="submit" name="update_user" value="Bijwerken">
    </form>
</body>
</html>
