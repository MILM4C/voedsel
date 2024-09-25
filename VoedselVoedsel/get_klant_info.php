<?php
session_start();
include 'config.php'; 

header('Content-Type: application/json');

// Controleer of de gebruiker ingelogd is
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Toegang geweigerd.']);
    exit();
}

// Controleer of klant_id is opgegeven
if (isset($_GET['klant_id'])) {
    $klantID = intval($_GET['klant_id']);

    // Bereid de query voor
    $query = "
        SELECT k.Voornaam, k.Achternaam, k.Adres, k.Telefoonnummer, k.Email, 
               g.AantalVolwassenen, g.AantalKinderen, g.AantalBabies, 
               GROUP_CONCAT(w.WensOmschrijving SEPARATOR ', ') AS Wensen
        FROM klanten k
        LEFT JOIN gezinssamenstelling g ON k.KlantID = g.GezinID
        LEFT JOIN klantwensen kw ON k.KlantID = kw.KlantID
        LEFT JOIN wensen w ON kw.WensID = w.WensID
        WHERE k.KlantID = ?
        GROUP BY k.KlantID
    ";

    if ($stmt = $conn->prepare($query)) {
        // Bind parameters
        $stmt->bind_param("i", $klantID);

        // Voer de query uit
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $klant = $result->fetch_assoc();
                echo json_encode($klant);
            } else {
                echo json_encode(['error' => 'Klant niet gevonden']);
            }
        } else {
            echo json_encode(['error' => 'Fout bij het uitvoeren van de query']);
        }

        // Sluit de statement af
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Fout bij het voorbereiden van de query']);
    }
} else {
    echo json_encode(['error' => 'Geen klant-ID opgegeven']);
}

// Sluit de database-verbinding af
$conn->close();
?>
