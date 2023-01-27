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

if ($date1formatted == NULL && $date2formatted == NULL) {
	$date1formatted = "01.01." . $thisyear;
	$date2formatted = $today_dmY;
}


//Datum-Formatkonvertierungen
//Datumsauswahl in Formatierung für Datenbank konvertieren
if ($date1formatted != NULL) {
	$date1timestamp = strtotime($date1formatted);
	$date1_ISO8601 = date("Y-m-d", $date1timestamp);
	//Datumsauswahl zusätzlich in Anzeigeformat konvertieren
	$date1_display = date("d.m.y", $date1timestamp);
}

if ($date2formatted != NULL) {
	$date2timestamp = strtotime($date2formatted . "23:59:59");
	$date2_ISO8601 = date("Y-m-d H:i:s", $date2timestamp);
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
$monthago_mY = date("m.Y", $monthago_timestamp);
$monthago_m = date("m", $monthago_timestamp);
$monthago_leapyear = date("L", $monthago_timestamp); //returns 1 if it's a leap year, 0 if it's not
$yearago_timestamp = strtotime("-1 year");
$yearago_dmY = date("d.m.Y", $yearago_timestamp);


if ($date1_dm == "01.01" && $date2_dm == "31.12") {
	if ($date2_Y - $date1_Y == 0) {
		$gewaehlterZeitraum = "Jahr " . $date2_Y;
	} elseif ($date2_Y > $date1_Y) {
		$gewaehlterZeitraum = $date1_Y . " - " . $date2_Y;
	} else {
		$gewaehlterZeitraum = $date1_display . " - " . $date2_display;
	}
} elseif ($date2formatted == $today_dmY) {
	if ($date1formatted == "01.01." . $thisyear) {
		$gewaehlterZeitraum = "Dieses Jahr";
	} elseif ($date1formatted == $yearago_dmY) {
		$gewaehlterZeitraum = "Letzte 12 Monate";
	} elseif ($date1formatted == $erstesMessdatum_dmY) {
		$gewaehlterZeitraum = "Ges. Zeitraum";
	}
	/*
	elseif ($date1formatted == $monthago_dmY)
	{
	$gewaehlterZeitraum = "Letzter Monat"; 
	} 
	*/else {
		$gewaehlterZeitraum = $date1_display . " - " . $date2_display;
	}
} elseif ($date1formatted == "01." . $monthago_mY) {
	if ($monthago_m == "01" || $monthago_m == "03" || $monthago_m == "05" || $monthago_m == "07" || $monthago_m == "08" || $monthago_m == "10" || $monthago_m == "12") {
		if ($date2formatted == "31." . $monthago_mY) {
			$gewaehlterZeitraum = "Letzter Monat";
		} else {
			$gewaehlterZeitraum = $date1_display . " - " . $date2_display;
		}
	} elseif ($monthago_m == "04" || $monthago_m == "06" || $monthago_m == "09" || $monthago_m == "11") {
		if ($date2formatted == "30." . $monthago_mY) {
			$gewaehlterZeitraum = "Letzter Monat";
		} else {
			$gewaehlterZeitraum = $date1_display . " - " . $date2_display;
		}
	} elseif ($monthago_m == "02") {
		if ($monthago_leapyear == 0 && $date2formatted == "28." . $monthago_mY) {
			$gewaehlterZeitraum = "Letzter Monat";
		} elseif ($monthago_leapyear == 1 && $date2formatted == "29." . $monthago_mY) {
			$gewaehlterZeitraum = "Letzter Monat";
		} else {
			$gewaehlterZeitraum = $date1_display . " - " . $date2_display;
		}
	} else {
		$gewaehlterZeitraum = $date1_display . " - " . $date2_display;
	}
} else {
	$gewaehlterZeitraum = $date1_display . " - " . $date2_display;
}

//Menge geretteter Lebensmittel im Zeitraum insgesamt abfragen
$select_ZeitraumMenge = $db->prepare("SELECT SUM(BewegMenge) FROM Bestand_Bewegung WHERE LStatusKey='2' AND BewegDatum BETWEEN :date1 AND :date2");
$daten_zeitraum = array(
	"date1" => $date1_ISO8601,
	"date2" => $date2_ISO8601
);
$erfolg = $select_ZeitraumMenge->execute($daten_zeitraum);


//Mengen geretteter Lebensmittel im Zeitraum nach Kategorien 
$select_KategorienMengen = $db->prepare("SELECT OKatName AS OberKategorie, SUM(BewegMenge) AS KGfairteilt, ROW_NUMBER() OVER(ORDER BY KGfairteilt DESC) AS RowNumber
		FROM (SELECT Bestand_Bewegung.LMKey, BewegMenge, BewegDatum, OKatName 
		FROM (Bestand_Bewegung LEFT JOIN Lebensmittel ON Bestand_Bewegung.LMKey=Lebensmittel.LMKey)
		LEFT JOIN OberKategorie ON Lebensmittel.OKatKey=OberKategorie.OKatKey
		WHERE LStatusKey=2 AND BewegDatum BETWEEN :date1 AND :date2) AS FairteiltesOKat
		GROUP BY OKatName
		ORDER BY KGfairteilt DESC");
$select_KategorienMengen->execute($daten_zeitraum);



// Fehlerüberprüfung
if ($select_KategorienMengen == false) {
	$fehler = $db->errorInfo();
	die("Folgender Datenbankfehler ist aufgetreten: " . $fehler[2]);
}

//Menge geretteter Lebensmittel anzeigefähig machen
$ZeitraumMenge = $select_ZeitraumMenge->fetchColumn();


// Ergebnistabelle auslesen und als assoz. Feld $KategorienMengen bereitstellen 
$KategorienMengen = $select_KategorienMengen->fetchAll();

//Variablen für das Balkendiagramm
$B1hidden = "hidden";
$B2hidden = "hidden";
$B3hidden = "hidden";
$B4hidden = "hidden";
$B5hidden = "hidden";
$B6hidden = "hidden";
$B7hidden = "hidden";
$B8hidden = "hidden";
$B9hidden = "hidden";


foreach ($KategorienMengen as $NachKategorien) {
	if ($NachKategorien['RowNumber'] == "1") {
		if ($NachKategorien['OberKategorie'] != NULL) {
			$Kategorien1Name = $NachKategorien['OberKategorie'];
		} else {
			$Kategorien1Name = "Unbekannt";
		}
		$Kategorien1kg = $NachKategorien['KGfairteilt'];
		$B1hidden = "";

	}
	if ($NachKategorien['RowNumber'] == "2") {
		if ($NachKategorien['OberKategorie'] != NULL) {
			$Kategorien2Name = $NachKategorien['OberKategorie'];
		} else {
			$Kategorien2Name = "Unbekannt";
		}
		$Kategorien2kg = $NachKategorien['KGfairteilt'];
		$B2hidden = "";
	}
	if ($NachKategorien['RowNumber'] == "3") {
		if ($NachKategorien['OberKategorie'] != NULL) {
			$Kategorien3Name = $NachKategorien['OberKategorie'];
		} else {
			$Kategorien3Name = "Unbekannt";
		}
		$Kategorien3kg = $NachKategorien['KGfairteilt'];
		$B3hidden = "";
	}
	if ($NachKategorien['RowNumber'] == "4") {
		if ($NachKategorien['OberKategorie'] != NULL) {
			$Kategorien4Name = $NachKategorien['OberKategorie'];
		} else {
			$Kategorien4Name = "Unbekannt";
		}
		$Kategorien4kg = $NachKategorien['KGfairteilt'];
		$B4hidden = "";
	}
	if ($NachKategorien['RowNumber'] == "5") {
		if ($NachKategorien['OberKategorie'] != NULL) {
			$Kategorien5Name = $NachKategorien['OberKategorie'];
		} else {
			$Kategorien5Name = "Unbekannt";
		}
		$Kategorien5kg = $NachKategorien['KGfairteilt'];
		$B5hidden = "";
	}
	if ($NachKategorien['RowNumber'] == "6") {
		if ($NachKategorien['OberKategorie'] != NULL) {
			$Kategorien6Name = $NachKategorien['OberKategorie'];
		} else {
			$Kategorien6Name = "Unbekannt";
		}
		$Kategorien6kg = $NachKategorien['KGfairteilt'];
		$B6hidden = "";
	}
	if ($NachKategorien['RowNumber'] == "7") {
		if ($NachKategorien['OberKategorie'] != NULL) {
			$Kategorien7Name = $NachKategorien['OberKategorie'];
		} else {
			$Kategorien7Name = "Unbekannt";
		}
		$Kategorien7kg = $NachKategorien['KGfairteilt'];
		$B7hidden = "";
	}
	if ($NachKategorien['RowNumber'] == "8") {
		if ($NachKategorien['OberKategorie'] != NULL) {
			$Kategorien8Name = $NachKategorien['OberKategorie'];
		} else {
			$Kategorien8Name = "Unbekannt";
		}
		$Kategorien8kg = $NachKategorien['KGfairteilt'];
		$B8hidden = "";
	}
	if ($NachKategorien['RowNumber'] == "9") {
		if ($NachKategorien['OberKategorie'] != NULL) {
			$Kategorien9Name = $NachKategorien['OberKategorie'];
		} else {
			$Kategorien9Name = "Unbekannt";
		}
		$Kategorien9kg = $NachKategorien['KGfairteilt'];
		$B9hidden = "";
	}
}



//Prozentzahlen für Balkendiagramm ausrechnen
//Fehlervermeidung: Kein Divide-by-Zero Error wenn Zeitraum ausgewähl, in dem nichts gerettet wurde
if ($ZeitraumMenge == 0) {
	$Kategorien1Prozent = 0;
	$Kategorien2Prozent = 0;
	$Kategorien3Prozent = 0;
	$Kategorien4Prozent = 0;
	$Kategorien5Prozent = 0;
	$Kategorien6Prozent = 0;
	$Kategorien7Prozent = 0;
	$Kategorien8Prozent = 0;
	$Kategorien9Prozent = 0;
} else {
	$Kategorien1Prozent = $Kategorien1kg / $ZeitraumMenge * 100;
	$Kategorien2Prozent = $Kategorien2kg / $ZeitraumMenge * 100;
	$Kategorien3Prozent = $Kategorien3kg / $ZeitraumMenge * 100;
	$Kategorien4Prozent = $Kategorien4kg / $ZeitraumMenge * 100;
	$Kategorien5Prozent = $Kategorien5kg / $ZeitraumMenge * 100;
	$Kategorien6Prozent = $Kategorien6kg / $ZeitraumMenge * 100;
	$Kategorien7Prozent = $Kategorien7kg / $ZeitraumMenge * 100;
	$Kategorien8Prozent = $Kategorien8kg / $ZeitraumMenge * 100;
	$Kategorien9Prozent = $Kategorien9kg / $ZeitraumMenge * 100;

}

//Zahlen in deutsche Formatierung konvertieren
$Kategorien1kg_display = number_format($Kategorien1kg, 1, ",", ".");
$Kategorien1Prozent_display = number_format($Kategorien1Prozent, 1, ",", ".");

$Kategorien2kg_display = number_format($Kategorien2kg, 1, ",", ".");
$Kategorien2Prozent_display = number_format($Kategorien2Prozent, 1, ",", ".");

$Kategorien3kg_display = number_format($Kategorien3kg, 1, ",", ".");
$Kategorien3Prozent_display = number_format($Kategorien3Prozent, 1, ",", ".");

$Kategorien4kg_display = number_format($Kategorien4kg, 1, ",", ".");
$Kategorien4Prozent_display = number_format($Kategorien4Prozent, 1, ",", ".");

$Kategorien5kg_display = number_format($Kategorien5kg, 1, ",", ".");
$Kategorien5Prozent_display = number_format($Kategorien5Prozent, 1, ",", ".");

$Kategorien6kg_display = number_format($Kategorien6kg, 1, ",", ".");
$Kategorien6Prozent_display = number_format($Kategorien6Prozent, 1, ",", ".");

$Kategorien7kg_display = number_format($Kategorien7kg, 1, ",", ".");
$Kategorien7Prozent_display = number_format($Kategorien7Prozent, 1, ",", ".");

$Kategorien8kg_display = number_format($Kategorien8kg, 1, ",", ".");
$Kategorien8Prozent_display = number_format($Kategorien8Prozent, 1, ",", ".");

$Kategorien9kg_display = number_format($Kategorien9kg, 1, ",", ".");
$Kategorien9Prozent_display = number_format($Kategorien9Prozent, 1, ",", ".");

// Login Session
$login = $_SESSION['login'];
if ($login == true) {
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
		<link rel="icon" type="image/x-icon" href="../favicon/favicon.ico">
		<!-- Fonts -->
		<link rel="preconnect" href="https://fonts.googleapis.com" />
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
		<link
			href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@200;300;400;500;600;700;800;900&family=Londrina+Solid&display=swap"
			rel="stylesheet" />
		<!-- CSS Stylesheet -->
		<link rel="stylesheet" href="../css/interne_wirkungsmessung.css" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"
			integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w=="
			crossorigin="anonymous" referrerpolicy="no-referrer" />
		<link href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
		<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
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
			<div class="content" style="position: relative;">

				<div class="balkendiagram-container">
					<!-- Button close the page -->

					<a
						href="<?php echo '12_interne_wirkungsmessung_dashboard.php?date1=' . $date1formatted . '&date2=' . $date2formatted; ?>">
						<div class="button-previous-page"></div>
					</a>

					<!-- Titel -->
					<div class="font-fira" style="padding-left: 20px;">
						<h2>Lebensmittel nach Kategorien</h2>
					</div>
					<!-- Bar Diagram -->

					<div class="category-list">
						<table class="balkendiagram-content">
							<tr <?php echo $B1hidden; ?>>
								<!-- Category name -->
								<td class="font-fira category-name">
									<?php echo $Kategorien1Name; ?>
								</td>
								<!-- Bar and info -->
								<td class="bar">
									<div class="bar-container">
										<div class="category-bar" id="bar1"
											data-percentage="<?php echo $Kategorien1Prozent; ?>"></div>
										<div class="font-fira percentage">
											<?php echo $Kategorien1Prozent_display; ?>% (
											<?php echo $Kategorien1kg_display; ?> Kg)
										</div>
									</div>
								</td>
							</tr>
							<tr <?php echo $B2hidden; ?>>
								<!-- Category name -->
								<td class="font-fira category-name">
									<?php echo $Kategorien2Name; ?>
								</td>
								<!-- Bar and info -->
								<td class="bar">
									<div class="bar-container">
										<div class="category-bar" id="bar2"
											data-percentage="<?php echo $Kategorien2Prozent; ?>"></div>
										<div class="font-fira percentage">
											<?php echo $Kategorien2Prozent_display; ?>% (
											<?php echo $Kategorien2kg_display; ?> Kg)
										</div>
									</div>
								</td>
							</tr>
							<tr <?php echo $B3hidden; ?>>
								<!-- Category name -->
								<td class="font-fira category-name">
									<?php echo $Kategorien3Name; ?>
								</td>
								<!-- Bar and info -->
								<td class="bar">
									<div class="bar-container">
										<div class="category-bar" id="bar3"
											data-percentage="<?php echo $Kategorien3Prozent; ?>"></div>
										<div class="font-fira percentage">
											<?php echo $Kategorien3Prozent_display; ?>% (
											<?php echo $Kategorien3kg_display; ?> Kg)
										</div>
									</div>
								</td>
							</tr>
							<tr <?php echo $B4hidden; ?>>
								<!-- Category name -->
								<td class="font-fira category-name">
									<?php echo $Kategorien4Name; ?>
								</td>
								<!-- Bar and info -->
								<td class="bar">
									<div class="bar-container">
										<div class="category-bar" data-percentage="<?php echo $Kategorien4Prozent; ?>">
										</div>
										<div class="font-fira percentage">
											<?php echo $Kategorien4Prozent_display; ?>% (
											<?php echo $Kategorien4kg_display; ?> Kg)
										</div>
									</div>
								</td>
							</tr>
							<tr <?php echo $B5hidden; ?>>
								<!-- Category name -->
								<td class="font-fira category-name">
									<?php echo $Kategorien5Name; ?>
								</td>
								<!-- Bar and info -->
								<td class="bar">
									<div class="bar-container">
										<div class="category-bar" data-percentage="<?php echo $Kategorien5Prozent; ?>">
										</div>
										<div class="font-fira percentage">
											<?php echo $Kategorien5Prozent_display; ?>% (
											<?php echo $Kategorien5kg_display; ?> Kg)
										</div>
									</div>
								</td>
							</tr>
							<tr <?php echo $B6hidden; ?>>
								<!-- Category name -->
								<td class="font-fira category-name">
									<?php echo $Kategorien6Name; ?>
								</td>
								<!-- Bar and info -->
								<td class="bar">
									<div class="bar-container">
										<div class="category-bar" data-percentage="<?php echo $Kategorien6Prozent; ?>">
										</div>
										<div class="font-fira percentage">
											<?php echo $Kategorien6Prozent_display; ?>% (
											<?php echo $Kategorien6kg_display; ?> Kg)
										</div>
									</div>
								</td>
							</tr>
							<tr <?php echo $B7hidden; ?>>
								<!-- Category name -->
								<td class="font-fira category-name">
									<?php echo $Kategorien7Name; ?>
								</td>
								<!-- Bar and info -->
								<td class="bar">
									<div class="bar-container">
										<div class="category-bar" data-percentage="<?php echo $Kategorien7Prozent; ?>">
										</div>
										<div class="font-fira percentage">
											<?php echo $Kategorien7Prozent_display; ?>% (
											<?php echo $Kategorien7kg_display; ?> Kg)
										</div>
									</div>
								</td>
							</tr>
							<tr <?php echo $B8hidden; ?>>
								<!-- Category name -->
								<td class="font-fira category-name">
									<?php echo $Kategorien8Name; ?>
								</td>
								<!-- Bar and info -->
								<td class="bar">
									<div class="bar-container">
										<div class="category-bar" data-percentage="<?php echo $Kategorien8Prozent; ?>">
										</div>
										<div class="font-fira percentage">
											<?php echo $Kategorien8Prozent_display; ?>% (
											<?php echo $Kategorien8kg_display; ?> Kg)
										</div>
									</div>
								</td>
							</tr>
							<tr <?php echo $B9hidden; ?>>
								<!-- Category name -->
								<td class="font-fira category-name">
									<?php echo $Kategorien9Name; ?>
								</td>
								<!-- Bar and info -->
								<td class="bar">
									<div class="bar-container">
										<div class="category-bar" data-percentage="<?php echo $Kategorien9Prozent; ?>">
										</div>
										<div class="font-fira percentage">
											<?php echo $Kategorien9Prozent_display; ?>% (
											<?php echo $Kategorien9kg_display; ?> Kg)
										</div>
									</div>
								</td>
							</tr>
						</table>

					</div>
				</div>
			</div>
			<!-- Footer -->
			<div class="action-container">
				<div class="time-button">
					<a href='<?php echo '13_interne_wirkungsmessung_zeitraum_waehlen.php?date1=' . $date1formatted . '&date2=' . $date2formatted . '&camefrom=kategorien'; ?>'
						class="link-button">
						<i class='fa fa-clock-o' style="font-size:34px; color: #99BB44;"></i>
						<div class="button-text">
							<p class="font-fira">
								<?php echo $gewaehlterZeitraum ?>
							</p>
						</div>
					</a>
				</div>
				<div class="footer-btn font-fira">
					<a href="<?php echo '12_interne_wirkungsmessung_dashboard.php?date1=' . $date1formatted . '&date2=' . $date2formatted; ?>"
						class="cancel-button">Zurück</a>
				</div>
			</div>
		</div>
		<!--Logout Overlay-->
		<div>
			<div class="overlayparent" id="logout-overlay">
				<div class="overlaychild" style="height: 191px;">
					<p class="olhead">
						Abmelden?
					</p>
					<div class="eingabe">
						<div class="buttonscontainer">
							<button class="buttonwhite" id="abmeldenAbbr">
								Abbrechen
							</button>
							<button class="buttongreen" id="abmeldenBtn" style="color: white" value="Abmelden">
								Abmelden
							</button>
						</div>
					</div>
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
				const width = (firstBarWidth / firstPercent) * percent;
				bar.style.width = `${width}px`;
			}
		});

		// Modale Box ansprechen
		var abmeldenOverlay = document.getElementById('logout-overlay');
		var abmeldenTrigger = document.getElementById('logout');
		// Buttons definieren, welche die modale Box triggern
		var abmeldenBtn = document.getElementById('abmeldenBtn');
		// abmeldenAbbr Element ansprechen
		var abmeldenAbbr = document.getElementById('abmeldenAbbr');

		// Funktion, dass sich die modale Box öffnet, wenn der Button getriggert wird
		abmeldenTrigger.onclick = function () {
			abmeldenOverlay.style.display = 'flex';
		}
		// Bei Klick auf Abbrechen -> Fenster schließen
		abmeldenAbbr.onclick = function () {
			abmeldenOverlay.style.display = 'none';
		}
		//User drückt auf Abmelden
		abmeldenBtn.onclick = function () {
			window.location.href = '../index.php?logout'
		}
		// Fenster schließen beim Klick außerhalb des Fensters
		window.onclick = function (event) {
			if (event.target == abmeldenOverlay) {
				abmeldenOverlay.style.display = 'none';
			}
		}
	</script>

	</html>

<?php
} else {
	session_destroy();
	header("Location: ../index.php");
}
?>