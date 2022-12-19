<?php
session_start();
$passwordErr = $_SESSION['passwordErr'];
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
        header("Location: ./admin.php");
    } else {
        $_SESSION['passwordErr'] = true;
        header("Location: ./01_foodsaver_anmeldung.php");
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

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Londrina Solid">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira Sans">
    <link rel="stylesheet" href="../css/foodsaver_anmeldung.css">
</head>

<body>
    <!-- Header der Anwendung -->
    <header>
        <img src="../media/logo.svg" width="194px;" alt="Raupe Logo" id="logo">
        <a href="#" class="MitarbeiterLogin" id="login">
            <span>Mitarbeiter*in</span>
            <button id="startscreen-mitarbeiter-button"><svg id="login-logo" xmlns="http://www.w3.org/2000/svg"
                    width="28px" height="28px" fill="#99BB44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#FFFFFF"
                    class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                </svg></button>
        </a>
    </header>

    <!-- Anmeldemaske -->
    <main>
        <form class="Anmeldeformular" method="POST" action="02_foodsaver_start.php">
            <div class="col-3">
                <label id="labelvorname" for="">Vorname</label>
                <input type="text" required id="vorname" name="vorname">
            </div>
            <div class="col-3" id="dummy">
                <label for="">Nachname (optional)</label>
                <input type="text" name="nachname">
            </div>
            <div class="col-6">
                <label for="">Deine Foodsaver-ID (optional / 6-stellige Nummer)</label>
                <input type="text" name="foodID" pattern="[0-9]{6}">
            </div>
            <div class="col-3">
                <label for="">E-Mail</label>
                <input type="email" id="email" name="email">
            </div>
            <div class="col-3">
                <label for="">Telefonnummer</label>
                <input type="tel" id="tel" name="tel">
            </div>

            <p id="telormail" class="col-6">Trage deine E-Mail oder deine Telefonnummer ein.</p>

            <a href="../index.php" class="button secondary col-3" id="breakupbtn">Abbrechen</a>
            <input type="submit" value="Weiter" class="button primary col-3" id="weiter" onclick="return formcheck()">
        </form>
    </main>

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
                        <?php
                        if ($passwordErr == false) { ?>
                        <input class="eingabefeld" name="password" id="eingabe" type="password"
                            style='text-align: center; font-size: 20px;'>
                        <?php }
                        if ($passwordErr == true) {
                        ?>
                        <input class="eingabefeld" name="password" id="eingabe" type="password"
                            style='border: 2px solid red; width: 468; height: 42px;'>
                        <p class="vergessen" id="vergessen" style='color: red; display: block;'>
                            Kennwort falsch
                        </p>
                        <?php } ?>

                        <div class="buttonscontainer" id="bcontainer">
                            <div class="buttonwhite" id="breakup">
                                <p class="buttontext" style="color: #99BB44">
                                    Abrechen
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
        //Variable für Fehlermeldung
        let passwordErr = <?php echo json_encode($passwordErr); ?>;

        //Funktion zur Formularüberprüfung
        function formcheck() {
            var email = document.getElementById('email').value;
            var tel = document.getElementById('tel').value;
            var vorname = document.getElementById('vorname').value;
            if (email == '' && tel == '' && vorname == '') {
                window.alert('nichts eingegeben');
                return false;
            } else if (email == '' && tel == '') {
                window.alert('kontakt fehlt');
                return false;
            } else {
                return true;
            }
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
            eingabe.style.border = 'none';
            vergessen.style.display = 'none';
            bcontainer.style.paddingtop = '64px';
            eingabe.style.height = '46px';
            eingabe.style.width = '472px';
        }
        // Modales Fenster schließen wenn außerhalb der Box geklickt wird
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = 'none';
                eingabe.value = '';
                eingabe.style.border = 'none';
                vergessen.style.display = 'none';
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