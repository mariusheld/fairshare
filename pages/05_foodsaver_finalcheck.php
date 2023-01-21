<?php
// --- CREATE SESSION --- 
session_start();
if ($_SESSION["foodsaverLogin"] == false) {
    session_destroy();
    header("Location: ../index.php");
}
require_once("../dbconnect/dbconnect.inc.php");
$db_handle = new DBController();
$conn = $db_handle->connectDB();

// Array wird geleert 
$_SESSION["array"] = array();

$letzteBox = 0;
$Zeitpunkt = date("Y-m-d H:i:s");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST['letzteBox']) &&
        $_POST['letzteBox'] == 'true'
    ) {
        $letzteBox = 1;
    }

    mysqli_select_db($conn, "u-projraupe");
    $finalCheckQuery = "INSERT INTO `BVerfuegbarkeit` (`Zeitpunkt`, `NochVerfuegbar`) VALUES (now(), '$letzteBox')";
    mysqli_query($conn, $finalCheckQuery);
    header("Location: ./06_foodsaver_endscreen.php");
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
    <link rel="icon" type="image/x-icon" href="../favicon/favicon.ico">
    <link rel="manifest" href="../favicon/manifest.json" />
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
            <img src="../media/finalCheck_background.svg" alt="background_image" class="background"></img>
            <div class="wrap-title font-fira">
                <form class="reminder" id="finalCheckForm" method="post"
                    action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div>
                        <h1 class="font-londrina">
                            DANKE FÜR DEINE SPENDE!
                        </h1>
                        <h2>
                            Bitte vergiss nicht
                        </h2>
                        <p>
                            ... die Oberflächen abzuwischen, <br />
                            ... das Licht auszumachen, <br />
                            ... die Türe zu schließen, <br />
                            ... dich an der Theke zu verabschieden.
                        </p>
                    </div>
                    <div class="check-item">
                        <input name="letzteBox" type="checkbox" value="true">
                        <img src="../media/checkbox.svg" alt="checkbox" />
                        <img src="../media/checkbox_checked.svg" alt="checkbox_checked" />
                        Ich habe die letzte Box genommen.
                    </div>
                </form>
                <!-- WEITERLEITUNG ZUM ENDSCREEN-->
                <input class="final-button" type="submit" form="finalCheckForm" value="Alles klar">
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

    <!-- Script Overlay fs-hilfe -->
    <?php
    echo '<script type="text/javascript" src="../script/05.js">
        </script>
        ';
    ?>
</body>

</html>