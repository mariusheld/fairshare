<?php
//Datenbankverbindung aufbauen
require_once("../dbconnect/dbconnect.inc.php");

//Abfrage an Datenbank senden
$query = $db->prepare("SELECT*FROM Lebensmittel, Box WHERE  Lebensmittel.LMkey = Box.LMkey"); //Wenn Datenbank alles Anzeigen soll wegen Kisten Bug: "SELECT*FROM Lebensmittel WHERE LMkey >= 13"
$erfolg = $query->execute();

//Fehlertest
if (!$erfolg) {
    $fehler = $query->errorInfo();
    die("Folgender Datenbankfehler ist aufgetreten:" . $fehler[2]);
}

//Array für die Icons in der Lagerübersicht
$icons = array(
    1 => "../media/kategorien/icon_backwaren-salzig.svg",
    2 => "../media/kategorien/icon_backwaren-suess.svg",
    3 => "../media/kategorien/icon_gemuese.svg",
    4 => "../media/kategorien/icon_konserven.svg",
    5 => "../media/kategorien/icon_kuehlprodukte.svg",
    6 => "../media/kategorien/icon_obst.svg",
    7 => "../media/kategorien/sonstiges.svg",
    8 => "../media/kategorien/icon_trockenprodukte.svg",
);

$_SESSION['password'] = array();

?>

<!DOCTYPE html>
<html lang=de>

<head>
    <meta charset="UTF-8" />
    <meta name="author" content="Marius Held" />
    <title>Abgabeübersicht</title>
    <link rel="stylesheet" href="../css/adminstyle.css" />
    <link rel="stylesheet" href="../css/popup_styles.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fira+Sans:wght@400;600&family=Londrina+Solid:wght@300;400&display=swap');
    </style>
</head>

<body>
    <?php
    //Session eröffnen
    session_name("adminbereich");
    session_start();

    //In der Session gespeicherte Login-Daten übernehmen
    $passwort = $_SESSION['password'];

    //Login Daten Prüfen
    if ($passwort != "raupe" or $passwort != "raupenkönigin") {
        //Passwort aus Post-Übermittlung bekommen
        $passwort = $_POST['password'];
        $_SESSION['password'] = $passwort;
    }
    //
//Zugang zur Lagerübersicht
    if ($passwort == "raupe" or $_GET['set']== true) {
    ?>
    <div class="pagewrap">
        <!--Logout Overlay-->
        <div class="helper" id="overtrigger">
            <div class="overlayparent">
                <div class="overlaychild" style="height: 191px; ">
                    <p class="olhead">
                        Abmelden?
                    </p>
                    <div class="eingabe">
                        <div class="buttonscontainer">
                            <button class="buttonwhite">
                                Abrechen
                            </button>
                            <div class="buttongreen">
                                <button class="buttongreen" id="btnlogout" style="color: white" value="Abmelden">
                                    Abmelden
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Logout Overlay-->
        <div class="navbar">
            <div class="navcontainer">
                <p class="navbarhead">
                    Lagerübersicht
                </p>
                <div class="logout" id="logout">
                    <p class="logouttext">
                        Ausloggen
                    </p>
                    <img alt="ausloggen" src="../media/lock_icon.svg" width="48" height="48" />
                </div>
            </div>
        </div>
        <!--Seiteninhalt-->
        <div class="seiteninhalt">
            <table>
                <!--Tabellenkopf-->
                <tr>
                    <th style="width: 25%;">Lebensmittel</th>
                    <th style="width: 20%;">Kistennr</th>
                    <th style="width: 20%;">Menge</th>
                    <th>Genießbar</th>
                    <th style="width: 50px;"></th>
                    <th style="width: 50px;"></th>
                </tr>
                <!--Tabelleninhalt-->

                <?php
        //Zellenweise Verarbeitung der Datenbankabfrage
        $result = $query->fetchAll();

        //Konsolenausgabe der Datenbankabfrage (nur möglich nach einem fetchAll() befehl der Abfrage)
        echo "<script>console.log(" . json_encode($result) . ");</script>";

        foreach ($result as $zeile) {
            echo "<tr>";
            //echo "<td> <img alt='icon' width='48' src='" . $icons[$zeile['OKatKey']] . "'></td>";
            echo "<td class='lmicon'><div class='tablecontainer'><img alt='lmicon' src='" . $icons[$zeile['OKatKey']] . "'><div style='font-weight: 600; padding-left: 16px;'>" . $zeile['Bezeichnung'] . "</div></div></td>";
            if ($zeile['Kuehlware'] == 0) {
                echo "<td>" . $zeile['BoxID'] . "</td>"; 
            } else {
                echo "<td><div class='tablecontainer'><div>4</div> <img style='padding-left: 16px;' alt='coolicnon' src='../media/freeze_icon.svg' width='32'></div></td>";
            }
            echo "<td>" . $zeile['Gewicht'] . " kg</td>";
            echo "<td>" . $zeile['VerteilDeadline'] . "</td>";
            if ($zeile['Anmerkung']) {
                echo "<td style='text-align: right'><img id='bubble' alt='dots' src='../media/comment_icon.svg' width='48px;'/></td>";
            } else {
                echo "<td style='text-align: right'><img id='bubble' style='visibility:hidden' alt='dots' src='../media/comment_icon.svg' width='48px;'/></td>";
            }
            echo 
            "<td style='text-align: right; position: relative'>
                <img onclick='open_close_options(this)' alt='dots' src='../media/edit_icon.svg' width='48px;'/>
                <ul class='options'>
                    <li><img src='../media/eye.svg' alt=''><span>Ansehen</span></li>
                    <li onclick='open_lebensmittel_fairteilen(this)'><img src='../media/arrows.svg' alt=''><span>Fairteilen</span></li>
                    <li><img src='../media/trashbin.svg' alt=''><span>Entsorgen</span></li>
                </ul>
            </td>";
            echo "</tr>\n";
        }
                ?>
                
        </div>
        <!--Seiteninhalt-->
        <footer>
            <div class="footerbg">
              <a href="admin.php?set=true"><button class="refreshbutton" id="refreshdash">
                    Liste Aktualisieren
                </button></a>
            </div>
        </footer>
    </div>
    </div>

    <!-- Nur zu Testzwecken, später entfernen -->
    <button id="open_nicht_genießbar">Nicht genießbar</button>
    <button id="open_keine_boxen">Keine Boxen</button>

    <!-- Popup "Nicht genießbar" -->
    <div class="overlay" id="popup_nicht_genießbar">
        <div class="popup-wrapper">
            <div class="popup active">
                <h3>HOPPLA!</h3>
                <p>Das Produkt <span class="marked-red">Laugenbrezeln (Kiste 4)</span> ist vermutlich nicht mehr genießbar. Bitte sieh dir das Lebensmittel im Lager an und entsorge oder verlängere es gegebenenfalls.</p>
                <button id="close_nicht_genießbar" class="secondary-btn">Produkt behalten</button>
                <button class="primary-btn">Produkt prüfen</button>
            </div>
        </div>
    </div>

    <!-- Popup "Keine Boxen" -->
    <div class="overlay" id="popup_keine_boxen">
        <div class="popup-wrapper">
            <div class="popup active">
                <h3>HOPPLA!</h3>
                <p>Jemand hat gerade die letzte Box genommen. <br> Sieh nach und sorge für Nachschub.</p>
                <button id="close_keine_boxen" class="secondary-btn">Später erinnern</button>
                <button class="primary-btn">Boxen nachgefüllt</button>
            </div>
        </div>
    </div>

    <!-- Script zum Öffnen der Pop-Ups -->
    <script type="text/javascript" src="../script/open_popups_mitarbeiter.js"></script>

    <?php
        echo "
         <script>
             // Modale Box ansprechen
             var modal = document.getElementById('overtrigger');
     
             // Buttons definieren, welche die modale Box triggern
             var btn = document.getElementById('logout');
     
             // <span> Element ansprechen, welches den Schließbutton anspricht
             var span = document.getElementsByClassName('buttonwhite')[0];
     
             // Funktion, dass sich die modale Box öffnet, wenn der Button getriggert wird
             btn.onclick = function() {
               modal.style.display = 'flex';
             }
             // Bei Klick auf Abbrechen -> Fenster schließen
             span.onclick = function() {
             modal.style.display = 'none';
             eingabe.value ='';
             eingabe.style.border='none';
             }
             // Fenster schließen beim Klick außerhalb des Fensters
             window.onclick = function(event) {
                 if (event.target == modal) {
                     modal.style.display = 'none';
                     eingabe.value ='';
                     eingabe.style.border='none';
                 }
             }
             //User drückt auf Abmelden
            btnlogout.onclick = function(){
                window.location.href = '../index.php'
            }
             //LogIn fehlgeschlagen
             if(login==false) {
                 document.getElementsById('eingabe').style.border = '2px red';
             }
         </script>
         <!--Skript Ende-->
         ";
        $login = false;
        $_SESSION['login'] = $login;
        //
        //Zugang zum Dashboard
    } else if ($passwort == "raupenkönigin") {
        //Dashboard Seite
    ?>
    <!--Logout Overlay-->
    <div class="helper" id="overtrigger">
        <div class="overlayparent">
            <div class="overlaychild" style="height: 191px; ">
                <p class="olhead">
                    Abmelden?
                </p>
                <div class="eingabe">
                    <div class="buttonscontainer">
                        <button class="buttonwhite">
                            Abrechen
                        </button>
                        <div class="buttongreen">
                            <button class="buttongreen" id="btnlogout" style="color: white" value="Anmelden">
                                Abmelden
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Logout Overlay-->
    <div class="navbar">
        <div class="navcontainer">
            <p class="navbarhead">
                Dashboard
            </p>
            <div class="logout" id="logout">
                <p class="logouttext">
                    Ausloggen
                </p>
                <img alt="ausloggen" src="../media/lock_icon.png" width="48" height="48" />
            </div>
        </div>
    </div>
    <!--Seiteninhalt-->
    <div class="seiteninhalt">
        <!--hier Dashboard einfügen-->
    </div>
    <!--Seiteninhalt-->
    <footer>
        <div class="footerbg">
            <button class="refreshbutton" id="refreshdash">
                Aktualisieren
            </button>
        </div>
    </footer>
    </div>
    <?php
        echo "
         <script>
             // Modale Box ansprechen
             var modal = document.getElementById('overtrigger');
     
             // Buttons definieren, welche die modale Box triggern
             var btn = document.getElementById('logout');
     
             // <span> Element ansprechen, welches den Schließbutton anspricht
             var span = document.getElementsByClassName('buttonwhite')[0];
     
             // Funktion, dass sich die modale Box öffnet, wenn der Button getriggert wird
             btn.onclick = function() {
               modal.style.display = 'flex';
             }
             // Bei Klick auf Abbrechen -> Fenster schließen
             span.onclick = function() {
             modal.style.display = 'none';
             eingabe.value ='';
             eingabe.style.border='none';
             }
             // Fenster schließen beim Klick außerhalb des Fensters
             window.onclick = function(event) {
                 if (event.target == modal) {
                     modal.style.display = 'none';
                     eingabe.value ='';
                     eingabe.style.border='none';
                 }
             }
             //User drückt auf Abmelden
            btnlogout.onclick = function(){
                window.location.href = '../index.php'
            }
             //LogIn fehlgeschlagen
             if(login==false) {
                 document.getElementsById('eingabe').style.border = '2px red';
             }
         </script>
         <!--Skript Ende-->
         ";
        $login = false;
        $_SESSION['login'] = $login;
    }
    //Keine Login-Daten vorhanden oder falsche Daten
    if ($passwort != "raupe" and $passwort != "raupenkönigin") {
        echo "<script>window.location.href = '../index.php'</script>";
        $login = true;
        $_SESSION['login'] = $login;
    }
    ?>
</body>

</html>