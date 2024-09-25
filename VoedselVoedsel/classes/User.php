<?php
class User {
    public static function requireLogin() {
        // Controleer of de gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            // Als de gebruiker niet is ingelogd, doorverwijzen naar de loginpagina
            header("Location: login.php");
            exit();
        }
    }

    public static function requireRole($allowedRoles) {
        // Controleer of de rol van de gebruiker is toegestaan
        if (!in_array($_SESSION['role'], $allowedRoles)) {
            // Als de rol van de gebruiker niet in de toegestane rollen zit, toon een foutbericht
            echo "Je hebt geen toegang tot deze pagina.";
            exit();
        }
    }

    public static function getUserRole() {
        // Haal de gebruikersrol op uit de sessie
        return $_SESSION['role'] ?? null;  // retourneert null als de rol niet is ingesteld
    }
}
