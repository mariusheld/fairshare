<!-- --------- PHP --------- -->

<?php
// TEMPORÄR ABGESCHALTET
// require_once("../scripts/dbcontroller.php");
// $db_handle = new DBController();

session_start();

// ----------- VARIABLES ----------

$LMkey = session_create_id();
$LMBez = "";
$LKkey = 0;
$kuehlcheck = false;
$kiste = null;
$menge = null;
$herkunft = "";
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

// TEMPORÄR ABGESCHALTET
// $kategorien = $db_handle->runQuery("SELECT * FROM `lkategorie`");
$kategorien = array();

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

    if(isset($_POST['kuehlcheck']) && 
        $_POST['kuehlcheck'] == 'true') 
    {
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

    if (empty($_POST["herkunft"])) {
        $herkunftErr = "Erforderlich";
    } else {
        $herkunft = test_input($_POST["herkunft"]);
    }

    // TEMPORÄR ABGESCHALTET
    // if (empty($_POST["LKkey"])) {
    //     $kategorieErr = "Erforderlich";
    // } else {
    //     $LKkey = test_input($_POST["LKkey"]);
    // }

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

    $stufen = ["","unkritisch","1 Tag","2 Tage","3 Tage","4 Tage","5 Tage","6 Tage", "1 Woche"];
    for($i = 0;$i < count($stufen);$i++) {
        if ($i == $haltbarkeit) {
            $haltbarkeit = $stufen[$i];
        }
    }

    // Objekt, dass die Daten für die Übersicht speichert
    $lebensmittel = (object) [
        // 'session_id' => session_id(),
        'LMkey' => $LMkey,
        'Bezeichnung' => $LMBez,
        'VerteilDeadline' => $haltbarkeit,
        'Anmerkung' => $comment,
        'Kuehlware'  => $kuehlcheck,
        'Gewicht' => $menge,
        'Herkunft' => $herkunft,
        // 'OKatKey' => $LKkey,
    ];

    $box = (object) [
        'session_id' => session_id(),
        'BoxID' => $kiste,
        'LMkey' => $LMkey,
    ];


    // ----------- add Object to Uebersicht --------
    // Wenn es keine Errors gibt und keine Variablen leer sind, wird das Objekt zur Übersicht übertragen und man wird zur Überischt weitergeleitet
    if (
        empty($lmbezErr) && empty($kisteErr) && empty($mengeErr) && empty($herkunftErr) && empty($kategorieErr) && empty($haltbarkeitErr) &&
         !empty( /*$lebensmittel->OKatKey && */ $lebensmittel->Bezeichnung && $lebensmittel->Gewicht && $lebensmittel->VerteilDeadline)
    ) {
        $eintrag = (object) [
            'session_id' => session_id(),
            'id' => session_create_id(),
            // 'Kategorie' => $lebensmittel->OKatKey,
            'Lebensmittel' => $lebensmittel->Bezeichnung,
            'Menge' => $lebensmittel->Gewicht,
            'Kistennr' => $box->BoxID,
            'Kuehlen' => $lebensmittel->Kuehlware, 
            'Genießbar' => $lebensmittel->VerteilDeadline,
            'Allergene' => $allergene,
            'Anmerkungen' => $comment
        ];
        array_push($_SESSION["array"], $eintrag);
        array_push($_SESSION["dbeintrag"], $lebensmittel);
        header("Location: ./03_foodsaver_uebersicht.php");
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
    <title>Foodsaver Lebensmittel Hinzufügen</title>
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
            <a>
                <h1 id="devlink" class="font-londrina">
                    BOX PACKEN
                </h1>
            </a>
            <!-- OVERLAY -->
            <a>
      			<img id="openHelp" src="../media/icon_help.svg" alt="icon_help"/>
			</a>
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
            		<a class="allesklarButton" href=""><h5>Alles klar</h5></a>
        		</div>
    		</div>
    	</div>
        
    <!-- Script Overlay fs-help -->
    <?php
    echo '<script>
    
        console.log("Hello world!");
        //Overlay auswählen
        var fsHilfe = document.getElementById("fsHilfe");
        
        //Das öffnet das Overlay
        var openHelp = document.getElementById("openHelp");
        
        //Das schließt das Overlay
        var exitHilfe = document.getElementsByClassName("allesklarButton")[0];
        
        //Öffnen wenn icon geklickt wird
        openHelp.onclick = function() {
            fsHilfe.style.display = "block";
        }
        
        //Schließen nach Button drücken
            exitHilfe.onclick = function() {
            fsHilfe.style.display = "none";
        }
        </script>
        ';    
    ?>
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
                                <!-- OVERLAY -->
                                <a href="/raupeimmersatt">
                                    <img height="22px" src="../media/icon_help_mini.svg" alt="icon_help" />
                                </a>
                            </div>
                            <div class="category-grid">
                                <?php
                                // LOOP TILL END OF DATA
                                foreach ($kategorien as $key => $row ) {
                                    
                                    $iconList = array(
                                        $icon_obst_url,
                                        $icon_gemuese_url,
                                        $icon_backwaren_suess_url,
                                        $icon_backwaren_salzig_url,
                                        $icon_kuehlprodukte_url,
                                        $icon_trockenprodukte_url,
                                        $icon_konserven_url,
                                        $icon_sonstiges_url
                                    );
                                ?>
                                <div class="radio-container kategorie">
                                    <input type="radio" name="LKkey" value="<?php echo $row['LKkey'] ?>" <?php if (
                                        isset($LKkey) && $LKkey==$row['LKkey'] ) echo "checked"; ?> >
                                    <div class="category-item">
                                        <?php echo "<img src='". $iconList[$key] . "'>" ?>
                                        <p>
                                            <?php echo $row['KatName'] ?>
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
                                <!-- OVERLAY -->
                                <a href="/raupeimmersatt">
                                    <img height="22px" src="../media/icon_help_mini.svg" alt="icon_help" />
                                </a>
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
                            <div class="grid-title">
                                <label>
                                    Wo gerettet?
                                </label>
                                <!-- OVERLAY -->
                                <a href="/raupeimmersatt">
                                    <img height="22px" src="../media/icon_help_mini.svg" alt="icon_help" />
                                </a>
                                <span class="error">*
                                    <?php echo $herkunftErr; ?>
                                </span>
                            </div>
                            <input name="herkunft" class="input" type="text" value="<?php echo $herkunft; ?>" />
                        </div>
                        <!-- Allergene INPUT  -->
                        <div class="grid-col-6">
                            <div class="grid-title">
                                <label>
                                    Allergene und Inhaltsstoffe
                                </label>
                                <!-- OVERLAY -->
                                <a href="/raupeimmersatt">
                                    <img height="22px" src="../media/icon_help_mini.svg" alt="icon_help" />
                                </a>
                            </div>
                            <input name="allergene" class="input" type="text" value="<?php echo $allergene; ?>" />
                        </div>
                        <!-- Anmerkungen INPUT -->
                        <div class="grid-col-6">
                            <label class="grid-title">
                                Anmerkungen
                            </label>
                            <textarea name="comment" rows="2" class="input"><?php echo $comment; ?></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="action-container">
            <div>
                <!-- OVERLAY -->
                <a href="/raupeimmersatt"> <img src="../media/icon_help_mini.svg" alt="icon_help" /></a>
                <p>Nicht erlaubte Lebensmittel</p>
            </div>
            <div class="action-wrap">
                <!-- NICHT ÄNDERN -->
                <a href="./01_foodsaver_start.php">Abbrechen</a>
                <input class="next-button" type="submit" form="myform" value="Hinzufügen">
            </div>
        </div>
    </div>
</body>
  <?php
    echo "<script>
                    //DevLink um Formular zu überspringen
                    document.getElementById('devlink').onclick = function() {
                        window.location.href = '03_foodsaver_uebersicht.php';
                    }                
             </script>"
  ?>
</html>