<?php
//Session 
session_name("adminbereich");
session_start();
$login = $_SESSION['login'];



    //Datenbankverbindung aufbauen
    require_once("../dbconnect/dbconnect.inc.php");

    //Abfrage an Datenbank senden
    $query = $db->prepare("SELECT*FROM Lebensmittel_alt, Lieferung, Foodsaver WHERE Lieferung.LMkey=Lebensmittel_alt.LMkey AND Lieferung.FSkey=Foodsaver.FSkey AND Foodsaver.Vorname='Julia'");
    $erfolg = $query->execute();

    //Fehlertest
    if(!$erfolg) {
        $fehler = $query->errorInfo();
        die("Folgender Datenbankfehler ist aufgetreten:" .$fehler[2]);
        }
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
    <!--Overlay-->
	<div class="helper" id="overtrigger" <?php if($login == true){echo "style='display:flex;'";}?>>
        <div class="overlayparent">
            <div class="overlaychild">
                <p class="olhead">
                    Kennwort
                </p>
                <div class="eingabe">
                    <form action="admin.php" method="POST">
                        <input class="eingabefeld" name="password" id="eingabe" type="text" <?php if($login == true){echo "style='border: 2px solid red;'";}?>>
						<p class="vergessen">
							Kennwort zurücksetzen
						</p>
						<div class="buttonscontainer">
							<div class="buttonwhite" id="breakup">
								<p class="buttontext" style="color: #99BB44">
									Abrechen
								</p>
							</div>
							<div class="buttongreen" >
								<input type="submit" class="buttongreen" style="color: white" value="Anmelden">
							</div>
						</div>
                    </form>
                </div>
            </div>
        </div>
	</div>
	<!-- Overlay-->
    <div class="navbar">
        <ul>
            <li>
               <a class="navlink" href="foodsaver.php">
                   <img id="logo" alt="FairShare Logo" width=200 src="../media/logo.png">
               </a>
            </li>
             <li>
                <div class="navlink" id="loginbutton">
                     <img id="question" alt="Hilfe" width="48" src="../media/question.png">
                 </div>
            </li> 
            </ul>
    </div>
    <div class="seiteninhalt">
        <table>
            <tr><th>Lebensmittel</th><th>Foodsaver</th><th>Herkunft</th><th>Genießbar</th><th></th><th style="width:60px"></th></tr>
            <?php 
                //Zeilenweise Verarbeitung der Datenbankabfrage
                $result = $query->fetchAll();

                //Konsolenausgabe der Datenbankabfrage (nur möglich nach einem fetchAll() befehl der Abfrage)
                echo "<script>console.log(" . json_encode($result) . ");</script>";

                foreach($result as $zeile) {
                    echo "<tr>";
                    echo "<td style='font-weight: bold'>" . $zeile['Bezeichnung'] . "</td>";
                    echo "<td>" . $zeile['Vorname'] ." ". $zeile['Nachname'] ."</td>";
                    echo "<td>" . $zeile['Herkunft'] . "</td>";
                    echo "<td>" . $zeile['VerteilDeadline'] . "</td>";
                if ($zeile['LMkey'] == 9) {
                    echo "<td style='text-align: right'><img id='bubble' style='visibility:hidden' alt='dots' src='../media/bubble.jpg' width='48px;'/></td>";
                } else {
                    echo "<td style='text-align: right'><img id='bubble'  alt='dots' src='../media/bubble.jpg' width='48px;'/></td>";
                }
                    echo "<td style='text-align: right'><img alt='dots' src='../media/dots.jpg' width='48px;'/></td>";
                    echo "</tr>\n";
                }
                //Tabelle Schließen
                echo "</table>";
            ?>
            <div class="PlusBus">
                <img id="PlusImage" alt="PlusBus" src="../media/Plusbus.png" width="80px"/>
            </div>
    </div>
    <footer>
        <div class="footer">
            <ul>
                <li>
                    <div class="support">
                        <img alt="support" class="supportimage" src="../media/green_question.png" width=35px/>
                        <p class="supporttext">Verstauen von Lebensmitteln</p>
                        
                    </div>
                </li>
                <li>
                    <div class="buttons">
                        <ul>
                            <li>
                                <div class="button" id="breakup">
                                    <p class="buttontext">Abrechen</p>
                                </div>
                            </li>
                            <li>
                                <div class="button">
                                    <p class="buttontext">Absenden</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>  
    </footer>
    <!-- Overlay Skript -->
    <?php
    echo "
	<script>
		// Get the modal
		var modal = document.getElementById('overtrigger');

		// Get the button that opens the modal
		var btn = document.getElementById('loginbutton');

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName('buttonwhite')[0];

		// When the user clicks the button, open the modal 
		btn.onclick = function() {
  		modal.style.display = 'flex';
		}
        // When the user clicks on Abbrechen, close the modal
        span.onclick = function() {
        modal.style.display = 'none';
        eingabe.value ='';
        eingabe.style.border='none';
        }
		// When the user clicks anywhere outside of the modal, close it
		window.onclick = function(event) {
			if (event.target == modal) {
				modal.style.display = 'none';
				eingabe.value ='';
                eingabe.style.border='none';
			}
		}
        //LogIn fehlgeschlagen
        if(login==false) {
            document.getElementsById('eingabe').style.border = '2px red';
        }
	</script>
	<!--Skript Ende-->
    "
    ?>
</body>
</html>