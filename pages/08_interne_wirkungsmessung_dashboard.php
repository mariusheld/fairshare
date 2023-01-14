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

//TODO: Was als Default anzeigen, wenn noch kein Zeitraum ausgewählt wurde?

//Datumsauswahl in Formatierung für Datenbank konvertieren

$thisyear = date("Y"); 
$today = date("d.m.Y");

if ($date1formatted == NULL && $date2formatted == NULL)
	{
	$date1formatted = "01.01.".$thisyear;
	$date2formatted = $today; 
	}


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

//Mengen geretteter Lebensmittel abfragen
$select_gesMenge = $db->prepare("SELECT SUM(BewegMenge) FROM Bestand_Bewegung WHERE LStatusKey='2'");
$erfolg = $select_gesMenge->execute(); 



$select_ZeitraumMenge = $db->prepare("SELECT SUM(BewegMenge) FROM Bestand_Bewegung WHERE LStatusKey='2' AND BewegDatum BETWEEN :date1 AND :date2");
$daten_zeitraum = array(
	"date1" => $date1_ISO8601, 
	"date2" => $date2_ISO8601 
);
$select_ZeitraumMenge->execute($daten_zeitraum); 


//SELECT SUM(BewegMenge) FROM Bestand_Bewegung WHERE LStatusKey=2 AND BewegDatum BEWETEN (('date1')) AND (('date2'))



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
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
  <!-- JS Scripts -->  
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <div class="container">
    <!-- Header -->  
    <header>
      <a>
        <h1 class="font-londrina">MENGE GERETTETER LEBENSMITTEL</h1>
      </a>
      <div class="header-btn">
      <div class="font-fira header-btn-title">
        <p>Zeitraum</p>
      </div>  
      <div class="time-button" >
        <a href='08_interne_wirkungsmessung_zeitraum_waehlen.php?camefrom=dashboard' class="link-button">  
          <i class='fa fa-clock-o'  style="font-size:30px;"></i>
          <div class="button-text">
<!-- TODO: Zeitraum-Benennungen (Jahr 2022 (u.ä.), letzte 12 Monate, letzter Monat, Gesamter Zeitraum) -->          
            <p class="font-fira"><?php echo $date1_display . " - " . $date2_display; ?></p>
          </div>
        </a>
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
<!-- TODO: Zahl aus Db holen und anzeigen lassen -->
<!-- SQL: SELECT SUM(BewegMenge) FROM Bestand_Bewegung WHERE LStatusKey=2 AND BewegDatum BEWETEN (('date1')) AND (('date2'))-->

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
          <div class="block flx-container">
            <!-- Liste der Kategorien -->  
            <div style="display: inline;">
              <p class="font-fira description"><strong>Nach Kategorien</strong></p>
              <div class="font-fira list-category">
                <table>
                  <tr>
                    <td style="text-align: right;">50%</td>
                    <td>&nbsp&nbsp&nbspBackwaren (salzig)</td>
                  </tr>
                  <tr>
                    <td style="text-align: right;">30%</td>
                    <td>&nbsp&nbsp&nbspBackwaren (süß)</td>
                  </tr>
                  <tr>
                    <td style="text-align: right;">5%</td>
                    <td>&nbsp&nbsp&nbspObst</td>
                  </tr>
                  <tr class="list-category-sml">
                    <td  style="text-align: right;">11%</td>
                    <td>&nbsp&nbsp&nbspGemüse</td>
                  </tr>
                  <tr class="list-category-sml">
                    <td style="text-align: right;">7%</td>
                    <td>&nbsp&nbsp&nbspTrockenprodukte</td>
                  </tr>
                  <tr class="list-category-sml">
                    <td style="text-align: right;">4%</td>
                    <td>&nbsp&nbsp&nbspMilchprodukte</td>
                  </tr>
                  <tr class="list-category-sml">
                    <td style="text-align: right;">2%</td>
                    <td>&nbsp&nbsp&nbspKonserven</td>
                  </tr>
                  <tr class="list-category-sml">
                    <td style="text-align: right;">2%</td>
                    <td>&nbsp&nbsp&nbspSonstiges</td>
                  </tr>
                </table>
              </div>
            </div> 
            <!-- Bilder der Kategorien --> 
            <div class="img-block">
              <img src="../media/kategorien/icon_backwaren-salzig.svg" class="img-category" style="width: 80px;"><br>
              <img src="../media/kategorien/icon_backwaren-suess.svg" class="img-category" style="width: 60px;"><br>
              <img src="../media/kategorien/icon_obst.svg" class="img-category" style="width: 40px;">
            </div> 
          </div> 
          <!-- Liste der Kategorien --> 
          <div class="block" style="height: 267px;">
            <div style="display: inline-block;">
              <p class="font-fira description" style="float:left; margin-bottom: 30px;"><strong>Nach Herkunft</strong></p>
              <div class="font-fira list-category">
                <table>
                  <tr>
                    <td style="text-align: right;">29%</td>
                    <td>&nbsp&nbsp&nbspBäckerei</td>
                  </tr>
                  <tr>
                    <td style="text-align: right;">22%</td>
                    <td>&nbsp&nbsp&nbspSupermarkt</td>
                  </tr>
                  <tr>
                    <td style="text-align: right;">18%</td>
                    <td>&nbsp&nbsp&nbspWochenmarkt</td>
                  </tr>
                  <tr class="list-category-sml">
                    <td  style="text-align: right;">11%</td>
                    <td>&nbsp&nbsp&nbspGastronomie</td>
                  </tr>
                  <tr class="list-category-sml">
                    <td style="text-align: right;">7%</td>
                    <td>&nbsp&nbsp&nbspVeranstaltung</td>
                  </tr>
                  <tr class="list-category-sml">
                    <td style="text-align: right;">4%</td>
                    <td>&nbsp&nbsp&nbspHaushalt</td>
                  </tr>
                  <tr class="list-category-sml">
                    <td style="text-align: right;">2%</td>
                    <td>&nbsp&nbsp&nbspTankstelle</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>
    </div>
        <!-- Footer --> 
        <div class="action-container">
          <div class="footer-btn font-fira">
            <a href="#" class="cancel-button">Lagerübersicht</a>
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
        aspectRatio: 2.2,
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