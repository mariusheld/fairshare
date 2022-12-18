<?php
//Datenbankverbindung aufbauen
require_once("../dbconnect/dbconnect.inc.php");

//Session eröffnen
session_start();

//Sessionlogin lokal speichern
$login = $_SESSION['login'];

//Abfrage an Datenbank senden
//Wenn Datenbank alles Anzeigen soll wegen Kisten Bug: "SELECT*FROM Lebensmittel WHERE LMkey >= 13"
$query = $db->prepare("SELECT*FROM Lebensmittel, Box WHERE  Lebensmittel.LMkey = Box.LMkey");
$erfolg = $query->execute();

//Fehlertest
if (!$erfolg) {
    $fehler = $query->errorInfo();
    die("Folgender Datenbankfehler ist aufgetreten:" . $fehler[2]);
}

//Array für die Icons in der Lagerübersicht
$icons = array(
    1 => "../media/kategorien/icon_backwaren-salzig.svg",
    2 => "../media/kategorien/icon_backwaren-suess.svg",
    3 => "../media/kategorien/icon_gemuese.svg",
    4 => "../media/kategorien/icon_konserven.svg",
    5 => "../media/kategorien/icon_kuehlprodukte.svg",
    6 => "../media/kategorien/icon_obst.svg",
    7 => "../media/kategorien/sonstiges.svg",
    8 => "../media/kategorien/icon_trockenprodukte.svg",
);

?>

<!DOCTYPE html>
<html lang=de>

<head>
    <meta charset="UTF-8" />
    <meta name="author" content="Marius Held" />
    <title>Abgabeübersicht</title>
    <script src="https://code.jquery.com/jquery-3.6.2.min.js" integrity="sha256-2krYZKh//PcchRtd+H+VyyQoZ/e3EcrkxhM8ycwASPA=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/adminstyle.css" />
    <link rel="stylesheet" href="../css/popup_styles.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fira+Sans:wght@400;600&family=Londrina+Solid:wght@300;400&display=swap');
    </style>
</head>

<body>
    <?php
    //Zugang zur Lagerübersicht
    if ($login == true) {
    ?>
    <div class="pagewrap">
        <!--Logout Button -->
        <div class="navbar">
            <div class="navcontainer">
                <p class="navbarhead">
                    Lagerübersicht
                </p>
                <div class="logout" id="logout">
                    <p class="logouttext">
                        Ausloggen
                    </p>
                    <img alt="ausloggen" src="../media/lock_icon.svg" width="48" height="48" />
                </div>
            </div>
        </div>
        <!--Seiteninhalt-->
        <div class="seiteninhalt">
            <table>
                <!--Tabellenkopf-->
                <tr>
                    <th style="width: 25%;">Lebensmittel</th>
                    <th style="width: 20%;">Kistennr</th>
                    <th style="width: 20%;">Menge</th>
                    <th>Genießbar</th>
                    <th style="width: 50px;"></th>
                    <th style="width: 50px;"></th>
                </tr>
                <!--Tabelleninhalt-->

                <?php
        //Zellenweise Verarbeitung der Datenbankabfrage
        $result = $query->fetchAll();

        //Konsolenausgabe der Datenbankabfrage (nur möglich nach einem fetchAll() befehl der Abfrage)
        echo "<script>console.log(" . json_encode($result) . ");</script>";

        $zähler = 0;

        foreach ($result as $zeile) {
            $zähler += 1;
            echo "<tr>";
            //echo "<td> <img alt='icon' width='48' src='" . $icons[$zeile['OKatKey']] . "'></td>";
            echo 
                "<td class='lmicon'>
                    <div class='tablecontainer'>
                        <img alt='lmicon' src='" . $icons[$zeile['OKatKey']] . "'>
                        <div id='bezeichnung-" . $zähler. "' style='font-weight: 600; padding-left: 16px;'>"
                            . $zeile['Bezeichnung'] . "
                        </div>
                    </div>
                </td>";
            if ($zeile['Kuehlware'] == 0) {
                echo "<td>" . $zeile['BoxID'] . "</td>";
            } else {
                echo "<td><div class='tablecontainer'><div>4</div> <img style='padding-left: 16px;' alt='coolicnon' src='../media/freeze_icon.svg' width='32'></div></td>";
            }
            echo "<td id='gewicht-" . $zähler. "'>" . $zeile['Gewicht'] . " kg</td>";
            echo "<td>" . $zeile['VerteilDeadline'] . "</td>";
            if ($zeile['Anmerkung']) {
                echo "<td style='text-align: right'><img id='bubble' alt='dots' src='../media/comment_icon.svg' width='48px;'/></td>";
            } else {
                echo "<td style='text-align: right'><img id='bubble' style='visibility:hidden' alt='dots' src='../media/comment_icon.svg' width='48px;'/></td>";
            }
            echo
                "<td style='text-align: right; position: relative;' >
                <img onclick='open_close_options(this)' alt='dots' src='../media/edit_icon.svg' width='48px;' style='cursor: pointer;'/>
                <ul class='options' id='" . $zähler. "'>
                    <li><img src='../media/eye.svg' alt=''><span>Ansehen</span></li>
                    <li onclick='open_lebensmittel_fairteilen(this)'><img src='../media/arrows.svg' alt=''><span>Fairteilen</span></li>
                    <li onclick='open_lebensmittel_entsorgen(this)'><img src='../media/trashbin.svg' alt=''><span>Entsorgen</span></li>
                </ul>
            </td>";
            echo "</tr>\n";
        }
                ?>

        </div>
        <!--Seiteninhalt-->
        <footer>
            <div class="footerbg">
                <a href="admin.php"><button class="refreshbutton" id="refreshdash">
                        Liste Aktualisieren
                    </button></a>
            </div>
        </footer>
    </div>
    </div>

    <!-- ----------- OVERLAYS ------------ -->

    <!--Logout Overlay-->
    <div class="helper" id="overtrigger">
        <div class="overlayparent">
            <div class="overlaychild" style="height: 191px; ">
                <p class="olhead">
                    Abmelden?
                </p>
                <div class="eingabe">
                    <div class="buttonscontainer">
                        <button class="buttonwhite">
                            Abrechen
                        </button>
                        <div class="buttongreen">
                            <button class="buttongreen" id="btnlogout" style="color: white" value="Abmelden">
                                Abmelden
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nur zu Testzwecken, später entfernen -->
    <button id="open_nicht_genießbar">Nicht genießbar</button>
    <button id="open_keine_boxen">Keine Boxen</button>

    <!-- Popup "Nicht genießbar" -->
    <div class="overlay" id="popup_nicht_genießbar">
        <div class="popup-wrapper">
            <div class="popup active">
                <h3>HOPPLA!</h3>
                <p>Das Produkt <span class="marked-red">Laugenbrezeln (Kiste 4)</span> ist vermutlich nicht mehr
                    genießbar. Bitte sieh dir das Lebensmittel im Lager an und entsorge oder verlängere es
                    gegebenenfalls.</p>
                <button id="close_nicht_genießbar" class="secondary-btn">Produkt behalten</button>
                <button class="primary-btn">Produkt prüfen</button>
            </div>
        </div>
    </div>

    <!-- Popup "Keine Boxen" -->
    <div class="overlay" id="popup_keine_boxen">
        <div class="popup-wrapper">
            <div class="popup active">
                <h3>HOPPLA!</h3>
                <p>Jemand hat gerade die letzte Box genommen. <br> Sieh nach und sorge für Nachschub.</p>
                <button id="close_keine_boxen" class="secondary-btn">Später erinnern</button>
                <button class="primary-btn">Boxen nachgefüllt</button>
            </div>
        </div>
    </div>

    <!-- Popup "Lebensmittel fairteilen" -->
    <div class="overlay" id="popup_lebensmittel_fairteilen">
        <div class="popup-wrapper">
            <div class="popup active">
                <div class="popup-header">
                    <img src="../media/kategorien/icon_backwaren-suess.svg" alt="Backwaren Süß">
                    <h5>Lebensmittel</h5>
                </div>
                <p>Welche Menge des Lebensmittels möchtest du als fairteilt markieren?</p>

                <form action="" class="popup-form">
                    <label class="popup-form-label" for="fairteil-menge">Menge (in kg)</label>
                    <input type="number" id="fairteil-menge">
                    <div class="bestand"></div>
                </form>


                <button class="secondary-btn" id="fairteilen-abbrechen">Abbrechen</button>
                <button class="primary-btn" id="fairteilen">Fairteilen</button>
            </div>
        </div>
    </div>

    <!-- Popup "Lebensmittel entsorgen" -->
    <div class="overlay" id="popup_lebensmittel_entsorgen">
        <div class="popup-wrapper">
            <div class="popup active">
                <div class="popup-header">
                    <img src="../media/kategorien/icon_backwaren-suess.svg" alt="Backwaren Süß">
                    <h5>Karottenkuchen</h5>
                </div>
                <p>Wenn du Lebensmittel entsorgst verschwinden sie aus der Datenanalyse. Welche Menge des Lebensmittels möchtest du entsorgen?</p>
        
                <form action="" class="popup-form">
                    <label class="popup-form-label" for="entsorgen-menge">Menge (in kg)</label>
                    <input type="number" id="entsorgen-menge">
                    <div class="bestand">/ 1 kg</div>
                </form>
                

                <button class="secondary-btn" id="entsorgen-abbrechen">Abbrechen</button>
                <button class="primary-btn-red">Entsorgen</button>
            </div>
        </div>
    </div>

    <!-- Script zum Öffnen der Pop-Ups -->
    <script type="text/javascript" src="../script/open_popups_mitarbeiter.js"></script>
    <!-- Script zum Öffnen und Schließen des Logout Overlays -->
    <script>
        // Modale Box ansprechen
        var modal = document.getElementById('overtrigger');

        // Buttons definieren, welche die modale Box triggern
        var btn = document.getElementById('logout');

        // <span> Element ansprechen, welches den Schließbutton anspricht
        var span = document.getElementsByClassName('buttonwhite')[0];

        // Funktion, dass sich die modale Box öffnet, wenn der Button getriggert wird
        btn.onclick = function () {
            modal.style.display = 'flex';
        }
        // Bei Klick auf Abbrechen -> Fenster schließen
        span.onclick = function () {
            modal.style.display = 'none';
        }
        // Fenster schließen beim Klick außerhalb des Fensters
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        //User drückt auf Abmelden
        btnlogout.onclick = function () {
            window.location.href = '../index.php'
        }
    </script>
    <?php
    } else {
        session_destroy();
        header("Location: ../index.php");
    }
    ?>
</body>

</html>