 <?php
 //NOTE TO BRIAN(me): Only thing changed here is $showdebug was changed to its current constant
 //	and indenting.
 //	config.php now has PEAR::DB functionality and has been tested


require("config.php");
require("Patroller.php");
if (file_exists ( "runningFromWeb.php" )) {
    include("runningFromWeb.php");
}


$roster = array();

 //Contants
$SHOW_DEBUG = true;

 //==========================
 //  showProgressIndicator
 //==========================

function showProgressIndicator() {
    global $connect_string, $startPatroller, $mysql_db, $mysql_host, $totalPatrollers, $recordsProcessed;
    global $SHOW_DEBUG, $blockSize, $gledhills_host;

    if(isset($startPatroller)) {
        //----------------------------------------------
        // setup variables for ROSTER progress indicator (from gledhills.com)
        //----------------------------------------------
        //get total patroller count
        $query_string = "SELECT COUNT(IDNumber) AS count  FROM roster WHERE 1";
        $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result l Roster)");
        if ($row = @mysql_fetch_array($result)) {
            $totalPatrollers=$row[count];
        }
        echo "total patroller records on web to syncronize: $totalPatrollers<br>\n";
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
    // if(isset($startPatroller)) {
    //     $left = 500  * $recordsProcessed / $totalPatrollers;
    //     $right = 500 - $left;
    // } else {
    $left = 500;
    $right = 0;
    //}

    //--------------------------------------
    // display progress indicator for ROSTER
    //--------------------------------------
    echo "  <table border=1 cellpadding=0 cellspacing=0 style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=500 >\n";
    echo "    <tr>\n";
    echo "      <td bgcolor=\"#0000FF\" width=$left>&nbsp;</td>\n";
    // if(isset($startPatroller)) {
    //     echo "      <td width=$right>&nbsp;</td>\n";
    // }
    echo "    </tr>\n";
    echo "  </table>\n";
    echo "  </center>\n";
    echo "</div>\n";
}

 //==========================
 // 	getSQLRosterDrop
 //==========================
 function getSQLRosterDrop($table) {
     return "DROP TABLE IF EXISTS " . $table . ";";
 }
 //-----------------------------------------------------------
 //---------------- end of functions -------------------------
 //-----------------------------------------------------------

 //*****************************************************************
 // Synchronize the ROSTER from gledhills.com to the local machine
 //******************************************************************
 ?>
 <html>

 <head>
 <meta http-equiv="Content-Language" content="en-us">
 <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
 <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
 <META HTTP-EQUIV="Expires" CONTENT="-1">
 <title>Synchronize with www.BrightonNSP.org</title>
 </head>

 <body background="images/ncmnthbk.jpg">

 <?php
//     $suffix = date("Y_m_d");
$suffix = "old";
$blockSize = 1000;

     if ($SHOW_DEBUG) echo "connect to remote machine<br>";
     $connect_string = @mysql_connect($gledhills_host, $mysql_username, $gledhills_mysql_password) or die ("Could not connect to the database at $gledhills_host.");

showProgressIndicator();

//----------------------------------------------------
// copy block of names from remote ROSTER to localhost
//----------------------------------------------------
 if(isset($startPatroller)) {
    //read roster from local
    $query_string = "SELECT * FROM roster WHERE IDNumber>$startPatroller ORDER BY IDNumber LIMIT $blockSize";
    if($SHOW_DEBUG) echo "$query_string<br>";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result 3 roster)");
    $count = 0;
    while ($row = @mysql_fetch_array($result)) {
        $patroller = new Patroller();
        $lastPatrollerID = $row[IDNumber];
        $patroller->init_from_row_query($row);
        $count += 1;
        $tmp = $recordsProcessed + $count;
        if ($SHOW_DEBUG) {
            echo "reading from web. ID $lastPatrollerID ($tmp /$totalPatrollers)<br>";
        } else {
	        echo ".";
	    }
        $roster[] = $patroller;
    }
    echo "<br>\n";
    echo "Reading 'roster' from $gledhills_host was Successful, now close remote connection.  Read $count patrollers.<br>";

    @mysql_close($connect_string);	//close gledhills.com connection
    $tmp_roster = "tmp_roster";

    echo "open local connection, and processing roster into $mysql_host<br>";
    $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");

    if($startPatroller == 0) {
        //STARTING FIRST BLOCK OF NAMES: SETUP TEMPORARY DATABASE TABLES
        //delete previous temp roster
        $query_string = getSQLRosterDrop($tmp_roster);
        $result = @mysql_db_query($mysql_db, $query_string) or die ("Error: new_roster DROP Failed on " . $query_string . " MYSQL error:" .mysql_error() );
        echo "&nbsp;&nbsp;&nbsp;'$tmp_roster' DROP Successful<br>";

        //create temp roster
        $rosterDB = new Patroller();
        $query_string = $rosterDB->getSQLRosterCreate($tmp_roster);
        echo "&nbsp;&nbsp;&nbsp;'$tmp_roster' CREATE Successful<br>";
        $result = @mysql_db_query($mysql_db, $query_string) or die ("  - $tmp_roster CREATE Failed on " . $query_string . " MYSQL error:" . mysql_error() );
    }

    reset($roster);
    $count = 0;
    while (list($key, $val) = each($roster)) {
        $query_string = $val->getSQLRosterInsert($tmp_roster);
        //====================================================================
        if ($SHOW_DEBUG) echo $query_string . "<br>"; 
        $result = @mysql_db_query($mysql_db, $query_string) or die ("  - $tmp_roster INSERT Failed on " . $query_string . " MYSQL error:" . mysql_error());
        echo ".";
        //====================================================================
        $count++;
    }
    echo "<br>";
    if ($SHOW_DEBUG) echo "&nbsp;&nbsp;&nbsp;All the NEW $count patrollers were inserted into , '$tmp_roster' Successfully<br>";

    if ($SHOW_DEBUG) echo "recordsProcessed=$recordsProcessed, count=$count, totalpatrollers=$totalPatrollers<br>";
    //
    //FINISHED -- THIS IS THE LAST BLOCK OF NAMES: RENAME OLD ROSTER, AND RENAME NEW TEMP ROSTER TO 'roster'
    //
    if($recordsProcessed + $count >= $totalPatrollers) {
        $new_roster = "roster_" . $suffix;
        $query_string = "DROP TABLE IF EXISTS " . $new_roster . ";";
        $result = @mysql_db_query($mysql_db, $query_string) or die ("Error: on \"" . $query_string . "\" MYSQL error:" .mysql_error() );
        echo "&nbsp;&nbsp;&nbsp;'$query_string' was Successful<br>";
        if($runningFromWeb) {
            echo "<font color='red' size=4>Synchronization is <b>Disabled</b> while viewing from web. ROSTER NEVER UPDATED</font><br>";
            echo "but ($tmp_roster) EXISTS<br>\n";
        }
        else {
            $query_string = "RENAME TABLE roster TO $new_roster;";
            $result = @mysql_db_query($mysql_db, $query_string) or die ("Error: on \"" . $query_string . "\" MYSQL error:" .mysql_error() );
            echo "&nbsp;&nbsp;&nbsp;'$query_string' was Successful<br>";

            $query_string = "RENAME TABLE $tmp_roster TO roster;";
            $result = @mysql_db_query($mysql_db, $query_string) or die ("Error: on \"" . $query_string . "\" MYSQL error:" .mysql_error() );
            echo "&nbsp;&nbsp;&nbsp;'$query_string' was Successful<br>";
        }
    }  //end renaming roster tables
    else {
        //prepare to process next block of names.   was removed
    }
    if ($SHOW_DEBUG) echo "close local connection<br>";
    @mysql_close($connect_string);	//close connection to localhost (finished writing block locally)
} //end isset($startPatroller)

 //@mysql_free_result($result);
echo "<h2>Finished downloading roster. $count total patrollers Downloaded to $mysql_host.</h2>\n";
 ?>
 <br>
 </body>
 </html>
