<?php
session_start();

// Honeypot controle || recaptcha kan ook maar moet je een cookie pop up tonen
if (!empty($_POST['website'])) {
    echo "Spam gedetecteerd. Bericht is niet verzonden.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Input veilig maken
    $naam = strip_tags($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $telefoon = strip_tags($_POST['phone'] ?? '');
    $bericht = strip_tags($_POST['message'] ?? '');

    // Header injection voorkomen
    function isHeaderInjection($str)
    {
        return preg_match("/[\r\n]/", $str);
    }

    if (!$email) {
        echo "Het e-mailadres moet geldig zijn. <br>";
        echo '<a href="index.html"><button>terug</button></a>';
        exit;
    }

    if (isHeaderInjection($email) || isHeaderInjection($naam) || isHeaderInjection($telefoon)) {
        echo "Ongeldige invoer gedetecteerd. <br>";
        echo '<a href="index.html"><button>terug</button></a>';
        exit;
    }

    // Validatie telefoonnummer
    if (!empty($telefoon) && !preg_match("/^[0-9+\-\s]*$/", $telefoon)) {
        echo "Telefoonnummer bevat ongeldige tekens. <br>";
        echo '<a href="index.html"><button>terug</button></a>';
        exit;
    }

    // Email naar websitebeheerder (jij)
    $to = "fpasman@gmail.com"; // <-- Jouw eigen e-mailadres
    $onderwerp = "Nieuw bericht van $naam via het contactformulier";

    $headers = "From: info@frankpasman.com\r\n"; // Eigen email adres bijvoorbeeld info@fysio-lin.nl! of zelfde als $to hierboven (maar dat kan spam opleveren)
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=utf-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

    $body = "Naam: $naam\n";
    $body .= "Email: $email\n";
    $body .= "Telefoon: $telefoon\n\n";
    $body .= "Bericht:\n$bericht";
    $body = wordwrap($body, 70);

    // Verstuur de e-mail naar jou
    if (mail($to, $onderwerp, $body, $headers)) {
        // Bevestigingsmail naar gebruiker
        $bevestiging_onderwerp = "Bevestiging: je bericht is ontvangen";
        $bevestiging_body = "Beste $naam,\n\n";
        $bevestiging_body .= "Bedankt voor je bericht. We hebben het goed ontvangen en nemen zo spoedig mogelijk contact met je op.\n\n";
        $bevestiging_body .= "Hieronder vind je een kopie van je bericht:\n";
        $bevestiging_body .= "----------------------------------------\n";
        $bevestiging_body .= "$body\n\n";
        $bevestiging_body .= "Met vriendelijke groet,\n";
        $bevestiging_body .= "Linda Hudepohl\n";
        $bevestiging_body .= "Let op: dit is een automatisch verzonden bericht. Reacties op deze e-mail worden niet gelezen.";

        $bevestiging_headers = "From: no-reply@frankpasman.com\r\n"; // NO REPLY AANMAKEN! Of mensen kunnen reageren op je bevestigings email!!! of info@fysio-lin.nl gebruiken.
        $bevestiging_headers .= "Reply-To: no-reply@frankpasman.com\r\n";
        $bevestiging_headers .= "Content-Type: text/plain; charset=utf-8\r\n";

        // Verstuur de bevestigingsmail naar gebruiker.
        mail($email, $bevestiging_onderwerp, $bevestiging_body, $bevestiging_headers);

        // Terugkoppeling aan gebruiker: bedanktpagina met ingevoerde e-mail adres van gebruiker ter controle.

        $_SESSION['email_bevestiging'] = $email;

        header("Location: bedankt.php");
        exit;
    } else {
        echo "Er is iets misgegaan met verzenden.";
    }
} else {
    echo "Ongeldige toegang.";
}
