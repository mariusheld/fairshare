<!-- ----------- PHP ---------- -->
<?php
session_start();
//Datenbankverbindung aufbauen
require_once("../dbconnect/dbconnect.inc.php");
$db_handle = new DBController();
$conn = $db_handle->connectDB();

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



if ($gesMenge < 1000)
	{
	$anzeigeeinheit_ges = "KILO";
	//Deutsche Zahlendarstellung (Komma vor Dezimalstellen)
	$gesMenge_display = number_format($gesMenge,1,",",".");
	}
else 
	{
	$anzeigeeinheit_ges = "TONNEN";
	$gesMenge_display = number_format($gesMenge/1000,1,",",".");
	}


if ($ZeitraumMenge < 1000)
	{
	$anzeigeeinheit_zeitraum = "KILO";
	//Deutsche Zahlendarstellung (Komma vor Dezimalstellen)
	$ZeitraumMenge_display = number_format($ZeitraumMenge,1,",",".");
	}
else 
	{
	$anzeigeeinheit_zeitraum = "TONNEN";
	$ZeitraumMenge_display = number_format($ZeitraumMenge/1000,1,",",".");
	}

// ---- Menge Lebensmittel nach Kategorien
$KategorieQuery = "SELECT SUM(BB.BewegMenge) AS menge, OK.OKatName AS katname, OK.OKatKey AS katkey
      FROM Bestand_Bewegung BB
      INNER JOIN Lebensmittel L ON BB.LMkey = L.LMkey
      INNER JOIN OberKategorie OK ON L.OKatKey = OK.OKatKey
      WHERE LStatusKey='2'
      AND BewegDatum BETWEEN '$date1_ISO8601' AND '$date2_ISO8601'
      GROUP BY OK.OKatName
      ORDER BY SUM(BB.BewegMenge) DESC";
$KategorieResult = mysqli_query($conn, $KategorieQuery);
while ($row = mysqli_fetch_assoc($KategorieResult)) {
$kategorieresultset[] = $row;
}

$kategorien = $kategorieresultset;

// ---- KategorieIcons 
$kat_icon_4 = '../media/kategorien/icon_backwaren-salzig.svg';
$kat_icon_3 = '../media/kategorien/icon_backwaren-suess.svg';
$kat_icon_1 = '../media/kategorien/icon_gemuese.svg';
$kat_icon_7 = '../media/kategorien/icon_konserven.svg';
$kat_icon_6 = '../media/kategorien/icon_kuehlprodukte.svg';
$kat_icon_2 = '../media/kategorien/icon_obst.svg';
$kat_icon_5 = '../media/kategorien/icon_trockenprodukte.svg';
$kat_icon_8 = '../media/kategorien/sonstiges.svg';


// ---- Menge Lebensmittel nach Herkunft

$HerkunftQuery = "SELECT SUM(BB.BewegMenge) AS menge, HK.HerkunftName AS herkunftname
      FROM Bestand_Bewegung BB
      INNER JOIN Lebensmittel L ON BB.LMkey = L.LMkey
      INNER JOIN HerkunftsKategorie HK ON L.HerkunftKey = HK.HerkunftKey
      WHERE LStatusKey='2'
      AND BewegDatum BETWEEN '$date1_ISO8601' AND '$date2_ISO8601'
      GROUP BY HK.HerkunftName
      ORDER BY SUM(BB.BewegMenge) DESC";
$HerkunftResult = mysqli_query($conn, $HerkunftQuery);
while ($row = mysqli_fetch_assoc($HerkunftResult)) {
$herkunftresultset[] = $row;
}

$herkunft = $herkunftresultset;

?>



<script>
//Datumsauswahl an JavaScript übergeben
var gotdate1 = "<?php echo $date1_ISO8601; ?>";
var gotdate2 = "<?php echo $date2_ISO8601; ?>";

var erstesMessdatum = "<?php echo $erstesMessdatum_dmY; ?>";
var date1formatted = "<?php echo $date1formatted; ?>";

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
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
  <!-- JS Scripts -->  
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <div class="container">
    <!-- Header -->  
    <header>
      <h1 class="font-londrina">MENGE GERETTETER LEBENSMITTEL</h1>
      <div class="header-btn">
        <div class="logout" id="logout">
            <p class="logouttext font-fira">
                Abmelden
            </p>
            <img alt="ausloggen" src="../media/lock_icon.svg" width="48" height="48" />
        </div>
      </div>
    </header>

    <!-- Hauptinhalt -->  
    <div class="content flx-container">
        <!-- Erste Spalte -->  
        <div class="column-1">
          <!--Info Blocks -->  
          <div class="flx-container">
            <div class="block">
              <p class="font-fira description">Insgesamt gerettete Lebensmittel:</p>
              <h1 class="font-londrina number"> <?php echo $gesMenge_display . " " . $anzeigeeinheit_ges ?></h1>
            </div>
            <div class="block" >
              <p class="font-fira description">Im ausgewählten Zeitraum gerettete Lebensmittel:</p>

              <h1 class="font-londrina number"> <?php echo $ZeitraumMenge_display . " " . $anzeigeeinheit_zeitraum ?> </h1>
            </div>
          </div>
          <!-- Chart -->  
          <div class="block bg-block">
            <p class="font-fira description"><strong>Nach Zeitraum (in kg)</strong></p>
            <div class="chart-container">
              <canvas id="myChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Zweite Spalte -->  
        <div class="column-2">
          <!-- Block für Kategorien -->  
          <a href="<?php echo '08_interne_wirkungsmessung_kategorien.php?date1=' . $date1formatted . '&date2=' . $date2formatted; ?>">
            <div class="block flx-container-sm">
              <!-- Liste der Kategorien -->  
              <div style="display: inline;">
                <p class="font-fira description"><strong>Nach Kategorien</strong></p>
                <div class="font-fira list-category">
                  <table>
                    <?php
                      if(is_array($kategorien)){
                        foreach($kategorien as $key => $row){
                          $menge = (float) $row['menge'];
                          $menge_ges = (float) $gesMenge;
                          // $menge_ges = 24;
                          $prozent = $menge / $menge_ges * 100;
                          if($key < 3){
                            ?>
                            <tr>
                              <td style="text-align: right;"><?php echo round($prozent) ?>%</td>
                              <td>&nbsp&nbsp&nbsp<?php echo $row['katname'] ?></td>
                            </tr>
                          <?php
                          } else {
                            ?>
                            <tr class="list-category-sml">
                              <td style="text-align: right;"><?php echo round($prozent) ?>%</td>
                              <td>&nbsp&nbsp&nbsp<?php echo $row['katname'] ?></td>
                            </tr>
                          <?php
                          }
                        }
                      }
                    ?>
                  </table>
                </div>
              </div>
              <!-- Bilder der Kategorien --> 
              <div class="img-block">
                <img src="<?php if($kategorien[0]['katkey'] == 1){
                                    echo $kat_icon_1;
                                } else if($kategorien[0]['katkey'] == 2){
                                    echo $kat_icon_2;
                                } else if($kategorien[0]['katkey'] == 3){
                                  echo $kat_icon_3;
                                } else if($kategorien[0]['katkey'] == 4){
                                  echo $kat_icon_4;
                                } else if($kategorien[0]['katkey'] == 5){
                                  echo $kat_icon_5;
                                } else if($kategorien[0]['katkey'] == 6){
                                  echo $kat_icon_6;
                                } else if($kategorien[0]['katkey'] == 7){
                                  echo $kat_icon_7;
                                } else if($kategorien[0]['katkey'] == 8){
                                  echo $kat_icon_8;
                                }?>" class="img-category" style="width: 70px;"><br>
                <img src="<?php if($kategorien[1]['katkey'] == 1){
                                    echo $kat_icon_1;
                                } else if($kategorien[1]['katkey'] == 2){
                                    echo $kat_icon_2;
                                } else if($kategorien[1]['katkey'] == 3){
                                  echo $kat_icon_3;
                                } else if($kategorien[1]['katkey'] == 4){
                                  echo $kat_icon_4;
                                } else if($kategorien[1]['katkey'] == 5){
                                  echo $kat_icon_5;
                                } else if($kategorien[1]['katkey'] == 6){
                                  echo $kat_icon_6;
                                } else if($kategorien[1]['katkey'] == 7){
                                  echo $kat_icon_7;
                                } else if($kategorien[1]['katkey'] == 8){
                                  echo $kat_icon_8;
                                }?>" class="img-category" style="width: 50px;"><br>
                <img src="<?php if($kategorien[2]['katkey'] == 1){
                                    echo $kat_icon_1;
                                } else if($kategorien[2]['katkey'] == 2){
                                    echo $kat_icon_2;
                                } else if($kategorien[2]['katkey'] == 3){
                                  echo $kat_icon_3;
                                } else if($kategorien[2]['katkey'] == 4){
                                  echo $kat_icon_4;
                                } else if($kategorien[2]['katkey'] == 5){
                                  echo $kat_icon_5;
                                } else if($kategorien[2]['katkey'] == 6){
                                  echo $kat_icon_6;
                                } else if($kategorien[2]['katkey'] == 7){
                                  echo $kat_icon_7;
                                } else if($kategorien[2]['katkey'] == 8){
                                  echo $kat_icon_8;
                                }?>" class="img-category" style="width: 40px;">
              </div> 
            </div>
          </a> 
          <!-- Liste der Herkünfte --> 
          <a href="<?php echo '08_interne_wirkungsmessung_herkunft.php?date1=' . $date1formatted . '&date2=' . $date2formatted; ?>">
            <div class="block" style="min-height: 225px;">
              <div style="display: inline-block;">
                <p class="font-fira description" style="float:left;"><strong>Nach Herkunft</strong></p>
                <div class="font-fira list-category">
                  <table>
                    <?php
                      if(is_array($herkunft)){
                        foreach($herkunft as $key => $row){
                          $menge = (float) $row['menge'];
                          $menge_ges = (float) $gesMenge;
                          // $menge_ges = 24;
                          $prozent = $menge / $menge_ges * 100;
                          if($key < 3){
                            ?>
                            <tr>
                              <td style="text-align: right;"><?php echo round($prozent) ?>%</td>
                              <td>&nbsp&nbsp&nbsp<?php echo $row['herkunftname'] ?></td>
                            </tr>
                          <?php
                          } else {
                            ?>
                            <tr class="list-category-sml">
                              <td style="text-align: right;"><?php echo round($prozent) ?>%</td>
                              <td>&nbsp&nbsp&nbsp<?php echo $row['herkunftname'] ?></td>
                            </tr>
                          <?php
                          }
                        }
                      }
                    ?>
                  </table>
                </div>
              </div>
            </div>
          </a>
        </div>
    </div>
        <!-- Footer --> 
        <div class="action-container">
          <div class="time-button" >
            <a href='<?php echo '08_interne_wirkungsmessung_zeitraum_waehlen.php?date1=' . $date1formatted . '&date2=' . $date2formatted . '&camefrom=dashboard'; ?>' class="link-button">  
              <i class='fa fa-clock-o'  style="font-size:34px; color: #99BB44;"></i>
              <div class="button-text">
                <p class="font-fira"><?php echo $gewaehlterZeitraum ?></p>
              </div>
            </a>
          </div>
          <div class="footer-btn font-fira">
            <a href='#' class="next-button">Export als CSV-Datei</a>
          </div>
        </div>
  </div>
</body>
<script>
  var ctx = document.getElementById('myChart').getContext('2d');
  var myChart = new Chart(ctx, {
      type: 'line',
      data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], //Labels
          datasets: [{
              label: null,
              data: [12, 19, 3, 5, 2, 3, 20, 33, 23, 12, 33, 10], //Data für Liniendiagram
              pointRadius: 0,  // Keine Punkte anzeigen
              backgroundColor: 'rgb(153,187,68, 1)',
              borderColor: 'rgb(153,187,68, 1)',
              borderWidth: 5
          }]
      },
      options: { 
        scaleShowVerticalLines: false,
        aspectRatio: 2.4,
        indexAxis: 'x',
        scales: {
          x: {
            beginAtZero: true,
            grid: {
              display: false
            },
            ticks: {
              font: {
                  family: 'Fira Sans',
                  size: 14,
              },
              color: 'black'
          }
          },
          y: {
            ticks: {
              font: {
                  family: 'Fira Sans',
                  size: 14,
              },
              color: 'black'
          }
          }
        },
          plugins: {
            legend: {
                display: false
            },
        }
      }
      
  });
</script>

</html>