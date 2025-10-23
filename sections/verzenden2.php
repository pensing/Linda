<?php
//sessie starten
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';


//wachtwoord ophalen
$config = require 'vendor/config.php';



if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Nee niet vandaag.");
}

// honeypot, als die is ingevuld, wss een bot, of een slimme gast. krijgt een niet zo aardige melding.
if (!empty($_POST['website'])) {
    exit("Opdonderen");
}

// sanitizen van de ingevude velden
$naam     = strip_tags($_POST['name'] ?? '');
$email    = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$telefoon = strip_tags($_POST['phone'] ?? '');
$bericht  = strip_tags($_POST['message'] ?? '');


function isHeaderInjection($str)
{
    return preg_match("/[\r\n]/", $str);
}


if (!$email || isHeaderInjection($email) || isHeaderInjection($naam) || isHeaderInjection($telefoon)) {
    exit("Ongeldige of gevaarlijke invoer gedetecteerd.");
}

// telefoon nummer optioneel, wel verplicht op contact pagina via html ben te lui en het werkt.
if (!empty($telefoon) && !preg_match("/^[0-9+\-\s]*$/", $telefoon)) {
    exit("Ongeldig telefoonnummer.  ");
}


$body = "Naam: $naam\n";
$body .= "Email: $email\n";
$body .= "Telefoon: $telefoon\n\n";
$body .= "Bericht:\n$bericht";
$body = wordwrap($body, 70);



try {
    //mail naar beheerder
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.oxcs.bluehost.com'; // aanpassen naar het domein van fysio-lin.nl
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@frankpasman.com'; // aanpassen ;)
    $mail->Password   = $config['smtp_password']; // aanpassen ;)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // ===== Debugmodus: 2 = debug | 0 = live ===== //
    $mail->SMTPDebug  = 0;

    $mail->setFrom('info@frankpasman.com', 'Frank Pasman'); // afzender zoals de gebruiker hem gaat ontvangen eigen domein en email adres nodig.
    $mail->addAddress('fpasman@gmail.com'); // persoonlijk of zakelijk email adres! aanpassen ik hoef ze niet meer!
    $mail->addReplyTo($email, $naam);

    $mail->isHTML(false);
    $mail->Subject = "Nieuw bericht van $naam via het contactformulier";
    $mail->Body    = $body;
    //verzenden
    $mail->send();

    //  bevestigingsmail
    $bevestiging = new PHPMailer(true);
    $bevestiging->isSMTP();
    $bevestiging->Host       = 'smtp.oxcs.bluehost.com'; // smtp van domein! aanpassen!
    $bevestiging->SMTPAuth   = true;
    $bevestiging->Username   = 'info@frankpasman.com'; // email adres van domein, aanpassen aub!
    $bevestiging->Password   = $config['smtp_password']; // ww netjes opslaan in config.php, ww nog aanpassen dus en neerzetten in mapje vendor
    $bevestiging->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $bevestiging->Port       = 587;

    // wederom 0 voor live 2 voor debug :D
    $bevestiging->SMTPDebug  = 0;

    $bevestiging->setFrom('info@frankpasman.com', 'Linda Hudepohl'); // email adres graag aanpassen!!!!
    $bevestiging->addAddress($email, $naam);
    $bevestiging->addReplyTo('no-reply@fysio-lin.nl'); // no-reply aanmaken? Optioneel,


    // Opmaak van bevestigingsmail. aanpassen/verwijderen naar wens
    $bevestiging_tekst = "Beste $naam,\n\n";
    $bevestiging_tekst .= "Bedankt voor je bericht. We hebben het goed ontvangen en nemen zo spoedig mogelijk contact met je op.\n\n";
    $bevestiging_tekst .= "Hieronder vind je een kopie van je bericht:\n";
    $bevestiging_tekst .= "----------------------------------------\n";
    $bevestiging_tekst .= "$body\n\n";
    $bevestiging_tekst .= "Met vriendelijke groet,\n";
    $bevestiging_tekst .= "Linda Hudepohl\n\n";
    $bevestiging_tekst .= "Let op: dit is een automatisch verzonden bericht. Reacties op deze e-mail worden niet gelezen.";

    $bevestiging->isHTML(false);
    $bevestiging->Subject = "Bevestiging: je bericht is ontvangen";
    $bevestiging->Body    = $bevestiging_tekst;

    $bevestiging->send();

    // Voor bevestigingspagina ingevulde email adress even bewaren om te kunnen laten zien.
    $_SESSION['email_bevestiging'] = $email;


    header("Location: bedankt.php");
    exit;
} catch (Exception $e) {
    // foutjes worden opgeslagen in error_log bestandje! HANDIGGGG
    error_log("PHPMailer fout: " . $e->getMessage());


    echo "Er is een fout opgetreden bij het verzenden. Probeer het later opnieuw.";
}
