<?php
// --- CREATE SESSION --- 
session_start();

// Session Arrays initialisieren
$_SESSION["array"] = array();
$_SESSION["dbeintragArray"] = array();
$_SESSION["kuehlcheck"] = 0;

// --- DATENBANKANBINDUNG FOODSAVER ANMELDUNG ---
// Datenbank verbinden
require_once("../dbconnect/dbconnect.inc.php");
$db_handle = new DBController();
$conn = $db_handle->connectDB();

// Letzten LMkey aus der Datenbank bekommen
$LMkeyQuery = "SELECT LMkey FROM Lebensmittel ORDER BY LMkey DESC LIMIT 1";
$LMkeyResult = mysqli_query($conn, $LMkeyQuery);
while ($row = mysqli_fetch_assoc($LMkeyResult)) {
  $key[] = $row;
  $_SESSION["latestLMkey"] = $key[0]['LMkey'];
}

if (!empty($_SESSION['vorname'])) {
  //Variablen aus POST holen
  $_SESSION["foodsaverLogin"] = true;
} else {
  header("Location: ../index.php");
}

//Konsolen Kontrolle ob POST liefert
// echo "<script>console.log('{$_SESSION["vorname"]}', '{$_SESSION["nachname"]}', '{$_SESSION["foodID"]}', '{$_SESSION["email"]}', '{$_SESSION["tel"]}');</script>";

///Insert in die Datenbank
echo "<script>console.log(" . json_encode($_SESSION["bekannt"]) . ");</script>";
if ($_SESSION["bekannt"] == "0"){
$eintragFS = $db->prepare("INSERT INTO Foodsaver (FoodsharingID, Vorname, Nachname, TelNr, Email)
VALUES (?, ?, ?, ?, ?)"); //$foodID, $vorname, $nachname, $tel, $email
$eintragFS->execute(array($_SESSION["foodID"], $_SESSION["vorname"], $_SESSION["nachname"], $_SESSION["tel"], $_SESSION["email"]));
unset($_SESSION["bekannt"]);
}
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
      <img src="../media/background.svg" alt="background_image" class="background"></img>
      <div class="wrap-title">
        <h1 class="title font-londrina">
          WELCHE LEBENSMITTEL <br />
          HAST DU GERETTET?
        </h1>
        <!-- WEITERLEITUNG zur 03_foodsaver_hinzufuegen Seite -->
        <a href="../pages/03_foodsaver_hinzufuegen.php">
          <img src="../media/add_icon.svg" alt="add_icon" />
        </a>
      </div>
      <div class="cancel-on-start">
        <div class="action-wrap font-fira">
          <!-- WEITERLEITUNG zum Startscreen -->
          <a id="openUebersichtAbbr">Abbrechen</a>
        </div>
      </div>
    </div>
  </div>

  <!-- ------------------ ALLE OVERLAYS ------------------ -->

  <!-- Overlay fs-hilfe -->
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
        <a class="exitButton">
          <h5>Nein, doch nicht</h5>
        </a>
        <a class="nextButton" href="../index.php">
          <h5>Ja, zur Startseite</h5>
        </a>
      </div>
    </div>
  </div>


  <!-- Script Overlays -->
  <?php
  echo '<script type="text/javascript" src="../script/02.js">
        </script>
        ';
  ?>
</body>

</html>