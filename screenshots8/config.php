<?php
// Set default timezone for PHP
date_default_timezone_set("America/Denver");

// Error reporting configuration
// Suppress warnings for undefined variables and array keys (common in legacy code)
// Keep fatal errors and parse errors visible
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', '1');

// OR for development (show only errors and warnings, not notices)
// error_reporting(E_ERROR | E_WARNING | E_PARSE);
// ini_set('display_errors', '1');

/*****************************************************************************
 * register cookies, POST's, & GET's                                         *
 *****************************************************************************/
// Helper function to safely get request variables (replaces extract())
function getRequestVar($name, $default = null) {
    if (isset($_POST[$name])) {
        return $_POST[$name];
    }
    if (isset($_GET[$name])) {
        return $_GET[$name];
    }
    if (isset($_COOKIE[$name])) {
        return $_COOKIE[$name];
    }
    return $default;
}

// Initialize common variables from request (replaces extract() but safer)
// This maintains backward compatibility while being more explicit
function initRequestVars() {
    // Get all unique keys from POST, GET, and COOKIE
    $allKeys = array_unique(array_merge(
        array_keys($_POST),
        array_keys($_GET),
        array_keys($_COOKIE)
    ));
    
    // Initialize variables in global scope
    foreach ($allKeys as $key) {
        if (!isset($GLOBALS[$key])) {
            $GLOBALS[$key] = getRequestVar($key);
        }
    }
}

// Call initRequestVars to maintain backward compatibility
initRequestVars();

/*****************************************************************************
 * email stuff                                                               *
 *****************************************************************************/
// Email configuration - moved to dbconfig.php for security
// If not set in dbconfig.php, use placeholder
if (!isset($email_to)) {
    $email_to = "admin@example.com";  // Placeholder - set in dbconfig.php
}
if (!isset($email_headers)) {
    $email_headers = "From: admin@example.com";  // Placeholder - set in dbconfig.php
}

/*****************************************************************************
 * Edit password                                                             *
 *****************************************************************************/

$edit_password  = "";   // Password required to edit the database.
                        //  (This is not the same as the MySQL password)

/*****************************************************************************
 * Database settings                                                         *
 *****************************************************************************/
require("dbconfig.php");
#require_once 'DB.php';

//Localhost database connection
#$dsn = "mysql://{$mysqli_username}:{$mysqli_password}@{$mysqli_host}/{$mysqli_db}";
#$db =& DB::connect($dsn);
#if (DB::isError ($db))
#    die ("Could not connect to the database at {$mysqli_host}.");
#$link = mysqli_connect('localhost', 'root', 'Gandalf2');
#if (!$link) {
#    die('Could not connect: ' . mysqli_error());
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
  $startPos = strpos((string) $strTime, ":");
  if($startPos > 0) {
    $hour = substr((string) $strTime,0,$startPos);
    $min = substr((string) $strTime,$startPos+1);
    if($hour > 0 && $hour < 25 && $min >= 0 && $min < 60)
        $seconds= $hour * 3600 + $min * 60;
    }
//echo "hour = ($hour), min = ($min)<br>\n";
    return $seconds;
}

/*****************************************************************************
 * Helper Functions to eliminate redundant code                             *
 *****************************************************************************/

/**
 * Set MySQL session timezone to match PHP timezone (America/Denver)
 * Call this after any mysqli_connect() to ensure database timezone matches PHP
 * @param mysqli $connect_string Database connection resource
 * @return void
 */
function setMySQLTimezone($connect_string) {
    if ($connect_string) {
        // Set MySQL session timezone to match PHP timezone (America/Denver)
        // Try named timezone first (MySQL 8.0+), fallback to offset if not supported
        $timezone_set = @mysqli_query($connect_string, "SET time_zone = 'America/Denver'");
        if (!$timezone_set) {
            // Fallback for older MySQL versions - calculate current offset
            // America/Denver is UTC-7 (MST) or UTC-6 (MDT during daylight saving)
            $is_dst = date('I'); // 1 if DST, 0 if not
            $offset = $is_dst ? '-06:00' : '-07:00';
            @mysqli_query($connect_string, "SET time_zone = '$offset'");
        }
    }
}

/**
 * Get database connection (replaces repeated mysqli_connect + mysqli_select_db)
 * Sets timezone for MySQL connection to match PHP timezone (America/Denver)
 * @return mysqli|false Connection resource or false on failure
 */
function getDBConnection() {
    global $mysqli_host, $mysqli_username, $mysqli_password, $mysqli_db;
    $connect_string = @mysqli_connect($mysqli_host, $mysqli_username, $mysqli_password);
    if ($connect_string) {
        mysqli_select_db($connect_string, $mysqli_db);
        setMySQLTimezone($connect_string);
    }
    return $connect_string;
}

/**
 * Get today's timestamp (midnight) - replaces repeated getdate() + mktime pattern
 * @return int Unix timestamp for today at midnight
 */
function getTodayTimestamp() {
    $arrDate = getdate();
    return mktime(0, 0, 0, $arrDate['mon'], $arrDate['mday'], $arrDate['year']);
}

/**
 * Get patroller's full name from roster by IDNumber
 * @param mysqli $connect_string Database connection
 * @param string $patrollerID Patroller ID number
 * @return string Full name or empty string if not found
 */
function getPatrollerName($connect_string, $patrollerID) {
    $query_string = "SELECT FirstName, LastName FROM roster WHERE IDNumber=\"" . mysqli_real_escape_string($connect_string, $patrollerID) . "\"";
    $result = @mysqli_query($connect_string, $query_string);
    if ($result && $row = @mysqli_fetch_array($result)) {
        return $row['FirstName'] . " " . $row['LastName'];
    }
    return "";
}

/**
 * Get patroller's full name formatted as "LastName, FirstName"
 * @param mysqli $connect_string Database connection
 * @param string $patrollerID Patroller ID number
 * @return string Formatted name or empty string if not found
 */
function getPatrollerNameFormatted($connect_string, $patrollerID) {
    $query_string = "SELECT FirstName, LastName FROM roster WHERE IDNumber=\"" . mysqli_real_escape_string($connect_string, $patrollerID) . "\"";
    $result = @mysqli_query($connect_string, $query_string);
    if ($result && $row = @mysqli_fetch_array($result)) {
        return $row['LastName'] . ", " . $row['FirstName'];
    }
    return "";
}

/**
 * Get patroller info from roster by IDNumber
 * @param mysqli $connect_string Database connection
 * @param string $patrollerID Patroller ID number
 * @return array|false Array with patroller data or false if not found
 */
function getPatrollerInfo($connect_string, $patrollerID) {
    $query_string = "SELECT * FROM roster WHERE IDNumber=\"" . mysqli_real_escape_string($connect_string, $patrollerID) . "\"";
    $result = @mysqli_query($connect_string, $query_string);
    if ($result && $row = @mysqli_fetch_array($result)) {
        return $row;
    }
    return false;
}

/**
 * Get classification prefix for display (1-, 2-, 3-, 4-)
 * @param string $classificationCode Classification code (SR, BAS, AUX, SRA, etc.)
 * @return string Prefix string
 */
function getClassificationPrefix($classificationCode) {
    if ($classificationCode == "SR") return "1-";
    if ($classificationCode == "BAS") return "2-";
    if ($classificationCode == "AUX" || $classificationCode == "SRA") return "3-";
    return "4-";
}

$getAreaShort = [-1 => "Unassigned", 0 => "Crest", 1 => "Snake", 2 => "Western", 3 => "Millicent",  4 => "Training", 5 => "Staff", 6 => "Any Area"];
$getArea      = [-1 => "Unassigned", 0 => "Crest", 1 => "Snake Creek", 2 => "Great Western",   3 => "Millicent",  4 => "Training", 5 => "Staff"];
// $getTopShack  = array(-1 => "Unassigned",0 => "Crest Top Shack",   1 => "Snake Creek Top", 2 => "Western Top", 3 => 

$getTopShack  = [-1 => "Unassigned",0 => "Crest Top Shack",   1 => "Snake Creek Top", 2 => "Western Top", 3 =>
"Millicent Top", 4 => "Aid Room 1", 5 => "Aid Room 2"];
$getTopShackDBName  = [0 => "Crest Top Shack",   1 => "Snake Creek Top", 2 => "Western Top", 3 => "Millicent Top", 4 => "Aid Room 1", 5 => "Aid Room 2"];
$areaCount = 5;
$getShifts=[0 => "Day"  , 1 => "Swing",         2 => "Full Night", 3=> "3/4 Night", 4 => "Night"];
$shiftCount = 5;
$shiftValue=[0 => 1  , 1 => 1, 2 => 1, 3=> 0.75, 4 => 0.5];

$getTeamLead=[0 => "No", 1 => "Team Lead", 2 =>"Asst Team Lead", 3=> "Extra"];

$shiftsOvr=[
0 => "Use actual time"  ,
1 => "Saturday 7:45",
2 => "Saturday 13:45",
3 => "Sunday 7:45",
4 => "Monday 7:45",
5 => "Monday 2:45 pm",
6 => "Monday 3:15 pm",
7 => "Monday 6:45pm",
8 => "Monday 11:45pm"];

/**
 * Get formatted date string with time for assignments table
 * Format: YYYY-MM-DD_HH:MM:SS
 * @param array|null $arrDate Optional date array from getdate(), uses current time if null
 * @return string Formatted date string with time component
 */
function getAssignmentDateString($arrDate = null) {
    if ($arrDate === null) {
        $arrDate = getdate();
    }
    $tdate = $arrDate['year'] . "-";
    if($arrDate['mon'] < 10) $tdate .= "0";
    $tdate .= $arrDate['mon'] . "-";
    if($arrDate['mday'] < 10) $tdate .= "0";
    $tdate .= $arrDate['mday'];
    // Add hour, minutes, and seconds
    $tdate .= "_";
    if($arrDate['hours'] < 10) $tdate .= "0";
    $tdate .= $arrDate['hours'] . ":";
    if($arrDate['minutes'] < 10) $tdate .= "0";
    $tdate .= $arrDate['minutes'] . ":";
    if($arrDate['seconds'] < 10) $tdate .= "0";
    $tdate .= $arrDate['seconds'];
    return $tdate;
}

?>
