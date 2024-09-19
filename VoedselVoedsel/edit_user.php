<?php
session_start();
include 'config.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'directie') {
    echo "Toegang geweigerd.";
    exit();
}


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Ongeldige gebruiker ID.";
    exit();
}

$userID = $_GET['id'];


$query = "SELECT * FROM users WHERE UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "Gebruiker niet gevonden.";
    exit();
}
$user = $result->fetch_assoc();


if (isset($_POST['update_user'])) {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['PasswordHash'];

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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
        }

        form {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input[type="text"], input[type="password"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .back-button {
            background-color: #555; 
            display: inline-block;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .back-button:hover {
            background-color: #333;
        }
    </style>
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
