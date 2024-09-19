<?php
session_start();
include 'config.php';  

// Role checkerererers man im going craaazzzy
if (!isset($_SESSION['user_id'])) {
    echo "Toegang geweigerd.";
    exit();
}

// Verwijder het voedselpakket
if (isset($_GET['pakket_id'])) {
    $pakketID = intval($_GET['pakket_id']);

    $conn->begin_transaction();

    try {
        //  Verwijder records uit de klant_pakket-tabel die naar het pakket verwijzen
        $query = "DELETE FROM klant_pakket WHERE PakketID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $pakketID);
        $stmt->execute();
        $stmt->close();

        //  Haal de producten en aantallen uit het pakket
        $query = "SELECT ProductID, Aantal FROM pakketinhoud WHERE PakketID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $pakketID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $productID = $row['ProductID'];
            $aantal = $row['Aantal'];

            //  Herstel de voorraad
            $query = "UPDATE producten SET Voorraad = Voorraad + ? WHERE ProductID = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $aantal, $productID);
            $stmt->execute();
            $stmt->close();
        }

        //  Verwijder de pakketinhoud
        $query = "DELETE FROM pakketinhoud WHERE PakketID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $pakketID);
        $stmt->execute();
        $stmt->close();

        // Verwijder het pakket
        $query = "DELETE FROM pakketten WHERE PakketID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $pakketID);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        echo "Voedselpakket succesvol verwijderd en voorraad hersteld.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Fout bij het verwijderen van het voedselpakket: " . $e->getMessage();
    }
} else {
    echo "Geen pakket ID opgegeven.";
}
?>
