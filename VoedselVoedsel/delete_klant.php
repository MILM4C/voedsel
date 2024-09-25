<?php
session_start();
include 'config.php';  
include 'autoload.php';  // Zorg ervoor dat de Klant klasse wordt geladen

// Controleer of de gebruiker is ingelogd
User::requireLogin();

// Haal het klant ID uit de URL
if (isset($_GET['klant_id'])) {
    $klantID = $_GET['klant_id'];

    // Verwijder de klant uit de database
    $klantClass = new Klant($conn);
    $klantClass->deleteKlant($klantID);

    // Stuur terug naar de klantenbeheerpagina
    header("Location: klantenbeheer.php");
    exit();
} else {
    echo "Geen klant ID opgegeven!";
    exit();
}
?>
