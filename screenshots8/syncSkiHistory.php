<?php
//NOTE TO BRIAN(me): Only thing changed here is $showdebug was changed to its current constant
//	and indenting.  
//	config.php now has PEAR::DB functionality and has been tested


require("config.php");
include("runningFromWeb.php");

//IP address database connection
//$dsn2 = "mysql://{$mysqli_username}:{$mysqli_password}@{$gledhills_host}/{$mysqli_db}";
//$db2 =& DB::connect($dsn2);
//if (DB::isError ($db2))
//    die ("Could not connect to the database at {$gledhills_host}.");

//$connect_string = @mysqli_connect($gledhills_host, $mysqli_username, $mysqli_password) or die ("Could not connect to the database at $gledhills_host.");

$arrDate = getdate();
$today=mktime(0, 0, 0, $arrDate['mon'], $arrDate['mday'], $arrDate['year']);
//    $strToday = date("F-d-Y h:m", $today);
$strToday = date("F-d-Y h:i:s a");
//echo "$mysqli_host at: $strToday<br>";
$localSkiHistory = [];
$remoteSkihistory = [];

//Contants
$SHOW_DEBUG = false;

// **********************
// *  class SklHistory  *
// **********************
class SkiHistory {
    public $date;
    public $checkin;
    public $areaID;
    public $shift;
    public $value;
    public $multiplier;
    public $patroller_id;
    public $history_id;
    public $sweep_ids;
    public $teamLead;
    public $name;

  function toString() {
    return serialize($this);
  }

  function getDate() {
      return $this->date;
  }

  function getTime() {
      return $this->checkin;
  }

  function getHistoryID() {
      return $this->history_id;
  }

  function getPatrollerID() {
      return $this->patroller_id;
  }

  function read_info($row) {
	$this->date = $row['date'];
	$this->checkin = $row['checkin'];
	$this->areaID = $row['areaID'];
	$this->shift = $row['shift'];
	$this->value = $row['value'];
	$this->multiplier = $row['multiplier'];
	$this->patroller_id = $row['patroller_id'];
	$this->history_id = $row['history_id'];
	$this->sweep_ids = $row['sweep_ids'];
	$this->teamLead = $row['teamLead'];
	$this->name = $row['name'];
  }

  function getSQLSkiHistoryDelete($table) {
	    return "DELETE FROM `" . $table . "` WHERE `history_id`='" . $this->history_id . "'";
  }

  function getSQLSkiHistoryInsert($table) {
	//example output
	//INSERT INTO `skihistory` VALUES (1099116000, 25878, 0, 0, 4, 1, '180535', 4, '10', 2, 'Mike Wardle');
	    return "INSERT INTO `" . $table . "` VALUES (" 
	. "'"    . $this->date
	. "', '" . $this->checkin
	. "', '" . $this->areaID
	. "', '" . $this->shift
	. "', '" . $this->value
	. "', '" . $this->multiplier
	. "', '" . $this->patroller_id
	. "', '" . $this->history_id 
	. "', '" . $this->sweep_ids
	. "', '" . $this->teamLead
	. "', '" . $this->name
	. "');";
  }

} //end class SkiHistory

//---------------------------------------------------------------
//---------------- end of 'skihistory' class and functions ------
//---------------------------------------------------------------


// ***********************
// *  class Assignments  *
// ***********************
class Assignments {
    public $Date;
    public $StartTime;
    public $EndTime;
    public $EventName;
    public $ShiftType;
    public $Count;
    public $P0;
    public $P1;
    public $P2;
    public $P3;
    public $P4;
    public $P5;
    public $P6;
    public $P7;
    public $P8;
    public $P9;

  function read_info($row) {
	//foreach ($row as $key => $value){
	//    $this->$key = $value;
	//}
	$this->Date = $row['Date'];
	$this->StartTime = $row['StartTime'];
	$this->EndTime = $row['EndTime'];
	$this->EventName = $row['EventName'];
	$this->ShiftType = $row['ShiftType'];
	$this->Count = $row['Count'];
	$this->P0 = $row['P0'];
    $this->P1 = $row['P1'];
    $this->P2 = $row['P2'];
    $this->P3 = $row['P3'];
    $this->P4 = $row['P4'];
    $this->P5 = $row['P5'];
    $this->P6 = $row['P6'];
    $this->P7 = $row['P7'];
    $this->P8 = $row['P8'];
    $this->P9 = $row['P9'];
  }

  function getSQLAssignmentsInsert($table) {
	return "INSERT INTO `" . $table . "` VALUES (" 
	    . "'"    . $this->Date
	    . "', '" . $this->StartTime 
	    . "', '" . $this->EndTime
	    . "', '" . $this->EventName
	    . "', '" . $this->ShiftType
	    . "', '" . $this->Count
	    . "', '" . $this->P0
        . "', '" . $this->P1
        . "', '" . $this->P2
        . "', '" . $this->P3
        . "', '" . $this->P4
        . "', '" . $this->P5
        . "', '" . $this->P6
        . "', '" . $this->P7
        . "', '" . $this->P8
        . "', '" . $this->P9
	    . "');";
  }

  function toString() {
	echo serialize($this);
  }

} //end class Assignments

//-----------------------------------------------------------
//---------------- end of 'Assignments' class and functions ------
//-----------------------------------------------------------

?>
<html>

<head>
<meta http-equiv="Content-Language" content="en-us"/>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252"/>
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
<meta HTTP-EQUIV="Expires" CONTENT="-1"/>
<title>Synchronize with www.BrightonNSP.org</title>
</head>

<body background="images/ncmnthbk.jpg" onLoad="redirect()">

<?php

$suffix = "old";
$totalHistories=1;
// ******************************************************************
// * Synchronize the SKI HISTORY from local machine to gledhills.com*
// ******************************************************************


//connect to the local database
if ($SHOW_DEBUG)echo "open local connection: $mysqli_host<br>";
$connect_string = @mysqli_connect($mysqli_host, $mysqli_username, $mysqli_password) or die ("Could not connect to the database.");
mysqli_select_db($connect_string, $mysqli_db);

//--------------------------------------------------------------------
if ($SHOW_DEBUG) echo "Reading ski histroy from $mysqli_host<br>";
//--------------------------------------------------------------------

// *******************************************
// ** progress indicator stuff             ***
// *******************************************
$query_string = "SELECT COUNT(history_id) AS count  FROM skihistory WHERE 1";
echo $query_string . "<br>";

$result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result l skiHistory)");
if ($row = @mysqli_fetch_array($result)) 
{
  $totalHistories=$row['count'];
}
$historiesProcessed = 0;

//get patrollers processed
$query_string = "SELECT COUNT(history_id) AS count  FROM skihistory WHERE 1";
$result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result 2 history)");
if ($row = @mysqli_fetch_array($result)) 
{
  $historiesProcessed=$row['count'];
}
echo "YTD -local- total ski histories=$historiesProcessed<br>";

//    $blockSize=10;
$blockSize=300;
//echo "setting blocksize to 300<br>";
$left = 500  * $historiesProcessed / $totalHistories;	 
$right = 500 - $left;

$historiesProcessed = 0;

// *******************************************
// ** get SKI HISTORIES processed   ***
// *******************************************
//    $query_string = "SELECT COUNT(history_id) AS count  FROM skihistory WHERE 1";
////
////    echo "****** query_string=" . $query_string . "<br>\n";
//    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result 2 skihistory)");
//    if ($row = @mysqli_fetch_array($result)) {
//	$historiesProcessed=$row[count];
//	echo "****** LOCAL ski histories=" . $historiesProcessed . "<br>\n";
//    }

    $query_string = "SELECT * FROM skihistory WHERE 1";
//echo "time=" .date("h:i:s ") ."<br>";
    if ($SHOW_DEBUG) echo "$query_string<br>\n";
    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result 2 skiHistory)");
//echo "time=" .date("h:i:s ") ."<br>" ;
    while ($row = @mysqli_fetch_array($result)) {
    	$tmp = new SkiHistory();
    	$lastID = $row['history_id'];
    //echo "id=" . $lastID . "<br>\n";
    	$tmp->read_info($row);
//        echo "history id=" . $tmp->getHistoryID() . "<br>";

//    	$localSkiHistory[] = $tmp;
        $k = $tmp->getHistoryID();
        $localSkiHistory[$k] = $tmp;  //
    }
//echo "time=" .date("h:i:s ") ."<br>" ;

//close localhost
    if ($SHOW_DEBUG) echo "close local connection<br>\n";

    @mysqli_close($connect_string);
    @mysqli_free_result($result);

// ===================================
// ===== OPEN REMOTE SKIHISTORY ======
// ===================================
    if ($SHOW_DEBUG) echo "open remote connection ($gledhills_host)<br>\n";
    $connect_string = @mysqli_connect($gledhills_host, $mysqli_username, $gledhills_mysqli_password) or die ("Could not connect to the database.");
mysqli_select_db($connect_string, $mysqli_db);
    $remote_history_table = "skihistory";
    $query_string = "SELECT COUNT(history_id) AS count  FROM $remote_history_table WHERE 1";
//
//    echo "****** query_string=" . $query_string . "<br>\n";
    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result 2 skihistory)");
    if ($row = @mysqli_fetch_array($result)) {
    $historiesProcessed=$row['count'];
//    echo "****** REMOTE ski histories=" . $historiesProcessed . "<br>\n";
echo "YTD -gledhills.com- ski histories to compare=$historiesProcessed<br>";
    }

    $query_string = "SELECT * FROM $remote_history_table WHERE 1";
    if ($SHOW_DEBUG) echo "$query_string<br>\n";
    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result 22 skiHistory)");
    while ($row = @mysqli_fetch_array($result)) {
        $tmp = new SkiHistory();
        $lastID = $row['history_id'];
    //echo "id=" . $lastID . "<br>\n";
        $tmp->read_info($row);
        $k = $tmp->getHistoryID();
        $remoteSkiHistory[$k] = $tmp;
    }

    // ********************************************************************
    // remove non-duplicate from gledhills.com  (may have used same "history_id" key 
    // ********************************************************************
echo "remove non-duplicate from gledhills.com<br>";
    reset($localSkiHistory);
    reset($remoteSkiHistory);
//loop through gledhills, and remove mismatched values
    foreach ($remoteSkiHistory as $historyID => $val) {
        //        echo "historyID=$historyID local=".$localSkiHistory[$historyID]." , remote=" . $remoteSkiHistory[$historyID] . "<br>";
        if(array_key_exists($historyID,$localSkiHistory)) {
        } else {
            echo "remote key value: $historyID, does NOT exist in local<br>";
            echo "removing key,value from remote<br>";
            $query_string = $val->getSQLSkiHistoryDelete($remote_history_table);
            echo "$query_string<br>";
            $result = @mysqli_query($connect_string, $query_string) or die ("  - $remoteSkiHistory DELETE Failed on " . $query_string . " MYSQL error:" . mysqli_error());

        }
    }
//echo "time=" .date("h:i:s ") ."<br>" ;
//loop through local, and 
//  1) if does NOT exist, then add it to remote (no chance of key dup (test was above, and table defines history_id as unique)
//  2) else test if equal
//     if NOT equal, then update remote
    $count = 0;
    foreach ($localSkiHistory as $historyID => $val) {
        //        echo "historyID=$historyID local=".$localSkiHistory[$historyID]." , remote=" . $remoteSkiHistory[$historyID] . "<br>";
        if(!array_key_exists($historyID,$remoteSkiHistory)) {
            $query_string = $val->getSQLSkiHistoryInsert($remote_history_table);
            echo "$query_string<br>";
            $result = @mysqli_query($connect_string, $query_string) or die ("  - $remoteSkiHistory INSERT Failed on " . $query_string . " MYSQL error:" . mysqli_error());
        } else {
            $rem_sh = $remoteSkiHistory[$historyID];
            if($rem_sh->toString() != $val->toString()) {
echo "UPDATING ski history record on remote machine.  For: ".$val->toString()."<br>";
                $query_string = $val->getSQLSkiHistoryDelete($remote_history_table);
                $result = @mysqli_query($connect_string, $query_string) or die ("  - $remoteSkiHistory DELETE Failed on " . $query_string . " MYSQL error:" . mysqli_error());
                $query_string = $val->getSQLSkiHistoryInsert($remote_history_table);
                $result = @mysqli_query($connect_string, $query_string) or die ("  - $remoteSkiHistory INSERT Failed on " . $query_string . " MYSQL error:" . mysqli_error());
            } else {
                $count += 1;
            }
        }
    }
echo $count . " exact duplicates between the data bases<br>";
//echo "time=" .date("h:i:s ") ."<br>" ;
//now loop through the skihistory, and verify what is on the calendar
    reset($localSkiHistory);
//loop through gledhills, and remove mismatched values
echo "remove mis-matches on gledhills.com<br>";
echo "Note to Steve:  This still needs to be cleaned up<br>";
echo "<br><br>Done.<br>";
//close last connection
@mysqli_close($connect_string);
@mysqli_free_result($result);


?>
<br>
</body>
</html>
