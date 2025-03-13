<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Laad .env als die beschikbaar is
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Debug modus inschakelen voor foutopsporing
$mail = new PHPMailer(true);
$mail->SMTPDebug = 2; // 2 = uitgebreide debugging

try {
    // SMTP-instellingen
    $mail->isSMTP();
    $mail->Host       = $_ENV['SMTP_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['SMTP_USER'];
    $mail->Password   = $_ENV['SMTP_PASS'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $_ENV['SMTP_PORT'];

    // **Afzender moet overeenkomen met het geauthenticeerde account!**
    $mail->setFrom($_ENV['SMTP_USER'], 'Brant Peije Cursus Team');
    $mail->addAddress('info@brantpeije.nl'); // Ontvanger

    // E-mailinhoud
    $mail->isHTML(true);
    $mail->Subject = 'Test e-mail via PHPMailer';
    $mail->Body    = 'Dit is een testmail verzonden via PHPMailer met Gmail SMTP.';

    if ($mail->send()) {
        echo "E-mail succesvol verzonden!";
    } else {
        echo "E-mail verzenden mislukt.";
    }
} catch (Exception $e) {
    echo "Fout bij verzenden: {$mail->ErrorInfo}";
}
?>
