<?php
//Session eröffnen
session_start();
//Datenbankverbindung aufbauen
require_once("../dbconnect/dbconnect.inc.php");
$db_handle = new DBController();

//Sessionlogin lokal speichern
$login = $_SESSION['login'];

// ----------- QUERYS -----------

$AlleLebensmittelQuery = $db->prepare("SELECT * FROM (Lebensmittel LEFT JOIN Lieferung ON Lebensmittel.LMkey=Lieferung.LMKey) LEFT JOIN Foodsaver ON Lieferung.FSkey=Foodsaver.FSKey ORDER BY VerteilDeadline");
$erfolg = $AlleLebensmittelQuery->execute();
//Zellenweise Verarbeitung der Datenbankabfrage
$AlleLebensmittelResult = $AlleLebensmittelQuery->fetchAll();
//Fehlertest
if (!$erfolg) {
    $fehler = $query->errorInfo();
    die("Folgender Datenbankfehler ist aufgetreten:" . $fehler[2]);
}

// --
$InitialeAbgabeSet = [];
$conn = $db_handle->connectDB();
$InitialeAbgabeQuery = "SELECT * FROM Bestand_Bewegung WHERE LStatusKey = 1";
$InitialeAbgabeResult = mysqli_query($conn, $InitialeAbgabeQuery);
while ($row = mysqli_fetch_assoc($InitialeAbgabeResult)) {
    $InitialeAbgabeSet[] = $row;
}
// --
$FairteilteAbgabeSet = [];
$FairteilteAbgabeQuery = "SELECT * FROM Bestand_Bewegung WHERE LStatusKey = 2";
$FairteilteAbgabeResult = mysqli_query($conn, $FairteilteAbgabeQuery);
while ($row = mysqli_fetch_assoc($FairteilteAbgabeResult)) {
    $FairteilteAbgabeSet[] = $row;
}
// -- 
$EntsorgteAbgabeSet = [];
$EntsorgteAbgabeQuery = "SELECT * FROM Bestand_Bewegung WHERE LStatusKey = 3";
$EntsorgteAbgabeResult = mysqli_query($conn, $EntsorgteAbgabeQuery);
while ($row = mysqli_fetch_assoc($EntsorgteAbgabeResult)) {
    $EntsorgteAbgabeSet[] = $row;
}
$bewegteAbgaben = array_merge($FairteilteAbgabeSet, $EntsorgteAbgabeSet);

if ($bewegteAbgaben != 0 && $InitialeAbgabeSet != 0) {
    foreach($bewegteAbgaben as $key => $value) {
        foreach($InitialeAbgabeSet as $key2 => $value2) {
            if($value['LMkey'] == $value2['LMkey']) {
                $InitialeAbgabeSet[$key2]['BewegMenge'] = $value2['BewegMenge'] - $value['BewegMenge'];   
            }
        }
    }
    $filteredSet = $InitialeAbgabeSet;

    foreach ($AlleLebensmittelResult as $key => $value) {
        foreach($filteredSet as $key2 => $value2) {
            if ($value['LMkey'] == $value2['LMkey']) {
                $AlleLebensmittelResult[$key]['Gewicht'] = $value2['BewegMenge'];
                if ($AlleLebensmittelResult[$key]['Gewicht'] == 0) {
                    unset($AlleLebensmittelResult[$key]);
                }
            }
        }
    }
}
$filteredLebensmittel = $AlleLebensmittelResult;



//Array für die Icons in der Lagerübersicht
$icons = array(
    1 => "../media/kategorien/icon_gemuese.svg",
    2 => "../media/kategorien/icon_obst.svg",
    3 => "../media/kategorien/icon_backwaren-suess.svg",
    4 => "../media/kategorien/icon_backwaren-salzig.svg",
    5 => "../media/kategorien/icon_trockenprodukte.svg",
    6 => "../media/kategorien/icon_kuehlprodukte.svg",
    7 => "../media/kategorien/icon_konserven.svg",
    8 => "../media/kategorien/sonstiges.svg",
);

$iconsred = array(
    1 => "../media/kategorien/icon_gemuese_red.svg",
    2 => "../media/kategorien/icon_obst_red.svg",
    3 => "../media/kategorien/icon_backwaren-suess_red.svg",
    4 => "../media/kategorien/icon_backwaren-salzig_red.svg",
    5 => "../media/kategorien/icon_trockenprodukte_red.svg",
    6 => "../media/kategorien/icon_kuehlprodukte_red.svg",
    7 => "../media/kategorien/icon_konserven_red.svg",
    8 => "../media/kategorien/sonstiges_red.svg",
);

$herkunft = array(
    1 => "Supermarkt",
    2 => "Wochenmarkt",
    3 => "Bäckerei",
    4 => "Gastronomie",
    5 => "Veranstaltung",
    6 => "Sonstiges"
);

//Zugriff auf db um Verfügbarkeit der  boxen zu prüfen
$BVerfuegbarkeit = $db->prepare("SELECT `NochVerfuegbar` FROM `BVerfuegbarkeit` ORDER BY `BVerfuegKey` DESC LIMIT 1");
$BVerfuegbarkeit->execute();

//Zellenweise Verarbeitung der Datenbankabfrage
 $BoxResult = $BVerfuegbarkeit->fetchColumn();


 //Wenn der Boxen key gesetzt wird (passiert wenn Mitarbeiter*in auf "Boxen nachgefüllt" drückt) dann wird BVerfuegbarkeit der letzten Eintrags auf 0 (-> Boxen verfügbar) gesetzt
if (isset($_GET['box']))
{
    $BVerfuegbarkeit =  $db->prepare("UPDATE `BVerfuegbarkeit` SET `NochVerfuegbar` = '0'  ORDER BY `BVerfuegKey` DESC LIMIT 1");
    $BAktualisiert = $BVerfuegbarkeit->execute();
    
    $BoxResult = $BVerfuegbarkeit->fetchColumn();
    }
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
    <link rel="icon" type="image/png" href="../media/favicon.png">
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
                            Abmelden
                        </p>
                        <img alt="ausloggen" src="../media/lock_icon.svg" width="48" height="48" />
                    </div>
                </div>
            </div>
            <!--Seiteninhalt-->
            <div class="seiteninhalt">
                <div class="wrap">
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
                        $zähler = 0;

                        //Datumsberechnung
                        $jetzt = time();
                        // $result['VerteilDeadline'] = strtotime($result['VerteilDeadline']);
                    
                        foreach ($filteredLebensmittel as $key => $zeile) {
                            $zähler += 1;
                            
                            $zeile['VerteilDeadline'] = round((strtotime($zeile['VerteilDeadline']) - $jetzt)  / (60 * 60 * 24), $precision = 1, $mode= PHP_ROUND_HALF_DOWN);
                            $ablaufdatum = $zeile['VerteilDeadline']; 
                            ?>
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
                                                <img width='48px' alt='lmicon' src='<?php echo $iconsred[$zeile['OKatKey']] ?>'>
                                                <div id='bezeichnung-<?php echo $zähler ?>'
                                                    style='font-weight: 600; padding-left: 16px; color: #E97878'>
                                                <?php echo $zeile['Bezeichnung'] ?>
                                                </div>
                                            </div>
                                        </td>
                                <?php } ?>

                                <td id='gewicht-<?php echo $zähler ?>'>
                                    <?php echo $zeile['Gewicht'] ?> kg
                                </td>
                                <!-- //If Else Abfrage für Rotfärbung der abgelaufenen Lebensmitel und Kühlicon FÄLLE: rot+Kühl, rot+oKühl, schwarz+Kühl, schwarz+oKühl -->
                                <?php 
                                if ($ablaufdatum < 9999) { ?>
                                    <td <?php if ($ablaufdatum <= 0)
                                        echo "style='display:flex; align-items:center; color: #E97878'"; 
                                        echo "style='display:flex; align-items:center;'"; ?>> 
                                        <?php echo $zeile['VerteilDeadline']; ?> <?php if($ablaufdatum == 1) echo"Tag"; else echo"Tage";
                                        if ($zeile['Kuehlware'] == 1) {echo "<img style='margin-left: 12px;' width='27px' src='../media/freeze_icon.svg'>";}?>
                                    </td>
                                <?php  } else { ?>
                                    <td style="display:flex; align-items: center;">
                                        unkritisch
                                        <?php if ($zeile['Kuehlware'] == 1) {echo "<img style='margin-left: 12px;' width='27px' src='../media/freeze_icon.svg'>";} ?>
                                    </td>
                                <?php  
                                } 
                                // if else Abfrage des Anmerkungsicons 
                                if ($zeile['Anmerkung']) { ?>
                                    <td style='text-align: right; position: relative;'>
                                        <img id='bubble' class="open_icon" alt='dots' src='../media/comment_icon.svg' width='48px;' onclick="open_close_options(this)"/>
                                    
                                        <!-- Anmerkung Pop-Up -->
                                        <div class="anmerkung">
                                            <h5>Anmerkung:</h5>
                                            <p><?php echo $zeile['Anmerkung'] ?></p>
                                        </div>
                                    </td>

                                <?php } else { ?>
                                    <td style='text-align: right'>
                                        <img id='bubble' style='visibility:hidden' alt='dots' src='../media/comment_icon.svg'
                                            width='48px;' />
                                    </td>
                                <?php } ?>
                                <td style='text-align: right; position: relative;'>
                                    <img id='options-btn-<?php echo $zähler ?>' class="open_icon" onclick='open_close_options(this)' alt='dots'
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

                                        <form id="entsorgen-<?php echo $zähler ?>" method="POST" action="014_admin_skript.php" class="popup-form">
                                            <label class="popup-form-label" for="entsorgen-menge">Menge (in kg)</label>
                                            <input type="number" name="entsorgen-menge" id="entsorgen-menge" max="<?php echo $zeile['Gewicht'] ?>" 
                                            value="<?php echo $zeile['Gewicht']; ?>">
                                            <input type="hidden" id="lmkey" name="lmkey" value="<?php echo $zeile['LMkey'] ?>">
                                            <div class="bestand">/ <?php echo $zeile['Gewicht'] ?> Kg</div>
                                        </form>
                                        <button class="secondary-btn" id="<?php echo $zähler ?>" onclick="entsorgen_abbrechen(this)">Abbrechen</button>
                                        <input type="submit" form="entsorgen-<?php echo $zähler ?>" class="primary-btn-red" value="Entsorgen"></input>
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
                                        <form id="fairteilen-<?php echo $zähler ?>" method="POST" action="014_admin_skript.php" class="popup-form">
                                            <label class="popup-form-label" for="fairteil-menge">Menge (in kg)</label>
                                            <input type="number" id="fairteil-menge" name="fairteil-menge" max="<?php echo $zeile['Gewicht'] ?>" 
                                            value="<?php echo $zeile['Gewicht']; ?>">
                                            <input type="hidden" id="lmkey" name="lmkey" value="<?php echo $zeile['LMkey'] ?>">
                                            <div class="bestand">/ <?php echo $zeile['Gewicht'] ?> Kg</div>
                                        </form>
                                        <button class="secondary-btn" id="<?php echo $zähler ?>" onclick="fairteilen_abbrechen(this)">Abbrechen</button>
                                        <input type="submit" form="fairteilen-<?php echo $zähler ?>" class="primary-btn" value="Fairteilen"></input>
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
                                            <div class="close-btn" id="<?php echo $zähler ?>" onclick="close_lebensmittel_ansehen(this)">
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
                                                    <div <?php if($ablaufdatum < 0) {echo "style='color: #E97878'";}?>><?php
                                                    if($ablaufdatum > 9999) {
                                                        echo "unkritisch";
                                                    } else {
                                                        echo $zeile['VerteilDeadline'];
                                                        if ($ablaufdatum == 1) {
                                                            echo " Tag";
                                                        } else {
                                                            echo" Tage";
                                                        }
                                                    }

                                                    if ($zeile['Kuehlware'] == 1) {echo "<img style='position: absolute; margin-left: 8px;' width='20px' src='../media/freeze_icon.svg'>";}?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="block">
                                                <div class="zeile">
                                                    <div>Geliefert von:</div>
                                                    <div><?php echo $zeile['Vorname'] ?></div>
                                                </div>
                                                <div class="zeile">
                                                    <div>E-Mail:</div>
                                                    <div><?php echo $zeile['Email'] ?></div>
                                                </div>
                                                <div class="zeile">
                                                    <div>Telefonnr:</div>
                                                    <div><?php echo $zeile['TelNr'] ?></div>
                                                </div>
                                            </div>

                                            <div class="block last">
                                                <div class="zeile">
                                                    <div>Gerettet von:</div>
                                                    <div><?php echo $herkunft[$zeile['HerkunftKey']] ?></div>
                                                </div>
                                                <div class="zeile">
                                                    <div>Gespendet am:</div>
                                                    <div><?php echo substr((strval($zeile['LieferDatum'])), 0, -9) ?></div>
                                                </div>
                                                <div class="zeile">
                                                    <div>Anmerkungen:</div>
                                                    <div><?php echo $zeile['Anmerkung'] ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button class="secondary-btn-red" id="<?php echo $zähler ?>" onclick='close_lebensmittel_ansehen(this); open_lebensmittel_entsorgen(this)'>Entsorgen</button>
                                        <button class="primary-btn" id="<?php echo $zähler ?>" onclick='close_lebensmittel_ansehen(this); open_lebensmittel_fairteilen(this)'>Fairteilen</button>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </table>
                </div>
            </div>
            <!--Seiteninhalt-->
            <footer>
                <div class="footerbg">
                    <!-- Nur zu Testzwecken, später entfernen -->
                    <button id="open_nicht_genießbar" onclick="open_NichtGenießbar()">Nicht genießbar</button>
                    <a href="admin.php"><button class="refreshbutton" id="refreshdash">
                            Liste Aktualisieren
                        </button></a>
                </div>
            </footer>
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
                                Abbrechen
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
        <div class="overlay" id="popup_keine_boxen" style="display: <?php if ($BoxResult == 1) {
                                echo "flex";
                            } else {
                                echo "none";
                            } ?>">
            <div class="popup-wrapper">
                <div class="popup active">
                    <h3>HOPPLA!</h3>
                    <p>Jemand hat gerade die letzte Box genommen. <br> Sieh nach und sorge für Nachschub.</p>
                    <button id="close_keine_boxen" class="secondary-btn" onclick="close_KeineBoxen();">Später erinnern</button>
                    <a class="primary-btn" style="text-align: center" onclick="close_KeineBoxen();" href="<?php if ($BoxResult == 1) {echo "admin.php?box=1";} ?>" >
                    Boxen nachgefüllt
                    </a>
                    
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