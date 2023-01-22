<?php
//User bekannt Session
session_start();
//session_name("user");

//Datenbankverbindung aufbauen
require_once("../dbconnect/dbconnect.inc.php");
$_SESSION['FSkey'] = "";
//Prüfen ob Vorname vorhanden, um festzustellen, ob von Registrierungsseite oder Anmeldungsseite kommt
if (isset($_POST["vorname"])) {
    $seitencheck = true;
} else {
    $seitencheck = false;
}

if ($seitencheck) {
    //Variablen aus dem Registrierungsformular holen
    $mail = $_POST["email"];
    $tel = $_POST["tel"];
    $ID = $_POST["foodID"];

    //IF Else Abfrage für Check, ob man bereits registriert ist.
    $bekannt = false;
    $dbmail = false;
    $dbtel = false;
    $dbID = false;

    if ($mail) {
        //Abfrage an Datenbank senden
        $querymail = $db->prepare("SELECT*FROM Foodsaver WHERE Foodsaver.Email = '$mail'");
        $erfolg = $querymail->execute();

        //Zellenweise Verarbeitung der Datenbankabfrage
        $resultmail = $querymail->fetchAll();

        //Check ob Array befüllt ist
        $anzahl = count($resultmail);

        if ($anzahl != 0) {
            $bekannt = true;
            $dbmail = true;
        }

        //Fehlertest
        if (!$erfolg) {
            $fehler = $querymail->errorInfo();
            die("Folgender Datenbankfehler ist aufgetreten:" . $fehler[2]);
        }
    }
    if ($tel) {
        //Abfrage an Datenbank senden
        $querytel = $db->prepare("SELECT*FROM Foodsaver WHERE Foodsaver.TelNr = '$tel'");
        $erfolg = $querytel->execute();

        //Zellenweise Verarbeitung der Datenbankabfrage
        $resulttel = $querytel->fetchAll();

        //Check ob Array befüllt ist
        $anzahl = count($resulttel);

        if ($anzahl != 0) {
            $bekannt = true;
            $dbtel = true;
        }

        //Fehlertest
        if (!$erfolg) {
            $fehler = $querytel->errorInfo();
            die("Folgender Datenbankfehler ist aufgetreten:" . $fehler[2]);
        }
    }
    if ($ID) {
        //Abfrage an Datenbank senden
        $queryID = $db->prepare("SELECT*FROM Foodsaver WHERE Foodsaver.FoodsharingID = '$ID'");
        $erfolg = $queryID->execute();

        //Zellenweise Verarbeitung der Datenbankabfrage
        $resultID = $queryID->fetchAll();

        //Check ob Array befüllt ist
        $anzahl = count($resultID);

        if ($anzahl != 0) {
            $bekannt = true;
            $dbID = true;
        }

        //Fehlertest
        if (!$erfolg) {
            $fehler = $queryID->errorInfo();
            die("Folgender Datenbankfehler ist aufgetreten:" . $fehler[2]);
        }
    }

    //Abfragen, ob der Nutzer durch obiges Testen bekannt oder unbekannt ist
    if ($bekannt == false) {
        //wenn nicht bekannt dann Daten aus Registrierungs-Formular an Danilos Seite weiterreichen
        $_SESSION["vorname"] = $_POST["vorname"];
        $_SESSION["nachname"] = $_POST["nachname"];
        $_SESSION["foodID"] = $_POST["foodID"];
        $_SESSION["email"] = $_POST["email"];
        $_SESSION["tel"] = $_POST["tel"];
        $_SESSION["foodsaverLogin"] = true;
        $_SESSION["bekannt"] = $bekannt;
        header("Location: ./03_foodsaver_anmeldung_feedback.php");
    } else {
        //wenn bekannt, dann Daten aus der Datenbankabfrage vom bekannten Nutzer in die Session 
        if ($dbmail == "1") {
            //echo $resultmail[0]["Email"];
            $_SESSION["vorname"] = $resultmail[0]["Vorname"];
            $_SESSION["nachname"] = $resultmail[0]["Nachname"];
            $_SESSION["foodID"] = $resultmail[0]["FoodsharingID"];
            $_SESSION["email"] = $resultmail[0]["Email"];
            $_SESSION["tel"] = $resultmail[0]["TelNr"];
            $_SESSION['FSkey'] = $resultmail[0]["FSkey"];
            $_SESSION["foodsaverLogin"] = true;
            $_SESSION["bekannt"] = $bekannt;
            header("Location: ./05_foodsaver_start.php");
            // echo $_SESSION["vorname"], $_SESSION["nachname"], $_SESSION["foodID"], $_SESSION["email"], $_SESSION["tel"];
        } else if ($dbtel == "1") {
            $_SESSION["vorname"] = $resulttel[0]["Vorname"];
            $_SESSION["nachname"] = $resulttel[0]["Nachname"];
            $_SESSION["foodID"] = $resulttel[0]["FoodsharingID"];
            $_SESSION["email"] = $resulttel[0]["Email"];
            $_SESSION["tel"] = $resulttel[0]["TelNr"];
            $_SESSION['FSkey'] = $resulttel[0]["FSkey"];
            $_SESSION["foodsaverLogin"] = true;
            $_SESSION["bekannt"] = $bekannt;
            header("Location: ./05_foodsaver_start.php");
            // echo $_SESSION["vorname"], $_SESSION["nachname"], $_SESSION["foodID"], $_SESSION["email"], $_SESSION["tel"];
        } else if ($dbID == "1") {
            $_SESSION["vorname"] = $resultID[0]["Vorname"];
            $_SESSION["nachname"] = $resultID[0]["Nachname"];
            $_SESSION["foodID"] = $resultID[0]["FoodsharingID"];
            $_SESSION["email"] = $resultID[0]["Email"];
            $_SESSION["tel"] = $resultID[0]["TelNr"];
            $_SESSION['FSkey'] = $resultID[0]["FSkey"];
            $_SESSION["foodsaverLogin"] = true;
            $_SESSION["bekannt"] = $bekannt;
            header("Location: ./05_foodsaver_start.php");
            //echo $_SESSION["vorname"], $_SESSION["nachname"], $_SESSION["foodID"], $_SESSION["email"], $_SESSION["tel"];
        }
    }

} else {

    //Übertragen der POST Daten aus der Anmeldeseite 
    if (isset($_POST["mail"]) && $_POST["mail"] != "") {
        $mail = $_POST["mail"];
    } else if (isset($_POST["tel"]) && $_POST["tel"] != "") {
        $tel = $_POST["tel"];
    } else if (isset($_POST["ID"]) && $_POST["ID"] != "") {
        $ID = $_POST["ID"];
    } else {
        $mail = "error";
        $tel = "error";
        $ID = "error";
    }

    //Abfrage, was aus Datenbank gezogen wurde
    if (isset($mail)) {
        //Abfrage an Datenbank senden
        $querymail = $db->prepare("SELECT*FROM Foodsaver WHERE Foodsaver.Email = '$mail'");
        $erfolg = $querymail->execute();

        //Zellenweise Verarbeitung der Datenbankabfrage
        $resultmail = $querymail->fetchAll();

        //Check ob Array befüllt ist
        $anzahl = count($resultmail);

        if ($anzahl == 0) {
            $_SESSION["userstatus"] = "unbekannt";
            header("Location: ./02_foodsaver_anmeldung.php");
        } else {
            $_SESSION["vorname"] = $resultmail[0]["Vorname"];
            $_SESSION["nachname"] = $resultmail[0]["Nachname"];
            $_SESSION["foodID"] = $resultmail[0]["FoodsharingID"];
            $_SESSION["email"] = $resultmail[0]["Email"];
            $_SESSION["tel"] = $resultmail[0]["TelNr"];
            $_SESSION['FSkey'] = $resultmail[0]["FSkey"];
            $_SESSION["foodsaverLogin"] = true;
            $_SESSION["bekannt"] = true;
            header("Location: ./05_foodsaver_start.php");

        }

        //Fehlertest
        if (!$erfolg) {
            $fehler = $querymail->errorInfo();
            die("Folgender Datenbankfehler ist aufgetreten:" . $fehler[2]);
        }
    } else if (isset($tel)) {
        //Abfrage an Datenbank senden
        $querytel = $db->prepare("SELECT*FROM Foodsaver WHERE Foodsaver.TelNr = '$tel'");
        $erfolg = $querytel->execute();

        //Zellenweise Verarbeitung der Datenbankabfrage
        $resulttel = $querytel->fetchAll();

        //Check ob Array befüllt ist
        $anzahl = count($resulttel);
        if ($anzahl == 0) {
            $_SESSION["userstatus"] = "unbekannt";
            header("Location: ./02_foodsaver_anmeldung.php");
        } else {
            $_SESSION["vorname"] = $resulttel[0]["Vorname"];
            $_SESSION["nachname"] = $resulttel[0]["Nachname"];
            $_SESSION["foodID"] = $resulttel[0]["FoodsharingID"];
            $_SESSION["email"] = $resulttel[0]["Email"];
            $_SESSION["tel"] = $resulttel[0]["TelNr"];
            $_SESSION['FSkey'] = $resulttel[0]["FSkey"];
            $_SESSION["foodsaverLogin"] = true;
            $_SESSION["bekannt"] = true;
            header("Location: ./05_foodsaver_start.php");
        }

        //Fehlertest
        if (!$erfolg) {
            $fehler = $querytel->errorInfo();
            die("Folgender Datenbankfehler ist aufgetreten:" . $fehler[2]);
        }
    } else if (isset($ID)) {
        //Abfrage an Datenbank senden
        $queryID = $db->prepare("SELECT*FROM Foodsaver WHERE Foodsaver.FoodsharingID = '$ID'");
        $erfolg = $queryID->execute();

        //Zellenweise Verarbeitung der Datenbankabfrage
        $resultID = $queryID->fetchAll();

        //Check ob Array befüllt ist
        $anzahl = count($resultID);

        if ($anzahl == 0) {
            $_SESSION["userstatus"] = "unbekannt";
            header("Location: ./02_foodsaver_anmeldung.php");
        } else {
            $_SESSION["vorname"] = $resultID[0]["Vorname"];
            $_SESSION["nachname"] = $resultID[0]["Nachname"];
            $_SESSION["foodID"] = $resultID[0]["FoodsharingID"];
            $_SESSION["email"] = $resultID[0]["Email"];
            $_SESSION["tel"] = $resultID[0]["TelNr"];
            $_SESSION['FSkey'] = $resultID[0]["FSkey"];
            $_SESSION["foodsaverLogin"] = true;
            $_SESSION["bekannt"] = true;
            header("Location: ./05_foodsaver_start.php");
        }

        //Fehlertest
        if (!$erfolg) {
            $fehler = $queryID->errorInfo();
            die("Folgender Datenbankfehler ist aufgetreten:" . $fehler[2]);
        }
    }
}
?>