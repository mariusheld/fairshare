<!-- --------- PHP --------- -->

<?php
session_start();
//Datenbankverbindung aufbauen
require_once("../dbconnect/dbconnect.inc.php");
$db_handle = new DBController();


//Auslesen, von welcher Seite die Zeitraumauswahl aufgerufen wurde
$camefrom = $_GET['camefrom'];
if (!$camefrom)
	{
	//für erstes Testing
	$camefrom = "zeitraum";
	
	//TODO: sobald Dashboard fertig ist, nur noch das hier
	//$camefrom = "dashboard";
	}


	if ($camefrom == "dashboard") 
		{
		$filterleadsto = "08_interne_wirkungsmessung_dashboard.php";
		}
	if ($camefrom == "herkunft")
		{
		$filterleadsto = "08_interne_wirkungsmessung_herkunft.php";
		}


//TODO: entfernen, sobald Dashboard fertig ist
	if ($camefrom == "zeitraum")
		{
		$filterleadsto = "08_interne_wirkungsmessung_zeitraum_waehlen.php";
		}
	

//TODO: Link zu dieser Seite in Dashboard und Balkendiagramm einbauen & camefrom mitgeben

//TODO: Code für Datumsauslesung und Konvertierung in Dashboard und Balkendiagramm übernehmen

//Datumsauswahl auslesen
$date1formatted = $_GET['date1'];
$date2formatted = $_GET['date2'];

//Datumsauswahl in Formatierung für Datenbank konvertieren
$date1timestamp = strtotime($date1formatted);
$date2timestamp = strtotime($date2formatted);

$date1_ISO8601 = date("Y-m-d", $date1timestamp);
$date2_ISO8601 = date("Y-m-d", $date2timestamp);


//Aktuelles Jahr
$thisyear = date("Y"); 
$lastyear = $thisyear -1; 

?>

<script>
//Datumsauswahl an JavaScript übergeben
var gotdate1 = "<?php echo $date1_ISO8601; ?>";
var gotdate2 = "<?php echo $date2_ISO8601; ?>";
// Test:
//alert("date1="+gotdate1+" & date2="+gotdate2);

//Herkunftsseite an JavaScript übergeben
var camefrom = "<?php echo $camefrom; ?>"; 
var filterleadsto = "<?php echo $filterleadsto; ?>"; 
//Test:
//alert("camefrom=" +camefrom + "& filterleadsto=" +filterleadsto);

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
  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.1.0.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script type="text/javascript" src="../script/datepicker-de.js"></script>
</head>

<body>
  <div class="container">
    <!-- Header -->  
    <header>
      <a>
        <h1 class="font-londrina">ZEITRAUM WÄHLEN</h1>
      </a>
    </header>

    <!-- Hauptinhalt -->  
    <div class="content flx-container">
    
    
        <!-- Erste Spalte -->  
        <div class="picker-column-1">
            <!-- Input Fields für Zeit -->  
            <div style="display: flex;">
                <div>
                    <label for="input1" class="font-fira date-picker-label">Start</label>
                    <input class="font-fira date-picker-field" type="text" id="input1" onchange="startChanged(this.value)">
                </div>
                <div>
                    <label for="input2" class="font-fira date-picker-label">Ende</label>
                    <input class="font-fira date-picker-field" type="text" id="input2" onchange="endChanged(this.value)">
                </div>
            </div>
            <!-- DatePicker -->  
            <div>
                <div id="divDatepicker"></div>
            </div>
        </div>

        <!-- Zweite Spalte -->  
        <div class="picker-column-2">
            <div class="font-fira">
                <p class="description">Zeitraum wählen</p>
            </div>
            <!-- Auswahl für Zeitabschnitte -->  
            <div class="custom-radio-group">
                <div class="radio-group">
                    <input type="radio" id="radio1" name="date-checkbox-group" value="option1">
                    <label for="radio1">Gesamter Zeitraum</label>
                </div>
                <div class="radio-group">
                    <input type="radio" id="radio2" name="date-checkbox-group" value="option2">
                    <label for="radio2">Dieses Jahr <?php echo '(' . $thisyear . ')'; ?></label>
                </div>
                <div class="radio-group">
                    <input type="radio" id="radio3" name="date-checkbox-group" value="option3">
                    <label for="radio3">Letztes Jahr <?php echo '(' . $lastyear . ')'; ?></label>
                </div>
                <div class="radio-group">
                    <input type="radio" id="radio4" name="date-checkbox-group" value="option4">
                    <label for="radio4">Letzte 12 Monate</label>
                </div>
                <div class="radio-group">
                    <input type="radio" id="radio5" name="date-checkbox-group" value="option5">
                    <label for="radio5">Letzter Monat</label>
                </div>
                <div class="radio-group">
                    <input type="radio" id="radio6" name="date-checkbox-group" value="option6">
                    <label for="radio6">Eigene</label>
                </div>
            </div>  
            <!-- Filtern Button --> 
            

            <div>
                <button class="filter-button" onclick="visitPage();">Filtern</button>
            </div>
        </div>
    </div>
  </div>
</body>

<script>
    //DatePicker - Auswahl und Verbindung mit Input Feldern
    $(function ($) {
    
        $("#divDatepicker").datepicker({
            numberOfMonths: 1,
            regional: 'de',
            dayNamesShort: ["Mo","Di","Mi","Do","Fr","Sa","So"],
            beforeShowDay: function(date) {
                var date1 = $.datepicker.parseDate($.datepicker._defaults.dateFormat, $("#input1").val());
                var date2 = $.datepicker.parseDate($.datepicker._defaults.dateFormat, $("#input2").val());
                if (date1 && date2 && date2<date1) {
                    $("#input1").val("");
                    $("#input2").val("");
                    return false;
                } else {
                    if (date1 && date && (date1.getTime() == date.getTime())) {
                        return [true, 'firstDate', ''];
                    }
                    if (date2 && date && (date2.getTime() == date.getTime())) {
                        return [true, 'lastDate', ''];
                    }
                    if (date >= date1 && date <= date2) {
                        return [true, 'dp-highlight', ''];
                    }
                    return [true, '', ''];
                }
            },
            onSelect: function(dateText, inst) {
                var date1 = $.datepicker.parseDate($.datepicker._defaults.dateFormat, $("#input1").val());
                var date2 = $.datepicker.parseDate($.datepicker._defaults.dateFormat, $("#input2").val());
                
                if (!date1 || date2 ) {
                    $("#input1").val(dateText);
                    $("#input2").val("");
                    $(this).datepicker();
                } else {
                    $("#input2").val(dateText);
                    $(this).datepicker();
                }
            }
        });
    });       
    </script>
    
    <script>
        //Input Felder - Verbindung mit DatePicker
        function startChanged(val) {
            $("#divDatepicker").datepicker("setDate", new Date(val));
            }
        function endChanged(val) {
            $("#divDatepicker").datepicker("setDate", new Date(val));
        }
    </script> 
    <script>
        //Auswahl der Zeitabschnitte - Verbindung mit DatePicker und Input Feldern
        const radioFullTime = document.getElementById("radio1");
        const radioThisYear = document.getElementById("radio2");
        const radioLastYear = document.getElementById("radio3");
        const radioLast12Months = document.getElementById("radio4");
        const radioLastMonth = document.getElementById("radio5");
        const radioCustom = document.getElementById("radio6");
        const date1 = document.getElementById("input1");
        const date2 = document.getElementById("input2");

        radioFullTime.addEventListener("click", function() {
            let today = new Date();
            date1.value = '01.01.2020';
            $("#divDatepicker").datepicker("setDate", new Date(date1));
            date2.value = `${today.getDate().toString().padStart(2, "0")}.${(today.getMonth()+1).toString().padStart(2, "0")}.${today.getFullYear()}`;
            $("#divDatepicker").datepicker("setDate", new Date(date2));
        });
        radioThisYear.addEventListener("click", function() {
            let today = new Date();
            let currentYear = new Date().getFullYear();
            date1.value = `01.01.${currentYear}`;
            $("#divDatepicker").datepicker("setDate", new Date(date1));
            date2.value = `${today.getDate().toString().padStart(2, "0")}.${(today.getMonth()+1).toString().padStart(2, "0")}.${today.getFullYear()}`;
            $("#divDatepicker").datepicker("setDate", new Date(date2));
        });
        radioLastYear.addEventListener("click", function() {
            let lastYear = new Date().getFullYear()- 1;
            date1.value = `01.01.${lastYear}`;
            $("#divDatepicker").datepicker("setDate", new Date(date1));
            date2.value = `31.12.${lastYear}`;
            $("#divDatepicker").datepicker("setDate", new Date(date2));
        });
        radioLast12Months.addEventListener("click", function() {
            let today = new Date();
            let oneYearAgo = new Date();
            oneYearAgo.setFullYear(today.getFullYear() - 1);
            date1.value = `${oneYearAgo.getDate().toString().padStart(2, "0")}.${(oneYearAgo.getMonth()+1).toString().padStart(2, "0")}.${oneYearAgo.getFullYear()}`;
            $("#divDatepicker").datepicker("setDate", new Date(date1));
            date2.value = `${today.getDate().toString().padStart(2, "0")}.${(today.getMonth()+1).toString().padStart(2, "0")}.${today.getFullYear()}`;
            $("#divDatepicker").datepicker("setDate", new Date(date2));
        });
        radioLastMonth.addEventListener("click", function() {
            let today = new Date();
            let lastMonth = new Date();
            lastMonth.setMonth(lastMonth.getMonth() - 1);
            date1.value = `${lastMonth.getDate().toString().padStart(2, "0")}.${(lastMonth.getMonth()+1).toString().padStart(2, "0")}.${lastMonth.getFullYear()}`;
            $("#divDatepicker").datepicker("setDate", new Date(date1));
            date2.value = `${today.getDate().toString().padStart(2, "0")}.${(today.getMonth()+1).toString().padStart(2, "0")}.${today.getFullYear()}`;
            $("#divDatepicker").datepicker("setDate", new Date(date2));
        });
        radioCustom.addEventListener("click", function() {
            date1.value = ''
            date2.value = ''
            $("#divDatepicker").datepicker("setDate", new Date(date1));
            $("#divDatepicker").datepicker("setDate", new Date(date2));
        });


    //Datumsauswahl an PHP übergeben 
    function visitPage(){
        
        //console.log("Logging01: date1="+date1.value+" date2="+date2.value+" ");

        window.location= filterleadsto+"?date1="+ date1.value + "&date2=" + date2.value ;
    	
    	//console.log("Logging02: date1="+date1.value+" date2="+date2.value+" ");
    	
    	//TODO: Testausgaben entfernen

    }
	
	</script>
    
</html>

