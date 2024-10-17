<?php
    require('menu.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="fullstack.css" rel="stylesheet">
    <title>Event Invoer</title>
</head>
<body>
    <!-- Formulier voor het invoeren van een nieuw event -->
    <form method="post" action="">
        <p><strong>Eventnaam</strong></p>
        <input type="text" name="naam" required>

        <!-- Tijd selectie -->
        <p><strong>Hoelaat:</strong></p>
        <select name="time1" required>
            <option value="16:00">16:00u</option>
            <option value="16:30">16:30u</option>
            <option value="17:00">17:00u</option>
            <option value="17:30">17:30u</option>
            <option value="18:00">18:00u</option>
        </select>

        <!-- Datum invoer -->
        <p><strong>Datum:</strong></p>
        <input type="date" name="datum" required>

        <!-- Prijs invoer -->
        <p><strong>Prijs (€):</strong></p>
        <select  name="prijs" required>
        <option value="">niets</option>
        <option value="1500.00">Single optreden: €1500</option>
        <option value="2000.00">All-inclusive optreden: €2000</option>        
        </select>
        <!-- Band selectie -->
        <p><strong>Kies bands (meerdere selecties mogelijk):</strong></p>
        <select name="band_ids[]" multiple required> <!-- Multi-select toegevoegd -->
            <?php
            // Verbind met de database en haal beschikbare bands op voor de dropdown
            require('localhost.php');
            $bandsQuery = $conn->query("SELECT idBands, bandname FROM Bands");
            while ($band = $bandsQuery->fetch_assoc()) {
                echo "<option type='checkbox' value='" . $band['idBands'] . "'>" . htmlspecialchars($band['bandname']) . "</option>";
            }
            ?>
        </select>

        <!-- Verzenden knop -->
        <br><br>
        <button type="submit">Verzenden</button>
    </form>
    <form>
    <a id="inlogpage" href="inlogpage.php">inlogpagina</a>
    </form>
    <!-- Reset Events knop -->
    <form method="post" action="">
        <button type="submit" name="reset" value="1">Reset Events</button>
    </form>

    <!-- Resultaten sectie -->
    <h2>Gekoppelde Events en Bands</h2>
    <div id="results">
        <?php
        // Controleer of het formulier is verzonden via POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['reset'])) {
                // Verwijder alle gekoppelde events als de resetknop is ingedrukt
                $resetQuery = "DELETE FROM Bands_has_Events";
                if ($conn->query($resetQuery) === TRUE) {
                    echo "<p style='color:green;'>Alle events succesvol verwijderd.</p>";
                } else {
                    echo "<p style='color:red;'>Fout bij het verwijderen van events: " . $conn->error . "</p>";
                }
            } else {
                // Haal de ingevoerde gegevens uit het formulier
                $name = mysqli_real_escape_string($conn, $_POST['naam']);
                $hoelaat = mysqli_real_escape_string($conn, $_POST['time1']);
                $datum = mysqli_real_escape_string($conn, $_POST['datum']);
                $prijs = mysqli_real_escape_string($conn, $_POST['prijs']);
                $band_ids = $_POST['band_ids']; // Array van geselecteerde bands

                // Controleer of de tijd al bestaat in de database
                $checkQuery = "SELECT * FROM Events WHERE hoelaat = '$hoelaat'";
                $checkResult = $conn->query($checkQuery);

                if ($checkResult->num_rows >= 1) {
                    // Voeg het nieuwe event toe aan de 'Events'-tabel
                    $insertEventQuery = "INSERT INTO Events (naam, hoelaat, datum, prijs) VALUES ('$name', '$hoelaat', '$datum', '$prijs')";

                    if ($conn->query($insertEventQuery) === TRUE) {
                        // Haal het ID van het zojuist toegevoegde event op
                        $event_id = $conn->insert_id;

                        // Voeg elke geselecteerde band toe aan het event in de 'Bands_has_Events'-tabel
                        foreach ($band_ids as $band_id) {
                            $band_id = mysqli_real_escape_string($conn, $band_id); // Beveiliging
                            $insertBandEventQuery = "INSERT INTO Bands_has_Events (Events_idEvents, Bands_idBands) VALUES ('$event_id', '$band_id')";
                            if (!$conn->query($insertBandEventQuery)) {
                                echo "<p style='color:red;'>Fout bij het koppelen van band met ID $band_id aan event: " . $conn->error . "</p>";
                            }
                        }
                    }
                }

                        echo "<p style='color:green;'>Nieuw event en geselecteerde bands succesvol gekoppeld.</p>";
                    }
                    
        }

        // Controleer of er resultaten zijn en toon ze in een tabel
// Query om data uit beide tabellen te halen en de bands te groeperen
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
    </div>
</body>
</html>
