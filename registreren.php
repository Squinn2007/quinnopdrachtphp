<?php
require('localhost.php'); // Zorg ervoor dat dit bestand correct de verbinding maakt met de database
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="fullstack.css">
    <title>Account maken</title>
</head>
<body>
    <!-- Laat het formulier naar dezelfde pagina posten -->
    <form id="formregistreer" action="" method="post">
        <br><br>
        <p id="name">Naam:</p>
        <input type="text" name="name" required>
        <p id="email">Email:</p>
        <input type="email" name="email" required>
        <p id="password">Wachtwoord:</p>
        <input type="password" name="password" required>
        <p id="password2">Wachtwoord bevestigen:</p>
        <input type="password" name="password2" required>
        <br><br>
        <button type="submit">Account aanmaken</button>
    </form>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Haal de gegevens uit het formulier
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $password2 = mysqli_real_escape_string($conn, $_POST['password2']);

    // Controleer of alle velden zijn ingevuld
    if (empty($name) || empty($email) || empty($password) || empty($password2)) {
        echo "<p style='color:red;'>Alle velden moeten ingevuld worden.</p>";
    } 
    // Controleer of de wachtwoorden overeenkomen
    else if ($password !== $password2) {
        echo "<p style='color:red;'>De wachtwoorden komen niet overeen.</p>";
    } 
    // Als alles correct is, sla het op in de database
    else {
        // Versleutel het wachtwoord
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO MyGuests (bandname, email, wachtwoord)
                VALUES ('$name', '$email', '$hashedPassword')";
    if ($conn->query($sql) === TRUE) {
     echo '<script>
                alert("Nieuw account succesvol aangemaakt. Log nu in.");
              window.location.href = "inlogpage.php"; // JavaScript redirect
             </script>';
        exit(); // Zorg ervoor dat de rest van het script niet meer wordt uitgevoerd
    } else {
            echo "<p style='color:red;'>Error: " . $sql . "<br>" . $conn->error . "</p>";
        }
    }
}
?>