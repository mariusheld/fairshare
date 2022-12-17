<?php
// --- CREATE SESSION --- 
session_start();

if ($_SESSION["foodsaverLogin"] == false) {
    session_destroy();
    header("Location: ../index.php");
}
// Array wird geleert 
$_SESSION["array"] = array();
// Session wird zerstört und resettet
session_destroy();
?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        FAIRSHARE
    </title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@200;300;400;500;600;700;800;900&family=Londrina+Solid&display=swap"
        rel="stylesheet" />
    <!-- CSS Stylesheet -->
    <link href="../css/formularstyle.css" rel="stylesheet" />
</head>

<body>
    <div class="container">
        <header>
            <img src="../media/logo.svg" alt="logo" />
            <!-- fsHilfe Trigger -->
            <img id="openHelp" src="../media/icon_help.svg" alt="icon_help" />
        </header>
        <div class="content">
            <div class="wrap-title font-fira">
                <div class="reminder">
                    <div>
                        <h1 class="font-londrina">
                            DANKE FÜR DEINE SPENDE!
                        </h1>
                        <h2>
                            Bitte vergiss nicht
                        </h2>
                        <p>
                            ... die Oberflächen abzuwischen, <br />
                            ... das Licht auszumachen, <br />
                            ... die Türe zu schließen, <br />
                            ... dich an der Theke zu verabschieden.
                        </p>
                    </div>
                    <div class="check-item">
                        <input name="check" type="checkbox">
                        <img src="../media/checkbox.svg" alt="checkbox" />
                        <img src="../media/checkbox_checked.svg" alt="checkbox_checked" />
                        Ich habe die letzte Box genommen.
                    </div>
                </div>
                <!-- WEITERLEITUNG ZUM ENDSCREEN-->
                <a class="final-button" href="./06_foodsaver_endscreen.php">Alles klar</a>
            </div>
        </div>
    </div>


    <!-- ------------------ ALLE OVERLAYS ------------------ -->

    <!-- OVERLAY fsHilfe -->
    <div id="fsHilfe">
        <div class="fs-hilfe">
            <h3 class="popupheader">LEBENSMITTEL RICHTIG ABGEBEN</h3>
            <div class="schrittliste-popup">
                <div>
                    <ul class="listpopupHilfe">
                        <img src="../media/kategorien/icon_gemuese.svg" class="icon-help-popup">
                        <h5 class="steps-hilfe">1. Hygiene</h5>
                        <li>
                            Hände waschen
                        </li>
                        <li>
                            Verwende das rechte Waschbecken
                        </li>
                    </ul>
                </div>
                <div>
                    <ul class="listpopupHilfe">
                        <img src="../media/kategorien/icon_gemuese.svg" class="icon-help-popup">
                        <h5 class="steps-hilfe">2. Boxen</h5>
                        <li>
                            Hol dir genügend Boxen unter der Theke
                        </li>
                        <li>
                            Boxen nicht auf den Boden stellen!
                        </li>
                    </ul>
                </div>
                <div>
                    <ul class="listpopupHilfe">
                        <img src="../media/kategorien/icon_gemuese.svg" class="icon-help-popup">
                        <h5 class="steps-hilfe">3. Vorbereitung</h5>
                        <li>
                            Sortiere Verdorbenes aus
                        </li>
                        <li>
                            Wasche dreckige Lebensmittel (linkes Becken)
                        </li>
                        <li>
                            Entferne unnötige Verpackungen
                        </li>
                    </ul>
                </div>
                <div>
                    <ul class="listpopupHilfe">
                        <img src="../media/kategorien/icon_gemuese.svg" class="icon-help-popup">
                        <h5 class="steps-hilfe">4. Lebensmittelabgabe</h5>
                        <li>
                            Packe die Lebensmittel in die Boxen
                        </li>
                        <li>
                            Trage die Lebensmittel ins System ein
                        </li>
                        <li>
                            Verstaue die Lebensmittel
                        </li>
                    </ul>
                </div>
            </div>
            <div class="buttoncenter">
                <a class="allesklarButton" href="">
                    <h5>Alles klar</h5>
                </a>
            </div>
        </div>
    </div>

    <!-- Script Overlay fs-hilfe -->
    <script type="text/javascript" src="../script/05.js"></script>
</body>

</html>