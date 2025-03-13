<?php
session_start();

// Als er geen inschrijving is gedetecteerd in de sessie:
if (!isset($_SESSION['inschrijving_voltooid']) || $_SESSION['inschrijving_voltooid'] !== true) {
    header("Location: inschrijven.html");
    exit();
}

// Haal info uit de sessie
$naam    = $_SESSION['inschrijving_info']['naam']    ?? 'cursist';
$email   = $_SESSION['inschrijving_info']['email']   ?? '';
$cursus  = $_SESSION['inschrijving_info']['cursus']  ?? '';
$locatie = $_SESSION['inschrijving_info']['locatie'] ?? '';

$emailFout = $_SESSION['email_error'] ?? '';

// Wis de sessie pas **na** het tonen van de gegevens
unset($_SESSION['inschrijving_info']);
unset($_SESSION['email_error']);
session_unset(); // Verwijder alle sessievariabelen
session_destroy(); // Vernietig de sessie
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bedankt voor je inschrijving</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            text-align: center; 
            padding: 50px; 
            background-color: #fffae6;
        }
        .bedankt-container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 { color: #d45a2a; }
        p {
            font-size: 18px;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .home-button {
            display: inline-block;
            background: #d45a2a;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
        }
        .home-button:hover {
            background: #c14e24;
        }
        .alert {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: left;
        }
        .inschrijf-details {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="bedankt-container">
        <h1>Bedankt voor je inschrijving!</h1>
        
        <p>Hallo <?php echo htmlspecialchars($naam); ?>, we hebben je inschrijving ontvangen.</p>
        
        <?php if (!empty($emailFout)): ?>
        <div class="alert">
            <strong>Let op:</strong> <?php echo htmlspecialchars($emailFout); ?>
        </div>
        <?php else: ?>
        <p>Een bevestigingsmail is verstuurd naar <?php echo htmlspecialchars($email); ?>.</p>
        <?php endif; ?>
        
        <?php if (!empty($cursus) && !empty($locatie)): ?>
        <div class="inschrijf-details">
            <h3>Je inschrijvingsdetails:</h3>
            <p><strong>Cursus:</strong> <?php echo htmlspecialchars($cursus); ?></p>
            <p><strong>Locatie:</strong> <?php echo htmlspecialchars($locatie); ?></p>
        </div>
        <?php endif; ?>
        
        <p>We nemen binnenkort contact met je op om de details van je cursus te bespreken.</p>
        
        <a href="index.html" class="home-button">Terug naar de homepage</a>
    </div>
</body>
</html>
