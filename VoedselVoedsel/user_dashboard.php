<?php
session_start();
include 'config.php';  // Zorg voor correcte databaseverbinding
include 'autoload.php'; // Laad de benodigde klassen

// Controleer of de gebruiker is ingelogd en de juiste rol heeft
User::requireLogin();
$user_role = User::getUserRole();  // Haal de rol van de ingelogde gebruiker op
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <h1>Welkom, <?php echo htmlspecialchars($user_role); ?>!</h1>
    <p><a href="logout.php" class="logout-button">Uitloggen</a></p>

    <div class="button-container">
        <?php if ($user_role === 'directie'): ?>
            <a href="manage_users.php" class="button">Beheer Gebruikers</a>
            <a href="klantenbeheer.php" class="button">Beheer Klanten</a>
        <?php endif; ?>
        <?php if ($user_role === 'vrijwilliger' || $user_role === 'directie'): ?>
            <a href="voedselpakketten.php" class="button">Voedselpakket Maken</a>
        <?php endif; ?>

        <?php if ($user_role === 'magazijnmedewerker' || $user_role === 'directie'): ?>
            <a href="manage_products.php" class="button">Beheer Producten</a>
            <a href="view_suppliers.php" class="button">Beheer Leveranciers</a>
        <?php endif; ?>
    </div>
</body>
</html>

