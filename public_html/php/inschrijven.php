<?php
session_start();

if (isset($_GET['reset']) && $_GET['reset'] === 'true') {
    session_unset();
    session_destroy();
    echo "Sessie gereset. Je kunt nu opnieuw inschrijven.";
    exit();
}

// Debugging (haal weg in productie)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt'); 

// Composer autoloader (Check the correct path)
require_once __DIR__ . '/../vendor/autoload.php';

// Gebruik PHPMailer & Dotenv
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// Laad .env (indien aanwezig)
if (file_exists(__DIR__ . '/../.env')) { // Ensure correct path
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..'); 
    $dotenv->load();
}

// Databaseverbinding (Check correct path)
require_once __DIR__ . '/db_connect.php';

// SMTP-instellingen
$smtp_host = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
$smtp_user = $_ENV['SMTP_USER'] ?? 'info@brantpeije.nl';
$smtp_pass = $_ENV['SMTP_PASS'] ?? '';
$smtp_port = $_ENV['SMTP_PORT'] ?? 587; // Standard Gmail port

error_log("Script gestart.");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    error_log("Formulier verzonden.");

    // Gegevens ophalen en sanitiseren
    $naam        = htmlspecialchars($_POST['naam'] ?? '');
    $email       = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $cursus      = htmlspecialchars($_POST['cursus'] ?? '');
    $locatie     = htmlspecialchars($_POST['locatie'] ?? '');
    $adres       = isset($_POST['adres']) ? htmlspecialchars($_POST['adres']) : 'N.v.t.';
    $opmerkingen = isset($_POST['opmerkingen']) ? htmlspecialchars($_POST['opmerkingen']) : '';

    // Check op dubbele inschrijving binnen 1 minuut
    try {
        $check_stmt = $conn->prepare("
            SELECT id FROM inschrijvingen 
            WHERE email = ? AND cursus = ? 
            AND inschrijfdatum > NOW() - INTERVAL 1 MINUTE
        ");
        $check_stmt->bind_param("ss", $email, $cursus);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            error_log("Dubbele inschrijving gedetecteerd voor: $email");
            $_SESSION['inschrijving_voltooid'] = true;
            $_SESSION['inschrijving_info'] = compact('naam', 'email', 'cursus', 'locatie');
            header("Location: bedankt.php");
            exit();
        }
        $check_stmt->close();
    } catch (Exception $e) {
        error_log("Database fout bij controle op dubbele inschrijving: " . $e->getMessage());
    }

    // Inschrijving opslaan
    try {
        $stmt = $conn->prepare("
            INSERT INTO inschrijvingen (naam, email, cursus, locatie, adres, opmerkingen) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssss", $naam, $email, $cursus, $locatie, $adres, $opmerkingen);

        if ($stmt->execute()) {
            error_log("Inschrijving succesvol opgeslagen voor: $naam ($email)");
            $_SESSION['inschrijving_voltooid'] = true;
            $_SESSION['inschrijving_info'] = compact('naam', 'email', 'cursus', 'locatie');

            // E-mail verzenden
            try {
                error_log("Start e-mail verzending...");

                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->SMTPDebug = 2; // Adjust to 3 for detailed debugging
                $mail->Debugoutput = function($str, $level) {
                    error_log("SMTP DEBUG [$level]: $str");
                };
                $mail->Host       = $smtp_host;
                $mail->SMTPAuth   = true;
                $mail->Username   = $smtp_user;
                $mail->Password   = $smtp_pass;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = $smtp_port;

                $mail->setFrom('info@brantpeije.nl', 'Cursus Team');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Bevestiging inschrijving';
                $mail->Body    = "Hallo $naam,<br><br>Je inschrijving is bevestigd.<br><br>Cursus: <strong>$cursus</strong><br>Locatie: <strong>$locatie</strong><br>Adres: <strong>$adres</strong><br><br>Met vriendelijke groet,<br>Cursus Team";

                if ($mail->send()) {
                    error_log("E-mail succesvol verzonden naar $email");
                } else {
                    error_log("E-mailfout: " . $mail->ErrorInfo);
                }

                // Beheerder notificeren
                $mailAdmin = new PHPMailer(true);
                $mailAdmin->isSMTP();
                $mailAdmin->Host       = $smtp_host;
                $mailAdmin->SMTPAuth   = true;
                $mailAdmin->Username   = $smtp_user;
                $mailAdmin->Password   = $smtp_pass;
                $mailAdmin->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mailAdmin->Port       = $smtp_port;

                $mailAdmin->setFrom('info@brantpeije.nl', 'Cursus Team');
                $mailAdmin->addAddress('info@brantpeije.nl');
                $mailAdmin->isHTML(true);
                $mailAdmin->Subject = 'Nieuwe inschrijving!';
                $mailAdmin->Body    = "Nieuwe inschrijving:<br><strong>Naam:</strong> $naam<br><strong>E-mail:</strong> $email<br><strong>Cursus:</strong> $cursus<br><strong>Locatie:</strong> $locatie<br><strong>Adres:</strong> $adres<br><strong>Opmerkingen:</strong> $opmerkingen";

                if ($mailAdmin->send()) {
                    error_log("Beheerder notificatie verzonden.");
                } else {
                    error_log("E-mail naar beheerder mislukt: " . $mailAdmin->ErrorInfo);
                }
            } catch (Exception $e) {
                error_log("E-mail fout: " . $e->getMessage());
            }

            header("Location: bedankt.php");
            exit();
        } else {
            error_log("Inschrijving mislukt: " . $stmt->error);
        }
        $stmt->close();
    } catch (Exception $e) {
        error_log("Database fout: " . $e->getMessage());
    }

    $conn->close();
}
?>
