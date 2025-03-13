<?php
// Zet foutmelding aan voor debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>PHPMailer Installatie Check</h1>";

// 1. Controleer Composer autoloader
echo "<h2>1. Controleer Composer autoloader</h2>";
if (file_exists('vendor/autoload.php')) {
    echo "<p>✅ vendor/autoload.php bestaat</p>";
    require 'vendor/autoload.php';
    echo "<p>✅ autoloader geladen</p>";
} else {
    echo "<p>❌ vendor/autoload.php bestaat niet</p>";
    echo "<p>Probeer Composer opnieuw te installeren:</p>";
    echo "<pre>composer require phpmailer/phpmailer</pre>";
}

// 2. Controleer PHPMailer bestanden
echo "<h2>2. Controleer PHPMailer bestanden</h2>";
$phpmailer_base = 'vendor/phpmailer/phpmailer';
$phpmailer_files = [
    $phpmailer_base . '/src/PHPMailer.php',
    $phpmailer_base . '/src/SMTP.php',
    $phpmailer_base . '/src/Exception.php'
];

echo "<ul>";
foreach ($phpmailer_files as $file) {
    echo "<li>$file: " . (file_exists($file) ? "✅ Bestaat" : "❌ Bestaat niet") . "</li>";
}
echo "</ul>";

// 3. Test PHPMailer klasse
echo "<h2>3. Test PHPMailer klasse</h2>";
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "<p>✅ PHPMailer klasse is beschikbaar via autoloader</p>";
} else {
    echo "<p>❌ PHPMailer klasse is NIET beschikbaar via autoloader</p>";
    
    // 3b. Probeer handmatige include
    echo "<h3>3b. Probeer handmatige include</h3>";
    foreach ($phpmailer_files as $file) {
        if (file_exists($file)) {
            include_once $file;
            echo "<p>✅ Handmatig geladen: $file</p>";
        } else {
            echo "<p>❌ Kan niet handmatig laden: $file</p>";
        }
    }
    
    // Check na handmatige include
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        echo "<p>✅ PHPMailer klasse is nu beschikbaar na handmatige include</p>";
    } else {
        echo "<p>❌ PHPMailer klasse is nog steeds NIET beschikbaar na handmatige include</p>";
    }
}

// 4. Test .env
echo "<h2>4. Test .env</h2>";
if (file_exists('.env')) {
    echo "<p>✅ .env bestand bestaat</p>";
    
    if (class_exists('Dotenv\Dotenv')) {
        echo "<p>✅ Dotenv klasse is beschikbaar</p>";
        
        try {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
            $dotenv->load();
            echo "<p>✅ .env bestand geladen</p>";
            
            $env_vars = ['SMTP_HOST', 'SMTP_USER', 'SMTP_PASS', 'SMTP_PORT'];
            echo "<ul>";
            foreach ($env_vars as $var) {
                echo "<li>$var: " . (isset($_ENV[$var]) ? "✅ Gevonden" : "❌ Niet gevonden") . "</li>";
            }
            echo "</ul>";
        } catch (Exception $e) {
            echo "<p>❌ Fout bij laden .env: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>❌ Dotenv klasse is NIET beschikbaar</p>";
        echo "<p>Installeer via: <code>composer require vlucas/phpdotenv</code></p>";
    }
} else {
    echo "<p>❌ .env bestand bestaat niet</p>";
    echo "<p>Maak een .env bestand aan met je SMTP instellingen:</p>";
    echo "<pre>
SMTP_HOST=jouw-smtp-server.nl
SMTP_USER=jouw-email@domein.nl
SMTP_PASS=jouw-wachtwoord
SMTP_PORT=587
</pre>";
}

// 5. Test een PHPMailer instantie
echo "<h2>5. Test een PHPMailer instantie</h2>";
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        echo "<p>✅ PHPMailer instantie aangemaakt</p>";
        
        echo "<pre>";
        // Toon alle beschikbare methoden (functies)
        echo "Beschikbare methoden (functies):\n";
        print_r(get_class_methods($mail));
        echo "</pre>";
    } catch (Exception $e) {
        echo "<p>❌ Fout bij aanmaken PHPMailer instantie: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>❌ Kan geen PHPMailer instantie testen omdat de klasse niet beschikbaar is</p>";
}

// 6. Installatiesuggesties
echo "<h2>6. Installatiesuggesties</h2>";
echo "<p>Als PHPMailer niet is gevonden, probeer dan:</p>";
echo "<ol>";
echo "<li>Composer installeren: <a href='https://getcomposer.org/download/' target='_blank'>https://getcomposer.org/download/</a></li>";
echo "<li>PHPMailer installeren: <code>composer require phpmailer/phpmailer</code></li>";
echo "<li>Als dat niet werkt, installeer handmatig door de bestanden te downloaden: <a href='https://github.com/PHPMailer/PHPMailer/releases' target='_blank'>https://github.com/PHPMailer/PHPMailer/releases</a></li>";
echo "<li>Plaats de bestanden in een <code>phpmailer</code> directory en update het script om daar naar te verwijzen</li>";
echo "</ol>";

?>