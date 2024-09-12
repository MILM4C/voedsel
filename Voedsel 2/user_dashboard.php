<?php
session_start();

// Controleer of de gebruiker ingelogd is
if (!isset($_SESSION['user_id'])) {
    echo "Toegang geweigerd.";
    exit();
}

// Verkrijg de rol van de ingelogde gebruiker
$user_role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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

        .logout-button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: white;
            background-color: #dc3545; /* Rood voor uitloggen */
            border: 1px solid #c82333;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.3s, border-color 0.3s;
            margin: 20px;
            display: block;
            text-align: center;
        }

        .logout-button:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }

        .button-wrapper {
            display: flex;
            flex-wrap: wrap;
            border: 2px solid #007BFF;
            border-radius: 5px;
            overflow: hidden;
        }

        .button {
            display: block;
            padding: 15px 30px;
            font-size: 16px;
            font-weight: bold;
            color: white;
            background-color: #007BFF;
            text-align: center;
            text-decoration: none;
            border: 0;
            transition: background-color 0.3s;
            flex: 1 1 auto; /* Zorgt ervoor dat knoppen flexibel zijn */
        }

        .button:hover {
            background-color: #0056b3;
        }

        .button:not(:last-child) {
            border-right: 2px solid #0056b3;
        }

        /* Responsieve aanpassingen */
        @media (max-width: 600px) {
            .button-wrapper {
                flex-direction: column;
            }

            .button:not(:last-child) {
                border-right: none;
                border-bottom: 2px solid #0056b3;
            }
        }
    </style>
</head>
<body>
    <h1>Welkom, <?php echo htmlspecialchars($user_role); ?>!</h1>
    <p><a href="logout.php" class="logout-button">Uitloggen</a></p>

    <div class="button-container">
        <div class="button-wrapper">
            <?php if ($user_role === 'directie'): ?>
                <a href="manage_users.php" class="button">Beheer Gebruikers</a>
            <?php endif; ?>
            <a href="manage_products.php" class="button">Beheer Producten</a>
            <a href="view_suppliers.php" class="button">Beheer Leveranciers</a>
        </div>
    </div>

    <!-- Hier komen de adminfunctionaliteiten -->

</body>
</html>
