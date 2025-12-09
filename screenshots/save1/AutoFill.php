<?php 
//echo "AuroFilling area $areaID (" . $getAreaShort[$areaID] . ")<br>";
$TLsweeps=array();
$ATLsweeps=array();
$BASsweeps=array();
$AUXsweeps=array();
$nextBASsweep=0;
$nextAUXsweep=0;
    $query_string = "SELECT * FROM sweepdefinitions WHERE areaID=$areaID";
//echo "$query_string<br>";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query 1");
    while ($row = @mysql_fetch_array($result)) {
        $skiLevel=$row[ski_level];
        $sweepID = $row[id];
        if($skiLevel == 0)       $TLsweeps[] = $sweepID;
        else if($skiLevel == 1) $ATLsweeps[] = $sweepID;
        else if($skiLevel == 2) $BASsweeps[] = $sweepID;
        else                    $AUXsweeps[] = $sweepID; //skilevel=3
    }

    $query_string = "SELECT * FROM skihistory WHERE shift=0 AND areaID=$areaID AND date=$today AND teamLead<3 ORDER BY checkin";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query 1");
//
// loop for anyone assigned to this area
//
    while ($row = @mysql_fetch_array($result)) {
        $patroller_id = $row[ patroller_id ];
        $history_id = $row[history_id ];
        //now lookup this patroller to get is classification code
        $query_string = "SELECT ClassificationCode, FirstName, LastName FROM roster WHERE IDNumber=$patroller_id";
        $result2 = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query 1");
        $name="??";
        if ($row2 = @mysql_fetch_array($result2)) {
            $class = $row2[ClassificationCode];
            $name = $row2[FirstName] . " " . $row2[LastName];
        } else {
            $class = "XXX";
        }
        $sweep_ids = "";
        $leadership = $row[teamLead]; //0=Normal, 1=TL, 2=ATL, 3=Extra
//echo "$name) class=$class, leader=$leadership<br>";
        if($leadership == 0) {          //normal patroller
            if($class == "BAS" || $class == "SR") {
                if($nextBASsweep < count($BASsweeps)) {
                    $sweep_ids = $BASsweeps[$nextBASsweep];
//echo "BAS) $name sweep " . $BASsweeps[$nextBASsweep] . "<br>";
                    $nextBASsweep++;
                } else if($nextAUXsweep < count($AUXsweeps)) {
                    $sweep_ids = $AUXsweeps[$nextAUXsweep];
//echo "bas*) $name sweep " . $AUXsweeps[$nextAUXsweep] . "<br>";
                    $nextAUXsweep++;
                } else {
//echo "Nore more sweeps<br>";
                }
            } else {
                if($nextAUXsweep < count($AUXsweeps)) {
                    $sweep_ids = $AUXsweeps[$nextAUXsweep];
//echo "AUX) $name sweep " . $AUXsweeps[$nextAUXsweep] . "<br>";
                    $nextAUXsweep++;
                }
            }
        } else if ($leadership == 1) {  //TL
            for($i=0; $i < count($TLsweeps); $i++)
//                echo "TL $name- $patroller_id, " . $TLsweeps[$i] . "<br>";
                $sweep_ids .= $TLsweeps[$i] + " ";
        } else if ($leadership == 2) {  //ATL
            for($i=0; $i < count($ATLsweeps); $i++)
//                echo "ATL $name- $patroller_id, " . $ATLsweeps[$i]  . "<br>";
                $sweep_ids .= $ATLsweeps[$i] + " ";
        } //skip leadership = 3 (extras)
//echo " teamLead= $teamLead, patroller_id =$patroller_id <br>";
        $query_string = "UPDATE skihistory SET sweep_ids=\"$sweep_ids\" WHERE history_id=$history_id ";
//echo "$query_string <br>";
        @mysql_db_query($mysql_db, $query_string) or die ("Invalid query 1");
    }
//tl
//atl
//
?>
