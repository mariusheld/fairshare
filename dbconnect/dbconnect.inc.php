<?php
// Datenbank Zugangsdaten
$DSN      = "mysql:host=localhost;dbname=u-ProjRaupe";
$benutzer = "ProjRaupe";
$passwort = "yooGheohu9";
$optionen = array();
// Verbindungsaufbau
$db       = new PDO($DSN, $benutzer, $passwort, $optionen);
?>
