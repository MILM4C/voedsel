<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "voedsel-2"; 

// Maak verbinding met de database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}
?>
