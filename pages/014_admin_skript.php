<?php
require_once("../dbconnect/dbconnect.inc.php");

//Abfrage an Datenbank senden
if (isset($_POST["entsorgen-menge"])) {
  $lmkey = $_POST["lmkey"];
  $bewegMenge = $_POST["entsorgen-menge"];
  $LStatusKey = 3;
  $EntsorgenQuery = $db->prepare("INSERT INTO `Bestand_Bewegung` (`LMkey`, `LStatusKey`, `BewegDatum`, `BewegMenge`, `EntsorgGrund`)
    VALUES ('$lmkey', '$LStatusKey', now(), '$bewegMenge', NULL)");
  $erfolg = $EntsorgenQuery->execute();
  // Fehlertest
  if (!$erfolg) {
    $fehler = $query->errorInfo();
    die("Folgender Datenbankfehler ist aufgetreten:" . $fehler[2]);
  }
  // Seite neu ladens
  header("Location: admin.php");
}

if (isset($_POST["fairteil-menge"])) {
  $lmkey = $_POST["lmkey"];
  $bewegMenge = $_POST["fairteil-menge"];
  $LStatusKey = 2;
  $EntsorgenQuery = $db->prepare("INSERT INTO `Bestand_Bewegung` (`LMkey`, `LStatusKey`, `BewegDatum`, `BewegMenge`, `EntsorgGrund`)
    VALUES ('$lmkey', '$LStatusKey', now(), '$bewegMenge', NULL)");
  $erfolg = $EntsorgenQuery->execute();
  // Fehlertest
  if (!$erfolg) {
    $fehler = $query->errorInfo();
    die("Folgender Datenbankfehler ist aufgetreten:" . $fehler[2]);
  }
  // Seite neu laden
  
  header("Location: admin.php?fairteilt=" . $_GET['id'] . "&menge=" . $_POST["fairteil-menge"]);
}
?>