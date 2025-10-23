<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // mss andere map, en map + bestand op slot zetten
$config = require 'vendor/config.php'; // ww voor email, aanpassen!!


// spam checks

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("niet vandaag");
}

if (!empty($_POST['website'])) {
    // honeypot, invullen is oprotten
    exit("Opdonderen");
}

// input sanitizen

$naam     = strip_tags($_POST['name'] ?? '');
$email    = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$telefoon = strip_tags($_POST['phone'] ?? '');
$bericht  = strip_tags($_POST['message'] ?? '');

function isHeaderInjection($str)
{
    return preg_match("/[\r\n]/", $str);
}

// email vereist!
if (!$email || isHeaderInjection($email) || isHeaderInjection($naam) || isHeaderInjection($telefoon)) {
    exit("Ongeldige of gevaarlijke invoer gedetecteerd.");
}

// Telefoon: optioneel, maar wel op formaat controleren
if (!empty($telefoon) && !preg_match("/^[0-9+\-\s]*$/", $telefoon)) {
    exit("Ongeldig telefoonnummer.");
}

// bericht maken
$body = "Naam: $naam\n";
$body .= "Email: $email\n";
$body .= "Telefoon: $telefoon\n\n";
$body .= "Bericht:\n$bericht";
$body = wordwrap($body, 70);

// mail versturen

try {
    // mail naar beheerder
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.oxcs.bluehost.com'; //aanpassen naar hosting van fysio-lin
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@frankpasman.com'; // aanpassen
    $mail->Password   = $config['smtp_password']; // mapje vendor/config.php
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->SMTPDebug  = 0; // Zet op 2 voor debug, maar 0 voor productie

    $mail->setFrom('info@frankpasman.com', 'Frank Pasman'); //e-mail adres aanmaken
    $mail->addAddress('fpasman@gmail.com'); // aanpassen, ik hoef ze niet meer
    $mail->addReplyTo($email, $naam);

    $mail->isHTML(false);
    $mail->Subject = "Nieuw bericht van $naam via het contactformulier";
    $mail->Body    = $body;

    $mail->send();

    // bevestigingsmail voor gebruiker
    $bevestiging = new PHPMailer(true);
    $bevestiging->isSMTP();
    $bevestiging->Host       = 'smtp.oxcs.bluehost.com'; // aanpassen
    $bevestiging->SMTPAuth   = true;
    $bevestiging->Username   = 'info@frankpasman.com'; // aanpassen!!!!
    $bevestiging->Password   = $config['smtp_password']; // ww aanpassen vendor/config.php
    $bevestiging->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $bevestiging->Port       = 587;
    $bevestiging->SMTPDebug  = 0;

    $bevestiging->setFrom('info@frankpasman.com', 'Linda Hudepohl'); // AANPASSEN!!!
    $bevestiging->addAddress($email, $naam);
    $bevestiging->addReplyTo('no-reply@frankpasman.com'); // no-reply aanmaken?!


    // opmaak bevestigingsmail, aanpassen?
    $bevestiging_tekst = "Beste $naam,\n\n";
    $bevestiging_tekst .= "Bedankt voor je bericht. We hebben het goed ontvangen en nemen zo spoedig mogelijk contact met je op.\n\n";
    $bevestiging_tekst .= "Hieronder vind je een kopie van je bericht:\n";
    $bevestiging_tekst .= "----------------------------------------\n";
    $bevestiging_tekst .= "$body\n\n";
    $bevestiging_tekst .= "Met vriendelijke groet,\n";
    $bevestiging_tekst .= "Linda Hudepohl\n\n";
    $bevestiging_tekst .= "Let op: dit is een automatisch verzonden bericht. Reacties op deze e-mail worden niet gelezen.";

    $bevestiging->isHTML(false); // op true zetten al je de bevestigingsmail wil stylen
    $bevestiging->Subject = "Bevestiging: je bericht is ontvangen";
    $bevestiging->Body    = $bevestiging_tekst;

    $bevestiging->send();

    // Voor bevestigingspagina email opslaan ter controle op bedankpagina
    $_SESSION['email_bevestiging'] = $email;

    // Redirect naar bedankt.php
    header("Location: bedankt.php");
    exit;
} catch (Exception $e) {
    // optioneel, foutjes worden gelogt :D
    error_log("PHPMailer fout: " . $e->getMessage());

    // Veilige foutmelding naar gebruiker
    echo "Er is een fout opgetreden bij het verzenden. Probeer het later opnieuw.";
}
