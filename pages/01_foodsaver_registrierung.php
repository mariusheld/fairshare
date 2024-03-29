<?php
session_start();
$_SESSION['login'] = false;
$passwordErr = false;
if (isset($_GET['error'])) {
    $passwordErr = true;
}
$_SESSION["foodsaverLogin"] = false;
// Passwort Formular Validierung
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (test_input($_POST["password"]) == "raupe") {
        // Login auf True setzen 
        $_SESSION['login'] = true;
        // Weiterleiten
        header("Location: ./10_lageruebersicht.php");
    } else if (test_input($_POST["password"]) == "raupenkönigin") {
        // Login auf True setzen 
        $_SESSION['login'] = true;
        // Weiterleiten
        header("Location: ./12_interne_wirkungsmessung_dashboard.php");
    } else {
        $_SESSION['passwordErr'] = true;
        header("Location: ./01_foodsaver_registrierung.php?error=true");
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        FAIRSHARE
    </title>
    <link rel="icon" type="image/x-icon" href="../favicon/favicon.ico">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Londrina Solid">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira Sans">
    <link rel="stylesheet" href="../css/foodsaver_anmeldung.css">
    <link rel="stylesheet" href="../css/adminstyle.css" />
    <link rel="stylesheet" href="../css/formularstyle.css" />
</head>

<body>
    <!-- Header der Anwendung -->
    <header>
        <img src="../media/logo.svg" alt="Raupe Logo" style="padding-left: 0;" id="logo">
        <div class="MitarbeiterLogin" id="login">
            <span style="letter-spacing: -0.9px; font-weight: 800;">Mitarbeiter*in</span>
            <button id="startscreen-mitarbeiter-button">
                <svg id="login-logo" xmlns="http://www.w3.org/2000/svg" width="28px" height="28px" fill="#99BB44"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="#FFFFFF" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                </svg>
            </button>
        </div>
    </header>

    <!-- Anmeldemaske -->
    <div class="seiteninhalt-anmeldung" style="text-align: left;">
        <form class="Anmeldeformular" id="myform" method="POST" action="04_foodsaver_anmeldung_skript.php">
            <div class="col-3">
                <label id="labelvorname" for="">Vorname</label>
                <input type="text" style="text-align: left;" required id="vorname" name="vorname">
            </div>
            <div class="col-3" id="dummy">
                <label for="">Nachname (optional)</label>
                <input style="text-align: left;" type="text" name="nachname">
            </div>
            <div class="col-6">
                <label for="">Deine Foodsaver-ID (optional / 6-stellige Nummer)</label>
                <input style="text-align: left;" type="text" name="foodID" pattern="[0-9]{6}">
            </div>
            <div class="col-3">
                <label id="labelemail" for="">E-Mail</label>
                <input type="email" id="email" name="email">
            </div>
            <div class="col-3">
                <label id="labeltel" for="">Telefonnummer</label>
                <input type="tel" id="tel" name="tel" pattern="^[0-9-+\s()]*$">
            </div>

            <p id="telormail" class="col-6">Trage deine E-Mail oder deine Telefonnummer ein.</p>
            <div class="col-6 datenschutz" style="display: flex; flex-direction: row; padding-top: 32px;">
                <input type="checkbox" name="datacheck" id="datacheck" style="margin-top: -14px;" />
                <img alt="nocheck" width="32px" height="32px" src="../media/checkbox.svg" id="checkboxunchecked"
                    class="checkbox" style="margin-left: -30px; margin-top: 0px;">
                <img alt="check" width="32px" height="32px" src="../media/checkbox_checked.svg" class="checkboxchecked"
                    style="margin-left: -30px; margin-top: 0px;">
                <p class="dataschutz">
                    Ich stimme zu, dass die Raupe meine Daten zur Auswertung
                    der Wirkungsmessung verwendet. Persönliche Daten werden
                    nicht an Dritte weitergegeben.
                </p>
            </div>
        </form>
    </div>
    <!--Footer-->
    <footer id="footer" style="visibility: visible;">
        <div class="action-container">
            <!-- OVERLAY Trigger Nicht erlaubte Lebensmittel -->
            <div id="openNichtErlaubteLm" style="visibility:hidden">
                <img src="../media/icon_help_mini.svg" alt="icon_help" />
                <p>Nicht erlaubte Lebensmittel</p>
            </div>
            <div class="action-wrap">
                <!-- SENDEN des Formulars und WEITERLEITUNG zur Foodsaver Übersicht -->
                <button style="margin-top:0px" id="btnbreakup" type="button"
                    onclick="window.location.href ='02_foodsaver_anmeldung.php'">Zurück</button>
                <input class="continue-button" type="submit" form="myform" onclick="return formcheck()"
                    value="Registrierung" style="width: 228px">
            </div>
        </div>
    </footer>

    <!-- ---------- OVERLAYS ----------- -->
    <!-- Anmelde Overlay -->
    <div class="helper" id="overtrigger">
        <div class="overlayparent">
            <div class="overlaychild">
                <p class="olhead">
                    Kennwort
                </p>
                <div class="eingabe">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                        autocomplete="off">
                        <?php if ($passwordErr == true) { ?>
                            <input class="eingabefeld" name="password" id="eingabe" type="password"
                                style='text-align: center; border: 2px solid #E97878; width: 468; height: 42px;'>
                            <p class="vergessen" id="vergessen" style='color: #E97878; display: block;'>
                                Kennwort falsch
                            </p>
                        <?php } else { ?>
                            <input class="eingabefeld" name="password" id="eingabe" type="password"
                                style='text-align: center; font-size: 20px;'>
                        <?php } ?>

                        <div class="buttonscontainer" id="bcontainer">
                            <div class="buttonwhite" id="breakup">
                                <p class="buttontext" style="color: #99BB44">
                                    Abbrechen
                                </p>
                            </div>
                            <div class="buttongreen">
                                <input type="submit" class="buttongreen" style="color: white" value="Anmelden">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        //Funktion zur Formularüberprüfung
        function formcheck() {
            var dataschutz = document.getElementById("datacheck").checked;
            var email = document.getElementById('email').value;
            var tel = document.getElementById('tel').value;
            var vorname = document.getElementById('vorname').value;
            if (email == '' && tel == '' && vorname == '') {
                //Label Rot färben
                document.getElementById("labelvorname").style.color = "#E97878";
                document.getElementById("labelemail").style.color = "#E97878";
                document.getElementById("labeltel").style.color = "#E97878";
                document.getElementById("telormail").style.color = "#E97878";
                //Datacheck Rot färben
                document.getElementById("checkboxunchecked").src = "../media/datacheck_empty.png"
                //Input Border Rot färben
                document.getElementById("vorname").style.border = "2px solid #E97878";
                document.getElementById("tel").style.border = "2px solid #E97878";
                document.getElementById("email").style.border = "2px solid #E97878";
                return false;
            } else if (email == '' && tel == '') {
                //Label Rot färben
                document.getElementById("labelemail").style.color = "#E97878";
                document.getElementById("labeltel").style.color = "#E97878";
                document.getElementById("telormail").style.color = "#E97878";
                //Dataschutz Rot färben
                document.getElementById("checkboxunchecked").src = "../media/datacheck_empty.png"
                //Input Border Rot
                document.getElementById("tel").style.border = "2px solid #E97878";
                document.getElementById("email").style.border = "2px solid #E97878";
                return false;
            } else if ((email != "" || tel != "") && vorname == "") {
                document.getElementById("labelvorname").style.color = "#E97878";
                document.getElementById("vorname").style.border = "2px solid #E97878";
                document.getElementById("checkboxunchecked").src = "../media/datacheck_empty.png"
            } else if (dataschutz == false) {
                document.getElementById("checkboxunchecked").src = "../media/datacheck_empty.png"
                return false;
            } else {
                return true;
            }
        }

        //Sobald etwas eingegeben wird, wird Rotfärbung aufgehoben
        email.onkeyup = () => {
            document.getElementById("email").style.border = "none";
            document.getElementById("labelemail").style.color = "#BEBEB9";
            document.getElementById("labeltel").style.color = "#BEBEB9";
            document.getElementById("telormail").style.color = "#BEBEB9";
            document.getElementById("tel").style.border = "none";
        }
        tel.onkeyup = () => {
            document.getElementById("email").style.border = "none";
            document.getElementById("labelemail").style.color = "#BEBEB9";
            document.getElementById("labeltel").style.color = "#BEBEB9";
            document.getElementById("telormail").style.color = "#BEBEB9";
            document.getElementById("tel").style.border = "none";
        }
        vorname.onkeyup = () => {
            document.getElementById("vorname").style.border = "none";
            document.getElementById("labelvorname").style.color = "#BEBEB9";
        }

        document.getElementById('logo').onclick = function () {
            window.location.href = '../index.php';
        }


        document.getElementById('logo').onclick = function () {
            window.location.href = '../index.php';
        }

        //JavaScript für das PopUp
        // Modales Fenster 
        var modal = document.getElementById('overtrigger');

        // Button der modale Fenster triggert
        var btn = document.getElementById('login');

        // Button der das modale Fenster schließst
        var span = document.getElementsByClassName('buttonwhite')[0];

        // Falsches Passwort Logik
        //Variable für Fehlermeldung
        let passwordErr = <?php echo json_encode($passwordErr); ?>;
        // Modales Fenster öffnen 
        if (passwordErr == true) {
            modal.style.display = 'flex';
        }

        btn.onclick = function () {
            modal.style.display = 'flex';
        }
        // Modales Fenster schließen wenn auf Abbrechen geklickt wird
        span.onclick = function () {
            modal.style.display = 'none';
            eingabe.value = '';
            bcontainer.style.paddingtop = '64px';
            eingabe.style.height = '46px';
            eingabe.style.width = '472px';
        }
        // Modales Fenster schließen wenn außerhalb der Box geklickt wird
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = 'none';
                eingabe.value = '';
                bcontainer.style.paddingtop = '64px';
                eingabe.style.height = '46px';
                eingabe.style.width = '472px';
            }
        }
        //Ausblenden des Overlays beim Laden der Seite
        document.onload = function (event) {
            modal.style.display = 'none';
        }
    </script>
</body>

</html>