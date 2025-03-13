<?php
session_start();


require 'vendor/autoload.php'; // Laad alle Composer-packages
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Zet foutmelding aan voor debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



echo "<h1>PHP Debugging Informatie</h1>";

// Controle PHP versie en extensies
echo "<h2>PHP Versie en Extensies</h2>";
echo "<p>PHP Versie: " . phpversion() . "</p>";
echo "<p>Geladen extensies: " . implode(', ', get_loaded_extensions()) . "</p>";

// Controleer of de benodigde PHP-extensies geladen zijn
$benodigdeExtensies = ['mysqli', 'pdo_mysql', 'openssl', 'mbstring'];
echo "<h3>Status van benodigde extensies:</h3>";
echo "<ul>";
foreach ($benodigdeExtensies as $ext) {
    echo "<li>$ext: " . (extension_loaded($ext) ? "✅ Geladen" : "❌ Niet geladen") . "</li>";
}
echo "</ul>";

// Controle database-verbinding
echo "<h2>Database Verbinding Test</h2>";
try {
    $host = "localhost";
    $dbname = "cursus_inschrijvingen";
    $username = "root";
    $password = "";
    
    $conn = new mysqli($host, $username, $password);
    if ($conn->connect_error) {
        echo "<p>❌ Database verbinding mislukt: " . $conn->connect_error . "</p>";
    } else {
        echo "<p>✅ Database verbinding geslaagd</p>";
        
        // Controleer of de database bestaat
        $result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");
        if ($result->num_rows > 0) {
            echo "<p>✅ Database '$dbname' bestaat</p>";
            
            // Selecteer de database
            $conn->select_db($dbname);
            
            // Controleer of de tabel bestaat
            $result = $conn->query("SHOW TABLES LIKE 'inschrijvingen'");
            if ($result->num_rows > 0) {
                echo "<p>✅ Tabel 'inschrijvingen' bestaat</p>";
                
                // Haal de laatste 5 inschrijvingen op
                $result = $conn->query("SELECT * FROM inschrijvingen ORDER BY inschrijfdatum DESC LIMIT 5");
                echo "<h3>Laatste 5 inschrijvingen:</h3>";
                if ($result->num_rows > 0) {
                    echo "<table border='1' cellpadding='5'>";
                    echo "<tr><th>ID</th><th>Naam</th><th>Email</th><th>Cursus</th><th>Inschrijfdatum</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['naam'] . "</td>";
                        echo "<td>" . $row['email'] . "</td>";
                        echo "<td>" . $row['cursus'] . "</td>";
                        echo "<td>" . $row['inschrijfdatum'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>Geen inschrijvingen gevonden</p>";
                }
            } else {
                echo "<p>❌ Tabel 'inschrijvingen' bestaat niet</p>";
            }
        } else {
            echo "<p>❌ Database '$dbname' bestaat niet</p>";
        }
    }
} catch (Exception $e) {
    echo "<p>❌ Fout bij database test: " . $e->getMessage() . "</p>";
}

// Controle voor sessies
echo "<h2>Sessie Test</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p>✅ Sessies zijn actief</p>";
    echo "<p>Sessie ID: " . session_id() . "</p>";
    echo "<h3>Huidige sessiegegevens:</h3>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
} else {
    echo "<p>❌ Sessies zijn niet actief</p>";
    echo "<p>Probeer session_start() aan te roepen</p>";
}

// Controle formulier en bestanden
echo "<h2>Bestandscontrole</h2>";
$benodigdeBestanden = [
    'inschrijven.html',
    'inschrijven.php',
    'bedankt.php',
    'db_connect.php'
];

echo "<ul>";
foreach ($benodigdeBestanden as $bestand) {
    echo "<li>$bestand: " . (file_exists($bestand) ? "✅ Bestaat" : "❌ Bestaat niet") . "</li>";
}
echo "</ul>";

// Serverinformatie
echo "<h2>Server Informatie</h2>";
echo "<p>Server software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Document root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Script filename: " . $_SERVER['SCRIPT_FILENAME'] . "</p>";

// PHPMailer configuratie test
echo "<h2>PHPMailer Configuratie</h2>";
// Controleer of .env bestaat
echo "<p>.env bestand: " . (file_exists('.env') ? "✅ Bestaat" : "❌ Bestaat niet") . "</p>";

// Controleer of vendor directory bestaat (voor Composer)
echo "<p>vendor directory: " . (is_dir('vendor') ? "✅ Bestaat" : "❌ Bestaat niet") . "</p>";
echo "<p>PHPMailer classes: " . (class_exists('PHPMailer\PHPMailer\PHPMailer') ? "✅ Beschikbaar" : "❌ Niet beschikbaar") . "</p>";

?>