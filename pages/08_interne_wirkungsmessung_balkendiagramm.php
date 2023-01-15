<!-- --------- PHP --------- -->
<?php
session_start();
//Datenbankverbindung aufbauen
require_once("../dbconnect/dbconnect.inc.php");
$db_handle = new DBController();
$conn = $db_handle->connectDB();


//ZEITRAUM
//Datumsauswahl auslesen
$date1formatted = $_GET['date1'];
$date2formatted = $_GET['date2'];


// Wenn noch kein Datum ausgewählt wurde, aktuelles Jahr anzeigen
$thisyear = date("Y"); 
$today_dmY = date("d.m.Y");

if ($date1formatted == NULL && $date2formatted == NULL)
	{
	$date1formatted = "01.01.".$thisyear;
	$date2formatted = $today_dmY; 
	}


//Datum-Formatkonvertierungen
//Datumsauswahl in Formatierung für Datenbank konvertieren
if ($date1formatted != NULL)
	{
	$date1timestamp = strtotime($date1formatted);
	$date1_ISO8601 = date("Y-m-d", $date1timestamp);
	//Datumsauswahl zusätzlich in Anzeigeformat konvertieren
	$date1_display = date("d.m.y", $date1timestamp);
	}

if ($date2formatted != NULL)
	{
	$date2timestamp = strtotime($date2formatted);
	$date2_ISO8601 = date("Y-m-d", $date2timestamp);
	//Datumsauswahl zusätzlich in Anzeigeformat konvertieren
	$date2_display = date("d.m.y", $date2timestamp);
	}

//Nutzerfreundliche Anzeige des ausgewählten Zeitraums
$date1_dm = date("d.m", $date1timestamp); 
$date1_Y = date("Y", $date1timestamp); 
$date2_dm = date("d.m", $date2timestamp); 
$date2_Y = date("Y", $date2timestamp); 
//TODO: Prüfen, ob das tatsächlich das erste Messdatum ist/sein soll
$erstesMessdatum_timestamp = strtotime("2020-01-01");
$erstesMessdatum_dmY = date("d.m.Y", $erstesMessdatum_timestamp); 
$monthago_timestamp = strtotime("-1 month"); 
$monthago_dmY = date("d.m.Y", $monthago_timestamp);
$yearago_timestamp = strtotime("-1 year"); 
$yearago_dmY = date("d.m.Y", $yearago_timestamp);


if ($date1_dm == "01.01" && $date2_dm == "31.12")
	{
	if ($date2_Y - $date1_Y == 0)
		{
		$gewaehlterZeitraum = "Jahr " . $date2_Y;
		}
	elseif ($date2_Y > $date1_Y)
		{
		$gewaehlterZeitraum = $date1_Y . " - " . $date2_Y; 
		}
	else 
		{
		$gewaehlterZeitraum = $date1_display . " - " . $date2_display;
		}
	} 
elseif ($date2formatted == $today_dmY)
	{
	if ($date1formatted == "01.01.".$thisyear)
		{
		$gewaehlterZeitraum = "Dieses Jahr";
		}
	elseif ($date1formatted == $yearago_dmY)
		{
		$gewaehlterZeitraum = "Letzte 12 Monate";
		}
	elseif ($date1formatted == $erstesMessdatum_dmY)
		{
		$gewaehlterZeitraum = "Ges. Zeitraum"; 
		}

	elseif ($date1formatted == $monthago_dmY)
		{
		$gewaehlterZeitraum = "Letzter Monat"; 
		} 
		
	else 
		{
		$gewaehlterZeitraum = $date1_display . " - " . $date2_display;
		}
	}
else 
	{ 
	$gewaehlterZeitraum = $date1_display . " - " . $date2_display; 
	}

//Mengen geretteter Lebensmittel abfragen
$select_gesMenge = $db->prepare("SELECT SUM(BewegMenge) FROM Bestand_Bewegung WHERE LStatusKey='2'");
$erfolg = $select_gesMenge->execute(); 

$select_ZeitraumMenge = $db->prepare("SELECT SUM(BewegMenge) FROM Bestand_Bewegung WHERE LStatusKey='2' AND BewegDatum BETWEEN :date1 AND :date2");
$daten_zeitraum = array(
	"date1" => $date1_ISO8601, 
	"date2" => $date2_ISO8601 
	);
$select_ZeitraumMenge->execute($daten_zeitraum); 



//Fehlertest
if (!$erfolg) {
    $fehler = $select_gesMenge->errorInfo();
    die("Folgender Datenbankfehler ist aufgetreten:" . $fehler[2]);
}


//Mengen geretteter Lebensmittel anzeigefähig machen
$gesMenge = $select_gesMenge->fetchColumn();
$ZeitraumMenge = $select_ZeitraumMenge->fetchColumn();


/*
//SQL für Balkendiagramm (in Adminer getestet)
SELECT HerkunftName AS Herkunft, SUM(BewegMenge) AS KGfairteilt
FROM 
(SELECT Bestand_Bewegung.LMKey, BewegMenge, BewegDatum, HerkunftName
FROM (Bestand_Bewegung LEFT JOIN Lebensmittel ON Bestand_Bewegung.LMKey=Lebensmittel.LMKey)
LEFT JOIN HerkunftsKategorie ON Lebensmittel.HerkunftKey=HerkunftsKategorie.HerkunftKey
WHERE LStatusKey=2) AS FairteiltesHerk
GROUP BY HerkunftName
ORDER BY KGfairteilt DESC
*/


//TODO: Tabelle aus Query in relevante Variablen übertragen
//TODO: Prozentanteil jeder Kategorie ausrechnen (KGfairteilt/ZeitraumMenge*100)
//TODO: Herkunftsbezeichnungen (per Variablen) in absteigender Reihenfolge in HTML einfügen
//TODO: Zahlen per Variablen in richtiger Reihenfolge in HTML einfügen (deutsch formatiert)
//TODO: ausgewählten Zeitraum bei Rückkehr zu Dashboard an Dashboard übergeben?


?>

<script>
//Datumsauswahl an JavaScript übergeben
var gotdate1 = "<?php echo $date1_ISO8601; ?>";
var gotdate2 = "<?php echo $date2_ISO8601; ?>";

// Test:
//alert("date1="+gotdate1+" & date2="+gotdate2);

</script> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Foodsharing Statistics</title>
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@200;300;400;500;600;700;800;900&family=Londrina+Solid&display=swap" rel="stylesheet" />
  <!-- CSS Stylesheet -->
  <link rel="stylesheet" href="../css/interne_wirkungsmessung.css"/> 
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
</head>

<body>
  <div class="container">
    <!-- Header -->  
    <header>
      <h1 class="font-londrina">MENGE GERETTETER LEBENSMITTEL</h1>
      <div class="header-btn">
      <div class="font-fira header-btn-title">
        <p>Zeitraum</p>
      </div>  
      <div class="time-button" >
        <a href='08_interne_wirkungsmessung_zeitraum_waehlen.php?camefrom=herkunft' class="link-button">  
          <i class='fa fa-clock-o'  style="font-size:30px;"></i>
          <div class="button-text">
            <p class="font-fira"><?php echo $gewaehlterZeitraum ?></p>
          </div>
        </a>
      </div>
      </div>
    </header>

    <!-- Hauptinhalt -->  
    <div class="content" style="position: relative;">

        <div class="balkendiagram-container">
            <!-- Button close the page -->  
<!-- TODO: Link zu Dashboard einfügen -->
            <a href=""><div class="button-previous-page"></div></a>

            <!-- Titel -->  
            <div class="font-fira">
                <h2>Lebensmittel nach Herkunft</h2>
            </div>
            <!-- Bar Diagram --> 
            <div class="category-list">
                <table class="balkendiagram-content" >
                    <tr>
                        <!-- Category name --> 
                        <td class="font-fira category-name">Supermarkt</td>
                        <!-- Bar and info --> 
                        <td class="bar">
                            <div class="bar-container">
                                <div class="category-bar" id="bar1" data-percentage="34.6"></div>
                                <div class="font-fira percentage">36,6% (5.900 Kg)</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <!-- Category name -->
                        <td class="font-fira category-name">Bäckerei</td>
                        <!-- Bar and info --> 
                        <td class="bar">
                            <div class="bar-container">
                                <div class="category-bar" id="bar2" data-percentage="23.1"></div>
                                <div class="font-fira percentage">23,1% (3.200 Kg)</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <!-- Category name -->
                        <td class="font-fira category-name">Wochenmarkt</td>
                        <!-- Bar and info --> 
                        <td class="bar">
                            <div class="bar-container">
                                <div class="category-bar" id="bar3" data-percentage="15.4"></div>
                                <div class="font-fira percentage">15,4% (2.400 Kg)</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <!-- Category name -->
                        <td class="font-fira category-name">Gastronomie</td>
                        <!-- Bar and info --> 
                        <td class="bar">
                            <div class="bar-container">
                                <div class="category-bar" data-percentage="11.5"></div>
                                <div class="font-fira percentage">11,5% (1.900 Kg)</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <!-- Category name -->
                        <td class="font-fira category-name">Haushalt</td>
                        <!-- Bar and info --> 
                        <td class="bar">
                            <div class="bar-container">
                                <div class="category-bar" data-percentage="7.7"></div>
                                <div class="font-fira percentage">7,7% (1.400 Kg)</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <!-- Category name -->
                        <td class="font-fira category-name">Veranstaltung</td>
                        <!-- Bar and info --> 
                        <td class="bar">
                            <div class="bar-container">
                                <div class="category-bar" data-percentage="3.9"></div>
                                <div class="font-fira percentage">3,9% (750 Kg)</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <!-- Category name -->
                        <td class="font-fira category-name">Tankstelle</td>
                        <!-- Bar and info --> 
                        <td class="bar">
                            <div class="bar-container">
                                <div class="category-bar" data-percentage="3.7"></div>
                                <div class="font-fira percentage">3,7% (725 Kg)</div>
                            </div>
                        </td>
                    </tr>
                </table>
                
            </div>
        </div>
    </div>
        <!-- Footer --> 
        <div class="footer-fixed">
          <div class="footer-btn font-fira">
<!-- TODO: Link zu Dashboard einfügen -->
            <a href="#" class="cancel-button">Zurück</a>
          </div>
          <div class="footer-btn font-fira">
<!-- TODO: Besprechen, ob der entfernt werden kann? -->
            <a href='#' class="next-button">Export als CSV-Datei</a>
          </div>
        </div>
  </div>
</body>
<script>
    //Get first bar and calculate the width of each next bar
    const firstBar = document.getElementById("bar1");
    const firstPercent = firstBar.getAttribute("data-percentage");
    const firstBarWidth = firstBar.clientWidth;

    const categoryBars = document.querySelectorAll('.category-bar');
    document.querySelectorAll(".category-bar").forEach(bar => {
    if (bar !== firstBar) {
        const percent = bar.getAttribute("data-percentage");
        const width = (firstBarWidth / firstPercent ) * percent;
        bar.style.width = `${width}px`;
    }
    });
</script>

</html>