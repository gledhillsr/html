<?php
//NOTE TO BRIAN(me): Only thing changed here is $showdebug was changed to its current constant
//	and indenting.  
//	config.php now has PEAR::DB functionality and has been tested


require("config.php");
$arrDate = getdate();
$today=mktime(0, 0, 0, $arrDate['mon'], $arrDate['mday'], $arrDate['year']);
//    $strToday = date("F-d-Y h:m", $today);
$strToday = date("F-d-Y h:i:s a");
//echo "$mysql_host at: $strToday<br>";
$roster = array();
$skihistory = array();

//Contants
SHOW_DEBUG = true;

/**********************/
/*  class SklHistory  */
/**********************/
class SkiHistory {
    var $date;
    var $checkin;
    var $areaID;
    var $shift;
    var $value;
    var $multiplier;
    var $patroller_id;
    var $history_id;
    var $sweep_ids;
    var $teamLead;
    var $name;

/**************/
/*  add_info  */
/**************/
    function add_info($row) {
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

/****************************/
/*  getSQLSkiHistoryInsert  */
/****************************/
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

/****************************/
/*  getSQLSkiHistoryCreate  */
/****************************/

function getSQLSkiHistoryCreate($table) {
    //#
    //# Table structure for table `skihistory`
    //#
    return "CREATE TABLE `" . $table . "` (" .
	" `date` int(11) NOT NULL default '0'," .
	" `checkin` mediumint(9) NOT NULL default '0'," .
	" `areaID` tinyint(4) NOT NULL default '0'," .
	" `shift` tinyint(4) NOT NULL default '0'," .
	" `value` tinyint(4) NOT NULL default '0'," .
	" `multiplier` tinyint(4) NOT NULL default '0'," .
	" `patroller_id` varchar(6) NOT NULL default ''," .
	" `history_id` mediumint(9) NOT NULL auto_increment," .
	" `sweep_ids` text NOT NULL," .
	" `teamLead` tinyint(4) NOT NULL default '0'," .
	" `name` varchar(24) NOT NULL default ''," .
	" UNIQUE KEY `history_id` (`history_id`)" .
	") TYPE=MyISAM AUTO_INCREMENT=1253" ;
}

/**************************/
/* 	getSQLSkiHistoryDrop  */
/**************************/
function getSQLSkiHistoryDrop($table) {
	return "DROP TABLE IF EXISTS " . $table . ";";
}
//---------------------------------------------------------------
//---------------- end of 'skihistory' class and functions ------
//---------------------------------------------------------------


/*********************/
/*  class Patroller  */
/*********************/
class Patroller {
    var	$IDNumber;
    var	$ClassificationCode;
    var	$LastName;
    var	$FirstName;
    var	$Spouse;
    var	$Address;
    var	$City;
    var	$State;
    var	$ZipCode;
    var	$HomePhone;
    var	$WorkPhone;
    var	$CellPhone;
    var	$Pager;
    var	$email;
    var	$EmergencyCallUp;
    var	$Password;
    var	$NightSubsitute;
    var	$Commitment;
    var	$Instructor;
    var	$Director;
    var	$lastUpdated;
    var	$carryOverCredits;
    var	$lastCreditUpdate;
    var	$canEarnCredits;
    var	$creditsEarned;
    var	$creditsUsed;
    var	$teamLead;
    var	$mentoring;
    var	$comments;

    function add_info($row) {
	//foreach ($row as $key => $value){
	//    $this->$key = $value;
	//}
	$this->IDNumber = $row[IDNumber];
	$this->ClassificationCode = $row[ClassificationCode];
	$this->LastName = $row[LastName];
	$this->FirstName = $row[FirstName];
	$this->Spouse = $row[Spouse];
	$this->Address = $row[Address];
	$this->City = $row[City];
	$this->State = $row[State];
	$this->ZipCode = $row[ZipCode];
	$this->HomePhone = $row[HomePhone];
	$this->WorkPhone = $row[WorkPhone];
	$this->CellPhone = $row[CellPhone];
	$this->Pager = $row[Pager];
	$this->email = $row[email];
	$this->EmergencyCallUp = $row[EmergencyCallUp];
	$this->Password = $row[Password];
	$this->NightSubsitute = $row[NightSubsitute];
	$this->Commitment = $row[Commitment];
	$this->Instructor = $row[Instructor];
	$this->Director = $row[Director];
	$this->lastUpdated = $row[lastUpdated];
	$this->carryOverCredits = $row[carryOverCredits];
	$this->lastCreditUpdate = $row[lastCreditUpdate];
	$this->canEarnCredits = $row[canEarnCredits];
	$this->creditsEarned = $row[creditsEarned];
	$this->creditsUsed = $row[creditsUsed];
	$this->teamLead = $row[teamLead];
	$this->mentoring = $row[mentoring];
	$this->comments = $row[comments];
    }

    function getSQLRosterInsert($table) {
	return "INSERT INTO `" . $table . "` VALUES (" 
	    . "'". $this->IDNumber
	    . "', '" . $this->ClassificationCode 
	    . "', '" . $this->LastName
	    . "', '" . $this->FirstName
	    . "', '" . $this->Spouse
	    . "', '" . $this->Address
	    . "', '" . $this->City
	    . "', '" . $this->State
	    . "', '" . $this->ZipCode
	    . "', '" . $this->HomePhone
	    . "', '" . $this->WorkPhone
	    . "', '" . $this->CellPhone
	    . "', '" . $this->Pager
	    . "', '" . $this->email
	    . "', '" . $this->EmergencyCallUp
	    . "', '" . $this->Password
	    . "', '" . $this->NightSubsitute
	    . "', '" . $this->Commitment
	    . "', '" . $this->Instructor
	    . "', '" . $this->Director
	    . "', '" . $this->lastUpdated
	    . "', '" . $this->carryOverCredits
	    . "', '" . $this->lastCreditUpdate
	    . "', '" . $this->canEarnCredits
	    . "', '" . $this->creditsEarned
	    . "', '" . $this->creditsUsed
	    . "', '" . $this->teamLead
	    . "', '" . $this->mentoring
	    . "', '" . $this->comment
	    . "');";
    }

    function display() {
//	echo $this->FirstName . " " . $this->LastName . ": " ;
//	echo $this->FirstName . " " . $this->LastName . ": ". $this->IDNumber . "<br>";
	echo serialize($this) . "<br>";
    }

} //end class Patroller

/************************/
/*  getSQLRosterCreate  */
/************************/
function getSQLRosterCreate($table) {
    //#
    //# Table structure for table `roster`
    //#
    return "CREATE TABLE `" . $table . "` (" .
	" `IDNumber` varchar(6) NOT NULL default ''," .
	" `ClassificationCode` varchar(4) default NULL," .
	" `LastName` varchar(24) NOT NULL default ''," .
	" `FirstName` varchar(24) NOT NULL default ''," .
	" `Spouse` varchar(24) default NULL," .
	" `Address` varchar(48) default NULL," .
	" `City` varchar(32) default NULL," .
	" `State` varchar(16) default NULL," .
	" `ZipCode` varchar(10) default NULL," .
	" `HomePhone` varchar(26) default NULL," .
	" `WorkPhone` varchar(26) default NULL," .
	" `CellPhone` varchar(26) default NULL," .
	" `Pager` varchar(26) default NULL," .
	" `email` varchar(48) default NULL," .
	" `EmergencyCallUp` varchar(8) default NULL," .
	" `Password` varchar(16) NOT NULL default ''," .
	" `NightSubsitute` varchar(4) default NULL," .
	" `Commitment` tinyint(4) NOT NULL default '2'," .
	" `Instructor` tinyint(4) NOT NULL default '0'," .
	" `Director` varchar(10) default NULL," .
	" `lastUpdated` date NOT NULL default '2003-01-01'," .
	" `carryOverCredits` smallint(6) NOT NULL default '0'," .
	" `lastCreditUpdate` bigint(11) NOT NULL default '0'," .
	" `canEarnCredits` tinyint(4) NOT NULL default '0'," .
	" `creditsEarned` smallint(6) NOT NULL default '0'," .
	" `creditsUsed` smallint(6) NOT NULL default '0'," .
	" `teamLead` tinyint(4) NOT NULL default '0'," .
	" `mentoring` tinyint(4) NOT NULL default '0'," .
	" `comments` text NOT NULL default ''," .
	" PRIMARY KEY  (`IDNumber`)" .
	" ) TYPE=MyISAM;";
}

/**********************/
/* 	getSQLRosterDrop  */
/**********************/
function getSQLRosterDrop($table) {
    return "DROP TABLE IF EXISTS " . $table . ";";
}
//-----------------------------------------------------------
//---------------- end of 'roster' class and functions ------
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

//    $suffix = date("Y_m_d");
$suffix = "old";
$totalHistories=1;
/******************************************************************/
/* Synchronize the ROSTER from gledhills.com to the local machine */
/******************************************************************/
if(isset($startPatroller)) {
    //----------------------------------------------
    // setup variables for ROSTER progress indicator (from gledhills.com)
    //----------------------------------------------
    if (SHOW_DEBUG) echo "connect to remote machine<br>";
    $connect_string = @mysql_connect($gledhills_host, $mysql_username, $mysql_password) or die ("Could not connect to the database at $gledhills_host.");
//get total patroller count
    $query_string = "SELECT COUNT(IDNumber) AS count  FROM roster WHERE 1";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result l Roster)");
    if ($row = @mysql_fetch_array($result)) {
	$totalPatrollers=$row[count];
    }
//get patrollers processed
    $query_string = "SELECT COUNT(IDNumber) AS count  FROM roster WHERE IDNumber<=$startPatroller";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result 2 Roster)");
    if ($row = @mysql_fetch_array($result)) {
	$recordsProcessed=$row[count];
    }
} //end isset($startPatroller)

//setup progress indicator
echo "<div align=center>\n";
echo "  <center>\n";
echo "<font size=5>---Downloading 'roster' from $gledhills_host to $mysql_host---<br></font>\n";
$blockSize=10;
if(isset($startPatroller)) {
    $left = 500  * $recordsProcessed / $totalPatrollers;
    $right = 500 - $left;
} else {
    $left = 500;
    $right = 0;
}

//--------------------------------------
// display progress indicator for ROSTER
//--------------------------------------
echo "  <table border=1 cellpadding=0 cellspacing=0 style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=500 >\n";
echo "    <tr>\n";
echo "      <td bgcolor=\"#0000FF\" width=$left>&nbsp;</td>\n";
if(isset($startPatroller)) 
    echo "      <td width=$right>&nbsp;</td>\n";
echo "    </tr>\n";
echo "  </table>\n";
echo "  </center>\n";
echo "</div>\n";

//----------------------------------------------------
// copy block of names from remote ROSTER to localhost
//----------------------------------------------------
if(isset($startPatroller)) {
    $query_string = "SELECT * FROM roster WHERE IDNumber>$startPatroller ORDER BY IDNumber LIMIT $blockSize";

    echo "$query_string<br>";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result 3 roster)");
    $count = 0;
    while ($row = @mysql_fetch_array($result)) {
	$patroller = new Patroller();
	$lastPatrollerID = $row[IDNumber];
	$patroller->add_info($row);
	$count += 1;
	$tmp = $recordsProcessed + $count;
    echo "ID $lastPatrollerID ($tmp /$totalPatrollers)<br>";
	$roster[] = $patroller;
    }

    if (SHOW_DEBUG) echo "Reading 'roster' from $gledhills_host was Successful, now close remote connection<br>";

    @mysql_close($connect_string);	//close gledhills.com connection
    $tmp_roster = "tmp_roster";

    if (SHOW_DEBUG) echo "open local connection, and processing the following on $mysql_host<br>";
    $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");

    if($startPatroller == 0) {

	//delete previous temp roster
	$query_string = getSQLRosterDrop($tmp_roster);
	$result = @mysql_db_query($mysql_db, $query_string) or die ("Error: new_roster DROP Failed on " . $query_string . " MYSQL error:" .mysql_error() );
	echo "&nbsp;&nbsp;&nbsp;'$tmp_roster' DROP Successful<br>";

	//create temp roster
	$query_string = getSQLRosterCreate($tmp_roster);
	echo "&nbsp;&nbsp;&nbsp;'$tmp_roster' CREATE Successful<br>";
	$result = @mysql_db_query($mysql_db, $query_string) or die ("  - $tmp_roster CREATE Failed on " . $query_string . " MYSQL error:" . mysql_error() );
    }

    reset($roster);
    $count = 0;
    while (list($key, $val) = each($roster)) {
	$query_string = $val->getSQLRosterInsert($tmp_roster);
	$result = @mysql_db_query($mysql_db, $query_string) or die ("  - $tmp_roster INSERT Failed on " . $query_string . " MYSQL error:" . mysql_error());
	$count++;
    }
    if (SHOW_DEBUG) echo "&nbsp;&nbsp;&nbsp;All the NEW $count patrollers were inserted into , '$tmp_roster' Successfully<br>";

    if (SHOW_DEBUG) echo "recordsProcessed=$recordsProcessed, count=$count, totalpatrollers=$totalPatrollers<br>";
    if($recordsProcessed + $count >= $totalPatrollers) {
	$new_roster = "roster_" . $suffix;
	$query_string = "DROP TABLE IF EXISTS " . $new_roster . ";";
	$result = @mysql_db_query($mysql_db, $query_string) or die ("Error: on \"" . $query_string . "\" MYSQL error:" .mysql_error() );
	echo "&nbsp;&nbsp;&nbsp;'$query_string' was Successful<br>";

	$query_string = "RENAME TABLE roster TO $new_roster;";
	$result = @mysql_db_query($mysql_db, $query_string) or die ("Error: on \"" . $query_string . "\" MYSQL error:" .mysql_error() );
	echo "&nbsp;&nbsp;&nbsp;'$query_string' was Successful<br>";

	$query_string = "RENAME TABLE $tmp_roster TO roster;";
	$result = @mysql_db_query($mysql_db, $query_string) or die ("Error: on \"" . $query_string . "\" MYSQL error:" .mysql_error() );
	echo "&nbsp;&nbsp;&nbsp;'$query_string' was Successful<br>";
    } //end renaming roster tables
		if (SHOW_DEBUG) echo "close local connection<br>";
    @mysql_close($connect_string);	//close connection to localhost (finished writing block locally)
} //end isset($startPatroller)

//connect to the local database
if (SHOW_DEBUG)echo "open local connection: $mysql_host<br>";
$connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");

//--------------------------------------------------------------------
if (SHOW_DEBUG) echo "Reading ski histroy from $mysql_host<br>";
//--------------------------------------------------------------------

if(!isset($startPatroller)) {
    $query_string = "SELECT COUNT(history_id) AS count  FROM skihistory WHERE 1";
    //echo $query_string . "<br>";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result l skiHistory)");
    if ($row = @mysql_fetch_array($result)) {
	$totalHistories=$row[count];
    }
    $historiesProcessed = 0;

    //get patrollers processed
    $query_string = "SELECT COUNT(history_id) AS count  FROM skihistory WHERE history_id<$startHistoryID";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result 2 history)");
    if ($row = @mysql_fetch_array($result)) {
      $historiesProcessed=$row[count];
    }

    $blockSize=10;
    //$blockSize=300;
    //echo "setting blocksize to 300<br>";
    $left = 500  * $historiesProcessed / $totalHistories;	 
    $right = 500 - $left;
} else {
    $right = 500;
}

echo "<div align=center>\n";
echo "  <center>\n";
echo "<br><font size=5>---Uploading 'skihistory' from $mysql_host to $gledhills_host---<br></font>\n";
echo "  <table border=1 cellpadding=0 cellspacing=0 style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=500 >\n";
echo "    <tr>\n";
if(!isset($startPatroller)) 
    echo "      <td bgcolor=\"#0000FF\" width=$left>&nbsp;</td>\n";
echo "      <td width=$right>&nbsp;</td>\n";
echo "    </tr>\n";
echo "  </table>\n";
echo "  </center>\n";
echo "</div>\n";
$historiesProcessed = 0;

if(!isset($startPatroller)) {
    //get patrollers processed
    $query_string = "SELECT COUNT(history_id) AS count  FROM skihistory WHERE history_id < $startHistoryID";

    echo "****** query_string=" . $query_string . "<br>\n";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result 2 Roster)");
    if ($row = @mysql_fetch_array($result)) {
	$historiesProcessed=$row[count];
	echo "****** historiesProcessed=" . $historiesProcessed . "<br>\n";
    }

    $query_string = "SELECT * FROM skihistory WHERE history_id >= $startHistoryID LIMIT $blockSize";

    if (SHOW_DEBUG) echo "$query_string<br>\n";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result 2 skiHistory)");
    while ($row = @mysql_fetch_array($result)) {
	$tmp = new SkiHistory();
	$lastID = $row[history_id];
//echo "id=" . $lastID . "<br>\n";
	$tmp->add_info($row);
	$skihistory[] = $tmp;
    }
}
/******************* restart this screen to update the progress indicators *****************/
if (SHOW_DEBUG) echo "*** isset(startpatroller)=" . isset($startPatroller) . ", historiesProcessed=$historiesProcessed, totalHistories=$totalHistories<br>";
if(isset($startPatroller) || $historiesProcessed < $totalHistories) {
    echo "<SCRIPT LANGUAGE=\"JavaScript\">\n";
    echo "<!--\n";
    echo "function redirect() {\n";
    echo "setTimeout(\"go_now()\",2);\n";
    echo "}\n";
    echo "function go_now() {\n";

    $lastID = $lastID+1;
    if(isset($startPatroller)&& $recordsProcessed + $count < $totalPatrollers)
	echo "window.location.href = \"Synchronize.php?startPatroller=$lastPatrollerID\"; \n";
    else {
	if(isset($startPatroller))
	    $lastID = 0;
	echo "window.location.href = \"Synchronize.php?startHistoryID=$lastID\"; \n";
    }
    echo "}\n";
    echo "-->\n";
    echo "</SCRIPT>\n";
}
/********************* end restart *********************************************************/

//close localhost
if (isset($startHistoryID)) {
    if (SHOW_DEBUG) echo "close local connection<br>\n";

    @mysql_close($connect_string);
    @mysql_free_result($result);

    if (SHOW_DEBUG) echo "open remote connection<br>\n";
    $connect_string = @mysql_connect($gledhills_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
    $tmp_history = "tmp_history";

    /****************************************************/
    /* initialize entrys for history (first time only)  */
    /****************************************************/
    if ($startHistoryID == 0) {
	//open gledhills.com

	//echo "-processing the following on $gledhills_host<br>";

	//drop tmp table on gldhills.com
	$query_string = getSQLSkiHistoryDrop($tmp_history);
	$result = @mysql_db_query($mysql_db, $query_string) or die ("Error: old_skihistory DROP Failed on " . $query_string . " MYSQL error:" .mysql_error() );
echo "&nbsp;&nbsp;&nbsp;'$query_string' was Successful<br>";  //drop tmp table

	//create tmp table on gldhills.com
	$query_string = getSQLSkiHistoryCreate($tmp_history);
	$result = @mysql_db_query($mysql_db, $query_string) or die ("  - $tmp_history CREATE Failed on " . $query_string . " MYSQL error:" . mysql_error() );
echo "&nbsp;&nbsp;&nbsp;'$tmp_history' CREATE Successful<br>";	//create tmp table
    } //end initial call

    /********************************************************************/
    /* copy a block of ski history data to tmp table on gledhills.com   */
    /********************************************************************/
    reset($skihistory);
    $count = 0;
    while (list($key, $val) = each($skihistory)) {
	$query_string = $val->getSQLSkiHistoryInsert($tmp_history);

	echo "$query_string<br>";
	$result = @mysql_db_query($mysql_db, $query_string) or die ("  - $tmp_history INSERT Failed on " . $query_string . " MYSQL error:" . mysql_error());
	$count++;
    }

    /************************************************************/
    /* rename tables on gledhills.com, to real skihistory table */
    /************************************************************/
    if($historiesProcessed >= $totalHistories) {

	//DELETE last saved skihistory_old
	$new_skihistory = "skihistory_" . $suffix;
	$query_string = "DROP TABLE IF EXISTS " . $new_skihistory . ";";
	$result = @mysql_db_query($mysql_db, $query_string) or die ("Error: $new_skihistory DROP Failed on " . $query_string . " MYSQL error:" .mysql_error() );

	echo "&nbsp;&nbsp;&nbsp;'$query_string' was Successful<br>";

	//RENAME skihistory to skihistory_old
	$query_string = "RENAME TABLE skihistory TO $new_skihistory;";
	$result = @mysql_db_query($mysql_db, $query_string) or die ("Error: $new_skihistory DROP Failed on " . $query_string . " MYSQL error:" .mysql_error() );
	echo "&nbsp;&nbsp;&nbsp;'$query_string' was Successful<br>";

	//RENAME tmp_history to skihistory
	$query_string = "RENAME TABLE $tmp_history TO skihistory;";
	$result = @mysql_db_query($mysql_db, $query_string) or die ("Error: $new_skihistory DROP Failed on " . $query_string . " MYSQL error:" .mysql_error() );
	echo "&nbsp;&nbsp;&nbsp;'$query_string' was Successful<br>";
	echo "<BR>===== FINISHED ====<BR>";
    }
} //end if $isset($startHistoryID)

//close last connection
@mysql_close($connect_string);
@mysql_free_result($result);

?>
<br>
<!--
Skipping download of assignments, directorsettings, and shiftdefinitions.<br>
Skipping upload of areadefinitions and sweepdefinitions.<br>

<br>PLEASE test this on <b>another</b> browser.  If the Synchronization failed, click here to <b>restore</b><br>
-->
</body>
</html>
