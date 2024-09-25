<?php
class Product {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // Haal alle producten op met optionele zoekparameters
    public function getAllProducts($search = '') {
        if ($search) {
            $query = "SELECT ProductID, ProductNaam, Categorie, Voorraad, Streepjescode, EANnummer FROM producten WHERE 
                      ProductNaam LIKE ? OR 
                      Categorie LIKE ? OR 
                      Voorraad LIKE ? OR 
                      Streepjescode LIKE ? OR 
                      EANnummer LIKE ?";
            $searchTerm = "%$search%";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        } else {
            $query = "SELECT ProductID, ProductNaam, Categorie, Voorraad, Streepjescode, EANnummer FROM producten";
            $stmt = $this->conn->prepare($query);
        }

        $stmt->execute();
        return $stmt->get_result();
    }

    // Verwijder een product
    public function deleteProduct($productID) {
        $deleteQuery = "DELETE FROM producten WHERE ProductID = ?";
        $stmt = $this->conn->prepare($deleteQuery);
        $stmt->bind_param("i", $productID);
        return $stmt->execute();
    }

    // Voeg een nieuw product toe
    public function addProduct($productNaam, $categorie, $voorraad, $streepjescode, $eanNummer) {
        $insertQuery = "INSERT INTO producten (ProductNaam, Categorie, Voorraad, Streepjescode, EANnummer) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($insertQuery);
        $stmt->bind_param("ssiss", $productNaam, $categorie, $voorraad, $streepjescode, $eanNummer);
        return $stmt->execute();
    }

    // Update een bestaand product
    public function updateProduct($productID, $productNaam, $categorie, $voorraad, $streepjescode, $eanNummer) {
        $updateQuery = "UPDATE producten SET ProductNaam = ?, Categorie = ?, Voorraad = ?, Streepjescode = ?, EANnummer = ? WHERE ProductID = ?";
        $stmt = $this->conn->prepare($updateQuery);
        $stmt->bind_param("ssissi", $productNaam, $categorie, $voorraad, $streepjescode, $eanNummer, $productID);
        return $stmt->execute();
    }

    // Haal een product op aan de hand van het ID
    public function getProductByID($productID) {
        $query = "SELECT ProductID, ProductNaam, Categorie, Voorraad, Streepjescode, EANnummer FROM producten WHERE ProductID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $productID);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
