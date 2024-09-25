<?php

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; 

    if (!empty($username) && !empty($password) && !empty($role)) {
        
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        
        $stmt = $pdo->prepare('INSERT INTO users (Username, PasswordHash, Role) VALUES (?, ?, ?)');
        $stmt->execute([$username, $passwordHash, $role]);

        echo 'Registration successful!';
    } else {
        echo 'Please fill in all fields.';
    }
}
?>

<form method="post" action="">
    <label>Username:</label><br>
    <input type="text" name="username" required><br>
    <label>Password:</label><br>
    <input type="password" name="password" required><br>
    <label>Role:</label><br>
    <select name="role" required>
        <option value="vrijwilliger">Vrijwilliger</option>
        <option value="magazijnmedewerker">Magazijnmedewerker</option>
        <option value="directie">Directie</option>
    </select><br>
    <button type="submit">Register</button>
</form>
