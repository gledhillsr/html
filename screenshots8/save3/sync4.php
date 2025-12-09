 <?php
 //NOTE TO BRIAN(me): Only thing changed here is $showdebug was changed to its current constant
 //	and indenting.
 //	config.php now has PEAR::DB functionality and has been tested


 require("config.php");
 $arrDate = getdate();
 $today=mktime(0, 0, 0, $arrDate['mon'], $arrDate['mday'], $arrDate['year']);
 //    $strToday = date("F-d-Y h:m", $today);
 $strToday = date("F-d-Y h:i:s a");
 //echo "$mysqli_host at: $strToday<br>";
 $roster = [];

 //Contants
$SHOW_DEBUG = false;


 //
 //  class Patroller
 //

 class Patroller {
     public $IDNumber;
     public $ClassificationCode;
     public $LastName;
     public $FirstName;
     public $Spouse;
     public $Address;
     public $City;
     public $State;
     public $ZipCode;
     public $HomePhone;
     public $WorkPhone;
     public $CellPhone;
     public $Pager;
     public $email;
     public $EmergencyCallUp;
     public $Password;
     public $NightSubsitute;
     public $Commitment;
     public $Instructor;
     public $Director;
     public $lastUpdated;
     public $carryOverCredits;
     public $lastCreditUpdate;
     public $canEarnCredits;
     public $creditsEarned;
     public $creditsUsed;
     public $teamLead;
     public $mentoring;
     public $comments;

     function add_info($row) {
 	//foreach ($row as $key => $value){
 	//    $this->$key = $value;
 	//}
 	$this->IDNumber = $row[\IDNUMBER];
 	$this->ClassificationCode = $row[\CLASSIFICATIONCODE];
 	$this->LastName = $row[\LASTNAME];
 	$this->FirstName = $row[\FIRSTNAME];
 	$this->Spouse = $row[\SPOUSE];
 	$this->Address = $row[\ADDRESS];
 	$this->City = $row[\CITY];
 	$this->State = $row[\STATE];
 	$this->ZipCode = $row[\ZIPCODE];
 	$this->HomePhone = $row[\HOMEPHONE];
 	$this->WorkPhone = $row[\WORKPHONE];
 	$this->CellPhone = $row[\CELLPHONE];
 	$this->Pager = $row[\PAGER];
 	$this->email = $row[\EMAIL];
 	$this->EmergencyCallUp = $row[\EMERGENCYCALLUP];
 	$this->Password = $row[\PASSWORD];
 	$this->NightSubsitute = $row[\NIGHTSUBSITUTE];
 	$this->Commitment = $row[\COMMITMENT];
 	$this->Instructor = $row[\INSTRUCTOR];
 	$this->Director = $row[\DIRECTOR];
 	$this->lastUpdated = $row[\LASTUPDATED];
 	$this->carryOverCredits = $row[\CARRYOVERCREDITS];
 	$this->lastCreditUpdate = $row[\LASTCREDITUPDATE];
 	$this->canEarnCredits = $row[\CANEARNCREDITS];
 	$this->creditsEarned = $row[\CREDITSEARNED];
 	$this->creditsUsed = $row[\CREDITSUSED];
 	$this->teamLead = $row[\TEAMLEAD];
 	$this->mentoring = $row[\MENTORING];
 	$this->comments = $row[\COMMENTS];
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
 	" ) ;";
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
 <meta http-equiv="Content-Language" content="en-us">
 <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
 <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
 <META HTTP-EQUIV="Expires" CONTENT="-1">
 <title>Synchronize with www.BrightonNSP.org</title>
 </head>

 <body background="images/ncmnthbk.jpg">

 </body>
 </html>

 <?php
//     $suffix = date("Y_m_d");
 $suffix = "old";
 /******************************************************************/
 /* Synchronize the ROSTER from gledhills.com to the local machine */
 /******************************************************************/
 if(isset($startPatroller)) {
     //----------------------------------------------
     // setup variables for ROSTER progress indicator (from gledhills.com)
     //----------------------------------------------
     if (SHOW_DEBUG) echo "connect to remote machine<br>";
     $connect_string = @mysqli_connect($gledhills_host, $mysqli_username, $mysqli_password) or die ("Could not connect to the database at $gledhills_host.");
     mysqli_select_db($connect_string, $mysqli_db);
 //get total patroller count
     $query_string = "SELECT COUNT(IDNumber) AS count  FROM roster WHERE 1";
     $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result l Roster)");
     if ($row = @mysqli_fetch_array($result)) {
 	$totalPatrollers=$row[\COUNT];
     }
 //get patrollers processed
     $query_string = "SELECT COUNT(IDNumber) AS count  FROM roster WHERE IDNumber<=$startPatroller";
     $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result 2 Roster)");
     if ($row = @mysqli_fetch_array($result)) {
     	$recordsProcessed=$row[\COUNT];
     }
 } //end isset($startPatroller)

 //setup progress indicator
 echo "<div align=center>\n";
 echo "  <center>\n";
 echo "<font size=5>---Downloading 'roster' from $gledhills_host to $mysqli_host---<br></font>\n";
 $blockSize=1000;
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
 if(isset($startPatroller)) {
     echo "      <td width=$right>&nbsp;</td>\n";
 }
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
     $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result 3 roster)");
     $count = 0;
     while ($row = @mysqli_fetch_array($result)) {
         $patroller = new Patroller();
         $lastPatrollerID = $row[\IDNUMBER];
         $patroller->add_info($row);
         $count += 1;
         $tmp = $recordsProcessed + $count;
         echo "reading from web. ID $lastPatrollerID ($tmp /$totalPatrollers)<br>";
         $roster[] = $patroller;
     }

     if ($SHOW_DEBUG) echo "Reading 'roster' from $gledhills_host was Successful, now close remote connection<br>";

     @mysqli_close($connect_string);	//close gledhills.com connection
     $tmp_roster = "tmp_roster";

     if ($SHOW_DEBUG) echo "open local connection, and processing the following on $mysqli_host<br>";
     $connect_string = @mysqli_connect($mysqli_host, $mysqli_username, $mysqli_password) or die ("Could not connect to the database.");
     mysqli_select_db($connect_string, $mysqli_db);

     if($startPatroller == 0) {
         //delete previous temp roster
         $query_string = getSQLRosterDrop($tmp_roster);
         $result = @mysqli_query($connect_string, $query_string) or die ("Error: new_roster DROP Failed on " . $query_string . " MYSQL error:" .mysqli_error() );
         echo "&nbsp;&nbsp;&nbsp;'$tmp_roster' DROP Successful<br>";

         //create temp roster
         $query_string = getSQLRosterCreate($tmp_roster);
         echo "&nbsp;&nbsp;&nbsp;'$tmp_roster' CREATE Successful<br>";
         $result = @mysqli_query($connect_string, $query_string) or die ("  - $tmp_roster CREATE Failed on " . $query_string . " MYSQL error:" . mysqli_error() );
     }

     reset($roster);
     $count = 0;
     foreach ($roster as $key => $val) {
         $query_string = $val->getSQLRosterInsert($tmp_roster);
         //====================================================================
         echo $query_string . "<br>";
         //todo remove
         $result = @mysqli_query($connect_string, $query_string) or die ("  - $tmp_roster INSERT Failed on " . $query_string . " MYSQL error:" . mysqli_error());
         //====================================================================
         $count++;
     }
     if ($SHOW_DEBUG) echo "&nbsp;&nbsp;&nbsp;All the NEW $count patrollers were inserted into , '$tmp_roster' Successfully<br>";

     if ($SHOW_DEBUG) echo "recordsProcessed=$recordsProcessed, count=$count, totalpatrollers=$totalPatrollers<br>";
     if($recordsProcessed + $count >= $totalPatrollers) {
         $new_roster = "roster_" . $suffix;
         $query_string = "DROP TABLE IF EXISTS " . $new_roster . ";";
         $result = @mysqli_query($connect_string, $query_string) or die ("Error: on \"" . $query_string . "\" MYSQL error:" .mysqli_error() );
         echo "&nbsp;&nbsp;&nbsp;'$query_string' was Successful<br>";
 echo "roster coppied to $tmp_roster.  BUT NOT RENAMED TO -ROSTER-";
         $query_string = "RENAME TABLE roster TO $new_roster;";
         $result = @mysqli_query($connect_string, $query_string) or die ("Error: on \"" . $query_string . "\" MYSQL error:" .mysqli_error() );
         echo "&nbsp;&nbsp;&nbsp;'$query_string' was Successful<br>";
 
         $query_string = "RENAME TABLE $tmp_roster TO roster;";
         $result = @mysqli_query($connect_string, $query_string) or die ("Error: on \"" . $query_string . "\" MYSQL error:" .mysqli_error() );
         echo "&nbsp;&nbsp;&nbsp;'$query_string' was Successful<br>";
     } //end renaming roster tables
 	if (SHOW_DEBUG) echo "close local connection<br>";
     @mysqli_close($connect_string);	//close connection to localhost (finished writing block locally)
 } //end isset($startPatroller)

 ////@mysqli_free_result($result);

 ?>
 <br>
 </body>
 </html>
