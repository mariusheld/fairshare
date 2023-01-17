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
//TODO: ggf. Gesamtmenge hier gar nicht abfragen
$select_gesMenge = $db->prepare("SELECT SUM(BewegMenge) FROM Bestand_Bewegung WHERE LStatusKey='2'");
$erfolg = $select_gesMenge->execute(); 

$select_ZeitraumMenge = $db->prepare("SELECT SUM(BewegMenge) FROM Bestand_Bewegung WHERE LStatusKey='2' AND BewegDatum BETWEEN :date1 AND :date2");
$daten_zeitraum = array(
	"date1" => $date1_ISO8601, 
	"date2" => $date2_ISO8601 
	);
$select_ZeitraumMenge->execute($daten_zeitraum); 


//Mengen geretteter Lebensmittel im Zeitraum nach Herkunft 
$select_HerkunftMengen = $db->prepare("SELECT HerkunftName AS Herkunft, SUM(BewegMenge) AS KGfairteilt, ROW_NUMBER() OVER(ORDER BY KGfairteilt DESC) AS RowNumber
		FROM (SELECT Bestand_Bewegung.LMKey, BewegMenge, BewegDatum, HerkunftName 
		FROM (Bestand_Bewegung LEFT JOIN Lebensmittel ON Bestand_Bewegung.LMKey=Lebensmittel.LMKey)
		LEFT JOIN HerkunftsKategorie ON Lebensmittel.HerkunftKey=HerkunftsKategorie.HerkunftKey
		WHERE LStatusKey=2 AND BewegDatum BETWEEN :date1 AND :date2) AS FairteiltesHerk
		GROUP BY HerkunftName
		ORDER BY KGfairteilt DESC");
$select_HerkunftMengen->execute($daten_zeitraum);


// Fehlerüberprüfung
if ($select_HerkunftMengen == false) 
	{
	$fehler = $db->errorInfo();
	die("Folgender Datenbankfehler ist aufgetreten: " . $fehler[2]);
	}

//Mengen geretteter Lebensmittel anzeigefähig machen
$gesMenge = $select_gesMenge->fetchColumn();
$ZeitraumMenge = $select_ZeitraumMenge->fetchColumn();


// Ergebnistabelle aus dem PDO auslesen und als assoz. Feld $KategorienMengen bereitstellen 
$HerkunftMengen = $select_HerkunftMengen->fetchAll();

//Variablen für das Balkendiagramm
$B1hidden = "hidden";
$B2hidden = "hidden";
$B3hidden = "hidden";
$B4hidden = "hidden";
$B5hidden = "hidden";
$B6hidden = "hidden";
$B7hidden = "hidden";

foreach ($HerkunftMengen as $NachHerkunft) 
{
	if ($NachHerkunft['RowNumber'] == "1") 
		{
		if ($NachHerkunft['Herkunft'] != NULL)
			{
			$Herkunft1Name =$NachHerkunft['Herkunft'];
			}
		else
			{
			$Herkunft1Name ="Unbekannt";
			}
		$Herkunft1kg = $NachHerkunft['KGfairteilt'];
		$B1hidden = "";
		
		}
	if ($NachHerkunft['RowNumber'] == "2") 
		{
		if ($NachHerkunft['Herkunft'] != NULL)
			{
			$Herkunft2Name =$NachHerkunft['Herkunft'];
			}
		else
			{
			$Herkunft2Name ="Unbekannt";
			}
		$Herkunft2kg = $NachHerkunft['KGfairteilt'];
		$B2hidden = "";
		}
	if ($NachHerkunft['RowNumber'] == "3") 
		{
		if ($NachHerkunft['Herkunft'] != NULL)
			{
			$Herkunft3Name =$NachHerkunft['Herkunft'];
			}
		else
			{
			$Herkunft3Name ="Unbekannt";
			}
		$Herkunft3kg = $NachHerkunft['KGfairteilt'];
		$B3hidden = "";
		}
	if ($NachHerkunft['RowNumber'] == "4") 
		{
		if ($NachHerkunft['Herkunft'] != NULL)
			{
			$Herkunft4Name =$NachHerkunft['Herkunft'];
			}
		else
			{
			$Herkunft4Name ="Unbekannt";
			}
		$Herkunft4kg = $NachHerkunft['KGfairteilt'];
		$B4hidden = "";
		}
	if ($NachHerkunft['RowNumber'] == "5") 
		{
		if ($NachHerkunft['Herkunft'] != NULL)
			{
			$Herkunft5Name =$NachHerkunft['Herkunft'];
			}
		else
			{
			$Herkunft5Name ="Unbekannt";
			}
		$Herkunft5kg = $NachHerkunft['KGfairteilt'];
		$B5hidden = "";
		}
	if ($NachHerkunft['RowNumber'] == "6") 
		{
		if ($NachHerkunft['Herkunft'] != NULL)
			{
			$Herkunft6Name =$NachHerkunft['Herkunft'];
			}
		else
			{
			$Herkunft6Name ="Unbekannt";
			}
		$Herkunft6kg = $NachHerkunft['KGfairteilt'];
		$B6hidden = "";
		}
	if ($NachHerkunft['RowNumber'] == "7") 
		{
		if ($NachHerkunft['Herkunft'] != NULL)
			{
			$Herkunft7Name =$NachHerkunft['Herkunft'];
			}
		else
			{
			$Herkunft7Name ="Unbekannt";
			}
		$Herkunft7kg = $NachHerkunft['KGfairteilt'];
		$B7hidden = "";
		}	
				
}
	
//Zahlen in deutsche Formatierung konvertieren

//TODO: Fix! Wenn Zeitraum ausgewählt, in dem keine LM gerettet, Division by zero Error

$Herkunft1kg_display = number_format($Herkunft1kg,1,",",".");
$Herkunft1Prozent = $Herkunft1kg/$ZeitraumMenge *100; 
$Herkunft1Prozent_display = number_format($Herkunft1Prozent,1,",",".");	

$Herkunft2kg_display = number_format($Herkunft2kg,1,",",".");
$Herkunft2Prozent = $Herkunft2kg/$ZeitraumMenge *100;
$Herkunft2Prozent_display = number_format($Herkunft2Prozent,1,",",".");	

$Herkunft3kg_display = number_format($Herkunft3kg,1,",",".");
$Herkunft3Prozent = $Herkunft3kg/$ZeitraumMenge *100;
$Herkunft3Prozent_display = number_format($Herkunft3Prozent,1,",",".");	

$Herkunft4kg_display = number_format($Herkunft4kg,1,",",".");
$Herkunft4Prozent = $Herkunft4kg/$ZeitraumMenge *100;
$Herkunft4Prozent_display = number_format($Herkunft4Prozent,1,",",".");	

$Herkunft5kg_display = number_format($Herkunft5kg,1,",",".");
$Herkunft5Prozent = $Herkunft5kg/$ZeitraumMenge *100;
$Herkunft5Prozent_display = number_format($Herkunft5Prozent,1,",",".");	

$Herkunft6kg_display = number_format($Herkunft6kg,1,",",".");
$Herkunft6Prozent = $Herkunft6kg/$ZeitraumMenge *100;
$Herkunft6Prozent_display = number_format($Herkunft6Prozent,1,",",".");	

$Herkunft7kg_display = number_format($Herkunft7kg,1,",",".");
$Herkunft7Prozent = $Herkunft7kg/$ZeitraumMenge *100;
$Herkunft7Prozent_display = number_format($Herkunft7Prozent,1,",",".");
	

/*
//SQL für Balkendiagramm (in Adminer getestet)
SELECT HerkunftName AS Herkunft, SUM(BewegMenge) AS KGfairteilt, ROW_NUMBER() OVER(ORDER BY KGfairteilt DESC) AS RowNumber
FROM 
(SELECT Bestand_Bewegung.LMKey, BewegMenge, BewegDatum, HerkunftName
FROM (Bestand_Bewegung LEFT JOIN Lebensmittel ON Bestand_Bewegung.LMKey=Lebensmittel.LMKey)
LEFT JOIN HerkunftsKategorie ON Lebensmittel.HerkunftKey=HerkunftsKategorie.HerkunftKey
WHERE LStatusKey=2) AS FairteiltesHerk
GROUP BY HerkunftName
ORDER BY KGfairteilt DESC
*/


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
        <a href='<?php echo '08_interne_wirkungsmessung_zeitraum_waehlen.php?date1=' . $date1formatted . '&date2=' . $date2formatted . '&camefrom=herkunft'; ?>' class="link-button">  
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

            <a href="<?php echo '08_interne_wirkungsmessung_dashboard.php?date1=' . $date1formatted . '&date2=' . $date2formatted; ?>"><div class="button-previous-page"></div></a>

            <!-- Titel -->  
            <div class="font-fira">
                <h2>Lebensmittel nach Herkunft</h2>
            </div>
            <!-- Bar Diagram --> 
              
            <div class="category-list">
                <table class="balkendiagram-content" >
                    <tr <?php echo $B1hidden; ?> >
                        <!-- Category name --> 
                        <td class="font-fira category-name"><?php echo $Herkunft1Name; ?></td>
                        <!-- Bar and info --> 
                        <td class="bar">
                            <div class="bar-container">
                                <div class="category-bar" id="bar1" data-percentage="<?php echo $Herkunft1Prozent; ?>"></div>
                                <div class="font-fira percentage"><?php echo $Herkunft1Prozent_display; ?>% (<?php echo $Herkunft1kg_display; ?> Kg)</div>
                            </div>
                        </td>
                    </tr>
                    <tr <?php echo $B2hidden; ?>>
                        <!-- Category name -->
                        <td class="font-fira category-name"><?php echo $Herkunft2Name; ?></td>
                        <!-- Bar and info --> 
                        <td class="bar">
                            <div class="bar-container">
                                <div class="category-bar" id="bar2" data-percentage="<?php echo $Herkunft2Prozent; ?>"></div>
                                <div class="font-fira percentage"><?php echo $Herkunft2Prozent_display; ?>% (<?php echo $Herkunft2kg_display; ?> Kg)</div>
                            </div>
                        </td>
                    </tr>
                    <tr <?php echo $B3hidden; ?>>
                        <!-- Category name -->
                        <td class="font-fira category-name"><?php echo $Herkunft3Name; ?></td>
                        <!-- Bar and info --> 
                        <td class="bar">
                            <div class="bar-container">
                                <div class="category-bar" id="bar3" data-percentage="<?php echo $Herkunft3Prozent; ?>"></div>
                                <div class="font-fira percentage"><?php echo $Herkunft3Prozent_display; ?>% (<?php echo $Herkunft3kg_display; ?> Kg)</div>
                            </div>
                        </td>
                    </tr>
                    <tr <?php echo $B4hidden; ?>>
                        <!-- Category name -->
                        <td class="font-fira category-name"><?php echo $Herkunft4Name; ?></td>
                        <!-- Bar and info --> 
                        <td class="bar">
                            <div class="bar-container">
                                <div class="category-bar" data-percentage="<?php echo $Herkunft4Prozent; ?>"></div>
                                <div class="font-fira percentage"><?php echo $Herkunft4Prozent_display; ?>% (<?php echo $Herkunft4kg_display; ?> Kg)</div>
                            </div>
                        </td>
                    </tr>
                    <tr <?php echo $B5hidden; ?>>
                        <!-- Category name -->
                        <td class="font-fira category-name"><?php echo $Herkunft5Name; ?></td>
                        <!-- Bar and info --> 
                        <td class="bar">
                            <div class="bar-container">
                                <div class="category-bar" data-percentage="<?php echo $Herkunft5Prozent; ?>"></div>
                                <div class="font-fira percentage"><?php echo $Herkunft5Prozent_display; ?>% (<?php echo $Herkunft5kg_display; ?> Kg)</div>
                            </div>
                        </td>
                    </tr>
                    <tr <?php echo $B6hidden; ?>>
                        <!-- Category name -->
                        <td class="font-fira category-name"><?php echo $Herkunft6Name; ?></td>
                        <!-- Bar and info --> 
                        <td class="bar">
                            <div class="bar-container">
                                <div class="category-bar" data-percentage="<?php echo $Herkunft6Prozent; ?>"></div>
                                <div class="font-fira percentage"><?php echo $Herkunft6Prozent_display; ?>% (<?php echo $Herkunft6kg_display; ?> Kg)</div>
                            </div>
                        </td>
                    </tr>
                    <tr <?php echo $B7hidden; ?>>
                        <!-- Category name -->
                        <td class="font-fira category-name"><?php echo $Herkunft7Name; ?></td>
                        <!-- Bar and info --> 
                        <td class="bar">
                            <div class="bar-container">
                                <div class="category-bar" data-percentage="<?php echo $Herkunft7Prozent; ?>"></div>
                                <div class="font-fira percentage"><?php echo $Herkunft7Prozent_display; ?>% (<?php echo $Herkunft7kg_display; ?> Kg)</div>
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
            <a href="<?php echo '08_interne_wirkungsmessung_dashboard.php?date1=' . $date1formatted . '&date2=' . $date2formatted; ?>" class="cancel-button">Zurück</a>
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