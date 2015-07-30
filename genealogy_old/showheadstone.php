<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "headstones";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
include($cms[tngpath] . "checklogin.php");
include($cms[tngpath] . "log.php" );

$showheadstone_url = getURL( "showheadstone", 1 );

db_connect($database_host,$database_name,$database_username,$database_password) or exit;
$query = "SELECT path, $headstones_table.notes, cemeteryID, lastname, firstname, status, showmap, disallowgedcreate FROM $headstones_table, $people_table, $trees_table WHERE $headstones_table.personID = \"$personID\" AND $people_table.personID = $headstones_table.personID AND $people_table.gedcom = \"$tree\" AND $headstones_table.gedcom = \"$tree\" AND $people_table.gedcom = $trees_table.gedcom";
$hsresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
$hs = mysql_fetch_assoc($hsresult);
$disallowgedcreate = $hs[disallowgedcreate];
mysql_free_result($hsresult);

writelog( "<a href=\"$showheadstone_url" . "personID=$personID&tree=$tree\">$text[headstonefor] $hs[firstname] $hs[lastname] ($personID)</a> $text[accessedby]" );
$size = GetImageSize( "$rootpath$headstonepath/$hs[path]" );

tng_header( "$text[headstonefor] $hs[firstname] $hs[lastname]", "" );
?>

<p class="header"><? echo "$text[headstonefor] $hs[firstname] $hs[lastname]"?></p>
<?
echo tng_menu( "", $personID, 1 );
?>
<br><span class="normal">
<? 
	if( $hs[path] ) {
		echo "<img src=\"$headstonepath/$hs[path]\" border=\"0\" $size[3] alt=\"$hs[firstname] $hs[lastname]\"><br><br>";
	} 
	else 
		echo "<b>$text[status]:</b> $hs[status]<br><br>";
?>
<? if( $hs[notes] ) {echo "<b>$text[notes]:</b> $hs[notes]<br><br>\n";} ?>
<?
	$query = "SELECT cemname, city, county, state, country, maplink FROM $cemeteries_table WHERE cemeteryID = \"$hs[cemeteryID]\"";
	$cemresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$cemetery = mysql_fetch_assoc($cemresult);
	mysql_free_result($cemresult);
?>
	<span class="subhead">
<?
		$location = $cemetery[cemname];
		if( $location && $cemetery[city] ) $location .= ", $cemetery[city]"; else $location = $cemetery[city];
		if( $location && $cemetery[county] ) $location .= ", $cemetery[county]"; else $location = $cemetery[county];
		if( $location && $cemetery[state] ) $location .= ", $cemetery[state]"; else $location = $cemetery[state];
		if( $location && $cemetery[country] ) $location .= ", $cemetery[country]"; else $location = $cemetery[country];

		echo $location;
?>
	</span>
	<br><br>
<?
	if( $hs[showmap] && $cemetery[maplink] ) {
		$mapsize = GetImageSize( "$rootpath$headstonepath/$cemetery[maplink]" );
		echo "<img src=\"$headstonepath/$cemetery[maplink]\" border=\"0\" $mapsize[3] alt=\"$cemetery[cemname]\"><br><br>\n";
	}

echo tng_menu( "", $personID, 2 );
tng_footer( "" );
?>
