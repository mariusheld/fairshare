<?php
    //Datenbankverbindung aufbauen
    require_once("../dbconnect/dbconnect.inc.php");

    //Abfrage an Datenbank senden
$query = $db->prepare("SELECT*FROM Lebensmittel WHERE LMkey=5 OR  LMkey=11");
    $erfolg = $query->execute();

    //Fehlertest
    if(!$erfolg) {
        $fehler = $query->errorInfo();
        die("Folgender Datenbankfehler ist aufgetreten:" .$fehler[2]);
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

?> 

<!DOCTYPE html>
<html lang=de>
       <head>
        <meta charset="UTF-8"/>
        <meta name="author" content="Marius Held"/>
        <title>Abgabeübersicht</title>
        <link rel="stylesheet" href="../css/foodsaver.css"/> 
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
    if($passwort != "raupe" OR $passwort != "raupenkönigin") {
        //Passwort aus Post-Übermittlung bekommen
        $passwort = $_POST['password'];
        $_SESSION['password'] = $passwort;
    }
    //
    //Zugang zur Lagerübersicht
    if ($passwort == "raupe") {
        ?>
        <div class="seiteninhalt">
              <!--Logout Overlay-->
        <div class="helper"  id="overtrigger" >
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
							<div class="buttongreen" >
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
				<div class="logout" id="logout" >
					<p class="logouttext">
						Ausloggen
					</p>
					<img class="logouticon" alt="ausloggen" src="../media/lock_icon.png" width="48" height="48"/>
				</div>
			</div>
		</div>
		<!--Seiteninhalt-->
		<div class="seiteninhalt">
			<table>
				<!--Tabellenkopf-->
				<tr>
					<th style="width: 25%;" >Lebensmittel</th><th style="width: 20%;" >Kistennr</th><th style="width: 20%;">Menge (in kg)</th><th>Genießbar</th><th style="width: 50px;"></th><th style="width: 50px;"></th>
				</tr>
				<!--Tabelleninhalt-->

                <?php
                    //Zellenweise Verarbeitung der Datenbankabfrage
                    $result = $query->fetchAll();

                    //Konsolenausgabe der Datenbankabfrage (nur möglich nach einem fetchAll() befehl der Abfrage)
                echo "<script>console.log(" . json_encode($result) . ");</script>";

                foreach($result as $zeile){
                    echo "<tr>";
                     //echo "<td> <img alt='icon' width='48' src='" . $icons[$zeile['OKatKey']] . "'></td>";
                    echo "<td class='lmicon'><div class='tablecontainer'><img alt='lmicon' src='" . $icons[$zeile['OKatKey']] . "'><div style='font-weight: 600; padding-left: 16px;'>" . $zeile['Bezeichnung'] . "</div></div></td>";
                if ($zeile['Kuehlware'] == 0) {
                            echo "<td>5</td>";    /* . $zeile['BoxID'] . */
                            } else {
                                  echo "<td><div class='tablecontainer'><div>4</div> <img style='padding-left: 16px;' alt='coolicnon' src='../media/Frame.png' width='32'></div></td>";
                            }
                    echo "<td>" . $zeile['Gewicht'] ."</td>";
                    echo "<td>" . $zeile['VerteilDeadline'] . "</td>"; 
                    if ($zeile['Anmerkung']) {
                        echo "<td style='text-align: right'><img id='bubble' alt='dots' src='../media/bubble.jpg' width='48px;'/></td>";
                    } else {
                        echo "<td style='text-align: right'><img id='bubble' style='visibility:hidden'  alt='dots' src='../media/bubble.jpg' width='48px;'/></td>";
                    }
                    echo "<td style='text-align: right'><img alt='dots' src='../media/dots.jpg' width='48px;'/></td>";
                    echo "</tr>\n";
                        }
                ?>
                <!--Tabelle hardgecodet
				<tr>
					<td class="lmicon"><div class="tablecontainer"><img alt="lmicon" src="../media/icon_obst.png" width="48"><div style="font-weight: 600; padding-left: 16px;">Banana</div></div></td>
					<td><div class="tablecontainer"><div>4</div> <img style="padding-left: 16px;" alt="coolicnon" src="../media/Frame.png" width="32"></div></td>
					<td>2 kg</td>
					<td>3 Tage</td>
					<td class="kommentaricon"><div class="tablecontainer" style="justify-content: flex-end; gap: 16px;"><img alt="dots" src="../media/bubble.jpg" width="48"><img alt="dots" src="../media/dots.jpg" width="48"></div></td>
			</tr>
            Tabelle hardgecodet-->
		</div>
		<!--Seiteninhalt-->
		<footer>
			<div class="footerbg">
				<button class="refreshbutton" id="refreshdash">
					Liste Aktualisieren
				</button>
			</div>
		</footer>
		</div>
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
        //
        //Zugang zum Dashboard
    } else if ($passwort == "raupenkönigin") {
        //Dashboard Seite
        ?>
        <!--Logout Overlay-->
        <div class="helper"  id="overtrigger" >
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
							<div class="buttongreen" >
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
					<img class="logouticon" alt="ausloggen" src="../media/lock_icon.png" width="48" height="48"/>
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
    if($passwort != "raupe" AND $passwort != "raupenkönigin") {
           echo"<script>window.location.href = 'https://mars.iuk.hdm-stuttgart.de/~mh341/foodsaver/index.php'</script>";
        $login = true;
        $_SESSION['login'] = $login;
    }
?>
</body>
</html>