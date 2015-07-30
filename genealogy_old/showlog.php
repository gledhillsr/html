<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "showlog";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");

if( $maxloglines )
	$loglines = $maxloglines;
else 
	$loglines = "";

$flags[norobots] = 1;
tng_header( "$text[logfilefor] $dbowner", $flags );
?>

<p class="header"><? echo "$loglines $text[mostrecentactions]"; ?></p>
<?
	echo tng_menu( "", "", 1 );
	$logpath = "$basepath$logfile";
	$lines = file( $logpath );
	
	foreach ( $lines as $line ) {
		echo "$line<br>\n";
	}

	echo tng_menu( "", "", 2 );
	tng_footer( "" );
?>
