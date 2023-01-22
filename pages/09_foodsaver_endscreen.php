<?php
// Session initialisieren, damit php erkennt welche Session im nächsten Schritt gemeint ist
session_start();

//--------------Gerettete LM für aktuellen FS auslesen-------------//
//Datenbankverbindung aufbauen
require_once("../dbconnect/dbconnect.inc.php");

//FSkey aus session holen
$FSkey = $_SESSION['FSkey'];

//SQL Abfrage vorbereiten
$select_FS_LMGewicht = $db->prepare("SELECT SUM(Gewicht) FROM Lieferung LEFT JOIN Lebensmittel ON Lieferung.LMKey=Lebensmittel.LMKey WHERE FSKey = :FSKey");

//FSkey in das Array eintragen
$daten_FS_LMGewicht = array(
    "FSKey" => $FSkey,
);
//SQL Abfrage ausführen
$select_FS_LMGewicht->execute($daten_FS_LMGewicht);
$gewicht = $select_FS_LMGewicht->fetchColumn();

// Session wird zerstört und resettet
session_destroy();
?>

<!DOCTYPE html>
<html lang=de>

<head>
    <meta charset="UTF-8" />
    <title>
        FAIRSHARE
    </title>
    <link rel="icon" type="image/x-icon" href="../favicon/favicon.ico">
    <link rel="stylesheet" href="../css/startendstyle.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans&family=Londrina+Solid&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body id="endscreen">
    <div id="gif">
        <img src="../media/startscreen.gif" alt="Wackelnde Lebensmittel GIF" class="gif">
    </div>
    <div id="endscreen-header">
        <img id="logo" src="../media/logo.svg" alt="fairshare" height="50px">
    </div>

    <div id="endscreen-content">
        <div>
            <h1 id="endscreen-title">
                <?php echo $gewicht; ?> KILOGRAMM
            </h1>
            <p id="endscreen-text">Lebensmittel hast du bisher für die Raupe gerettet!</p>
            <p id="endscreen-text-danke">Danke für deinen Beitrag!</p>
            <button id="endscreen-button">Beenden</button>
        </div>
    </div>

    <div id="endscreen-footer">
        <p id="endscreen-footer-text">Raupe Immersatt, Stuttgart 2023</p>
    </div>
    <script>
        //Weiterleitung auf Startscreen
        document.getElementById('endscreen-button').onclick = function () {
            window.location.href = '../index.php'
        }
    </script>
</body>

</html>