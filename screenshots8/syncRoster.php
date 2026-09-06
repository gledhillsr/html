 <?php
 //NOTE TO BRIAN(me): Only thing changed here is $showdebug was changed to its current constant
 //	and indenting.
 //	config.php now has PEAR::DB functionality and has been tested


require("config.php");
require("Patroller.php");
if (file_exists ( "runningFromWeb.php" )) {
    include("runningFromWeb.php");
}


$roster = [];
$count = 0;

 //Contants
$SHOW_DEBUG = true;

 //==========================
 //  showProgressIndicator
 //==========================

function showProgressIndicator() {
    global $mysqli_host, $totalPatrollers, $lockerApiUrl;

    //The counting queries that used to live here ran over a MySQL connection to
    //gledhills.com. That connection is gone - the roster now arrives in one JSON
    //response - so the total is simply how many members came back.
    echo "<div align=center>\n";
    echo "  <center>\n";
    echo "<font size=5>---Downloading 'roster' from the web site to $mysqli_host---<br></font>\n";
    echo "<font size=2>$lockerApiUrl</font><br>\n";
    echo "  <table border=1 cellpadding=0 cellspacing=0 style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=500 >\n";
    echo "    <tr>\n";
    echo "      <td bgcolor=\"#0000FF\" width=500>&nbsp;</td>\n";
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
 //==========================
 //  carryPasswordsForward
 //==========================
 // The roster arrives from the web site with no Password / newPassword column, and that stays
 // that way on purpose - shipping password hashes over the API is a far worse trade than not
 // having them here. So the passwords already on THIS machine are copied into the new table
 // before it takes over, matched on IDNumber.
 //
 // Without this the rename left every member with a blank password, which the app reads as
 // "no password set" and answers by accepting the member's LAST NAME instead. Login appeared
 // broken for anyone who typed their real password, and was wide open to anyone who did not.
 //
 // Note this keeps the local passwords rather than fetching current ones, so a password
 // changed on the web site since the last sync will not follow it down here. Nothing is
 // carried for a member who is new since the last sync - there is no local row to copy from.
 function carryPasswordsForward($connect_string, $tmp_roster) {
     $tables = @mysqli_query($connect_string, "SHOW TABLES LIKE 'roster'");
     if (!$tables || mysqli_num_rows($tables) == 0) {
         echo "&nbsp;&nbsp;&nbsp;no existing 'roster' here to carry passwords from (first sync?)<br>";
         return;
     }

     $sets = array("t.`Password` = r.`Password`");
     //a roster left behind by a sync from before this was fixed has no newPassword column at all
     $columns = @mysqli_query($connect_string, "SHOW COLUMNS FROM `roster` LIKE 'newPassword'");
     if ($columns && mysqli_num_rows($columns) > 0) {
         $sets[] = "t.`newPassword` = r.`newPassword`";
     }
     else {
         echo "&nbsp;&nbsp;&nbsp;<font color='red'>the existing 'roster' has no newPassword column, so only the" .
              " old plaintext Password can be carried. Hashed passwords are gone - restore them from a" .
              " roster_old that still has the column, or reset them.</font><br>";
     }

     $query_string = "UPDATE `" . $tmp_roster . "` t JOIN `roster` r ON r.`IDNumber` = t.`IDNumber`" .
                     " SET " . implode(", ", $sets) . ";";
     $result = @mysqli_query($connect_string, $query_string)
         or die("Error: carrying passwords forward failed on " . $query_string .
                " MYSQL error:" . mysqli_error($connect_string));
     echo "&nbsp;&nbsp;&nbsp;passwords carried forward: " . mysqli_affected_rows($connect_string) .
          " member(s) updated<br>";
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

     //--------------------------------------------------------------
     // Read the roster from the web site over HTTPS.
     //
     // This used to be a MySQL connection straight to gledhills.com, pulled down
     // in blocks of $blockSize because the link was slow and remote. Port 3306
     // there is firewalled, so that never actually worked from here - and it
     // meant this machine held a production database credential. It is now one
     // request to LockerRoomApiController, which returns every member at once,
     // so the block paging is gone with it.
     //
     // NOTE: the response deliberately carries no Password / newPassword column,
     // so no password hash ever crosses the network. The passwords already on this
     // machine are copied into the new table instead - see carryPasswordsForward(),
     // called just before the rename.
     //--------------------------------------------------------------
showProgressIndicator();

if (isset($startPatroller)) {
    if (empty($lockerApiUrl) || empty($lockerApiKey)) {
        die("<h2>Not configured: set \$lockerApiUrl and \$lockerApiKey in dbconfig.php</h2>");
    }
    $url = rtrim($lockerApiUrl, "/") . "/roster?resort=" . rawurlencode($lockerResort);
    if ($SHOW_DEBUG) echo "reading roster from $url<br>";

    $answer = webFetch($url, $lockerApiKey);
    if ($answer['error'] !== "") {
        die("<h2>Could not reach the web site: " . htmlspecialchars($answer['error']) . "</h2>");
    }
    $data = json_decode($answer['body'], true);
    if (!is_array($data) || !isset($data['members'])) {
        $msg = (is_array($data) && isset($data['error'])) ? $data['error'] : "unreadable answer";
        die("<h2>HTTP " . $answer['status'] . ": " . htmlspecialchars($msg) . "</h2>");
    }

    foreach ($data['members'] as $row) {
        $patroller = new Patroller();
        $patroller->init_from_row_query($row);
        $roster[] = $patroller;
    }
    $totalPatrollers = count($roster);
    $count = $totalPatrollers;
    $recordsProcessed = 0;
    echo "Read $count patrollers from the web site.<br>";

    $tmp_roster = "tmp_roster";

    echo "open local connection, and processing roster into $mysqli_host<br>";
    $connect_string = @mysqli_connect($mysqli_host, $mysqli_username, $mysqli_password) or die ("Could not connect to the database.");
     mysqli_select_db($connect_string, $mysqli_db);

    if($startPatroller == 0) {
        //STARTING FIRST BLOCK OF NAMES: SETUP TEMPORARY DATABASE TABLES
        //delete previous temp roster
        $query_string = getSQLRosterDrop($tmp_roster);
        $result = @mysqli_query($connect_string, $query_string) or die ("Error: new_roster DROP Failed on " . $query_string . " MYSQL error:" .mysqli_error($connect_string) );
        echo "&nbsp;&nbsp;&nbsp;'$tmp_roster' DROP Successful<br>";

        //create temp roster
        $rosterDB = new Patroller();
        $query_string = $rosterDB->getSQLRosterCreate($tmp_roster);
        $result = @mysqli_query($connect_string, $query_string) or die ("  - $tmp_roster CREATE Failed on " . $query_string . " MYSQL error:" . mysqli_error($connect_string) );
        echo "&nbsp;&nbsp;&nbsp;'$tmp_roster' CREATE Successful<br>";
    }

    reset($roster);
    $count = 0;
    foreach ($roster as $key => $val) {
        $query_string = $val->getSQLRosterInsert($tmp_roster, $connect_string);
        //====================================================================
        if ($SHOW_DEBUG) echo $query_string . "<br>";
        $result = @mysqli_query($connect_string, $query_string) or die ("  - $tmp_roster INSERT Failed on " . $query_string . " MYSQL error:" . mysqli_error($connect_string));
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
        $result = @mysqli_query($connect_string, $query_string) or die ("Error: on \"" . $query_string . "\" MYSQL error:" .mysqli_error($connect_string) );
        echo "&nbsp;&nbsp;&nbsp;'$query_string' was Successful<br>";
        if($runningFromWeb) {
            echo "<font color='red' size=4>Synchronization is <b>Disabled</b> while viewing from web. ROSTER NEVER UPDATED</font><br>";
            echo "but ($tmp_roster) EXISTS<br>\n";
        }
        else {
            //must run before the rename below, while `roster` is still the old table
            carryPasswordsForward($connect_string, $tmp_roster);

            $query_string = "RENAME TABLE roster TO $new_roster;";
            $result = @mysqli_query($connect_string, $query_string) or die ("Error: on \"" . $query_string . "\" MYSQL error:" .mysqli_error($connect_string) );
            echo "&nbsp;&nbsp;&nbsp;'$query_string' was Successful<br>";

            $query_string = "RENAME TABLE $tmp_roster TO roster;";
            $result = @mysqli_query($connect_string, $query_string) or die ("Error: on \"" . $query_string . "\" MYSQL error:" .mysqli_error($connect_string) );
            echo "&nbsp;&nbsp;&nbsp;'$query_string' was Successful<br>";
        }
    }  //end renaming roster tables
    else {
        //prepare to process next block of names.   was removed
    }
    if ($SHOW_DEBUG) echo "close local connection<br>";
    @mysqli_close($connect_string);	//close connection to localhost (finished writing block locally)
} //end isset($startPatroller)

 //@mysqli_free_result($result);
echo "<h2>Finished downloading roster. $count total patrollers Downloaded to $mysqli_host.</h2>\n";
 ?>
 <br>
 </body>
 </html>
