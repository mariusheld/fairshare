<?php
//Session 
session_start();
$_SESSION['passwordErr'] = false;
$_SESSION['login'] = false;
//Datenbankverbindung aufbauen
require_once("dbconnect/dbconnect.inc.php");

//Test-Abfrage an Datenbank senden
$query = $db->prepare("SELECT*FROM Lebensmittel");
$erfolg = $query->execute();

//Fehlertest
if (!$erfolg) {
    $fehler = $query->errorInfo();
    die("Folgender Datenbankfehler ist aufgetreten:" . $fehler[2]);
}
?>
<!DOCTYPE html>
<html lang=de>

<head>
    <meta charset="UTF-8" />
    <title>
        FAIRSHARE
    </title>
    <link rel="stylesheet" href="css/startendstyle.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans&family=Londrina+Solid&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body id="startscreen">
    <!--Overlay-->
    <div class="helper" id="overtrigger" <?php if ($login == true) { echo "style='display:flex;'"; } ?>>
        <div class="overlayparent">
            <div class="overlaychild">
                <p class="olhead">
                    Kennwort
                </p>
                <div class="eingabe">
                    <form action="pages/admin.php" method="POST" autocomplete="off">
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
    <div id="startscreen-header">
        <img id="logo" src="media/logo.svg" alt="fairshare" width="200px">
        <!-- <div id="startscreen-mitarbeiter-login">
            <p id="startscreen-mitarbeiter">Mitarbeiter*in</p>
            <button id="startscreen-mitarbeiter-button"><svg id="login-logo" xmlns="http://www.w3.org/2000/svg"
                    width="28px" height="28px" fill="#99BB44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#FFFFFF"
                    class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                </svg></button>
        </div> -->
    </div>

    <div id="startscreen-content">
        <div>
            <h1 id="startscreen-title">50 Tonnen</h1>
            <p id="startscreen-text">Lebensmittel wurden bisher gerettet.</p>
            <button id="startscreen-button">Tippe, um zu beginnen</button>
        </div>
    </div>

    <div id="startscreen-footer">
        <p id="startscreen-footer-text">Raupe Immersatt, Stuttgart 2023</p>
    </div>
    
    <script>
        //Weiterleitung zum Foodsaver Anmeldeprozess
        var startscreenButton = document.getElementById('startscreen-button');
        startscreenButton.onclick = function () {
            window.location.href = 'pages/01_foodsaver_anmeldung.php';
        }
        // Modales Fenster 
        var modal = document.getElementById('overtrigger');

        // Button der modale Fenster triggert
        var btn = document.getElementById('startscreen-mitarbeiter-login');

        // Button der das modale Fenster schließst
        var span = document.getElementsByClassName('buttonwhite')[0];

        // Modales Fenster öffnen 
        btn.onclick = function () {
            modal.style.display = 'flex';
        }
        // Modales Fenster schließen wenn auf Abbrechen geklickt wird
        span.onclick = function () {
            modal.style.display = 'none';
            eingabe.value = '';
            eingabe.style.border = 'none';
            vergessen.style.display = 'none';
            bcontainer.style.paddingtop = '64px';
            eingabe.style.height = '46px';
            eingabe.style.width = '472px';
        }
        // Modales Fenster schließen wenn außerhalb der Box geklickt wird
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = 'none';
                eingabe.value = '';
                eingabe.style.border = 'none';
                vergessen.style.display = 'none';
                bcontainer.style.paddingtop = '64px';
                eingabe.style.height = '46px';
                eingabe.style.width = '472px';
            }
        }
        //Ausblenden des Overlays beim Laden der Seite
        document.onload = function (event) {
            modal.style.display = 'none';
        }
    </script>
</body>

</html>