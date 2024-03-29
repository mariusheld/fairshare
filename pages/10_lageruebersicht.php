<?php
session_start();
//Datenbankverbindung aufbauen
require_once("../dbconnect/dbconnect.inc.php");
$db_handle = new DBController();

// ----------- QUERYS -----------

$conn = $db_handle->connectDB();
// --
$AlleLebensmittelQuery = "SELECT Lebensmittel.LMKey AS LMkey, Lebensmittel.Bezeichnung, Lebensmittel.Gewicht, Lebensmittel.Betrieb, Lebensmittel.Anmerkung, Lebensmittel.HerkunftKey, Lebensmittel.Kuehlware, Lieferung.LieferDatum, Lebensmittel.OKatKey, Lebensmittel.VerteilDeadline, BekAllergene.AllergenName, Foodsaver.Email, Foodsaver.Nachname, Foodsaver.TelNr, Foodsaver.Vorname
FROM (((Lebensmittel 
LEFT JOIN Lieferung ON Lebensmittel.LMkey=Lieferung.LMKey) 
LEFT JOIN Foodsaver ON Lieferung.FSkey=Foodsaver.FSKey) 
LEFT JOIN LM_Allergene ON Lebensmittel.LMkey=LM_Allergene.LMKey) 
LEFT JOIN BekAllergene ON LM_Allergene.AllergenKey=BekAllergene.AllergenKey  ORDER BY VerteilDeadline";
$AlleLebensmittelResult = mysqli_query($conn, $AlleLebensmittelQuery);
while ($row = mysqli_fetch_assoc($AlleLebensmittelResult)) {
    $AlleLebensmittelSet[] = $row;
}

// --
$InitialeAbgabeSet = [];
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
    foreach ($bewegteAbgaben as $key => $value) {
        foreach ($InitialeAbgabeSet as $key2 => $value2) {
            if ($value['LMkey'] == $value2['LMkey']) {
                $InitialeAbgabeSet[$key2]['BewegMenge'] = $value2['BewegMenge'] - $value['BewegMenge'];
            }
        }
    }
    $filteredSet = $InitialeAbgabeSet;

    foreach ($AlleLebensmittelSet as $key => $value) {
        foreach ($filteredSet as $key2 => $value2) {
            if ($value['LMkey'] == $value2['LMkey']) {
                $AlleLebensmittelSet[$key]['Gewicht'] = $value2['BewegMenge'];
                if ($AlleLebensmittelSet[$key]['Gewicht'] == 0) {
                    unset($AlleLebensmittelSet[$key]);
                }
            }
        }
    }
}
$filteredLebensmittel = $AlleLebensmittelSet;

// ----------- ICONS -----------
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

// ----------- Letzte Box genommen -----------
//Zugriff auf db um Verfügbarkeit der  boxen zu prüfen
$BVerfuegbarkeit = $db->prepare("SELECT `NochVerfuegbar` FROM `BVerfuegbarkeit` ORDER BY `BVerfuegKey` DESC LIMIT 1");
$BVerfuegbarkeit->execute();

//Zellenweise Verarbeitung der Datenbankabfrage
$BoxResult = $BVerfuegbarkeit->fetchColumn();

//Wenn der Boxen key gesetzt wird (passiert wenn Mitarbeiter*in auf "Boxen nachgefüllt" drückt) dann wird BVerfuegbarkeit der letzten Eintrags auf 0 (-> Boxen verfügbar) gesetzt
if (isset($_GET['box']) && $_GET['box'] == 1) {
    $BVerfuegbarkeit = $db->prepare("UPDATE `BVerfuegbarkeit` SET `NochVerfuegbar` = '0'  ORDER BY `BVerfuegKey` DESC LIMIT 1");
    $BAktualisiert = $BVerfuegbarkeit->execute();

    $BoxResult = 0;
}

// ----------- Lebensmittel Fairteilen und Entsorgen -----------

if (isset($_POST["fairteil-menge"])) {
    $lmkey = $_POST["lmkey"];
    $bewegMenge = $_POST["fairteil-menge"];
}
if (isset($_GET['fairteilen'])) {
    $lmkey = $_GET['lmkey'];
    $bewegMenge = $_GET['bewegMenge'];
    $LStatusKey = 2;
    $FairteilQuery = $db->prepare("INSERT INTO `Bestand_Bewegung` (`LMkey`, `LStatusKey`, `BewegDatum`, `BewegMenge`, `EntsorgGrund`) 
    VALUES ('$lmkey', '$LStatusKey', now(), '$bewegMenge', NULL)");
    $erfolg = $FairteilQuery->execute();
    // Fehlertest
    if (!$erfolg) {
        $fehler = $query->errorInfo();
        die("Folgender Datenbankfehler ist aufgetreten:" . $fehler[2]);
    }
    // Seite neu laden
    header("Location: 10_lageruebersicht.php?success=true");
}

if (isset($_POST["entsorgen-menge"])) {
    $lmkey = $_POST["lmkey"];
    $bewegMenge = $_POST["entsorgen-menge"];
}
if (isset($_GET['entsorgen'])) {
    $lmkey = $_GET['lmkey'];
    $bewegMenge = $_GET['bewegMenge'];
    $LStatusKey = 3;
    $EntsorgenQuery = $db->prepare("INSERT INTO `Bestand_Bewegung` (`LMkey`, `LStatusKey`, `BewegDatum`, `BewegMenge`, `EntsorgGrund`)
    VALUES ('$lmkey', '$LStatusKey', now(), '$bewegMenge', NULL)");
    $erfolg = $EntsorgenQuery->execute();
    // Fehlertest
    if (!$erfolg) {
        $fehler = $query->errorInfo();
        die("Folgender Datenbankfehler ist aufgetreten:" . $fehler[2]);
    }
    // Seite neu laden
    header("Location: 10_lageruebersicht.php?success=true");
}


//Zugang zur Lagerübersicht nur mit Login
$login = $_SESSION['login'];
if ($login == true) {
    ?>

    <!DOCTYPE html>
    <html lang=de>

    <head>
        <meta charset="UTF-8" />
        <title>FAIRSHARE</title>
        <link rel="icon" type="image/x-icon" href="../favicon/favicon.ico">

        <script src="https://code.jquery.com/jquery-3.6.2.min.js"
            integrity="sha256-2krYZKh//PcchRtd+H+VyyQoZ/e3EcrkxhM8ycwASPA=" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="../css/adminstyle.css" />
        <link rel="stylesheet" href="../css/lageruebersicht.css">
        <link rel="icon" type="image/png" href="../media/favicon.png">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Fira+Sans:wght@400;600&family=Londrina+Solid:wght@300;400&display=swap');
        </style>
    </head>

    <body>
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
                            <th style="width: 50px;"></th>
                            <th style="width: 45%;">Lebensmittel</th>
                            <th style="width: 15%;">Menge</th>
                            <th>Genießbar</th>
                            <th style="width: 50px;"></th>
                            <th style="width: 50px;"></th>
                        </tr>
                        <!--Tabelleninhalt-->

                        <?php
                        $zähler = 0;
                        //Datumsberechnung
                        $jetzt = time();

                        foreach ($filteredLebensmittel as $key => $zeile) {
                            $zähler += 1;
                            $zeile['VerteilDeadline'] = round((strtotime($zeile['VerteilDeadline']) - $jetzt) / (60 * 60));
                            if ($zeile['VerteilDeadline'] < 9999) {

                                $stunden = strval($zeile['VerteilDeadline']);

                                if ($stunden > 24) {
                                    $tage = round(($stunden / 24), $precision = 0);
                                    $zeitangabe = "";
                                    if ($tage == 1 || $tage == -1) {
                                        $zeitangabe = $tage . " Tag ";
                                    } else {
                                        $zeitangabe = $tage . " Tage ";
                                    }
                                } else {
                                    $zeitangabe = "";
                                    if ($stunden == 1 || $stunden == -1) {
                                        $zeitangabe = $stunden . " Stunde ";
                                    } else {
                                        $zeitangabe = $stunden . " Stunden ";
                                    }
                                }
                            }
                            $ablaufdatum = $zeile['VerteilDeadline'];
                            ?>
                            <tr>
                            <td class="mehrfachauswahl">
                                    <div class="check-item">
                                        <input name="check" type="checkbox" id="check-<?php echo $zähler ?>" onclick="addToMehrfachauswahl(this, <?php echo $zeile['Gewicht'] ?>,<?php echo $zeile['LMkey'] ?>)"> 
                                        <img src='../media/checkbox.svg' alt='checkbox' />
                                        <img src='../media/checkbox_checked.svg' alt='checkbox_checked' />
                                        <!-- </form> -->
                                    </div>
                                </td>
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
                                        <?php echo $zeitangabe; ?> <?php 
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
                                if ($zeile['Anmerkung'] || $zeile['AllergenName']) { ?>
                                    <td style='text-align: right; position: relative;'>
                                        <img id='bubble' class="open_icon" alt='dots' src='../media/comment_icon.svg' width='48px;' onclick="open_close_options(this)"/>
                                    
                                        <!-- Anmerkung Pop-Up -->
                                        <div class="anmerkung">
                                            <?php if ($zeile['Anmerkung']) { ?>
                                                <h5>Anmerkung:</h5>
                                                <p><?php echo $zeile['Anmerkung'] ?></p>
                                            <?php } ?>
                                            <?php if ($zeile['AllergenName']) { ?>
                                                <h5 <?php if ($zeile['Anmerkung']) { ?> style="margin-top: 32px;"<?php } ?>>Allergene und Inhaltsstoffe:</h5>
                                                <p><?php echo $zeile['AllergenName'] ?></p>
                                            <?php } ?>
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
                                            <h5>
                                                <?php echo $zeile['Bezeichnung'] ?> entsorgen
                                            </h5>
                                        </div>
                                        <p>Wenn du Lebensmittel entsorgst verschwinden sie aus der Datenanalyse. Welche Menge
                                            des Lebensmittels möchtest du entsorgen?</p>
                                        <form id="entsorgen-<?php echo $zähler ?>" method="POST"
                                            action="10_lageruebersicht.php?entsorgid=<?php echo $zähler ?>" class="popup-form">
                                            <label class="popup-form-label" for="entsorgen-menge">Menge (in kg)</label>
                                            <input type="number" min="0.01" step="0.01" id="entsorgen-menge"
                                                name="entsorgen-menge" max="<?php echo $zeile['Gewicht'] ?>"
                                                value="<?php echo $zeile['Gewicht']; ?>">
                                            <input type="hidden" id="lmkey" name="lmkey" value="<?php echo $zeile['LMkey'] ?>">
                                            <input type="hidden" id="bezeichnung" name="bezeichnung"
                                                value="<?php echo $zeile['Bezeichnung'] ?>">
                                            <input type="hidden" id="okatkey" name="okatkey"
                                                value="<?php echo $zeile['OKatKey'] ?>">
                                            <div class="bestand">/
                                                <?php echo $zeile['Gewicht'] ?> Kg
                                            </div>
                                        </form>
                                        <button class="secondary-btn" id="<?php echo $zähler ?>"
                                            onclick="entsorgen_abbrechen(this)">Abbrechen</button>
                                        <input type="submit" form="entsorgen-<?php echo $zähler ?>" class="primary-btn-red"
                                            value="Entsorgen"></input>
                                    </div>
                                </div>
                            </div>

                            <!-- Popup "Lebensmittel entsorgen bestätigen" -->
                            <div class="overlay" id="popup_lebensmittel_entsorgen_bestätigen-<?php echo $zähler ?>" <?php if (isset($_GET["entsorgid"]) && $_GET['entsorgid'] == $zähler)
                                   echo "style='display: flex'"; ?>>
                                <div class="popup-wrapper">
                                    <div class="popup active">
                                        <div class="popup-header">
                                            <img src="<?php echo $icons[$zeile['OKatKey']] ?>">
                                            <h5>
                                                <?php echo $zeile['Bezeichnung'] ?> entsorgen?
                                            </h5>
                                        </div>
                                        <p>
                                            Möchtest du wirklich
                                            <?php if (isset($_POST["entsorgen-menge"])) echo $_POST["entsorgen-menge"] ?> 
                                                kg des Lebensmittels <br />
                                                <span class="marked-green">
                                                    <?php echo $zeile['Bezeichnung']; echo " (" . $zeile['Gewicht'] . " kg) " ?>
                                                </span>
                                            als entsorgt markieren?
                                        </p>
                                        <button class="secondary-btn" id="<?php echo $zähler ?>"
                                            onclick="entsorgen_bestätigen_abbrechen(this)">Abbrechen</button>
                                        <a class="primary-btn-red"
                                            href="10_lageruebersicht.php?entsorgen&lmkey=<?php echo "" . $lmkey . "&bewegMenge=" . $bewegMenge . ""; ?>"
                                            style="text-align: center">Entsorgen</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Popup "Lebensmittel fairteilen" -->
                            <div class="overlay" id="popup_lebensmittel_fairteilen-<?php echo $zähler ?>">
                                <div class="popup-wrapper">
                                    <div class="popup active">
                                        <div class="popup-header">
                                            <img src="<?php echo $icons[$zeile['OKatKey']] ?>">
                                            <h5>
                                                <?php echo $zeile['Bezeichnung'] ?> fairteilen?
                                            </h5>
                                        </div>
                                        <p>Wenn du Lebensmittel als fairteilt markierst verschwinden sie aus der Übersicht.
                                            Welche Menge des Lebensmittels möchtest du fairteilen?</p>
                                        <form id="fairteilen-<?php echo $zähler ?>" method="POST"
                                            action="10_lageruebersicht.php?fairteilid=<?php echo $zähler ?>" class="popup-form">
                                            <label class="popup-form-label" for="fairteil-menge">Menge (in kg)</label>
                                            <input type="number" min="0.01" step="0.01" id="fairteil-menge"
                                                name="fairteil-menge" max="<?php echo $zeile['Gewicht'] ?>"
                                                value="<?php echo $zeile['Gewicht']; ?>">
                                            <input type="hidden" id="lmkey" name="lmkey" value="<?php echo $zeile['LMkey'] ?>">
                                            <input type="hidden" id="bezeichnung" name="bezeichnung"
                                                value="<?php echo $zeile['Bezeichnung'] ?>">
                                            <input type="hidden" id="okatkey" name="okatkey"
                                                value="<?php echo $zeile['OKatKey'] ?>">
                                            <div class="bestand">/
                                                <?php echo $zeile['Gewicht'] ?> Kg
                                            </div>
                                        </form>
                                        <button class="secondary-btn" id="<?php echo $zähler ?>"
                                            onclick="fairteilen_abbrechen(this)">Abbrechen</button>
                                        <input type="submit" form="fairteilen-<?php echo $zähler ?>" class="primary-btn"
                                            value="Fairteilen"></input>
                                    </div>
                                </div>
                            </div>

                            <!-- Popup "Lebensmittel fairteilen bestätigen" -->
                            <div class="overlay" id="popup_lebensmittel_fairteilen_bestätigen-<?php echo $zähler ?>" <?php if (isset($_GET["fairteilid"]) && $_GET['fairteilid'] == $zähler)
                                   echo "style='display: flex'"; ?>>
                                <div class="popup-wrapper">
                                    <div class="popup active">
                                        <div class="popup-header">
                                            <img src="<?php echo $icons[$zeile['OKatKey']] ?>">
                                            <h5>
                                                <?php echo $zeile['Bezeichnung'] ?> fairteilen?
                                            </h5>
                                        </div>
                                        <p>
                                            Möchtest du wirklich
                                            <?php if (isset($_POST["fairteil-menge"])) echo $_POST["fairteil-menge"] ?>
                                                kg des Lebensmittels <br />
                                                <span class="marked-green">
                                                    <?php echo $zeile['Bezeichnung'];
                                                    echo " (" . $zeile['Gewicht'] . " kg) " ?>
                                                </span>
                                            als fairteilt markieren?
                                        </p>
                                        <button class="secondary-btn" id="<?php echo $zähler ?>"
                                            onclick="fairteilen_bestätigen_abbrechen(this)">Abbrechen</button>
                                        <a class="primary-btn"
                                            href="10_lageruebersicht.php?fairteilen&lmkey=<?php echo "" . $lmkey . "&bewegMenge=" . $bewegMenge . ""; ?>"
                                            style="text-align: center">Fairteilen</a>
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
                                                <h5>
                                                    <?php echo $zeile['Bezeichnung'] ?>
                                                </h5>
                                            </div>
                                            <div class="close-btn" id="<?php echo $zähler ?>"
                                                onclick="close_lebensmittel_ansehen(this)">
                                                <img src="../media/cross.svg" alt="Schließen">
                                            </div>
                                        </div>

                                        <div class="info">
                                            <div class="block">
                                                <div class="zeile">
                                                    <div>Menge:</div>
                                                    <div>
                                                        <?php echo $zeile['Gewicht'] ?> kg
                                                    </div>
                                                </div>
                                                <div class="zeile">
                                                    <div>Inhaltsstoffe:</div>
                                                    <div>
                                                        <?php if ($zeile['AllergenName']) {
                                                            echo $zeile['AllergenName'];
                                                        } else {
                                                            echo "-";
                                                        } ?>
                                                    </div>
                                                </div>
                                                <div class="zeile">
                                                    <div>Genießbar bis:</div>
                                                    <div <?php if ($ablaufdatum < 0) {
                                                        echo "style='color: #E97878'";
                                                    } ?>><?php
                                                    if ($ablaufdatum > 9999) {
                                                        echo "unkritisch";
                                                    } else {
                                                        echo $zeitangabe;
                                                    }

                                                    if ($zeile['Kuehlware'] == 1) {
                                                        echo "<img style='position: absolute; margin-left: 8px;' width='20px' src='../media/freeze_icon.svg'>";
                                                    } ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="block">
                                                <div class="zeile">
                                                    <div>Geliefert von:</div>
                                                    <div>
                                                        <?php echo "" . $zeile['Vorname'] . " " . $zeile['Nachname'] . "" ?>
                                                    </div>
                                                </div>
                                                <div class="zeile">
                                                    <div>E-Mail:</div>
                                                    <div>
                                                        <?php echo $zeile['Email'] ?>
                                                    </div>
                                                </div>
                                                <div class="zeile">
                                                    <div>Telefonnr:</div>
                                                    <div>
                                                        <?php echo $zeile['TelNr'] ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="block last">
                                                <div class="zeile">
                                                    <div>Gerettet von:</div>
                                                    <div>
                                                        <?php echo $herkunft[$zeile['HerkunftKey']] ?>
                                                    </div>
                                                </div>
                                                <div class="zeile">
                                                    <div>Gespendet am:</div>
                                                    <div>
                                                        <?php echo substr((strval($zeile['LieferDatum'])), 0, -9) ?>
                                                    </div>
                                                </div>
                                                <div class="zeile">
                                                    <div>Anmerkungen:</div>
                                                    <div>
                                                        <?php echo $zeile['Anmerkung'] ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <button class="secondary-btn-red" id="<?php echo $zähler ?>"
                                            onclick='close_lebensmittel_ansehen(this); open_lebensmittel_entsorgen(this)'>Entsorgen</button>
                                        <button class="primary-btn" id="<?php echo $zähler ?>"
                                            onclick='close_lebensmittel_ansehen(this); open_lebensmittel_fairteilen(this)'>Fairteilen</button>
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
            <footer id="mfwFooter" style="display: none;">
                <div class="mfwFooter" style="width:100%;">
                    <div>
                        <h3>Mehrfachauswahl</h3>
                    </div>
                    <div>
                        <a href="10_lageruebersicht.php?mfwAbbrechen" class="secondary-btn">Abbrechen</a>
                        <button class="primary-btn-red" onclick="mfwEntsorgenBtn()">
                            <img src="../media/arrows_white.svg" alt="Entsorgen">
                            Entsorgen</button>
                        <button class="primary-btn" onclick="mfwFairteilenBtn()">
                            <img src="../media/trashbin_white.svg" alt="Fairteilen">
                            Fairteilen</button>
                    </div>
                </div>
            </footer>
            <footer id="footer">
                <div class="footerbg">
                    <a href="10_lageruebersicht.php">
                        <button class="refreshbutton" id="refreshdash">
                            Liste Aktualisieren
                        </button>
                    </a>
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
                                <a href="../index.php?logout" style="color: white">
                                    <div class="buttongreen">
                                        Abmelden
                                    </div>
                                </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MEHRFACHAUSWAHL POPUPS! -->
        <!-- Popup "Mehrfachauswahl entsorgen bestätigen" -->
        <div class="overlay" id="popup_mfw_entsorgen_bestätigen">
            <div class="popup-wrapper">
                <div class="popup active">
                    <div class="popup-header">
                        <h3>Entsorgen?</h3>
                    </div>
                    <p>
                        Bist du sicher, dass du alle ausgewählten Lebensmittel entsorgen möchtest?
                    </p>
                    <button class="secondary-btn" onclick="mfw_bestätigen_abbrechen()">Abbrechen</button>
                    <button class="primary-btn-red" onclick="mfwEntsorgen()" style="text-align: center">Entsorgen</button>
                </div>
            </div>
        </div>
        <!-- Popup "Mehrfachauswahl fairteilen bestätigen" -->
        <div class="overlay" id="popup_mfw_fairteilen_bestätigen">
            <div class="popup-wrapper">
                <div class="popup active">
                    <div class="popup-header">
                        <h3>Fairteilen?</h3>
                    </div>
                    <p>
                        Bist du sicher, dass du alle ausgewählten Lebensmittel fairteilen möchtest?
                    </p>
                    <button class="secondary-btn" onclick="mfw_bestätigen_abbrechen()">Abbrechen</button>
                    <button class="primary-btn" onclick="mfwFairteilen()" style="text-align: center">Fairteilen</button>
                </div>
            </div>
        </div>


        <?php if (
            isset($_GET['fairteilid']) ||
            isset($_GET['entsorgid']) ||
            isset($_GET['mfwAbbrechen']) ||
            isset($_GET['success'])
        ) { ?>
            <!-- // Popup "Lebensmittel fairteilen bestätigen" -->
            <!-- // Popup "Lebensmittel entsorgen bestätigen" -->
        <?php } else { ?>
            <!-- Popup "Keine Boxen" -->
            <div class="overlay" id="popup_keine_boxen" style="display: 
                        <?php if ($BoxResult == 1) {
                            echo "flex";
                        } else {
                            echo "none";
                        } ?>">
                <div class="popup-wrapper">
                    <div class="popup active">
                        <div class="popup-header">
                            <h3>HOPPLA!</h3>
                        </div>
                        <p>Jemand hat gerade die letzte Box genommen. <br> Sieh nach und sorge für Nachschub.</p>
                        <button id="close_keine_boxen" class="secondary-btn" onclick="close_KeineBoxen();">Später
                            erinnern</button>
                        <!-- <a id="close_keine_boxen" class="secondary-btn" onclick="close_KeineBoxen();" href="10_lageruebersicht.php?box=2">Später erinnern</a> -->
                        <a class="primary-btn" style="text-align: center" onclick="close_KeineBoxen();"
                            href="10_lageruebersicht.php?box=1">
                            Boxen nachgefüllt
                        </a>

                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- Script zum Öffnen der Pop-Ups -->
        <script type="text/javascript" src="../script/10.js"></script>
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
        </script>"
            ?>
    </body>

    </html>

    <?php


} else {
    session_destroy();
    header("Location: ../index.php");
}
?>