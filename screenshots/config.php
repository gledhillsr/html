<?php
/*****************************************************************************
 * register cookies, POST's, & GET's                                         *
 *****************************************************************************/
//echo "in config.php, register_globals= " . ini_get('register_globals') . "<br>";
//if(!ini_get('register_globals'))
//DAD - This is bad, but we can get to this later...
//if(!ini_get('register_globals'))
extract($_COOKIE);
extract($_POST);
extract($_GET);

/*****************************************************************************
 * email stuff                                                               *
 *****************************************************************************/

$email_to ="steve@Gledhills.com";
$email_headers = "From: steve@gledhills.com";

date_default_timezone_set("America/Denver");
/*****************************************************************************
 * Edit password                                                             *
 *****************************************************************************/

$edit_password  = "";   // Password required to edit the database.
                        //  (This is not the same as the MySQL password)

/*****************************************************************************
 * Database settings                                                         *
 *****************************************************************************/

$mysql_username = "root";           // MySQL user name
$mysql_password = "AekdbKazs3990";    // MySQL password (leave empty if no password is required.)
$mysql_web_username = "sgled57";           // MySQL user name
$mysql_web_password = "gandalf";    // MySQL password (leave empty if no password is required.)
$mysql_db       = "Brighton";        // MySQL database name
$mysql_host     = "localhost";      // MySQL server host name
                                    // ("localhost" should be fine on
                                    // most systems)
$gledhills_host = "gledhills.com";      // MySQL server host name
$gledhills_mysql_password = "AekdbKazs3990";
#require_once 'DB.php';

//Localhost database connection
#$dsn = "mysql://{$mysql_username}:{$mysql_password}@{$mysql_host}/{$mysql_db}";
#$db =& DB::connect($dsn);
#if (DB::isError ($db))
#    die ("Could not connect to the database at {$mysql_host}.");
#$link = mysql_connect('localhost', 'root', 'Gandalf2');
#if (!$link) {
#    die('Could not connect: ' . mysql_error());
#}
#echo 'Connected successfully';


/*****************************************************************************
 * File locations                                                            *
 *****************************************************************************/
//$header_file    = "header.php";     // Path and name of the header file
//$footer_file    = "footer.php";     // Path and name of the footer file
//$box_header_file= "box_header.php";   // Path and name of the footer file
//$styles_file    = "styles.php";     // Path and name of the style sheet

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

$totals_color            = "yellow";    // Row color for Totals
$row_color_1             = "#F7F7EF";   // Row 1 background color
$row_color_2             = "#F0F0E8";   // Row 2 background color

$body_bgcolor            = "#FFFFFF";   // Body background color
$body_text_color         = "#000000";   // Body text color
$body_link_color         = "#000000";   // Body link color
$body_vlink_color        = "#000000";   // Body visited link color
$body_alink_color        = "red";       // Body activated link color
$body_hover_color        = "red";       // Body hover color

$go_button               = "images/go.gif";    // URL of the "go" button used onthe page

$check_mark1              = "images/tick_red.gif";  //for Reset
$check_mark2              = "images/tick_green.gif";    //for Reset
$check_mark3              = "images/tick_blue.gif";

$green_dot               = "images/greendot.gif";   //for Adds, Moves, Changes
$yellow_dot              = "images/yellowdot.gif";
$red_dot                 = "images/reddot.gif";

/*****************************************************************************
 * End of configuration file.                                                *
 *****************************************************************************/

/*****************************************************************************
 * secondsToTime (seconds from mid-night)                                    *
 * given a number of seconds, return hh:mm:ss                                *
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

/*****************************************************************************
 * timeToSeconds (string hh:mm:ss)                                           *
 * given hh:mm:ss, return the number of seconds                              *
 *****************************************************************************/
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

$getAreaShort = array(-1 => "Unassigned", 0 => "Crest", 1 => "Snake", 2 => "Western", 3 => "Millicent",  4 => "Training", 5 => "Staff", 6 => "Any Area");
$getArea      = array(-1 => "Unassigned", 0 => "Crest", 1 => "Snake Creek", 2 => "Great Western",   3 => "Millicent",  4 => "Training", 5 => "Staff");
// $getTopShack  = array(-1 => "Unassigned",0 => "Crest Top Shack",   1 => "Snake Creek Top", 2 => "Western Top", 3 => 

$getTopShack  = array(-1 => "Unassigned",0 => "Crest Top Shack",   1 => "Snake Creek Top", 2 => "Western Top", 3 =>
"Millicent Top", 4 => "Aid Room 1", 5 => "Aid Room 2");
$getTopShackDBName  = array(0 => "Crest Top Shack",   1 => "Snake Creek Top", 2 => "Western Top", 3 => "Millicent Top", 4 => "Aid Room 1", 5 => "Aid Room 2");
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
6 => "Monday 3:15 pm",
7 => "Monday 6:45pm",
8 => "Monday 11:45pm");

?>
