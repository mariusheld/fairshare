<!-- ----------- PHP ---------- -->
<?php
session_start();
// TEMPORÄR ABGESCHALTET
// require_once("../scripts/dbcontroller.php");
// $db_handle = new DBController();
// $connection = $db_handle->connectDB();

// Session Objekt wird lokal gespeichert
$array = json_decode(json_encode($_SESSION["array"]), true);
$dbeintrag = json_decode(json_encode($_SESSION["dbeintrag"]), true);

// TEMPORÄR ABGESCHALTET
// Vollständiger Array mit allen Einträgen wird an die Datenbank übertragen. Hier kommen die Querys hin 
// function sendList($dbeintrag, $connection) {
//   foreach ($dbeintrag as $key => $value) {
//     $LMbez = $value['Bezeichnung'];
//     $LMHer = $value['Herkunft'];
//     mysqli_select_db($connection, "raupeimmersatt");
//     $query = "INSERT INTO `lebensmittel`(`Bezeichnung`,`Herkunft`) VALUES ('$LMbez','$LMHer') ";
//     mysqli_query($connection, $query);
//     header("Location: ./04_final_check.php");
//   };
// }
// Datenbank Query Trigger
if (isset($_GET['send'])) {
  // sendList($dbeintrag, $connection);
  header("Location: ./04_final_check.php");
}

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Foodsaver Übersicht</title>
  <!-- Fonts  -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@200;300;400;500;600;700;800;900&family=Londrina+Solid&display=swap"
    rel="stylesheet" />
  <!-- CSS Stylesheet  -->
  <link href="../css/formularstyle.css" rel="stylesheet" />
</head>

<body class="font-fira">
  <div class="container">
    <header>
      <a> <img src="../media/logo.svg" alt="logo" /></a>
      <!-- OVERLAY -->
      <a href="/raupeimmersatt"> <img src="../media/icon_help.svg" alt="icon_help" /></a>
    </header>
    <div class="content">
      <div class="wrap">
        <!-- Tabelle -->
        <table class="table">
          <tr class="table-headers">
            <th class="grid-col-3">Lebensmittel</th>
            <th class="grid-col-2">Kistennr</th>
            <th class="grid-col-2">Menge</th>
            <th class="grid-col-3">Genießbar</th>
            <th class="grid-col-1"></th>
            <th class="grid-col-1"></th>
          </tr>
          <?php
          // Tabelleneintrag
          // LOOP TILL END OF DATA
          foreach ($array as $row) {
          ?>
          <tr class="table-items">
            <td class="grid-col-3">
              <?php echo $row['Lebensmittel'] ?>
            </td>
            <td class="grid-col-2 flex">
              <?php echo $row['Kistennr'] ?>
              <?php if ($row['Kuehlen'] == true)
              echo "<img src='../media/freeze_icon.svg' alt='freeze_icon' />"; ?>
            </td>
            <td class="grid-col-2">
              <?php echo $row['Menge'] ?> Kg
            </td>
            <td class="grid-col-3">
              <?php echo $row['Genießbar'] ?>
            </td>
            <td class="grid-col-1">
              <img src='../media/comment_icon.svg' alt='comment_icon' />
            </td>
            <td class="grid-col-1">
              <img src="../media/edit_icon.svg" alt="edit_icon" />
            </td>
          </tr>
          <?php
          }
          ?>

        </table>
        <div class="add-icon">
          <!-- NICHT ÄNDERN -->
          <a href="02_foodsaver_hinzufuegen.php">
            <img src="../media/add_icon.svg" alt="add_icon" />
          </a>
        </div>
      </div>
    </div>
    <div class="action-container">
      <div>
        <!-- OVERLAY -->
        <a href="/raupeimmersatt"> <img src="../media/icon_help_mini.svg" alt="icon_help" /></a>
        <p>Verstauen von Lebensmitteln</p>
      </div>
      <div class="action-wrap">
        <!-- OVERLAY UND WEITERLEITUNG ZUR STARTSEITE -->
        <a href="../index.php">Abbrechen</a>
        <!-- NICHT ÄNDERN -->
        <a href='03_foodsaver_uebersicht.php?send=true' class="next-button">Abschließen</a>
      </div>
    </div>
  </div>
</body>

</html>