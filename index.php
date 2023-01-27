<?php
session_start();
$_SESSION['passwordErr'] = false;
$_SESSION["foodsaverLogin"] = false;
$_SESSION["latestLMkey"] = 0;
$_SESSION["userstatus"] = "";
$_SESSION["bekannt"] = false;

if (isset($_GET['logout'])) {
  $_SESSION["login"] = false;
  $_SESSIOn["foodsaverLogin"] = false;
}

//Datenbankverbindung aufbauen
require_once("dbconnect/dbconnect.inc.php");

//Lebensmittel-Abfrage an Datenbank senden
$query = $db->prepare("SELECT SUM(BewegMenge) FROM `Bestand_Bewegung` WHERE Bestand_Bewegung.LStatusKey = 2");
$erfolg = $query->execute();

//Zellenweise Verarbeitung der Datenbankabfrage
$result = $query->fetchColumn();

$gewicht = intval($result);
$gewicht = round($gewicht / 1000, $precision = 2) + 50;

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
    <link rel="icon" type="image/x-icon" href="favicon/favicon.ico">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body id="startscreen">
    <div id="gif">
        <img src="media/startscreen.gif" alt="Wackelnde Lebensmittel GIF" class="gif">
    </div>
    <div id="startscreen-header">
        <img id="logo" src="media/logo.svg" alt="fairshare">
    </div>

    <div id="startscreen-content">
        <div>
            <h1 id="startscreen-title">
                <?php echo $gewicht ?> TONNEN
            </h1>
            <p id="startscreen-text">Lebensmittel wurden bisher gerettet.</p>
            <button id="startscreen-button">Tippe, um zu beginnen</button>
        </div>
    </div>

    <div id="startscreen-footer">
        <a id="startscreen-footer-text" href="pages/17_credits.html">Ein Lehrprojekt der HdM Stuttgart</a>
    </div>

    <script>
        //Weiterleitung zum Foodsaver Anmeldeprozess
        var startscreenButton = document.getElementById('startscreen-button');
        startscreenButton.onclick = function () {
            window.location.href = 'pages/02_foodsaver_anmeldung.php';
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