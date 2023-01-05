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
<html lang=de>
       <head>
        <meta charset="UTF-8"/>
        <meta name="author" content="Marius Held"/>
        <title>Abgabeübersicht</title>
        <link rel="stylesheet" href="../css/adminstyle.css"/> 
        <link rel="stylesheet" href="../css/formularstyle.css"/> 
        <link rel="stylesheet" href="../css/foodsaver_anmeldung.css"/> 
        <style>
           @import url('https://fonts.googleapis.com/css2?family=Fira+Sans:wght@400;600&family=Londrina+Solid:wght@300;400&display=swap');
        </style>
        </head>
        <body>
        <header style="padding-left: 0;">
            <img src="../media/logo.svg" style="margin-left:0">
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

		<!--Seiteninhalt-->
		<div class="seiteninhalt">
			<form class="emailco" method="POST" >
                <div class="header">
                    Anmelden per...
                </div>
                <div class="radiocontainer">
                    <button id="btnmail" type="button" onclick="buttoncheck(this)" class="btn">E-Mail</button>
                    <button id="btntel" type="button" onclick="buttoncheck(this)" class="btn">Telefonummer</button>
                    <button id="btnid" type="button" onclick="buttoncheck(this)" class="btn">Foodsaver-ID</button>
                </div>
            </form>
            <div id="regcontainer" class="regcontainer">
            <div class="header" style="margin-top: 48px;">
                Zum ersten mal hier?
            </div>
            <a href="01_foodsaver_anmeldung.php">
            <button class="reg">Registrieren</button>
            </a>
        </div>
        <div class="formvariabel" style="text-align: left;">
            <form class="anmeldung" method="POST">
                <div class="anmeldungmail" id="anmail">
                    <label  for="mail">Deine E-Mail</label></br>
                    <input type="email" name="mail"  style="width:310px; height: 46px;"/>
                </div>
                <div class="anmeldungtel" id="antel">
                    <label  for="tel">Deine Telefonnummer</label></br>
                    <input type="tel" name="tel"  style="width:310px; height: 46px;"/>
                </div>
                <div class="anmeldungid" id="anid">
                    <label  for="ID">Deine Foodsaver-ID</label></br>
                    <input type="text" name="ID"  style="width:310px; height: 46px;"/>
                </div>
            </form>
        </div>
    </div>
		<!--Seiteninhalt-->
		<footer>
			<div class="action-container">
                <!-- OVERLAY Trigger Nicht erlaubte Lebensmittel -->
                <div id="openNichtErlaubteLm" style="visibility:hidden">
                    <img src="../media/icon_help_mini.svg" alt="icon_help" />
                    <p>Nicht erlaubte Lebensmittel</p>
                </div>
                <div class="action-wrap">
                    <!-- SENDEN des Formulars und WEITERLEITUNG zur Foodsaver Übersicht -->
                    <a id="openHinzufuegenAbbr">Abbrechen</a>
                    <input class="continue-button" type="submit" form="myform" value="Anmeldung">
                </div>
            </div>
		</footer>
		</div>
        
         <script>
            //JavaScript Funktionen für die Buttons
            function buttoncheck(btn) {
                //Speichern der Button ID in eine Variable
                var x = btn.id;
                // If/else Funktion für die Buttons
                if (x == "btnmail"){
                    //CSS zum Button Styling
                    var mailclick = document.getElementById("btnmail");
                    mailclick.style.border = "solid 2px #99BB44";
                    mailclick.style.boxShadow = "0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)";

                    //Reset Styling der anderen Buttons
                    var telclick = document.getElementById("btntel");
                    var idclick = document.getElementById("btnid");
                    telclick.style.border ="solid 2px #E5E5E3";
                    idclick.style.border ="solid 2px #E5E5E3";
                    telclick.style.boxShadow = "none";
                    idclick.style.boxShadow = "none";

                    //CSS Anweisungen zur Ein-/Ausblendung der einzelnen Elemente
                    document.getElementById("regcontainer").style.display = "none";
                    document.getElementById("antel").style.display = "none";
                    document.getElementById("anid").style.display = "none";
                    document.getElementById("anmail").style.display = "block";
                    
                } else if (x == "btntel") {
                     //CSS zum Button Styling
                     var telclick = document.getElementById("btntel");
                    telclick.style.border = "solid 2px #99BB44";
                    telclick.style.boxShadow = "0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)";

                    //Reset Styling der anderen Buttons
                    var mailclick = document.getElementById("btnmail");
                    var idclick = document.getElementById("btnid");
                    mailclick.style.border ="solid 2px #E5E5E3";
                    idclick.style.border ="solid 2px #E5E5E3";
                    mailclick.style.boxShadow = "none";
                    idclick.style.boxShadow = "none";

                    //CSS Anweisungen zur Ein-/Ausblendung der einzelnen Elemente
                    document.getElementById("regcontainer").style.display = "none";
                    document.getElementById("antel").style.display = "block";
                    document.getElementById("anid").style.display = "none";
                    document.getElementById("anmail").style.display = "none";

                }else if (x == "btnid") {
                     //CSS zum Button Styling
                     var idclick = document.getElementById("btnid");
                    idclick.style.border = "solid 2px #99BB44";
                    idclick.style.boxShadow = "0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)";

                     //Reset Styling der anderen Buttons
                     var mailclick = document.getElementById("btnmail");
                    var telclick = document.getElementById("btntel");
                    mailclick.style.border ="solid 2px #E5E5E3";
                    telclick.style.border ="solid 2px #E5E5E3";
                    mailclick.style.boxShadow = "none";
                    telclick.style.boxShadow = "none";

                    //CSS Anweisungen zur Ein-/Ausblendung der einzelnen Elemente
                    document.getElementById("regcontainer").style.display = "none";
                    document.getElementById("antel").style.display = "none";
                    document.getElementById("anid").style.display = "block";
                    document.getElementById("anmail").style.display = "none";
                }
            }

            //Variable für Fehlermeldung
           let passwordErr = <?php echo json_encode($passwordErr); ?>;

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
         <!--Skript Ende-->
         </body>
</html>