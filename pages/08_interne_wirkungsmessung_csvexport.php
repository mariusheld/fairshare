<?php

//Datenbankverbindung aufbauen
require_once("../dbconnect/dbconnect.inc.php");
$db_handle = new DBController();
$conn = $db_handle->connectDB();

$csv = $conn->query("SELECT LM.LMkey, LM.Bezeichnung, OK.OKatName, SUM(BB.BewegMenge) AS menge, HK.HerkunftName, LI.LieferDatum, BB.BewegDatum AS Fairteildatum
                    FROM Lebensmittel LM
                    INNER JOIN OberKategorie OK ON LM.OKatKey = OK.OKatKey
                    INNER JOIN Bestand_Bewegung BB ON LM.LMkey = BB.LMkey
                    INNER JOIN HerkunftsKategorie HK ON LM.HerkunftKey = HK.HerkunftKey
                    INNER JOIN Lieferung LI ON LM.LMkey = LI.LMkey
                    WHERE BB.LStatusKey = 2
                    GROUP BY LM.LMkey"); 
    
    if($csv->num_rows > 0){ 
        $delimiter = ";"; 
        $filename = "wirkungsmessung_export_" . date('Y-m-d') . ".csv"; 
        
        // Create a file pointer 
        $f = fopen('php://memory', 'w'); 
        
        // Set column headers 
        $fields = array('KEY', 'BEZEICHNUNG', 'OBERKATEGORIE', 'MENGE IN KG', 'HERKUNFT', 'LIEFERDATUM', 'FAIRTEILDATUM'); 
        fputcsv($f, $fields, $delimiter); 
        
        // Output each row of the data, format line as csv and write to file pointer 
        while($row = $csv->fetch_assoc()){ 
            $lineData = array($row['LMkey'], utf8_decode($row['Bezeichnung']), utf8_decode($row['OKatName']), $row['menge'], utf8_decode($row['HerkunftName']), $row['LieferDatum'], $row['Fairteildatum']); 
            fputcsv($f, $lineData, $delimiter); 
        } 
        
        // Move back to beginning of file 
        fseek($f, 0); 
        
        // Set headers to download file rather than displayed 
        header('Content-Type: text/csv'); 
        header('Content-Disposition: attachment; filename="' . $filename . '";'); 
        
        //output all remaining data on a file pointer 
        fpassthru($f); 
    } 
    exit;

?>