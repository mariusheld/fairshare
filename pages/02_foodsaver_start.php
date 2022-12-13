<?php
// --- CREATE SESSION --- 
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
// Session Arrays initialisieren 
$_SESSION["kategorien"] = array();
$_SESSION["array"] = array();
$_SESSION["dbeintrag"] = array();
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
          <a href="../index.php">Abbrechen</a>
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
          console.log("Button geklickt");
            fsHilfe.style.display = "block";
        }
        
        //Schließen nach Button drücken
            exitHilfe.onclick = function() {
              console.log("Button geklickt");
            fsHilfe.style.display = "none";
        }
        </script>
        ';
  ?>

</body>

</html>