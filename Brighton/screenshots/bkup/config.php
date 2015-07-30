<?php

/*****************************************************************************
 * register cookies, POST's, & GET's                                         *
 *****************************************************************************/

//DAD - This is bad, but we can get to this later...
//if(!ini_get('register_globals'))
extract($_COOKIE);
extract($_POST);
extract($_GET);

/*****************************************************************************
 * email stuff                                                               *
 *****************************************************************************/

$email_to = "steve@Gledhills.com";
$email_headers = "From: steve@gledhills.com";

/*****************************************************************************
 * Edit password                                                             *
 *****************************************************************************/

$edit_password = "";   // Password required to edit the database.
                        //  (This is not the same as the MySQL password)

/*****************************************************************************
 * Database settings                                                         *
 *****************************************************************************/
$mysql_username = "root";           // MySQL user name
$mysql_password = "XXXXXXX";    // MySQL password (leave empty if no password is required.)
$mysql_db       = "Brighton";        // MySQL database name
$mysql_host     = "localhost";      // MySQL server host name
$gledhills_host = "64.32.145.130";      // MySQL server host name

require_once 'DB.php';

//Localhost database connection
$dsn = "mysql://{$mysql_username}:{$mysql_password}@{$mysql_host}/{$mysql_db}";
$db =& DB::connect($dsn);
if (DB::isError ($db))
    die ("Could not connect to the database at {$mysql_host}.");

//IP address database connection

$dsn2 = "mysql://{$mysql_username}:{$mysql_password}@{$gledhills_host}/{$mysql_db}";
$db2 =& DB::connect($dsn2);
if (DB::isError ($db2))
    die ("Could not connect to the database at {$gledhills_host}.");

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

//This belongs in a CSS file (styles.css was made, but not implemented yet)
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

require_once 'functions.php';

?>
