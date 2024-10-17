<?php
// Vereist bestand voor het menu
require('menu.php');
?>

<?php
// Verbinding maken met de database (vervang localhost.php door jouw bestand)
require('localhost.php'); 

// Verwerking van het formulier en resetknop
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Reset de Bands- en de gekoppelde Events-gegevens
    if (isset($_POST['reset']) && $_POST['reset'] == '1') {
        // Stap 1: Verwijder de gekoppelde evenementen van de bands
        $deleteRelationQuery = "DELETE FROM Bands_has_Events";
        
        if ($conn->query($deleteRelationQuery) === TRUE) {
            
        } else {
            echo "<p style='color:red;'>Fout bij het verwijderen van gekoppelde evenementen: " . $conn->error . "</p>";
        }

        // Stap 2: Verwijder alle bands uit de Bands tabel
        $deleteBandsQuery = "DELETE FROM Bands";
        
        if ($conn->query($deleteBandsQuery) === TRUE) {
            echo "<p style='color:green;'>Alle Bands succesvol verwijderd.</p>";
        } else {
            echo "<p style='color:red;'>Fout bij het verwijderen van bands: " . $conn->error . "</p>";
        }
    }

    // Verwerk het toevoegen van een nieuwe band
    if (isset($_POST['bandname']) && isset($_POST['genre'])) {
        $bandname = mysqli_real_escape_string($conn, $_POST['bandname']);
        $genre = mysqli_real_escape_string($conn, $_POST['genre']);

        // Controleer of de band al bestaat
        $checkQuery = "SELECT * FROM Bands WHERE bandname = '$bandname'";
        $checkResult = $conn->query($checkQuery);

        if ($checkResult->num_rows > 0) {

        } else {
            // Voeg nieuwe band toe aan de database
            $insertQuery = "INSERT INTO Bands (genre, bandname) VALUES ('$genre', '$bandname')";
            if ($conn->query($insertQuery) === TRUE) {

            } else {
                echo "<p style='color:red;'>Fout bij het opslaan van de band: " . $conn->error . "</p>";
            }
        }
    }
}

// Haal alle bands op en toon ze
$query = $conn->query("SELECT * FROM Bands");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="fullstack.css" rel="stylesheet">
    <title>Band Invoerformulier</title>
</head>
<body>
    <!-- Formulier voor het invoeren van bandnaam en genre -->
    <form method="post" action="">
        <p><strong>Vul hier de bandnaam in:</strong></p>
        <input type="text" name="bandname" required> 
        <p><strong>Genre:</strong></p>
        <input type="radio" name="genre" value="pop" required> Pop<br>
        <input type="radio" name="genre" value="rock"> Rock<br>
        <input type="radio" name="genre" value="klassiek"> Klassiek<br>
        <input type="radio" name="genre" value="jazz"> Jazz<br>
        <br>
        <button type="submit">Verzenden</button>
    </form>

    <!-- Formulier voor het resetten van de bands -->
    <form method="post" action="">
        <button type="submit" name="reset" value="1">Reset Bands</button>
    </form>

    <!-- Tabel om de ingevoerde bands weer te geven -->
    <h2>Alle Bands:</h2>
    <?php
    if ($query->num_rows > 0) {
        echo "<table border='1'>
                <tr>
                    <th>Bandnaam</th>
                    <th>Genre</th>
                </tr>";

        // Loop door de rijen van de bands
        while ($row = $query->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['bandname']) . "</td>";
            echo "<td>" . htmlspecialchars($row['genre']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Geen bands gevonden.</p>";
    }
    ?>
</body>
</html>

