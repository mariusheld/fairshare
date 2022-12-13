<?php
session_start();
$login = false;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        FAIRSHARE
    </title>

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

        header img {
            cursor: pointer;
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
        form>p {
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

            cursor: pointer;
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

        .error>label {
            color: var(--error-red);
        }

        .error>input {
            border: 2px solid var(--error-red);
        }

        /*Mitarbeiter-Login*/
        #startscreen-mitarbeiter-button {
            width: 48px;
            height: 48px;
            background-color: #99BB44;
            border: 2px solid #E5E5E3;
            border-radius: 8px;
            margin-left: 13px;

            cursor: pointer;
        }

        #login-logo {
            margin: 4px auto auto auto;
        }

        /*AnmeldePopUp*/
        /*Formatierung des Overlays*/
        /*Seiteninhalt & Grundpositionierung*/
        div.helper {
            height: 100vh;
            display: flex;
            justify-content: center;
        }

        div.overlayparent {
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
        }

        /*Das eigentliche PopUp*/
        div.overlaychild {
            width: 536px;
            height: 287px;
            background: #FFFDFD;
            box-shadow: 0px 4px 24px rgba(0, 0, 0, 0.12);
            border-radius: 16px;
            margin-top: 0;
            /*Positionierung der inneren Elemente*/
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
        }

        /*Headline im PopUp*/
        p.olhead {
            margin: 0;
            font-family: "Londrina Solid";
            font-size: 36px;
            text-transform: uppercase;
            padding-top: 6px;
        }

        /*Kennworteingabe*/
        div.eingabe {
            padding-top: 24px;
        }

        input.eingabefeld {
            width: 472px;
            height: 46px;
            background: #F2F1F0;
            border: none;
            border-radius: 8px;
        }

        input[type=text] {
            font-family: "Fira Sans";
            font-size: 16px;
            text-align: center;
        }

        p.vergessen {
            display: none;
            font-family: 'Fira Sans';
            font-style: normal;
            font-weight: 600;
            font-size: 14px;
            line-height: 17px;
            text-align: left;
            color: #BEBEB9;
            padding-left: 0px;
            margin-top: 0px;
            padding-top: 9px;
            order: -1;
        }

        /*Positionierung der Buttons*/
        div.buttonscontainer {
            display: flex;
            flex-direction: row;
            gap: 16px;
            margin: 0;
            padding-top: 64px;
            order: 0;
        }

        /*Formatierung der Buttons*/
        input.buttongreen {
            height: 54px;
            width: 228px;
            background: #99BB44;
            box-shadow: 0px 4px 24px rgba(0, 0, 0, 0.12);
            border-radius: 16px;
            border: none;
            text-align: center;
            font-family: 'Fira Sans';
            font-weight: 600;
            font-size: 18px;
            margin: 0;
        }

        input.buttongreen:hover {
            background-color: #7F9D32;
            color: white;
        }

        div.buttonwhite {
            height: 54px;
            width: 228px;
            border: 2px solid #E6E5E3;
            border-radius: 16px;
        }

        div.buttonwhite:hover {
            border: 2px solid #7F9D32;
            color: #7F9D32;
        }

        p.buttontext {
            text-align: center;
            font-family: 'Fira Sans';
            font-weight: 600;
            font-size: 18px;
            margin: 0;
            padding-top: 15px;
        }

        /*Logout Buttons*/
        /*Formatierung der Buttons*/
        button.buttongreen {
            height: 54px;
            width: 228px;
            background: #99BB44;
            box-shadow: 0px 4px 24px rgba(0, 0, 0, 0.12);
            border-radius: 16px;
            border: none;
            text-align: center;
            font-family: 'Fira Sans';
            font-weight: 600;
            font-size: 18px;
            margin: 0;
        }

        button.buttongreen:hover {
            background-color: #7F9D32;
            color: white;
        }

        button.buttonwhite {
            height: 54px;
            width: 228px;
            border: 2px solid #E6E5E3;
            border-radius: 16px;
            text-align: center;
            font-family: 'Fira Sans';
            font-weight: 600;
            font-size: 18px;
            margin: 0;
            color: #99BB44;
            background-color: white;
        }

        button.buttonwhite:hover {
            color: #7F9D32;
            border: 2px solid #7F9D32;
        }

        /*Modalisierung
        /* The Modal (background) */
        #overtrigger {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgb(0, 0, 0);
            /* Fallback color */
            background-color: rgba(0, 0, 0, 0.3);
            /* Black w/ opacity */
        }
    </style>
</head>

<body>
    <!--Overlay-->
    <div class="helper" id="overtrigger" <?php if ($login == true) { echo "style='display:flex;'"; } ?>>
        <div class="overlayparent">
            <div class="overlaychild">
                <p class="olhead">
                    Kennwort
                </p>
                <div class="eingabe">
                    <form action="admin.php" method="POST" autocomplete="off">
                        <input class="eingabefeld" name="password" id="eingabe" type="password"
                            style="text-align: center; font-size: 20px;" <?php if ($login == true) { echo "style='border:
                            2px solid red; width: 468; height: 42px;'"; } ?>>
                        <p class="vergessen" id="vergessen" <?php if ($login == true) { echo "style='color: red; display:
                            block;'"; } ?>>
                            Kennwort falsch
                        </p>
                        <div class="buttonscontainer" id="bcontainer" <?php if ($login == true) { echo
                            "style='padding-top: 24px'"; } ?>>
                            <div class="buttonwhite" id="breakup">
                                <p class="buttontext" style="color: #99BB44">
                                    Abrechen
                                </p>
                            </div>
                            <div class="buttongreen">
                                <input type="submit" class="buttongreen" style="color: white" value="Anmelden">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Overlay-->
    <!-- Header der Anwendung -->
    <header>
        <img src="../media/logo.svg" width="194px;" alt="Raupe Logo" id="logo">
        <a href="#" class="MitarbeiterLogin" id="login">
            <span>Mitarbeiter*in</span>
            <button id="startscreen-mitarbeiter-button"><svg id="login-logo" xmlns="http://www.w3.org/2000/svg"
                    width="28px" height="28px" fill="#99BB44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#FFFFFF"
                    class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                </svg></button>
        </a>
    </header>

    <!-- Anmeldemaske -->
    <main>
        <form class="Anmeldeformular" action="02_foodsaver_start.php">
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

            <button formaction="../index.php" class="button secondary col-3" id="breakupbtn">Abbrechen</button>
            <input type="submit" value="Weiter" class="button primary col-3" id="weiter">
        </form>
    </main>
</body>
<?php
echo "<script>
                //Button Abbrechen leitet auf Startseite zurück
                //document.getElementById('breakupbtn').onclick = function(){
                  //  window.location.href = '../index.php';
                //}
                document.getElementById('logo').onclick = function() {
                    window.location.href = '../index.php';
                }
                //JavaScript für das PopUp
                // Modales Fenster 
                var modal = document.getElementById('overtrigger');

                // Button der modale Fenster triggert
                var btn = document.getElementById('login');

                // Button der das modale Fenster schließst
                var span = document.getElementsByClassName('buttonwhite')[0];

                // Modales Fenster öffnen 
                btn.onclick = function() {
                modal.style.display = 'flex';
                }
                // Modales Fenster schließen wenn auf Abbrechen geklickt wird
                span.onclick = function() {
                modal.style.display = 'none';
                eingabe.value ='';
                eingabe.style.border='none';
                vergessen.style.display = 'none';
                bcontainer.style.paddingtop = '64px';
                eingabe.style.height = '46px';
                eingabe.style.width = '472px';
                }
                // Modales Fenster schließen wenn außerhalb der Box geklickt wird
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = 'none';
                        eingabe.value ='';
                        eingabe.style.border='none';
                        vergessen.style.display = 'none';
                        bcontainer.style.paddingtop = '64px';
                        eingabe.style.height = '46px';
                        eingabe.style.width = '472px';
                    }
                }
                //Ausblenden des Overlays beim Laden der Seite
                document.onload = function(event){
                    modal.style.display = 'none';
                }
                </script>";
?>

</html>