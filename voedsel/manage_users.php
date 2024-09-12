<?php
session_start();
include 'db_connection.php';

// Controleer of de gebruiker ingelogd is en een admin is
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    echo "Toegang geweigerd.";
    exit();
}

// Voeg een nieuwe gebruiker toe
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Valideer rol
    if ($role !== 'admin' && $role !== 'user') {
        echo "Ongeldige rol. Alleen 'admin' of 'user' zijn toegestaan.";
    } else {
        // Hash het wachtwoord
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Voeg de gebruiker toe
        $query = "INSERT INTO users (Username, PasswordHash, Role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $username, $passwordHash, $role);

        if ($stmt->execute()) {
            echo "Gebruiker succesvol toegevoegd.";
        } else {
            echo "Fout bij toevoegen van gebruiker: " . $conn->error;
        }
        $stmt->close();
    }
}

// Verwijder een gebruiker
if (isset($_GET['delete'])) {
    $userID = $_GET['delete'];

    // Verwijder de gebruiker
    $query = "DELETE FROM users WHERE UserID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);

    if ($stmt->execute()) {
        echo "Gebruiker succesvol verwijderd.";
    } else {
        echo "Fout bij verwijderen van gebruiker: " . $conn->error;
    }
    $stmt->close();
}

// Werk gebruikersgegevens bij
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $userID = $_POST['userID'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    // Update de gebruikersgegevens
    $query = "UPDATE users SET Username = ?, Role = ? WHERE UserID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $username, $role, $userID);

    if ($stmt->execute()) {
        echo "Gebruiker succesvol bijgewerkt.";
    } else {
        echo "Fout bij bijwerken van gebruiker: " . $conn->error;
    }
    $stmt->close();
}

// Verkrijg de lijst van gebruikers
$query = "SELECT * FROM users";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beheer Gebruikers</title>
</head>
<body>
    <h1>Beheer Gebruikers</h1>
    <p><a href="admin_dashboard.php">Terug naar dashboard</a></p>

    <!-- Formulier om een nieuwe gebruiker toe te voegen -->
    <h2>Nieuwe Gebruiker Toevoegen</h2>
    <form method="post" action="manage_users.php">
        <label for="username">Gebruikersnaam:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Wachtwoord:</label>
        <input type="password" id="password" name="password" required><br>
        <label for="role">Rol:</label>
        <select id="role" name="role" required>
            <option value="user">Gebruiker</option>
            <option value="admin">Admin</option>
        </select><br>
        <input type="submit" name="add_user" value="Toevoegen">
    </form>

    <!-- Tabel met bestaande gebruikers -->
    <h2>Gebruikerslijst</h2>
    <table border="1">
        <tr>
            <th>Gebruikersnaam</th>
            <th>Rol</th>
            <th>Acties</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <form method="post" action="manage_users.php">
                <td>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($row['Username']); ?>" required>
                </td>
                <td>
                    <select name="role" required>
                        <option value="user" <?php if ($row['Role'] === 'user') echo 'selected'; ?>>Gebruiker</option>
                        <option value="admin" <?php if ($row['Role'] === 'admin') echo 'selected'; ?>>Admin</option>
                    </select>
                </td>
                <td>
                    <input type="hidden" name="userID" value="<?php echo $row['UserID']; ?>">
                    <input type="submit" name="update_user" value="Bijwerken">
                    <a href="manage_users.php?delete=<?php echo $row['UserID']; ?>" onclick="return confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?');">Verwijderen</a>
                </td>
            </form>
        </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>
