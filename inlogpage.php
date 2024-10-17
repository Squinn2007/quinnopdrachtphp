<?php
require('menu.php');
require('localhost.php'); // Zorg dat dit bestand correct de verbinding maakt met de database

// Controleer of de verbinding succesvol is
if ($conn->connect_error) {
    die("Verbinding met de database mislukt: " . $conn->connect_error);
}

// Verwerk de login als het formulier is verzonden
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Haal de ingevoerde gegevens op en controleer of ze niet leeg zijn
    if (!empty($_POST['name']) && !empty($_POST['password'])) {
        $name = $_POST['name'];
        $password = $_POST['password'];

        // Gebruik een prepared statement om SQL-injectie te voorkomen
        $stmt = $conn->prepare("SELECT * FROM MyGuests WHERE bandname = ? AND wachtwoord = ?");
        $stmt->bind_param("ss", $name, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Als de gegevens kloppen, geef een succesbericht en stuur door naar bands.php
            echo "<script>alert('Inlog succesvol, welkom $name');</script>";

            // Verwijs naar bands.php met verborgen formulier en POST
            echo "<form id='redirectForm' action='Bands.php' method='post'>
                      <input type='hidden' name='name' value='$name'>
                      <input type='hidden' name='password' value='$password'>
                  </form>
                  <script type='text/javascript'>
                      document.getElementById('redirectForm').submit();
                  </script>";
        } else {
            // Als de gegevens niet kloppen, geef een foutmelding
            echo "<p style='color:red;'>Naam of wachtwoord is onjuist. Probeer het opnieuw. </p>";
        }
        
        $stmt->close(); // Sluit het prepared statement
    } else {
        // Als naam of wachtwoord niet zijn ingevuld, geef een foutmelding
        if (empty($_POST['name'])) {
            echo "<p style='color:red;'>Naam mag niet leeg zijn.</p>";
        }
        if (empty($_POST['password'])) {
            echo "<p style='color:red;'>Wachtwoord mag niet leeg zijn.</p>";
        }
    }
}

// Sluit de databaseverbinding
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="fullstack.css">
    <title>Inlog</title>
</head>
<body>
<form id="forminlog" action="" method="post">
    <br><br>
    <p id="name">Naam:</p>
    <input type="text" name="name" required>
    <br><br>
    <p>Wachtwoord:</p>
    <input type="password" name="password" required>
    <br><br>
    <button type="submit">Verzenden</button>
</form>
</body>
<form>
<a id="registreren" href="http://localhost/website/phpfullstack/registreren.php">inloggegevens vergeten? Registreren</a>
</form>
<?php
require("localhost.php");

$query = $conn->query("
    SELECT Events.naam AS eventnaam, Events.hoelaat, Events.datum, Events.prijs,
           GROUP_CONCAT(Bands.bandname ORDER BY Bands.bandname ASC SEPARATOR ', ') AS bands,
           GROUP_CONCAT(Bands.genre ORDER BY Bands.genre ASC SEPARATOR ', ') AS genres
    FROM Bands_has_Events
    INNER JOIN Events ON Bands_has_Events.Events_idEvents = Events.idEvents
    INNER JOIN Bands ON Bands_has_Events.Bands_idBands = Bands.idBands
    GROUP BY Events.idEvents
");

// Controleer of er resultaten zijn en toon ze in een tabel
if ($query->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>Eventnaam</th>
                <th>Bands</th>
                <th>Genres</th>
                <th>Tijd</th>
                <th>Datum</th>
                <th>Prijs (€)</th>
            </tr>";

    while ($row = $query->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['eventnaam']) . "</td>";  // Weergave van de eventnaam
        echo "<td>" . htmlspecialchars($row['bands']) . "</td>";      // Weergave van de bands (gecombineerd)
        echo "<td>" . htmlspecialchars($row['genres']) . "</td>";     // Weergave van de genres (gecombineerd)
        echo "<td>" . htmlspecialchars($row['hoelaat']) . "</td>";    // Weergave van de tijd
        echo "<td>" . htmlspecialchars($row['datum']) . "</td>";      // Weergave van de datum
        echo "<td>€" . htmlspecialchars($row['prijs']) . "</td>";     // Weergave van de prijs
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>Geen evenementen gevonden</p>";
}
?>

</html>