<?php
session_start();
include 'config.php'; 

// Controleer of de gebruiker ingelogd is
if (!isset($_SESSION['user_id'])) {
    echo "Toegang geweigerd.";
    exit();
}

// klantinfo
if (isset($_GET['klant_id'])) {
    $klantID = intval($_GET['klant_id']);

    
    $query = "SELECT k.Voornaam, k.Achternaam, k.Adres, k.Telefoonnummer, k.Email, 
                      g.AantalVolwassenen, g.AantalKinderen, g.AantalBabies, 
                      GROUP_CONCAT(w.WensOmschrijving SEPARATOR ', ') AS Wensen
               FROM klanten k
               LEFT JOIN gezinssamenstelling g ON k.KlantID = g.GezinID
               LEFT JOIN klantwensen kw ON k.KlantID = kw.KlantID
               LEFT JOIN wensen w ON kw.WensID = w.WensID
               WHERE k.KlantID = ?
               GROUP BY k.KlantID";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $klantID);
    $stmt->execute();
    $result = $stmt->get_result();
    $klant = $result->fetch_assoc();

    
    echo json_encode($klant);
} else {
    echo json_encode(['error' => 'Geen klant-ID opgegeven']);
}
?>
