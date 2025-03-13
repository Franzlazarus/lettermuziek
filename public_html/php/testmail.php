<?php
// Zet foutmeldingen aan
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Laad Composer's autoloader
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Haal SMTP-config uit .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mail = new PHPMailer(true);

try {
    // Zet debugging aan
    $mail->SMTPDebug = 2; // Volledige debugoutput
    $mail->isSMTP();
    $mail->Host       = $_ENV['SMTP_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['SMTP_USER'];
    $mail->Password   = $_ENV['SMTP_PASS'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $_ENV['SMTP_PORT'];

    // Afzender en ontvanger
    $mail->setFrom($_ENV['SMTP_USER'], 'Test Afzender');
    $mail->addAddress('jouw-email@example.com'); // Vervang door je eigen e-mail

    // Mail content
    $mail->isHTML(true);
    $mail->Subject = 'Testmail';
    $mail->Body    = 'Dit is een testmail vanaf je PHP-script.';

    // Verstuur de mail
    if ($mail->send()) {
        echo "✅ E-mail verzonden!";
    } else {
        echo "❌ E-mail mislukt.";
    }

} catch (Exception $e) {
    echo "Fout bij verzenden: {$mail->ErrorInfo}";
}
?>
