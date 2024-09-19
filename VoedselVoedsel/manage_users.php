<?php
session_start();
include 'config.php';

// Role checkersdsds
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'directie') {
    echo "Toegang geweigerd.";
    exit();
}

// Search bar
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $query = "SELECT * FROM users WHERE 
              Username LIKE ? OR 
              Role LIKE ?";
    $searchTerm = "%$search%";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
} else {
    $query = "SELECT * FROM users";
    $stmt = $conn->prepare($query);
}

$stmt->execute();
$result = $stmt->get_result();

// Nieuwe gebruiker toevoegen
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $insertQuery = "INSERT INTO users (Username, PasswordHash, Role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sss", $username, $password, $role);
    if ($stmt->execute()) {
        echo "Nieuwe gebruiker succesvol toegevoegd.";
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Fout bij toevoegen van gebruiker: " . $conn->error;
    }
    $stmt->close();
}

// Verwijder gebruiker
if (isset($_POST['delete_user'])) {
    $userID = $_POST['delete_user_id'];
    $deleteQuery = "DELETE FROM users WHERE UserID = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $userID);
    if ($stmt->execute()) {
        echo "Gebruiker succesvol verwijderd.";
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Fout bij verwijderen van gebruiker: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beheer Gebruikers</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }

        
        .button {
            display: inline-block;
            padding: 10px 15px;
            margin: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            background-color: #4CAF50; 
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #45a049;
        }

        .edit-button {
            background-color: #008CBA; 
        }

        .edit-button:hover {
            background-color: #007bb5;
        }

        .delete-button {
            background-color: #f44336; 
        }

        .delete-button:hover {
            background-color: #e60000;
        }

        
        .search-bar {
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 10px;
            font-size: 16px;
            width: 300px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

       
        .back-button {
            background-color: #555; 
        }

        .back-button:hover {
            background-color: #333;
        }
    </style>
</head>
<body>
    <h1>Gebruikerslijst</h1>

    
    <p><a href="user_dashboard.php" class="button back-button">Terug naar Dashboard</a></p>

    
    <div class="search-bar">
        <form method="get" action="manage_users.php">
            <input type="text" name="search" placeholder="Zoek op Gebruikersnaam of Rol" value="<?php echo htmlspecialchars($search); ?>">
            <input type="submit" value="Zoeken">
        </form>
    </div>

  
    <p><a href="add_user.php" class="button">Nieuwe Gebruiker Toevoegen</a></p>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Gebruikersnaam</th>
                <th>Rol</th>
                <th>Acties</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['Username']); ?></td>
                    <td><?php echo htmlspecialchars($row['Role']); ?></td>
                    <td>
                       
                        <a href="edit_user.php?id=<?php echo $row['UserID']; ?>" class="button edit-button">Bewerken</a>
                        
                        <form method="post" action="manage_users.php" style="display:inline;">
                            <input type="hidden" name="delete_user_id" value="<?php echo $row['UserID']; ?>">
                            <button type="submit" name="delete_user" class="button delete-button">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Geen gebruikers gevonden.</p>
    <?php endif; ?>
</body>
</html>
