<?php
include("begin.php");
$textpart = "login";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
session_start();
session_register('destinationpage');

include($cms[tngpath] . "genlib.php");
include($cms[tngpath] . "log.php" );
db_connect($database_host,$database_name,$database_username,$database_password) or exit;

$login_url = getURL( "login", 1 );

$query = "SELECT * FROM $users_table WHERE username = \"$username\" AND password=\"$password\"";
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
$found = mysql_num_rows( $result );
if( $found == 1 ) {
	$row = mysql_fetch_assoc( $result );
	session_register('logged_in');
	session_register('allow_admin_db');
	session_register('allow_edit_db');
	session_register('allow_add_db');
	session_register('allow_delete_db');
	session_register('allow_living_db');
	session_register('allow_lds_db');
	session_register('assignedtree');
	session_register('currentuser');
	session_register('session_rp');
	
	$logged_in = 1;
	$allow_admin_db = $row[allow_admin];
	$allow_edit_db = $row[allow_edit];
	$allow_add_db = $row[allow_add];
	$allow_delete_db = $row[allow_delete];
	$allow_living_db = $row[allow_living];
	if( !$ldsdefault ) //always do lds
		$allow_lds_db = 1;
	elseif( $ldsdefault == 2 )  //depends on permissions
		$allow_lds_db = $row[allow_lds];
	else  //never do lds
		$allow_lds_db = 0;
	$assignedtree = $row[gedcom];
	$currentuser = $row[username];
	$session_rp = $rootpath;
	
	if( $destinationpage )
		header( "Location: " . $destinationpage );
	else
		header( "Location: " . $homepage );
}
else {
$textpart = "login";
	include($cms[tngpath] . "getlang.php");
	include($cms[tngpath] . "$mylanguage/text.php");
	$message = $text[loginfailed];
	header( "Location: " . "$login_url" . "message=" . urlencode($message) );
}
mysql_free_result($result);
?>
