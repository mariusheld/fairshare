<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Londrina Solid">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira Sans">

    <style>

        /* Farben-Variablen */
        :root {
        --apple-green: #99BB44;
        --dark-green: #446622;
        --light-green: #D5E989;
        --onyx: #1E212B;
        --concrete: #BEBEB9;
        --sand: #E5E5E3;
        --alabaster: #F2F1F0;
        --white: #FFFDFD;
        --error-red: #E97878;
        }

        /* Allgemeine CSS Einstellungen */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Fira Sans;
        }

        /* CSS für den Header */
        header {
            background-color: var(--apple-green);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 32px;
            box-shadow: 0px 0px 16px rgba(0, 0, 0, 0.08);
            margin-bottom: 80px;
        }

        /* Formatierung für Login im Header */
        .MitarbeiterLogin {
            display: flex;
            align-items: center;
            color: var(--white);
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
        }

        /* Login-Button im Header */
        #LoginButton {
            border: 2px solid var(--sand);
            border-radius: 8px;
            padding: 12px;
            margin-left: 12px;
        }

        /* Einrichtung des 12col-Grids für Hauptbereich der Seite*/
        main {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 16px;
            padding: 0 32px;
        }

        /* Grid für Anmeldemaske */
        .Anmeldeformular {
            grid-column: 4 / span 6;
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 16px;
        }

        /* Elemente, die sich über die Hälfte der Anmeldemaske ausdehnen */
        .col-3 {
            grid-column: span 3;
            display: flex;
            flex-direction: column;
        }

        /* Elemente, die sich über die gesamte Anmeldemaske ausdehnen */
        .col-6 {
            grid-column: span 6;
            display: flex;
            flex-direction: column;
        }

        /* Styling der Beschriftungen der Eingabefelder */
        label {
            color: var(--concrete);
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        /* Styling der Eingabefelder */
        input {
            background-color: var(--alabaster);
            border-radius: 8px;
            border: none;
            padding: 10px 12px;
            height: 46px;

            /* Styling der Schrift */
            color: var(--onyx);
            font-size: 18px;
        }

        /* Styling der Anweisung (zu Email und Tel-nr) */
        form > p {
            color: var(--concrete);
            font-size: 14px;
            text-align: center;
        }

        /* Styling Button allgemein */
        .button {
            /* Layout Button */
            height: 54px;
            border-radius: 16px;

            /* Schrift */
            font-size: 18px;
            font-weight: 600;

            /* Positionierung Schrift */
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 32px;
        }

        /* Styling Primary Button */
        .primary {
            background-color: var(--apple-green);
            border: none;
            color: var(--white);
        }

        /* Styling Secondary Button */
        .secondary {
            background-color: var(--white);
            border: 2px solid var(--sand);
            color: var(--apple-green);
        }

        .error > label {
            color: var(--error-red);
        }

        .error > input {
            border: 2px solid var(--error-red);
        }
    </style>
</head>

<body>
    <!-- Header der Anwendung -->
    <header>
        <img src="assets/logo.svg" alt="Raupe Logo">
        <a href="#" class="MitarbeiterLogin">
            <span>Mitarbeiter*in</span>
            <div id="LoginButton">
                <img src="assets/key.svg" alt="Anmeldung">
            </div>
        </a>
    </header>

    <!-- Anmeldemaske -->
    <main>
        <form class="Anmeldeformular" action="">
            <div class="col-3">
                <label for="">Vorname</label>
                <input type="text">
            </div>
            <div class="col-3">
                <label for="">Nachname (optional)</label>
                <input type="text">
            </div>
            <div class="col-6">
                <label for="">Deine Foodsaver-ID (optional)</label>
                <input type="text">
            </div>
            <div class="col-3">
                <label for="">E-Mail</label>
                <input type="email">
            </div>
            <div class="col-3">
                <label for="">Telefonnummer</label>
                <input type="tel">
            </div>
            
            <p class="col-6">Trage deine E-Mail oder deine Telefonnummer ein.</p>

            <button class="button secondary col-3">Abbrechen</button>
            <input type="submit" value="Weiter" class="button primary col-3">
        </form>
    </main>
</body>
</html>