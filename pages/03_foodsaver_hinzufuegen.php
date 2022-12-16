<!-- --------- PHP --------- -->

<?php
//Datenbankverbindung aufbauen
require_once("../dbconnect/dbconnect.inc.php");
$db_handle = new DBController();
session_start();

// ----------- VARIABLES ----------
$LMBez = "";
$OKatKey = 0;
$kuehlcheck = false;
$kiste = null;
$menge = null;
$HerkunftKey = "";
$verbrDatum = date(0);
$haltbarkeit = "";
$comment = "";
$allergene = "";

// ---- Error Variables 
$lmbezErr = $kisteErr = $mengeErr = $herkunftErr = $kategorieErr = $haltbarkeitErr = "";

// ---- KategorieIcons 
$icon_backwaren_salzig_url = '../media/kategorien/icon_backwaren-salzig.svg';
$icon_backwaren_suess_url = '../media/kategorien/icon_backwaren-suess.svg';
$icon_gemuese_url = '../media/kategorien/icon_gemuese.svg';
$icon_konserven_url = '../media/kategorien/icon_konserven.svg';
$icon_kuehlprodukte_url = '../media/kategorien/icon_kuehlprodukte.svg';
$icon_obst_url = '../media/kategorien/icon_obst.svg';
$icon_trockenprodukte_url = '../media/kategorien/icon_trockenprodukte.svg';
$icon_sonstiges_url = '../media/kategorien/sonstiges.svg';

// ----------- QUERYS -----------
$conn = $db_handle->connectDB();
$FSkeyQuery = "SELECT FSkey FROM Foodsaver ORDER BY FSkey DESC LIMIT 1";
$FSkeyResult = mysqli_query($conn, $FSkeyQuery);
$KategorieQuery = "SELECT*FROM OberKategorie";
$KategorieResult = mysqli_query($conn, $KategorieQuery);
$HerkunftQuery = "SELECT*FROM HerkunftsKategorie";
$HerkunftResult = mysqli_query($conn, $HerkunftQuery);

while ($row = mysqli_fetch_assoc($KategorieResult)) {
    $kategorieresultset[] = $row;
}
while ($row = mysqli_fetch_assoc($HerkunftResult)) {
    $herkunftresultset[] = $row;
}
while ($row = mysqli_fetch_assoc($FSkeyResult)) {
    $latestFSkey[] = $row;
}

// SET LMKEY -------------
$LMkey = $_SESSION["latestLMkey"] + 1;
// SET FSKEY -------------
$FSkey = $latestFSkey[0]['FSkey'];
// SET KATEGORIEN -------------
$kategorien = $kategorieresultset;
// SET HERKUNFTSKATEGORIEN -------------
$herkunftkategorien = $herkunftresultset;

function consolelog($data, bool $quotes = false)
{
    $output = json_encode($data);
    if ($quotes) {
        echo "<script>console.log('{$output}' );</script>";
    } else {
        echo "<script>console.log({$output} );</script>";
    }
}

consolelog($LMkey);

// ------- FORM VALIDATION ----------
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["LMBez"])) {
        $lmbezErr = "Erforderlich";
    } else {
        $LMBez = test_input($_POST["LMBez"]);
        // check if LMBez only contains letters and whitespace
        if (!preg_match("/^[a-zA-Z-' ]*$/", $LMBez)) {
            $lmbezErr = "Only letters and white space allowed";
        }
    }

    if (
        isset($_POST['kuehlcheck']) &&
        $_POST['kuehlcheck'] == 'true'
    ) {
        $kuehlcheck = true;
    }

    if (empty($_POST["kiste"])) {
        $kisteErr = "Erforderlich";
    } else {
        $kiste = test_input($_POST["kiste"]);
    }

    if (empty($_POST["menge"])) {
        $mengeErr = "Erforderlich";
    } else {
        $menge = test_input($_POST["menge"]);
    }

    if (empty($_POST["HerkunftKey"])) {
        $herkunftErr = "Erforderlich";
    } else {
        $HerkunftKey = test_input($_POST["HerkunftKey"]);
    }

    if (empty($_POST["OKatKey"])) {
        $kategorieErr = "Erforderlich";
    } else {
        $OKatKey = test_input($_POST["OKatKey"]);
    }

    if (empty($_POST["haltbarkeit"])) {
        $haltbarkeitErr = "Erforderlich";
    } else {
        $haltbarkeit = test_input($_POST["haltbarkeit"]);
    }

    if (!empty($_POST["allergene"])) {
        $allergene = test_input($_POST["allergene"]);
    }

    if (!empty($_POST["comment"])) {
        $comment = test_input($_POST["comment"]);
    }

    // ---- CREATE OBJECTS -------
    $unkritisch = date("Y-m-d", time() + 1000000);
    $one = date("Y-m-d", time() + 86400);
    $two = date("Y-m-d", time() + 172800);
    $three = date("Y-m-d", time() + 259200);
    $four = date("Y-m-d", time() + 345600);
    $five = date("Y-m-d", time() + 432000);
    $six = date("Y-m-d", time() + 518400);
    $week = date("Y-m-d", time() + 604800);

    $daten = ["", $unkritisch, $one, $two, $three, $four, $five, $six, $week];
    $stufen = ["", "unkritisch", "1 Tag", "2 Tage", "3 Tage", "4 Tage", "5 Tage", "6 Tage", "1 Woche"];
    for ($i = 0; $i < count($stufen); $i++) {
        if ($i == $haltbarkeit) {
            $haltbarkeit = $stufen[$i];
            $VerteilDeadline = $daten[$i];
        }
    }

    // Objekt, dass die Daten für die Übersicht speichert
    $lieferung = (object) [
        'FSkey' => $FSkey,
        'LMkey' => $LMkey,
        'LieferDatum' => date('Y-m-d'),
    ];

    $lebensmittel = (object) [
        'LMkey' => $LMkey,
        'Bezeichnung' => $LMBez,
        'VerteilDeadline' => $VerteilDeadline,
        'Anmerkung' => $comment,
        'Kuehlware' => $kuehlcheck,
        'Gewicht' => $menge,
        'OKatKey' => $OKatKey,
        'Herkunft' => $HerkunftKey,
    ];

    $box = (object) [
        'BoxID' => $kiste,
        'LMkey' => $LMkey,
        'BStatusKey' => 5,
    ];

    $dbeintrag = array($lieferung, $lebensmittel, $box);


    // ----------- add Object to Uebersicht --------
    // Wenn es keine Errors gibt und keine Variablen leer sind, wird das Objekt zur Übersicht übertragen und man wird zur Überischt weitergeleitet
    if (
        empty($lmbezErr) && empty($kisteErr) && empty($mengeErr) && empty($herkunftErr) && empty($kategorieErr) && empty($haltbarkeitErr) &&
        !empty($lebensmittel->OKatKey && $lebensmittel->Bezeichnung && $lebensmittel->Gewicht && $lebensmittel->VerteilDeadline)
    ) {
        $eintrag = (object) [
            'session_id' => session_id(),
            'id' => session_create_id(),
            'Kategorie' => $lebensmittel->OKatKey,
            'Lebensmittel' => $lebensmittel->Bezeichnung,
            'Menge' => $lebensmittel->Gewicht,
            'Kistennr' => $box->BoxID,
            'Kuehlen' => $lebensmittel->Kuehlware,
            'Genießbar' => $haltbarkeit,
            'Herkunft' => $lebensmittel->Herkunft,
            'Allergene' => $allergene,
            'Anmerkungen' => $comment
        ];
        array_push($_SESSION["array"], $eintrag);
        array_push($_SESSION["dbeintragArray"], $dbeintrag);
        $_SESSION["latestLMkey"] = $_SESSION["latestLMkey"] + 1;
        header("Location: ./04_foodsaver_uebersicht.php");
        exit();
    }
}

?>

<!-- --------- HTML --------- -->

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        FAIRSHARE
    </title>
    <!-- Fonts  -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@200;300;400;500;600;700;800;900&family=Londrina+Solid&display=swap"
        rel="stylesheet" />
    <!-- CSS Stylesheet  -->
    <link href="../css/formularstyle.css" rel="stylesheet" />
</head>

<body class="font-fira">
    <div class="container">
        <header>
            <h1 class="font-londrina">
                BOX PACKEN
            </h1>
            <!-- fsHilfe Trigger -->
            <img id="openHelp" src="../media/icon_help.svg" alt="icon_help" />
        </header>
        <div class="content">
            <form id="myform" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="grid-spacing">
                    <div class="grid">
                        <!-- Lebensmittel INPUT -->
                        <div class="grid-col-4">
                            <label class="grid-title">
                                Lebensmittel

                                <span class="error">*
                                    <?php echo $lmbezErr; ?>
                                </span>
                            </label>
                            <input name="LMBez" class="input" type="text" value="<?php echo $LMBez; ?>" />
                        </div>
                        <!-- Kühlen Check? -->
                        <div class="grid-col-2">
                            <div class="kuehlcheck">
                                <div class="check-item">
                                    <input name="kuehlcheck" type="checkbox" value="true">
                                    <img src="../media/checkbox.svg" alt="checkbox" />
                                    <img src="../media/checkbox_checked.svg" alt="checkbox_checked" />
                                    In den Kühlschrank?
                                </div>
                            </div>
                        </div>
                        <!-- Kategorie auswählen -->
                        <div class="grid-col-6">
                            <div class="grid-title">
                                <label>
                                    Kategorie
                                    <span class="error">*
                                        <?php echo $kategorieErr; ?>
                                    </span>
                                </label>
                                <!-- OVERLAY Trigger -->
                                <div>
                                    <img class="close_icon" height="22px" src="../media/overlay_schließen.svg"
                                        alt="icon_help" />
                                    <img class="open_icon hilfeKategorien" height="22px"
                                        src="../media/icon_help_mini.svg" alt="icon_help" />
                                </div>
                                <!-- OVERLAY hilfeKategorien -->
                                <div id="hilfeKategorien">
                                    <div class="outline">
                                        <p>Bei Lebensmitteln verschiedener Kategorien, bitte Sonstiges auswählen.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="category-grid">
                                <?php
                                // LOOP TILL END OF DATA
                                foreach ($kategorien as $key => $row) {

                                    $iconList = array(
                                        $icon_backwaren_salzig_url,
                                        $icon_backwaren_suess_url,
                                        $icon_gemuese_url,
                                        $icon_konserven_url,
                                        $icon_kuehlprodukte_url,
                                        $icon_obst_url,
                                        $icon_sonstiges_url,
                                        $icon_trockenprodukte_url,
                                    );
                                ?>
                                <div class="radio-container kategorie">
                                    <input type="radio" name="OKatKey" value="<?php echo $row['OKatKey'] ?>" <?php if (
                                        isset($OKatKey) && $OKatKey==$row['OKatKey'] ) echo "checked"; ?> >
                                    <div class="category-item">
                                        <?php echo "<img src='" . $iconList[$key] . "'>" ?>
                                        <p>
                                            <?php echo $row['OKatName'] ?>
                                        </p>
                                    </div>
                                </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="grid">
                        <!-- Kistennummer INPUT -->
                        <div class="grid-col-3">
                            <label class="grid-title">
                                Kistennummer
                                <span class="error">*
                                    <?php echo $kisteErr; ?>
                                </span>
                            </label>
                            <input name="kiste" class="input" type="number" value="<?php echo $kiste; ?>" min="0" />
                        </div>
                        <!-- Menge INPUT  -->
                        <div class="grid-col-3">
                            <div class="grid-title">
                                <label>
                                    Menge (in kg)
                                </label>
                                <!-- OVERLAY Trigger-->
                                <div>
                                    <img class="close_icon" height="22px" src="../media/overlay_schließen.svg"
                                        alt="icon_help" />
                                    <img class="open_icon hilfeMenge" height="22px" src="../media/icon_help_mini.svg"
                                        alt="icon_help" />
                                </div>
                                <!-- OVERLAY hilfeMenge -->
                                <div id="hilfeMenge">
                                    <div class="outline">
                                        <p>Unter dem Waschbecken findest Du die Waage.</p>
                                    </div>
                                </div>
                                <span class="error">*
                                    <?php echo $mengeErr; ?>
                                </span>
                            </div>
                            <input name="menge" class="input" type="number" step="0.1" value="<?php echo $menge; ?>"
                                min="0" />
                        </div>
                        <!-- Haltbarkeit INPUT -->
                        <div class="grid-col-6">
                            <label class="grid-title">
                                Wie lange genießbar?
                                <span class="error">*
                                    <?php echo $haltbarkeitErr; ?>
                                </span>
                            </label>
                            <div class="rangeslider">
                                <img height="6px" src="../media/range-thumb-left.svg" alt="range-thumb">
                                <input id="input" type="range" min="1" max="8" value="0" name="haltbarkeit"
                                    class="slider">
                                <img height="6px" src="../media/range-thumb-right.svg" alt="range-thumb">
                            </div>
                            <div class="output" id="output"></div>
                            <script>
                                var values = ["", "unkritisch", "1 Tag", "2 Tage", "3 Tage", "4 Tage", "5 Tage", "6 Tage", "1 Woche"];

                                var input = document.getElementById('input'),
                                    output = document.getElementById('output');
                                input.oninput = function () {
                                    output.innerHTML = values[this.value];
                                };
                                input.oninput();
                            </script>
                        </div>
                        <!-- Herkunft INPUT -->
                        <div class="grid-col-6">
                            <label class="grid-title">
                                Wo gerettet?
                                <span class="error">*
                                    <?php echo $herkunftErr; ?>
                                </span>
                            </label>
                            <div class="haltbarkeit-grid">
                                <?php
                                // LOOP TILL END OF DATA
                                foreach ($herkunftkategorien as $key => $row) {
                                ?>
                                <div class="radio-container haltbarkeit">
                                    <input type="radio" name="HerkunftKey" value="<?php echo $row['HerkunftKey'] ?>"
                                        <?php if (isset($HerkunftKey) && $HerkunftKey==$row['HerkunftKey']) echo
                                        "checked"; ?>>
                                    <div class="haltbarkeit-item">
                                        <p>
                                            <?php echo $row['HerkunftName'] ?>
                                        </p>
                                    </div>
                                </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                        <!-- Allergene INPUT  -->
                        <div class="grid-col-6">
                            <div class="grid-title">
                                <label>
                                    Allergene und Inhaltsstoffe
                                </label>
                                <!-- OVERLAY Trigger-->
                                <div>
                                    <img class="close_icon" height="22px" src="../media/overlay_schließen.svg"
                                        alt="icon_help" />
                                    <img class="open_icon hilfeAllergene" height="22px"
                                        src="../media/icon_help_mini.svg" alt="icon_help" />
                                </div>
                                <!-- OVERLAY hilfeAllergene -->
                                <div id="hilfeAllergene">
                                    <div class="outline">
                                        <p>Bei Lebensmitteln mit Allergenen oder anderen kritischen Inhaltsstoffen,
                                            diese bitte angeben.</p>
                                    </div>
                                </div>
                            </div>
                            <input name="allergene" class="input" type="text" value="<?php echo $allergene; ?>" />
                        </div>
                        <!-- Anmerkungen INPUT -->
                        <div class="grid-col-6">
                            <label class="grid-title">
                                Anmerkungen
                            </label>
                            <textarea name="comment" rows="1" class="input"><?php echo $comment; ?></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="action-container">
            <!-- OVERLAY Trigger Nicht erlaubte Lebensmittel -->
            <div id="openNichtErlaubteLm">
                <img src="../media/icon_help_mini.svg" alt="icon_help" />
                <p>Nicht erlaubte Lebensmittel</p>
            </div>
            <div class="action-wrap">
                <!-- SENDEN des Formulars und WEITERLEITUNG zur Foodsaver Übersicht -->
                <a id="openHinzufuegenAbbr">Abbrechen</a>
                <input class="continue-button" type="submit" form="myform" value="Hinzufügen">
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

    <!-- OVERLAY fsNichtErlaubteLm -->
    <div id="fsNichtErlaubteLm">
        <div class="nichtErlaubteLebensmittel">
            <div class="help-popup">
                <img src="../media/help_grey.svg" alt="help_grey_icon">
                <h3 class="popupheader header-help">NICHT ERLAUBTE LEBENSMITTEL</h3>
            </div>
            <p class="textpopup">Folgende Lebensmittel dürfen wir aus hygienetechnischen Gründen nicht annehmen:</p>
            <ul class="listpopup">
                <li>
                    Brühwurstprodukte nach Ablauf des MHDs (z.B. Wurststreifen für Salat, Wiener, Mortadella, Lyoner,
                    Leberkäse)
                </li>
                <li>
                    Keine Lebensmittel mit Verbrauchsdatum
                </li>
                <li>
                    Keine rohen Eier oder Speisen mit rohen Eiern (z.B. Mousse au Chocolat)
                </li>
                <li>
                    Kein gekochter Reis
                </li>
                <li>
                    Keine selbstzubereiteten Speisen
                </li>
                <li>
                    Keine Produkte aus nicht erhitzter Rohmilch
                </li>
            </ul>
            <div class="buttoncenter">
                <a class="allesklarButton" href="">
                    <h5>Alles klar</h5>
                </a>
            </div>
        </div>
    </div>

    <!-- OVERLAY fsHinzufuegenAbbr -->
    <div id="fsHinzufuegenAbbr">
        <div class="popupklein">
            <h3 class="popupheader">Zurück zur Übersicht</h3>
            <p class="textpopup">Deine Angaben werden nicht gespeichert.
                <br />Bist du sicher, dass du zurück zur Übersicht willst?
            </p>
            <div class="button-spacing-popup">
                <a class="exitButton" href="">
                    <h5>Nein, doch nicht</h5>
                </a>
                <a class="nextButton" href="../index.php">
                    <h5>Ja, zur Übersicht</h5>
                </a>
            </div>
        </div>
    </div>

    <!-- Script Overlays -->
    <?php
    echo '<script type="text/javascript" src="../script/03.js">
        </script>
        ';
    ?>
</body>

</html>