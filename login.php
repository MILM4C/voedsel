<?php 
session_start();
include 'db_connection.php';

// Verwerk het loginformulier
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Zoek de gebruiker op in de database
    $query = "SELECT UserID, PasswordHash, Role FROM users WHERE Username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verifieer wachtwoord en rol
    if ($user && password_verify($password, $user['PasswordHash'])) {
        $_SESSION['admin'] = ($user['Role'] === 'admin');
        $_SESSION['user_id'] = $user['UserID'];
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Ongeldige gebruikersnaam of wachtwoord.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form method="post" action="login.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
