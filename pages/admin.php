<?php
//Datenbankverbindung aufbauen
require_once("../dbconnect/dbconnect.inc.php");

//Session eröffnen
session_start();

//Sessionlogin lokal speichern
$login = $_SESSION['login'];

//Abfrage an Datenbank senden
//Wenn Datenbank alles Anzeigen soll wegen Kisten Bug: "SELECT*FROM Lebensmittel WHERE LMkey >= 13"
$query = $db->prepare("SELECT*FROM Lebensmittel ORDER BY VerteilDeadline");
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

$herkunft = array(
    1 => "Supermarkt",
    2 => "Wochenmarkt",
    3 => "Bäckerei",
    4 => "Gastronomie",
    5 => "Veranstaltung",
    6 => "Sonstiges"
);

?>

<!DOCTYPE html>
<html lang=de>

<head>
    <meta charset="UTF-8" />
    <meta name="author" content="Marius Held" />
    <title>Abgabeübersicht</title>
    <script src="https://code.jquery.com/jquery-3.6.2.min.js"
        integrity="sha256-2krYZKh//PcchRtd+H+VyyQoZ/e3EcrkxhM8ycwASPA=" crossorigin="anonymous"></script>
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
                        <th style="width: 45%;">Lebensmittel</th>
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
                    // echo "<script>console.log(" . json_encode($result) . ");</script>";
                    function consolelog($data, bool $quotes = false)
                    {
                        $output = json_encode($data);
                        if ($quotes) {
                            echo "<script>console.log('{$output}' );</script>";
                        } else {
                            echo "<script>console.log({$output} );</script>";
                        }
                    }

                    consolelog($result);

                    $zähler = 0;

                    //Datumsberechnung
                    $jetzt = time();
                    // $result['VerteilDeadline'] = strtotime($result['VerteilDeadline']);
                
                    foreach ($result as $key => $zeile) {
                        $zähler += 1;
                        $zeile['VerteilDeadline'] = round((strtotime($zeile['VerteilDeadline']) - $jetzt) / (60 * 60 * 24));
                        $ablaufdatum = $zeile['VerteilDeadline']; ?>
                        <tr>
                            <?php
                            if ($ablaufdatum > 0) { ?>
                                <td class='lmicon'>
                                    <div class='tablecontainer'>
                                        <img width='48px' alt='lmicon' src='<?php echo $icons[$zeile['OKatKey']] ?>'>
                                        <div id='bezeichnung- <?php echo $zähler ?>' style='font-weight: 600; padding-left: 16px;'>
                                            <?php echo $zeile['Bezeichnung'] ?>
                                        </div>
                                    </div>
                                </td>
                            <?php } else if ($ablaufdatum <= 0) { ?>
                                    <td class='lmicon'>
                                        <div class='tablecontainer'>
                                            <img width='48px' alt='lmicon' src='<?php echo $icons[$zeile['OKatKey']] ?>'>
                                            <div id='bezeichnung-<?php echo $zähler ?>'
                                                style='font-weight: 600; padding-left: 16px; color: red'>
                                            <?php echo $zeile['Bezeichnung'] ?>
                                            </div>
                                        </div>
                                    </td>
                            <?php } ?>

                            <td id='gewicht-<?php echo $zähler ?>'>
                                <?php echo $zeile['Gewicht'] ?> kg
                            </td>
                            <!-- //If Else Abfrage für Rotfärbung der abgelaufenen Lebensmitel und Kühlicon FÄLLE: rot+Kühl, rot+oKühl, schwarz+Kühl, schwarz+oKühl -->
                            <?php if ($zeile['Kuehlware'] == 0 && $ablaufdatum > 0) {
                                echo "<td>" . $zeile['VerteilDeadline'] . " Tage </td>";
                            } else if ($zeile['Kuehlware'] == 1 && $ablaufdatum > 0) { ?>
                                    <td>
                                        <div class='tablecontainer'>
                                            <div><?php echo $zeile['VerteilDeadline'] ?> Tage</div> <img style='padding-left: 16px;'
                                                alt='coolicnon' src='../media/freeze_icon.svg' width='32'>
                                        </div>
                                    </td>

                            <?php } else if ($zeile['Kuehlware'] == 1 && $ablaufdatum <= 0) { ?>
                                        <td style='color: red'><?php echo $zeile['VerteilDeadline'] ?> Tage </td>

                            <?php } else { ?>
                                        <td style='color: red'>
                                            <div class='tablecontainer'>
                                                <div>
                                            <?php echo $zeile['VerteilDeadline'] ?> Tage
                                                </div>
                                                <img style='padding-left: 16px;' alt='coolicnon' src='../media/freeze_icon.svg' width='32'>
                                            </div>
                                        </td>
                            <?php }
                            // if else Abfrage des Anmerkungsicons 
                            if ($zeile['Anmerkung']) { ?>
                                <td style='text-align: right'>
                                    <img id='bubble' alt='dots' src='../media/comment_icon.svg' width='48px;' />
                                </td>

                            <?php } else { ?>
                                <td style='text-align: right'>
                                    <img id='bubble' style='visibility:hidden' alt='dots' src='../media/comment_icon.svg'
                                        width='48px;' />
                                </td>
                            <?php } ?>
                            <td style='text-align: right; position: relative;'>
                                <img id='options-btn-<?php echo $zähler ?>' onclick='open_close_options(this)' alt='dots'
                                    src='../media/edit_icon.svg' width='48px;' style='cursor: pointer;' />
                                <ul class='options' id='<?php echo $zähler ?>'>
                                    <li onclick='open_lebensmittel_ansehen(this)' id="<?php echo $zähler ?>">
                                        <img src='../media/eye.svg' alt=''><span>Ansehen</span>
                                    </li>
                                    <li onclick='open_lebensmittel_fairteilen(this)' id="<?php echo $zähler ?>">
                                        <img src='../media/arrows.svg' alt=''><span>Fairteilen</span>
                                    </li>
                                    <li onclick='open_lebensmittel_entsorgen(this)' id="<?php echo $zähler ?>">
                                        <img src='../media/trashbin.svg' alt=''><span>Entsorgen</span>
                                    </li>
                                </ul>
                            </td>
                        </tr>

                        <!-- Popup "Lebensmittel entsorgen" -->
                        <div class="overlay" id="popup_lebensmittel_entsorgen-<?php echo $zähler ?>">
                            <div class="popup-wrapper">
                                <div class="popup active">
                                    <div class="popup-header">
                                         <img src='<?php echo $icons[$zeile['OKatKey']] ?>'>
                                        <h5><?php echo $zeile['Bezeichnung'] ?> entsorgen</h5>
                                    </div>
                                    <p>Wenn du Lebensmittel entsorgst verschwinden sie aus der Datenanalyse. Welche Menge des Lebensmittels möchtest du entsorgen?</p>

                                    <form action="" class="popup-form">
                                        <label class="popup-form-label" for="entsorgen-menge">Menge (in kg)</label>
                                        <input type="number" id="entsorgen-menge" max="<?php echo $zeile['Gewicht'] ?>">
                                        <div class="bestand">/ <?php echo $zeile['Gewicht'] ?> Kg</div>
                                    </form>

                                    <button class="secondary-btn" id="<?php echo $zähler ?>" onclick="entsorgen_abbrechen(this)">Abbrechen</button>
                                    <button class="primary-btn-red">Entsorgen</button>
                                </div>
                            </div>
                        </div>

                        <!-- Popup "Lebensmittel fairteilen" -->
                        <div class="overlay" id="popup_lebensmittel_fairteilen-<?php echo $zähler ?>">
                            <div class="popup-wrapper">
                                <div class="popup active">
                                    <div class="popup-header">
                                        <img src="<?php echo $icons[$zeile['OKatKey']] ?>">
                                        <h5><?php echo $zeile['Bezeichnung'] ?> fairteilen?</h5>
                                    </div>
                                    <p>Wenn du Lebensmittel als fairteilt markierst verschwinden sie aus der Übersicht. Welche Menge des Lebensmittels möchtest du fairteilen?</p>
                                    <form action="" class="popup-form">
                                        <label class="popup-form-label" for="fairteil-menge">Menge (in kg)</label>
                                        <input type="number" id="fairteil-menge" max="<?php echo $zeile['Gewicht'] ?>">
                                        <div class="bestand">/ <?php echo $zeile['Gewicht'] ?> Kg</div>
                                    </form>
                                    <button class="secondary-btn" id="<?php echo $zähler ?>" onclick="fairteilen_abbrechen(this)">Abbrechen</button>
                                    <button class="primary-btn" id="fairteilen"
                                        onclick="window.location.href='admin.php?fairteilen=1'">Fairteilen</button>
                                </div>
                            </div>
                        </div>
                          
                        <!-- Popup "Lebensmittel ansehen" -->
                        <div class="overlay" id="popup_lebensmittel_ansehen-<?php echo $zähler ?>">
                            <div class="popup-wrapper">
                                <div class="popup ansehen">
                                    <div class="popup-header-cross">
                                        <div class="container">
                                            <img src="<?php echo $icons[$zeile['OKatKey']] ?>">
                                            <h5><?php echo $zeile['Bezeichnung'] ?></h5>
                                        </div>
                                        <div class="close-btn" id="close">
                                            <img src="../media/cross.svg" alt="Schließen">
                                        </div>
                                    </div>

                                    <div class="info">
                                        <div class="block">
                                            <div class="zeile">
                                                <div>Menge:</div>
                                                <div><?php echo $zeile['Gewicht'] ?> kg</div>
                                            </div>
                                            <div class="zeile">
                                                <div>Inhaltsstoffe:</div>
                                                <div>-</div>
                                            </div>
                                            <div class="zeile">
                                                <div>Genießbar bis:</div>
                                                <div <?php if($ablaufdatum < 0) {echo "style='color: red'";}?>><?php echo $zeile['VerteilDeadline'] ?> Tage <?php if($zeile['Kuehlware'] == 0) {echo "<img style='position: absolute; margin-left: 8px;' width='20px' src='../media/freeze_icon.svg'>";}?></div>
                                            </div>
                                        </div>

                                        <div class="block">
                                            <div class="zeile">
                                                <div>Geliefert von:</div>
                                                <div>Jürgen</div>
                                            </div>
                                            <div class="zeile">
                                                <div>E-Mail:</div>
                                                <div>email.com</div>
                                            </div>
                                            <div class="zeile">
                                                <div>Telefonnr:</div>
                                                <div>10264606194316</div>
                                            </div>
                                        </div>

                                        <div class="block last">
                                            <div class="zeile">
                                                <div>Gerettet von:</div>
                                                <div><?php echo $herkunft[$zeile['HerkunftKey']] ?></div>
                                            </div>
                                            <div class="zeile">
                                                <div>Gespendet am:</div>
                                                <div>20.11.2022</div>
                                            </div>
                                            <div class="zeile">
                                                <div>Anmerkungen:</div>
                                                <div><?php echo $zeile['Anmerkung'] ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button class="secondary-btn-red">Entsorgen</button>
                                    <button class="primary-btn">Fairteilen</button>
                                </div>
                            </div>
                        </div>
                   
                    <?php
                    }
                    ?>

            </div>
            <!--Seiteninhalt-->
            <footer>
                <div class="footerbg">
                    <!-- Nur zu Testzwecken, später entfernen -->
                    <button id="open_nicht_genießbar">Nicht genießbar</button>
                    <button id="open_keine_boxen">Keine Boxen</button>
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
                    <button class="primary-btn" id="fairteilen"
                        onclick="window.location.href='admin.php?fairteilen=1'">Fairteilen</button>
                </div>
            </div>
        </div>

        <?php
        if (isset($_GET['fairteilen'])) { ?>
            <div class='overlay' id='popup_lebensmittel_fairteilt'>
                <div class='popup-wrapper'>
                    <div class='popup active'>
                        <div class='popup-header'>
                            <h3>PRODUKT FAIRTEILT</h3>
                        </div>
                        <p>Das Lebensmittel wurde in den Fairteiler gelegt.</p>
                        <button class='center-btn'>Alles klar</button>
                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- Script zum Öffnen der Pop-Ups -->
        <script type="text/javascript" src="../script/open_popups_mitarbeiter.js"></script>
        <!-- Script zum Öffnen und Schließen des Logout Overlays -->
        <?php
        echo "
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
        </script>"
            ?>
        <?php
    } else {
        session_destroy();
        header("Location: ../index.php");
    }
    ?>
</body>

</html>