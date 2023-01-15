<!-- ----------- PHP ---------- -->
<?php
session_start();
ob_start();
// Datenbankverbindung aufbauen
require_once("../dbconnect/dbconnect.inc.php");
$db_handle = new DBController();
$conn = $db_handle->connectDB();

if ($_SESSION["foodsaverLogin"] == false) {
  session_destroy();
  header("Location: ../index.php");
}

// Session Objekt wird lokal gespeichert
$array = json_decode(json_encode($_SESSION["array"]), true);
$dbeintragArray = json_decode(json_encode($_SESSION["dbeintragArray"]), true);

function consolelog($data, bool $quotes = false)
{
  $output = json_encode($data);
  if ($quotes) {
    echo "<script>console.log('{$output}' );</script>";
  } else {
    echo "<script>console.log({$output} );</script>";
  }
}

// Vollständiger Array mit allen Einträgen wird an die Datenbank übertragen. -------------------
function sendList($dbeintragArray, $conn)
{
  mysqli_select_db($conn, "u-projraupe");
  foreach ($dbeintragArray as $dbEintrag) {
    // LIEFERUNG QUERY
    $lieferungEintrag = $dbEintrag[0];

    $FSkey = $lieferungEintrag['FSkey'];
    $LMkeyLieferung = $lieferungEintrag['LMkey'];
    $LieferDatum = $lieferungEintrag['LieferDatum'];

    $lieferungQuery = "INSERT INTO `Lieferung` (`FSkey`, `LMkey`, `LieferDatum`) VALUES ('$FSkey', '$LMkeyLieferung', '$LieferDatum')";
    mysqli_query($conn, $lieferungQuery);

    // LEBENSMITTEL QUERY
    $lebensmittelEintrag = $dbEintrag[1];

    $LMkey = $lebensmittelEintrag['LMkey'];
    $Bezeichnung = $lebensmittelEintrag['Bezeichnung'];
    $VerteilDeadline = $lebensmittelEintrag['VerteilDeadline'];
    $Anmerkung = $lebensmittelEintrag['Anmerkung'];
    $Kuehlware = $lebensmittelEintrag['Kuehlware'];
    $Gewicht = $lebensmittelEintrag['Gewicht'];
    $Betrieb = $lebensmittelEintrag['Betrieb'];
    $OKatKey = $lebensmittelEintrag['OKatKey'];
    $HerkunftKey = $lebensmittelEintrag['Herkunft'];

    $lebensmittelQuery = "INSERT INTO `Lebensmittel` (`LMkey`, `Bezeichnung`, `VerteilDeadline`, `Anmerkung`, `Kuehlware`, `Gewicht`, `Betrieb`, `OKatKey`, `HerkunftKey`) VALUES ('$LMkey', '$Bezeichnung', '$VerteilDeadline', '$Anmerkung', '$Kuehlware', '$Gewicht', '$Betrieb', '$OKatKey', '$HerkunftKey')";
    mysqli_query($conn, $lebensmittelQuery);

    // Bestand_Bewegung QUERY
    $LMkeyBestandBewegung = $lieferungEintrag['LMkey'];
    $LStatusKey = 1;
    $BewegDatum = date("Y-m-d h:i:sa");
    $BewegMenge = $lebensmittelEintrag['Gewicht'];

    $BestandBewegungQuery = "INSERT INTO `Bestand_Bewegung` (`LMkey`, `LStatusKey`, `BewegDatum`, `BewegMenge`) VALUES ('$LMkeyBestandBewegung', '$LStatusKey', '$BewegDatum', '$BewegMenge')";
    mysqli_query($conn, $BestandBewegungQuery);
  }
}

// Datenbank Query Trigger
if (isset($_GET['send'])) {
  sendList($dbeintragArray, $conn);
  header("Location: ./05_foodsaver_finalcheck.php");
}

if (isset($_GET['key'])) {
  unset($_SESSION["array"][$_GET['key']]);
  unset($_SESSION["dbeintragArray"][$_GET['key']]);
  header("Location: ./04_foodsaver_uebersicht.php");
}

// ---- KategorieIcons 
$icon_backwaren_salzig_url = '../media/kategorien/icon_backwaren-salzig.svg';
$icon_backwaren_suess_url = '../media/kategorien/icon_backwaren-suess.svg';
$icon_gemuese_url = '../media/kategorien/icon_gemuese.svg';
$icon_konserven_url = '../media/kategorien/icon_konserven.svg';
$icon_kuehlprodukte_url = '../media/kategorien/icon_kuehlprodukte.svg';
$icon_obst_url = '../media/kategorien/icon_obst.svg';
$icon_trockenprodukte_url = '../media/kategorien/icon_trockenprodukte.svg';
$icon_sonstiges_url = '../media/kategorien/sonstiges.svg';

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
  <!-- CSS Stylesheet  -->
  <link href="../css/formularstyle.css" rel="stylesheet" />
</head>

<body class="font-fira">
  <div id="grauer-hintergrund"></div>
  <div class="container">
    <header>
      <img src="../media/logo.svg" alt="logo" />
      <!-- fsHilfe Trigger -->
      <img id="openHelp" src="../media/icon_help.svg" alt="icon_help" />
    </header>
    <div class="content">
      <div class="wrap">
        <!-- Tabelle -->
        <table class="table">
          <tr class="table-headers">
            <th class="grid-col-6">Lebensmittel</th>
            <th class="grid-col-2">Menge</th>
            <th class="grid-col-3">Genießbar</th>
            <th class="grid-col-1"></th>
          </tr>
          <?php
          // Tabelleneintrag
          // LOOP TILL END OF DATA
          foreach ($array as $key => $row) {
            ?>
            <tr class="table-items" id="<?php echo $array[$key]['id'] ?>">
              <td class="grid-col-6">
                <?php

                $iconList = array(
                  "0",
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
                <div class="flex">
                  <?php echo "<img src='" . $iconList[$row['Kategorie']] . "'>" ?>
                  <p>
                    <?php echo $row['Lebensmittel'] ?>
                  </p>
                </div>
              </td>
              <td class="grid-col-2">
                <?php echo $row['Menge'] ?> Kg
              </td>
              <td class="grid-col-3">
                <div class="flex">
                  <?php echo $row['Genießbar'] ?>
                  <?php if ($row['Kuehlen'] == true)
                    echo "<img src='../media/freeze_icon.svg' alt='freeze_icon' />"; ?>
                </div>
              </td>
              <td class="grid-col-1">
                <div class="interaktion-buttons">
                  <!-- OVERLAY TRIGGER -->
                  <?php
                  if ($array[$key]['Anmerkungen'] == true || $array[$key]['Allergene'] == true) { ?>
                    <img src='../media/comment_icon.svg' alt='comment_icon'
                      id="anmerkungButton:<?php echo $array[$key]['id'] ?>"
                      onClick='changeAnmerkung("<?php echo $array[$key]['id'] ?>")' class="open_icon" />
                    <?php
                  }
                  ?>
                  <!-- OVERLAY TRIGGER -->
                  <img src="../media/edit_icon.svg" alt="edit_icon" id="editButton:<?php echo $array[$key]['id'] ?>"
                    onClick="changeBearbeiten('<?php echo $array[$key]['id'] ?>')" class="open_icon" />

                  <!-- Overlay fs-anmerkung-allergene -->
                  <div id="overlay:<?php echo $array[$key]['id'] ?>" class="fs-uebersicht-anmerkungen popup">
                    <div class="popup-anmerkung">
                      <?php if ($row['Anmerkungen'] == true) {
                        echo "<h5>Anmerkung:</h5>";
                        echo "<p>" . $array[$key]['Anmerkungen'] . "</p>";
                      }
                      ;
                      ?>
                      <?php if ($row['Allergene'] == true) {
                        echo "<h5 class='header2'>Allergene und Inhaltsstoffe:</h5>";
                        echo "<p>" . $array[$key]['Allergene'] . "</p>";
                      }
                      ;
                      ?>
                    </div>
                  </div>

                  <!-- Overlay fs-lm-optionen -->
                  <div id="overlayBearbeiten:<?php echo $array[$key]['id'] ?>" class="fs-uebersicht-bearbeiten popup">
                    <div class="popup-uebersicht-bearbeiten">
                      <a class="bearbeiten" href="03_foodsaver_hinzufuegen.php?editieren=<?php echo $key; ?> ">
                        <img src="../media/bearbeiten.svg" alt="Stift zum Bearbeiten">
                        <h5>Bearbeiten</h5>
                      </a>
                      <div class="trennlinie">
                        <img src="../media/trennlinie.svg" alt="Trennlinie">
                      </div>
                      <div class="loeschen" onClick="openLoeschen('<?php echo $array[$key]['id'] ?>')">
                        <img src="../media/loeschen.svg" alt="Müllerimer zum Löschen">
                        <h5>Löschen</h5>
                      </div>
                    </div>
                  </div>

                  <!-- Overlay fs-lm-loeschen -->
                  <div id="overlayLoeschen:<?php echo $array[$key]['id'] ?>" class="fs-uebersicht-loeschen">
                    <div class="popup-lebensmittel-löschen">
                      <div class="lebensmittel-zum-löschen-popup">
                        <?php echo "<img src='" . $iconList[$row['Kategorie']] . "'>" ?>
                        <h5 class="popupheaderklein">
                          <?php echo $array[$key]['Lebensmittel'] ?>
                        </h5>
                      </div>
                      <p class="textpopup">
                        <?php echo $key ?>
                        Möchtest Du das ausgewählte Lebensmittel wirklich aus der Liste löschen?
                      </p>
                      <div class="button-spacing-popup">
                        <a class="exitButton" href="">
                          <h5>Abbrechen</h5>
                        </a>
                        <a class="deleteButton" href="04_foodsaver_uebersicht.php?key=<?php echo $key ?>">
                          <h5>Löschen</h5>
                        </a>
                      </div>
                    </div>
                  </div>

                </div>
              </td>
            </tr>
            <script>
              let key = <?php echo json_encode($key); ?>;
              let array = <?php echo json_encode($array); ?>;
            </script>
            <?php
          }
          ?>
        </table>
        <div class="add-icon">
          <!-- WEITERLEITUNG zur 03_foodsaver_hinzufuegen Seite, um ein neues Element anzulegen -->
          <a href="03_foodsaver_hinzufuegen.php">
            <img src="../media/add_icon.svg" alt="add_icon" />
          </a>
        </div>
      </div>
    </div>
    <div class="action-container">
      <!-- OVERLAY Trigger Lebensmittel verstauen, id im div-tag setzen ist besser -->
      <div id="openLmVerstauen">
        <img src="../media/icon_help_mini.svg" alt="icon_help" />
        <p>Verstauen von Lebensmitteln</p>
      </div>
      <div class="action-wrap">
        <!-- OVERLAY Trigger Hinzufügen Abbrechen -->
        <a id="openUebersichtAbbr">Abbrechen</a>
        <!-- Datenbank schreiben und WEITERLEITUNG zum Final Check -->
        <a href='04_foodsaver_uebersicht.php?send=true' class="continue-button">Abschließen</a>
      </div>
    </div>
  </div>

  <!-- ----------------- ALLE OVERLAYS ---------------- -->
  <!-- OVERLAY fsHilfe -->
  <div id="fsHilfe">
    <div class="fs-hilfe">
      <h3 class="popupheader">LEBENSMITTEL RICHTIG ABGEBEN</h3>
      <div class="schrittliste-popup">
        <div>
          <ul class="listpopupHilfe">
            <img src="../media/1-hygiene.svg" class="icon-help-popup">
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
            <img src="../media/2-boxen.svg" class="icon-help-popup">
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
            <img src="../media/3-vorbereitung.svg" class="icon-help-popup">
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
            <img src="../media/4-verpacken.svg" class="icon-help-popup">
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
        <a class="allesklarButton">
          <h5>Alles klar</h5>
        </a>
      </div>
    </div>
  </div>

  <!-- Overlay fs-uebersicht-abbr -->
  <div id="fsUebersichtAbbr">
    <div class="popupklein">
      <h3 class="popupheader">Zurück zur STARTSEITE</h3>
      <p class="textpopup">Deine Angaben werden nicht gespeichert.
        <br />Bist du sicher, dass du zurück zur Startseite willst?
      </p>
      <div class="button-spacing-popup">
        <a class="exitButton" id="exit-uebersicht">
          <h5>Nein, doch nicht</h5>
        </a>
        <a class="nextButton" href="../index.php">
          <h5>Ja, zur Startseite</h5>
        </a>
      </div>
    </div>
  </div>

  <!-- Overlay fs-lm-verstauen -->
  <div id="fsLmVerstauen">
    <div class="nichtErlaubteLebensmittel">
      <div class="help-popup">
        <img src="../media/help_grey.svg">
        <h3 class="popupheader header-help">LEBENSMITTEL verstauen</h3>
      </div>
      <p class="textpopup">Bitte beachte Folgendes beim Verstauen der Lebensmittel:</p>
      <ul class="listpopup">
        <li>
          Lebensmittel in Fairteiler-Box(en) nach Sorten und Lagerbedarf sortieren (gekühlt oder ungekühlt)
        </li>
        <li>
          Deckel aus dem Regal über dir holen, Box(en) verschließen & Box(en) in Regale und Kühlschränke (Check: Kühlschrank eingesteckt und an?) einräumen.
        </li>
      </ul>
      <div class="buttoncenter">
        <a class="allesklarButton">
          <h5>Alles klar</h5>
        </a>
      </div>
    </div>
  </div>

  <!-- Script Overlays -->
  <?php
  echo '<script type="text/javascript" src="../script/04.js">
        </script>
        ';
  ?>
  <script>
    //Öffnen wenn icon geklickt wird
    for (let item of document.getElementsByClassName("allesklarButton")) {
      item.onclick = function (event) {
        document.getElementById("fsHilfe").style.display = "none";
        document.getElementById("fsLmVerstauen").style.display = "none";
      };
    }
    document.getElementById("exit-uebersicht").onclick = function () {
      document.getElementById("fsUebersichtAbbr").style.display = "none";
    };
  </script>
</body>

</html>

<?php
ob_end_flush();
// Output Buffering: You can also solve this issue by using output buffering. 
// You can do this by adding ob_start(); at the top of your script and ob_end_flush(); 
// at the bottom of your script. This will buffer the output and send it to the browser only when your script is finished executing.
?>