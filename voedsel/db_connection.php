<?php
$servername = "localhost"; // Je servernaam
$username = "root";        // Je database gebruikersnaam
$password = "";            // Je database wachtwoord
$dbname = "voedsel";       // Je database naam

// Maak de verbinding
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}
?>
