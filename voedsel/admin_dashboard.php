<?php 
session_start();
include 'db_connection.php';

// Controleer of de gebruiker ingelogd is en een admin is
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    echo "Toegang geweigerd.";
    exit();
}

// HTML Layout voor de adminpagina
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welkom, Admin!</h1>
    <p><a href="logout.php">Uitloggen</a></p>

    <h2>Beheeropties</h2>
    <ul>
        <li><a href="manage_users.php">Beheer Gebruikers</a></li>
        <li><a href="manage_products.php">Beheer Producten</a></li>
    </ul>
</body>
</html>
