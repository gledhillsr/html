<?php
/*****************************************************************************
 * resort specific variables                                                 *
 ****************************************************************************/
$resortFull = "Utah Olympic Park";
$resort     = "UOP";
$resortURL  = "http://www.imd.org/uop.html";
$resortImg  = "uop.jpg";
$imgHeight  = 80;

/*****************************************************************************
 * register cookies, POST's, & GET's                                         *
 *****************************************************************************/

//if(!ini_get('register_globals'))
{
	$__am = array('COOKIE','POST','GET');
	while(list(,$__m) = each($__am)){
//var_dump($__m);
//echo "---" . ${"HTTP_".$__m."_VARS"} . "---<br>";
		$__ah = &${"HTTP_".$__m."_VARS"};
//var_dump($__ah);
		if(!is_array($__ah)) continue;
		while(list($__n, $__v) = each ($__ah)) {
//echo "yyy(".$__n."--".$__v.")<br>";
			$$__n = $__v;
		}
	}
}

/*****************************************************************************
 * email stuff                                                               *
 *****************************************************************************/

$email_to ="steve@Gledhills.com";
$email_headers = "From: brian@gledhills.com";

/*****************************************************************************
 * Edit password                                                             *
 *****************************************************************************/

$edit_password  = "";   // Password required to edit the database.
                     	//  (This is not the same as the MySQL password)

/*****************************************************************************
 * Database settings                                                         *
 *****************************************************************************/

$mysql_username = "root";           // MySQL user name
$mysql_password = "my_password";    // MySQL password (leave empty if no password is required.)
$mysql_db       = "brighton";        // MySQL database name

$mysql_host     = "localhost";      // MySQL server host name
                                    // ("localhost" should be fine on 
                                    // most systems)
$subagent_table = "subagent";      // MySQL table name
$masterAgent_table = "masteragent";      // MySQL table name

/*****************************************************************************
 * File locations                                                            *
 *****************************************************************************/

//$inventory_url  = "inventory.php";          // URL of the index.php file

$header_file    = "header.php";     // Path and name of the header file
$footer_file    = "footer.php";     // Path and name of the footer file
//$box_header_file= "box_header.php";	// Path and name of the footer file
$styles_file    = "styles.php";     // Path and name of the style sheet

/*****************************************************************************
 * IMDb link mode - See the README for more information.                     *
 *****************************************************************************/
$edit = 1;
$review_blank = "Not Yet Reviewed";
$review_n     = "Review OK";
$review_y     = "Needs Review";

/*****************************************************************************
 * Default maximum results per page.                                         *
 * Valid settings are "50", "100", "200" or "all"                            *
 * "all" displays all results from the database on a single page.            *
 *****************************************************************************/

$max_results_default = "50";    // Default Maximum results per page
									
/*****************************************************************************
 * Colors and misc. settings                                                 *
 *****************************************************************************/

$max_results_background_color = "#F0F0F0";   // Search box background color

$header_text_color       = "#000000";   // Header/Footer text color
$header_hover_color      = "red";       // Header/Footer hover color
$header_color            = "#C8C8C8";   // Header/Footer background color

$text_color              = "#000000";   // DVD Results text color
$text_hover_color        = "red";       // DVD Results hover color
$table_bgcolor           = "#FFFFFF";   // DVD Results table border color

$totals_color			 = "yellow";	// Row color for Totals
$row_color_1             = "#F7F7EF";   // Row 1 background color
$row_color_2             = "#F0F0E8";   // Row 2 background color

$body_bgcolor            = "#FFFFFF";   // Body background color
$body_text_color         = "#000000";   // Body text color
$body_link_color         = "#000000";   // Body link color
$body_vlink_color        = "#000000";   // Body visited link color
$body_alink_color        = "red";       // Body activated link color
$body_hover_color        = "red";       // Body hover color

$go_button               = "images/go.gif";    // URL of the "go" button used onthe page

$check_mark1              = "images/tick_red.gif";	//for Reset
$check_mark2              = "images/tick_green.gif";	//for Reset
$check_mark3              = "images/tick_blue.gif";

$green_dot               = "images/greendot.gif";	//for Adds, Moves, Changes
$yellow_dot              = "images/yellowdot.gif";
$red_dot                 = "images/reddot.gif";

/*****************************************************************************
 * The following variables define the name of each column, as it will        *
 * appear on the header                                                      *
 *****************************************************************************/
//---------- subAgent --------
//$subAgent_field[1][1] = "Login Name";   // MySQL varchar(10)
//$subAgent_field[1][2] = "Password";  	// MySQL varchar(10)
//$subAgent_field[1][3] = "Master Agent"; // MySQL varchar(10)
//$subAgent_field[1][4] = "Email"; 		// MySQL varchar(20)
//$subAgent_field[1][5] = "Login Count"; 	// MySQL int
//$subAgent_field[1][6] = "Agent Name";	// MySQL varchar(20)
//$subAgent_field[1][7] = "Company Name"; // MySQL varchar(20)
//$subAgent_field[1][8] = "Telephone"; 	// MySQL varchar(20)
//$subAgent_field[1][9] = "Fax"; 			// MySQL varchar(12)
/*****************************************************************************
 * The following variables define the column names to be retrieved from the  *
 * MySQL table. The Follwing fields MUST exactly match the Database          *
 *****************************************************************************/
//---------- subAgent --------------
$subAgent_field[2][1] = "loginID";        	// MySQL varchar(10)
$subAgent_field[2][2] = "password";  		// MySQL varchar(10)
$subAgent_field[2][3] = "masterAgent";    	// MySQL varchar(10)
$subAgent_field[2][4] = "email"; 			// MySQL varchar(20)
$subAgent_field[2][5] = "loginCount"; 		// MySQL int
$subAgent_field[2][6] = "firstName"; 		// MySQL varchar(20)
$subAgent_field[2][7] = "lastName"; 		// MySQL varchar(20)
$subAgent_field[2][8] = "companyName"; 		// MySQL varchar(20)
$subAgent_field[2][9] = "telephone"; 		// MySQL varchar(20)
$subAgent_field[2][10] = "fax"; 			// MySQL varchar(20)
$subAgent_field[2][11] = "cell"; 			// MySQL varchar(20)
$subAgent_field[2][12] = "showCommissions"; // MySQL varchar(20)
$subAgent_field[2][13] = "admin"; 			// MySQL varchar(20)
$subAgent_field[2][14] = "superUser"; 		// MySQL varchar(20)
$subAgent_field[2][15] = "commissionPassThru"; 	// MySQL smallint(5)
$subAgent_field[2][16] = "showIfGreater"; 	// MySQL tinyint(4)

//---------- masterAgent --------------
$mastAgent_field[2][1] = "loginID";        	// MySQL varchar(10)
$mastAgent_field[2][2] = "password";  		// MySQL varchar(10)
$mastAgent_field[2][3] = "name";    		// MySQL varchar(30)
$mastAgent_field[2][4] = "email";    		// MySQL varchar(24)
$mastAgent_field[2][5] = "ratePlansAndCommissions";    	// blob
$mastAgent_field[2][6] = "providerList";    	// blob
/*****************************************************************************
 * End of configuration file.                                                *
 *****************************************************************************/
function secondsToTime($sec) {
//echo "initial = $sec<br>";
    if(!$sec || $sec == 0)
        return "";
    $tmp = $sec % 3600;
    $h = ($sec - $tmp) / 3600;
    $sec -= ($h * 3600);
//echo "h = $h, new seconds=$sec<br>";
    $tmp = $sec % 60;
    $m = ($sec - $tmp) / 60;
    $sec -= ($m * 60);
//echo "m = $m, new seconds=$sec<br>";
    $str = $h . ":";
    if($m < 10)
        $str .= "0";
    $str .= $m;

//    $str .= ":";
//    if($sec < 10)
//        $str .= "0";
//    $str .= $sec;
    return (string) $str;
}

function timeToSeconds($strTime) {
    if(!$strTime || $strTime == 0)
        return 0;
//echo " original Time=$strTime";
  $startPos = strpos($strTime, ":");
  if($startPos > 0) {
    $hour = substr($strTime,0,$startPos);
    $min = substr($strTime,$startPos+1);
    if($hour > 0 && hour < 25 && $min >= 0 && $min < 60)
        $seconds= $hour * 3600 + $min * 60;
    }
//echo "hour = ($hour), min = ($min)<br>\n";
    return $seconds;
}

$getAreaShort = array(-1 => "Unassigned", 0 => "Crest", 1 => "Western", 2 => "Millicent",  3 => "Training", 4 => "Staff", 5 => "Any Area");
$getArea      = array(-1 => "Unassigned", 0 => "Crest/Snake Creek", 1 => "Great Western",   2 => "Millicent",  3 => "Training", 4 => "Staff");
$getTopShack  = array(-1 => "Unassigned",0 => "Crest Top Shack",   1 => "Snake Creek Top", 2 => "Majestic Top", 3 => "Western Top", 4 => "Millicent Top", 5 => "Aid Room 1", 6 => "Aid Room 2");
$areaCount = 5;
$getShifts=array(0 => "Day"  , 1 => "Swing",         2 => "Full Night", 3=> "3/4 Night", 4 => "Night");
$shiftCount = 5;
$shiftValue=array(0 => 1  , 1 => 1, 2 => 1, 3=> 0.75, 4 => 0.5);

$getTeamLead=array(0 => "No", 1 => "Team Lead", 2 =>"Asst Team Lead", 3=> "Extra");

$shiftsOvr=array(
0 => "Use actual time"  ,
1 => "Saturday 7:45",
2 => "Saturday 13:45",
3 => "Sunday 7:45",
4 => "Monday 7:45",
5 => "Monday 2:45 pm",
6 => "Monday 5:45 pm",
7 => "Monday 6:45pm",
8 => "Monday 11:45pm");

?>
