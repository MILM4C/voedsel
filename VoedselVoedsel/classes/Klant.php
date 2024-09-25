<?php
class Klant {
    private $conn;

    // Constructor om de databaseverbinding te initialiseren
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Functie om alle klanten op te halen
    public function getAllKlanten() {
        $sql = "SELECT k.KlantID, k.Voornaam, k.Achternaam, k.Adres, k.Telefoonnummer, k.Email, 
                       g.AantalVolwassenen, g.AantalKinderen, g.AantalBabies, 
                       GROUP_CONCAT(w.WensOmschrijving SEPARATOR ', ') AS Wensen
                FROM klanten k
                LEFT JOIN gezinssamenstelling g ON k.KlantID = g.GezinID
                LEFT JOIN klantwensen kw ON k.KlantID = kw.KlantID
                LEFT JOIN wensen w ON kw.WensID = w.WensID
                GROUP BY k.KlantID";
        $result = $this->conn->query($sql);
        return $result;
    }

    // Functie om een klant op basis van klantID op te halen
    public function getKlantById($klantID) {
        $stmt = $this->conn->prepare("SELECT k.KlantID, k.Voornaam, k.Achternaam, k.Adres, 
                                               k.Telefoonnummer, k.Email, 
                                               g.AantalVolwassenen, g.AantalKinderen, 
                                               g.AantalBabies, 
                                               GROUP_CONCAT(w.WensOmschrijving SEPARATOR ', ') AS Wensen
                                        FROM klanten k
                                        LEFT JOIN gezinssamenstelling g ON k.KlantID = g.GezinID
                                        LEFT JOIN klantwensen kw ON k.KlantID = kw.KlantID
                                        LEFT JOIN wensen w ON kw.WensID = w.WensID
                                        WHERE k.KlantID = ?
                                        GROUP BY k.KlantID");
        $stmt->bind_param("i", $klantID);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_assoc();
    }

    // Functie om een klant te bewerken
    public function updateKlant($klantID, $voornaam, $achternaam, $adres, $telefoonnummer, $email, $aantal_volwassenen, $aantal_kinderen, $aantal_babies) {
        // Update de klantgegevens
        $stmt = $this->conn->prepare("UPDATE klanten SET Voornaam = ?, Achternaam = ?, Adres = ?, Telefoonnummer = ?, Email = ? WHERE KlantID = ?");
        $stmt->bind_param("sssssi", $voornaam, $achternaam, $adres, $telefoonnummer, $email, $klantID);
        $stmt->execute();
        $stmt->close();
    
        // Update de gezinsamenstelling
        $stmt = $this->conn->prepare("UPDATE gezinssamenstelling SET AantalVolwassenen = ?, AantalKinderen = ?, AantalBabies = ? WHERE GezinID = ?");
        $stmt->bind_param("iiii", $aantal_volwassenen, $aantal_kinderen, $aantal_babies, $klantID); // assuming 'KlantID' is used as 'GezinID'
        $stmt->execute();
        $stmt->close();
    }
    
    

    // Functie om een klant toe te voegen
    public function addKlant($voornaam, $achternaam, $adres, $telefoonnummer, $email) {
        $stmt = $this->conn->prepare("INSERT INTO klanten (Voornaam, Achternaam, Adres, Telefoonnummer, Email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $voornaam, $achternaam, $adres, $telefoonnummer, $email);
        $stmt->execute();
        $stmt->close();
    }

    // Functie om een klant te verwijderen
    public function deleteKlant($klantID) {
        // Eerst de verwijzingen in de klant_pakket tabel verwijderen
        $stmt = $this->conn->prepare("DELETE FROM klant_pakket WHERE KlantID = ?");
        $stmt->bind_param("i", $klantID);
        $stmt->execute();
        $stmt->close();

        // Vervolgens de klant zelf verwijderen
        $stmt = $this->conn->prepare("DELETE FROM klanten WHERE KlantID = ?");
        $stmt->bind_param("i", $klantID);
        $stmt->execute();
        $stmt->close();
    }
}
