<?php
// Databaseconfiguratie
$host = "localhost";
$dbname = "cursus_inschrijvingen";
$username = "root";
$password = ""; // Laat leeg als je geen wachtwoord hebt ingesteld

// Probeer verbinding te maken met de database
try {
    $conn = new mysqli($host, $username, $password);
    
    // Controleer de verbinding
    if ($conn->connect_error) {
        error_log("Verbinding mislukt: " . $conn->connect_error);
        throw new Exception("Verbinding mislukt");
    }
    
    // Controleer of database bestaat, zo niet, maak deze aan
    $conn->query("CREATE DATABASE IF NOT EXISTS `$dbname`");
    
    // Selecteer de database
    $conn->select_db($dbname);
    
    // Bereid de database voor op UTF-8
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    // Log de fout, maar ga door
    error_log("Database verbindingsfout: " . $e->getMessage());
}
?>