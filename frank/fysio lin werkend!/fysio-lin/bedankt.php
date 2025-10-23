<?php
session_start();

if (empty($_SESSION['email_bevestiging'])) {
    // Geen geldig e-mailadres in sessie, terugsturen naar home-page
    header("Location: ../index.html");
    exit;
}

$email = $_SESSION['email_bevestiging'];

// Sessie meteen leegmaken en vernietigen zodat de pagina maar één keer getoond kan worden
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Fysio-Lin -- Bedankt!</title>
    <link rel="stylesheet" href="/reset.css" />
    <link rel="stylesheet" href="/main.css" />
    <link rel="stylesheet" href="/base.css" />
    <style>
        * {
            padding: 0;
            margin: 0;
        }

        .thanks {
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
            justify-items: center;
            z-index: 1;
            padding: 1rem;
        }

        .thanks p {
            margin-top: 1rem;
            font-size: 2rem;
            margin-bottom: 100px;
            position: relative;
        }

        .blocks {
            position: relative;
            z-index: 0;
            display: flex;
            flex-direction: row;
            margin: auto;
        }

        .blocks .beige {
            position: absolute;
            top: 12.5rem;
            right: 4rem;
            background-color: var(--color-accent2);
            min-width: 200px;
            min-height: 50vh;

        }

        .lighter {
            font-weight: lighter;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="blocks">
        <div class="blue"></div>
        <div class="beige"></div>
    </div>
    <div class="thanks">
        <div class="text-wrapper">
            <h1>We hebben je bericht ontvangen!</h1>
            <p>We hebben een bevestiging gestuurd naar: <strong><?= htmlspecialchars($email) ?></strong>.</p>
            <a href="../index.html"><button>Terug</button></a> <br>
            <span class="lighter">Niets ontvangen? Bekijk je spam folder en controleer het e-mail adres.</span>
        </div>
    </div>
</body>

</html>