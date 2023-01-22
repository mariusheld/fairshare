<?php
require_once("../dbconnect/dbconnect.inc.php");

if (isset($_GET['mfwArrayFairteilen'])) {
  $myArrayString = $_GET['mfwArrayFairteilen'];
  $myArrayObjects = explode("|", $myArrayString);

  foreach ($myArrayObjects as $key => $value) {
    if ($value != "") {
      $myArray = explode(",", $value);
      $lmkey = $myArray[0];
      $bewegMenge = $myArray[1];
      $LStatusKey = 2;
      $EntsorgenQuery = $db->prepare("INSERT INTO `Bestand_Bewegung` (`LMkey`, `LStatusKey`, `BewegDatum`, `BewegMenge`, `EntsorgGrund`)
        VALUES ('$lmkey', '$LStatusKey', now(), '$bewegMenge', NULL)");
      $erfolg = $EntsorgenQuery->execute();
      // Fehlertest
      if (!$erfolg) {
        $fehler = $query->errorInfo();
        die("Folgender Datenbankfehler ist aufgetreten:" . $fehler[2]);
      }
    }
  }
  header("Location: admin.php?success=true");
}

if (isset($_GET['mfwArrayEntsorgen'])) {
  $myArrayString = $_GET['mfwArrayEntsorgen'];
  $myArrayObjects = explode("|", $myArrayString);

  foreach ($myArrayObjects as $key => $value) {
    if ($value != "") {
      $myArray = explode(",", $value);
      $lmkey = $myArray[0];
      $bewegMenge = $myArray[1];
      $LStatusKey = 3;
      $EntsorgenQuery = $db->prepare("INSERT INTO `Bestand_Bewegung` (`LMkey`, `LStatusKey`, `BewegDatum`, `BewegMenge`, `EntsorgGrund`)
        VALUES ('$lmkey', '$LStatusKey', now(), '$bewegMenge', NULL)");

      $erfolg = $EntsorgenQuery->execute();
      // Fehlertest
      if (!$erfolg) {
        $fehler = $query->errorInfo();
        die("Folgender Datenbankfehler ist aufgetreten:" . $fehler[2]);
      }
    }
  }
  header("Location: admin.php?success=true");
}

?>