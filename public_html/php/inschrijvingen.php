<?php
require 'db_connect.php';

$sql = "SELECT id, naam, email, cursus, locatie, adres, opmerkingen, inschrijfdatum FROM inschrijvingen ORDER BY inschrijfdatum DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inschrijvingen Overzicht</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { color: #d45a2a; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f8f8f8; }
    </style>
</head>
<body>

    <h2>Overzicht van Inschrijvingen</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Naam</th>
            <th>Email</th>
            <th>Cursus</th>
            <th>Locatie</th>
            <th>Adres</th>
            <th>Opmerkingen</th>
            <th>Inschrijfdatum</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['naam']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['cursus']}</td>
                        <td>{$row['locatie']}</td>
                        <td>{$row['adres']}</td>
                        <td>{$row['opmerkingen']}</td>
                        <td>{$row['inschrijfdatum']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='7'>Geen inschrijvingen gevonden.</td></tr>";
        }
        $conn->close();
        ?>
    </table>

</body>
</html>
