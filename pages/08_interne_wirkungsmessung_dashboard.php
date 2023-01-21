<!-- ----------- PHP ---------- -->
<?php
session_start();
//Datenbankverbindung aufbauen
require_once("../dbconnect/dbconnect.inc.php");
$db_handle = new DBController();
$conn = $db_handle->connectDB();

//Datumsauswahl auslesen

if (isset($_GET['date1'])) {
  $date1formatted = $_GET['date1'];
} else {
  $date1formatted = NULL;
}

if (isset($_GET['date2'])) {
  $date2formatted = $_GET['date2'];
} else {
  $date2formatted = NULL;
}



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



if ($gesMenge < 1000) {
  $anzeigeeinheit_ges = "KILO";
  //Deutsche Zahlendarstellung (Komma vor Dezimalstellen)
  $gesMenge_display = number_format($gesMenge, 1, ",", ".");
} else {
  $anzeigeeinheit_ges = "TONNEN";
  $gesMenge_display = number_format($gesMenge / 1000, 1, ",", ".");
}


if ($ZeitraumMenge < 1000) {
  $anzeigeeinheit_zeitraum = "KILO";
  //Deutsche Zahlendarstellung (Komma vor Dezimalstellen)
  $ZeitraumMenge_display = number_format($ZeitraumMenge, 1, ",", ".");
} else {
  $anzeigeeinheit_zeitraum = "TONNEN";
  $ZeitraumMenge_display = number_format($ZeitraumMenge / 1000, 1, ",", ".");
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

// ----------------------------------------------------------------
// ----------------------------------------------------------------
// ---- JS Graph --------------------------------------------------
// ----------------------------------------------------------------
// ----------------------------------------------------------------


// ---- Arrays für JS Graphen erstellen ----
$zeitabschnitte = [];
$abschnittmengen = [12, 19, 3, 5, 2, 3, 20, 33, 23, 12, 33, 10];


// ---- Alle geretteten Lebensmittel pro Tag innerhalb des gewählten Zeitraumes abfragen ----
$MengeByDayQuery = "SELECT SUM(BewegMenge) AS menge, BewegDatum AS `date`
      FROM Bestand_Bewegung 
      WHERE LStatusKey='2'
      AND BewegDatum BETWEEN '$date1_ISO8601' AND '$date2_ISO8601'
      GROUP BY DATE(BewegDatum)";
$MengeByDayResult = mysqli_query($conn, $MengeByDayQuery);
while ($row = mysqli_fetch_assoc($MengeByDayResult)) {

  // $mengebydayresultset[] = $row;
  $formdat = date("d.m.y", strtotime($row['date']));
  $mengebydayresultset[$formdat] = $row;

}
$mengebyday = array();
$mengebyday = $mengebydayresultset;

//  print_r($mengebyday);


// ---- Jeden Tag des gewählten Zeitabschnittes in Array speichern ----
// ---- Neues Enddatum errechnen, das in die Perdiod einberechnet wird ---- 
$calc_end_date = date('d-m-Y', strtotime("+1 day", strtotime($date2_ISO8601)));
$perioddays = new DatePeriod(
  new DateTime($date1_ISO8601),
  new DateInterval('P1D'),
  new DateTime($calc_end_date),
);

// ---- Array für Tage erstellen ----
$displaymonths = array();
foreach ($perioddays as $date) {
  $displaydays[] = $date->format('d.m.y');
}


// ---- Array für Wochen erstellen ---- 
$displayweeks = array();
foreach ($perioddays as $date) {
  if (!in_array('KW ' . $date->format('W') . '', $displayweeks)) {
    $displayweeks[] = 'KW ' . $date->format('W') . '';
  }
}

// ---- Array für Monate erstellen ----
$displaymonths = array();
foreach ($perioddays as $date) {
  if (!in_array($date->format('M Y'), $displaymonths)) {
    $displaymonths[] = $date->format('M Y');
  }
}
// echo 'displaymonths';
// print_r($displaymonths);


// ---- Array für Quartale erstellen ---- 
$displayquarters = array();
foreach ($perioddays as $date) {
  $currentquarter = ceil($date->format('n') / 3);
  if ($currentquarter == 1 && !in_array('Q1 ' . $date->format('Y') . '', $displayquarters)) {
    $displayquarters[] = 'Q1 ' . $date->format('Y') . '';
  } else if ($currentquarter == 2 && !in_array('Q2 ' . $date->format('Y') . '', $displayquarters)) {
    $displayquarters[] = 'Q2 ' . $date->format('Y') . '';
  } else if ($currentquarter == 3 && !in_array('Q3 ' . $date->format('Y') . '', $displayquarters)) {
    $displayquarters[] = 'Q3 ' . $date->format('Y') . '';
  } else if ($currentquarter == 4 && !in_array('Q4 ' . $date->format('Y') . '', $displayquarters)) {
    $displayquarters[] = 'Q4 ' . $date->format('Y') . '';
  }
}
// echo 'displayquarter';
// print_r($displayquarters);

// ---- Array für Jahre erstellen ----
$displayyears = array();
foreach ($perioddays as $date) {
  if (!in_array($date->format('Y'), $displayyears)) {
    $displayyears[] = $date->format('Y');
  }
}



// ---- Array mit Mengen pro Tag erstellen ----
$dayAmountArray = [];
if (is_array($mengebyday)) {
  foreach ($displaydays as $val) {

    if (array_key_exists($val, $mengebyday)) {
      $dayAmountArray[$val] = $mengebyday[$val]['menge'];
    } else {
      $dayAmountArray[$val] = 0;
    }

  }
}
// echo '$dayAmountArray';
// print_r($dayAmountArray);
// $abschnittmengenAusgelesen = implode(',', $dayAmountArray);
// echo '$abschnittmengenAusgelesen: '.$abschnittmengenAusgelesen;


// ---- Array mit Mengen pro Woche erstellen ----
$mengebyweek = [];
if (is_array($mengebyday)) {
  foreach ($mengebyday as $val) {
    if (array_key_exists('KW ' . date('W', strtotime($val['date'])), $mengebyweek)) {
      $mengebyweek['KW ' . date('W', strtotime($val['date']))] = $mengebyweek['KW ' . date('W', strtotime($val['date']))] + $val['menge'];
    } else {
      $mengebyweek['KW ' . date('W', strtotime($val['date']))] = $val['menge'];
    }
  }
}

$weekAmountArray = [];
foreach ($displayweeks as $val) {

  if (array_key_exists($val, $mengebyweek)) {
    $weekAmountArray[$val] = $mengebyweek[$val];
  } else {
    $weekAmountArray[$val] = 0;
  }
}


// ---- Array mit Mengen pro Monat erstellen ----
$mengebymonth = [];
if (is_array($mengebyday)) {
  foreach ($mengebyday as $val) {
    if (array_key_exists(date('M Y', strtotime($val['date'])), $mengebymonth)) {
      $mengebymonth[date('M Y', strtotime($val['date']))] = $mengebymonth[date('M Y', strtotime($val['date']))] + $val['menge'];
    } else {
      $mengebymonth[date('M Y', strtotime($val['date']))] = $val['menge'];
    }
  }
}

$monthAmountArray = [];
foreach ($displaymonths as $val) {

  if (array_key_exists($val, $mengebymonth)) {
    $monthAmountArray[$val] = $mengebymonth[$val];
  } else {
    $monthAmountArray[$val] = 0;
  }
}
// echo 'monthAmountArray';
// print_r($monthAmountArray);



// ---- Array mit Mengen pro Quartal erstellen ----
$mengebyquarter = [];
if (is_array($mengebyday)) {
  foreach ($mengebyday as $val) {
    $date = date('n', strtotime($val['date']));
    $currentquarter = ceil($date / 3);
    if (array_key_exists('Q' . $currentquarter . ' ' . date('Y', strtotime($val['date'])), $mengebyquarter)) {
      $mengebyquarter['Q' . $currentquarter . ' ' . date('Y', strtotime($val['date']))] = $mengebyquarter['Q' . $currentquarter . ' ' . date('Y', strtotime($val['date']))] + $val['menge'];
    } else {
      $mengebyquarter['Q' . $currentquarter . ' ' . date('Y', strtotime($val['date']))] = $val['menge'];
    }
  }
}
// echo 'mengebyquarter';
// print_r($mengebyquarter);

$quarterAmountArray = [];
foreach ($displayquarters as $val) {

  if (array_key_exists($val, $mengebyquarter)) {
    $quarterAmountArray[$val] = $mengebyquarter[$val];
  } else {
    $quarterAmountArray[$val] = 0;
  }
}
// echo 'quarterAmountArray';
// print_r($quarterAmountArray);



// ---- Array mit Mengen pro Jahr erstellen ----
$mengebyyear = [];
if (is_array($mengebyday)) {
  foreach ($mengebyday as $val) {
    if (array_key_exists(date('Y', strtotime($val['date'])), $mengebyyear)) {
      $mengebyyear[date('Y', strtotime($val['date']))] = $mengebyyear[date('Y', strtotime($val['date']))] + $val['menge'];
    } else {
      $mengebyyear[date('Y', strtotime($val['date']))] = $val['menge'];
    }
  }
}

$yearAmountArray = [];
foreach ($displayyears as $val) {

  if (array_key_exists($val, $mengebyyear)) {
    $yearAmountArray[$val] = $mengebyyear[$val];
  } else {
    $yearAmountArray[$val] = 0;
  }
}
// echo 'yearAmountArray';
// print_r($yearAmountArray);


// ---- Tage zwischen den gewählten Daten berechnen ----
$start_date = strtotime($date1_ISO8601);
$end_date = strtotime($date2_ISO8601);
$tage = ($end_date - $start_date) / 60 / 60 / 24 + 1;



// ---- Beide Arrays den richtigen Zeitabschnitten zuweisen --- 
if ($tage <= 12) {
  $zeitabschnitte = $displaydays;
  $abschnittmengen = $dayAmountArray;
} else if ($tage > 12 && $tage <= 84) {
  $zeitabschnitte = $displayweeks;
  $abschnittmengen = $weekAmountArray;
} else if ($tage > 84 && $tage <= 365) {
  $zeitabschnitte = $displaymonths;
  $abschnittmengen = $monthAmountArray;
} else if ($tage > 365 && $tage <= 1094) {
  $zeitabschnitte = $displayquarters;
  $abschnittmengen = $quarterAmountArray;
} else if ($tage > 1094) {
  $zeitabschnitte = $displayyears;
  $abschnittmengen = $yearAmountArray;
}


// ---- Punkt anzeigen, wenn nur ein Tag ausgewählt ist ----
$radius = 0;
if ($tage == 1) {
  $radius = 3;
}

// Login Session
$login = $_SESSION['login'];
if ($login == true) {
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
    <link rel="icon" type="image/x-icon" href="../favicon/favicon.ico">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@200;300;400;500;600;700;800;900&family=Londrina+Solid&display=swap"
      rel="stylesheet" />
    <!-- CSS Stylesheet -->
    <link rel="stylesheet" href="../css/interne_wirkungsmessung.css" />
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
              <h1 class="font-londrina number">
                <?php echo $gesMenge_display . " " . $anzeigeeinheit_ges ?>
              </h1>
            </div>
            <div class="block">
              <p class="font-fira description">Im ausgewählten Zeitraum gerettete Lebensmittel:</p>

              <h1 class="font-londrina number">
                <?php echo $ZeitraumMenge_display . " " . $anzeigeeinheit_zeitraum ?>
              </h1>
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
          <a
            href="<?php echo '08_interne_wirkungsmessung_kategorien.php?date1=' . $date1formatted . '&date2=' . $date2formatted; ?>">
            <div class="block flx-container-sm" style="min-height: 239px;" >
              <!-- Liste der Kategorien -->
              <div style="display: inline;">
                <p class="font-fira description"><strong>Nach Kategorien</strong>
                  <i class="fa fa-angle-right" aria-hidden="true" style="color: #99BB44; margin-left: 5px;"></i></p>
                <div class="font-fira list-category">
                  <table>
                    <?php
                    if (is_array($kategorien)) {
                      foreach ($kategorien as $key => $row) {
                        $menge = (float) $row['menge'];
                        $menge_ges = (float) $ZeitraumMenge;
                        // $menge_ges = 24;
                        $prozent = $menge / $menge_ges * 100;
                        if ($key < 3) {
                          ?>
                          <tr>
                            <td style="text-align: right;">
                              <?php echo round($prozent) ?>%
                            </td>
                            <td>&nbsp&nbsp&nbsp
                              <?php echo $row['katname'] ?>
                            </td>
                          </tr>
                          <?php
                        } else {
                          ?>
                          <tr class="list-category-sml">
                            <td style="text-align: right;">
                              <?php echo round($prozent) ?>%
                            </td>
                            <td>&nbsp&nbsp&nbsp
                              <?php echo $row['katname'] ?>
                            </td>
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
              <?php //if (!empty($kategorien)){ ?>
                <img src="<?php if ($kategorien[0]['katkey'] == 1) {
                  echo $kat_icon_1;
                } else if ($kategorien[0]['katkey'] == 2) {
                  echo $kat_icon_2;
                } else if ($kategorien[0]['katkey'] == 3) {
                  echo $kat_icon_3;
                } else if ($kategorien[0]['katkey'] == 4) {
                  echo $kat_icon_4;
                } else if ($kategorien[0]['katkey'] == 5) {
                  echo $kat_icon_5;
                } else if ($kategorien[0]['katkey'] == 6) {
                  echo $kat_icon_6;
                } else if ($kategorien[0]['katkey'] == 7) {
                  echo $kat_icon_7;
                } else if ($kategorien[0]['katkey'] == 8) {
                  echo $kat_icon_8;
                } ?>" class="img-category" style="width: 70px;"><br>
                <img src="<?php if ($kategorien[1]['katkey'] == 1) {
                  echo $kat_icon_1;
                } else if ($kategorien[1]['katkey'] == 2) {
                  echo $kat_icon_2;
                } else if ($kategorien[1]['katkey'] == 3) {
                  echo $kat_icon_3;
                } else if ($kategorien[1]['katkey'] == 4) {
                  echo $kat_icon_4;
                } else if ($kategorien[1]['katkey'] == 5) {
                  echo $kat_icon_5;
                } else if ($kategorien[1]['katkey'] == 6) {
                  echo $kat_icon_6;
                } else if ($kategorien[1]['katkey'] == 7) {
                  echo $kat_icon_7;
                } else if ($kategorien[1]['katkey'] == 8) {
                  echo $kat_icon_8;
                } ?>" class="img-category" style="width: 50px;"><br>
                <img src="<?php if ($kategorien[2]['katkey'] == 1) {
                  echo $kat_icon_1;
                } else if ($kategorien[2]['katkey'] == 2) {
                  echo $kat_icon_2;
                } else if ($kategorien[2]['katkey'] == 3) {
                  echo $kat_icon_3;
                } else if ($kategorien[2]['katkey'] == 4) {
                  echo $kat_icon_4;
                } else if ($kategorien[2]['katkey'] == 5) {
                  echo $kat_icon_5;
                } else if ($kategorien[2]['katkey'] == 6) {
                  echo $kat_icon_6;
                } else if ($kategorien[2]['katkey'] == 7) {
                  echo $kat_icon_7;
                } else if ($kategorien[2]['katkey'] == 8) {
                  echo $kat_icon_8;
                } ?>" class="img-category" style="width: 40px;">
                <?php //} ?>
              </div>
            </div>
          </a>
          <!-- Liste der Herkünfte -->
          <a
            href="<?php echo '08_interne_wirkungsmessung_herkunft.php?date1=' . $date1formatted . '&date2=' . $date2formatted; ?>">
            <div class="block" style="min-height: 225px;">
              <div style="display: inline-block;">
                <p class="font-fira description" style="float:left;"><strong>Nach Herkunft</strong>
                <i class="fa fa-angle-right" aria-hidden="true" style="color: #99BB44; margin-left: 5px;"></i></p>
                <div class="font-fira list-category">
                  <table>
                    <?php
                    if (is_array($herkunft)) {
                      foreach ($herkunft as $key => $row) {
                        $menge = (float) $row['menge'];
                        $menge_ges = (float) $ZeitraumMenge;
                        // $menge_ges = 24;
                        $prozent = $menge / $menge_ges * 100;
                        if ($key < 3) {
                          ?>
                          <tr>
                            <td style="text-align: right;">
                              <?php echo round($prozent) ?>%
                            </td>
                            <td>&nbsp&nbsp&nbsp
                              <?php echo $row['herkunftname'] ?>
                            </td>
                          </tr>
                          <?php
                        } else {
                          ?>
                          <tr class="list-category-sml">
                            <td style="text-align: right;">
                              <?php echo round($prozent) ?>%
                            </td>
                            <td>&nbsp&nbsp&nbsp
                              <?php echo $row['herkunftname'] ?>
                            </td>
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
        <div class="time-button">
          <a href='<?php echo '08_interne_wirkungsmessung_zeitraum_waehlen.php?date1=' . $date1formatted . '&date2=' . $date2formatted . '&camefrom=dashboard'; ?>'
            class="link-button">
            <i class='fa fa-clock-o' style="font-size:34px; color: #99BB44;"></i>
            <div class="button-text">
              <p class="font-fira">
                <?php echo $gewaehlterZeitraum ?>
              </p>
            </div>
          </a>
        </div>
        <div class="footer-btn font-fira" id="csv-export">
          <a href='#' class="next-button">Export als CSV-Datei</a>
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
      window.location.href = '../index.php'
    }
    // Fenster schließen beim Klick außerhalb des Fensters
    window.onclick = function (event) {
      if (event.target == abmeldenOverlay) {
        abmeldenOverlay.style.display = 'none';
      }
    }


    var csv_button = document.getElementById("csv-export");
    csv_button.addEventListener("click", function () {
      console.log('export');
      location.href = "08_interne_wirkungsmessung_csvexport.php";
    });

    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($zeitabschnitte); ?>, //Labels
        datasets: [{
          label: null,
          data: <?php echo json_encode($abschnittmengen); ?>, //Data für Liniendiagram
          pointRadius: <?php echo $radius ?>,  // Keine Punkte anzeigen
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

  <?php
} else {
  session_destroy();
  header("Location: ../index.php");
}
?>