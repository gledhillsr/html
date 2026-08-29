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

  //The row as the upload endpoint wants it: column name to value, matching the
  //skihistory table exactly.  Values go as strings; the web site writes them
  //through a prepared statement, so MySQL converts them back on the way in.
  function toRow() {
      return [
          "date"         => (string)$this->date,
          "checkin"      => (string)$this->checkin,
          "areaID"       => (string)$this->areaID,
          "shift"        => (string)$this->shift,
          "value"        => (string)$this->value,
          "multiplier"   => (string)$this->multiplier,
          "patroller_id" => (string)$this->patroller_id,
          "history_id"   => (string)$this->history_id,
          "sweep_ids"    => (string)$this->sweep_ids,
          "teamLead"     => (string)$this->teamLead,
          "name"         => (string)$this->name,
      ];
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
// NOTE: When creating new Assignments, set $Date using getAssignmentDateString() 
// from config.php to ensure it includes hour, minutes, and seconds.
// Format: YYYY-MM-DD_HH:MM:SS (e.g., "2024-01-15_14:30:45")
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
setMySQLTimezone($connect_string);

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

// ==========================================================
// ===== SEND THE HISTORY UP TO THE WEB SITE ================
// ==========================================================
// This used to open a MySQL connection to gledhills.com and reconcile the two
// tables row by row from here - a SELECT of the whole remote table, then an
// INSERT, DELETE or DELETE+INSERT per differing row, each its own round trip.
// Port 3306 there is firewalled, so it could not run at all, and it needed a
// production database credential on this machine.
//
// The whole local table now goes up in one POST and the web site does the
// reconciling.  Same end state: rows it does not have are added, rows that
// differ are replaced, rows this machine no longer has are removed.
if (empty($lockerApiUrl) || empty($lockerApiKey)) {
    die("<h2>Not configured: set \$lockerApiUrl and \$lockerApiKey in dbconfig.php</h2>");
}

$rows = [];
foreach ($localSkiHistory as $historyID => $val) {
    $rows[] = $val->toRow();
}
echo "sending " . count($rows) . " ski history records to the web site...<br>\n";
flush();

$url = rtrim($lockerApiUrl, "/") . "/skihistory?resort=" . rawurlencode($lockerResort);
if (isset($force) && $force) {
    $url .= "&force=true";
}
$answer = webPost($url, $lockerApiKey, ["rows" => $rows]);

if ($answer['error'] !== "") {
    die("<h2>Could not reach the web site: " . htmlspecialchars($answer['error']) . "</h2>");
}
$apiResult = json_decode($answer['body'], true);
if (!is_array($apiResult)) {
    die("<h2>HTTP " . $answer['status'] . ": unreadable answer</h2>");
}

if ($answer['status'] == 409 && isset($apiResult['refused'])) {
    //the web site would have deleted a large share of its history - almost always a sign
    //that the local database is incomplete rather than a real correction
    echo "<h2 style='color:#C00000'>Upload refused</h2>\n";
    echo htmlspecialchars($apiResult['refused']) . "<br><br>\n";
    echo "If the local ski history really is correct and the web site should match it, ";
    echo "<a href=\"syncSkiHistory.php?force=1\">click here to upload anyway</a>.<br>\n";
}
else if ($answer['status'] != 200) {
    $msg = isset($apiResult['error']) ? $apiResult['error'] : "unexpected answer";
    echo "<h2 style='color:#C00000'>HTTP " . $answer['status'] . ": " . htmlspecialchars($msg) . "</h2>\n";
}
else {
    echo "<b>" . (int)$apiResult['inserted'] . "</b> added, ";
    echo "<b>" . (int)$apiResult['updated']  . "</b> updated, ";
    echo "<b>" . (int)$apiResult['deleted']  . "</b> removed, ";
    echo "<b>" . (int)$apiResult['unchanged'] . "</b> already matched.<br>\n";
}

echo "<br><br>Done.<br>";
//The local connection was closed before the upload, and there is no remote
//connection any more - closing $connect_string again here is a fatal error
//in PHP 8 ("mysqli object is already closed"), which @ does not suppress.


?>
<br>
</body>
</html>
