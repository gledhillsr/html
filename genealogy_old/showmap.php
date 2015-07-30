<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "showmap";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");
include($cms[tngpath] . "log.php" );

$showmap_url = getURL( "showmap", 1 );
$showheadstone_url = getURL( "showheadstone", 1 );
$getperson_url = getURL( "getperson", 1 );

$query = "SELECT cemname, city, county, state, country, maplink FROM $cemeteries_table WHERE cemeteryID = \"$cemeteryID\"";
$cemresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
$cemetery = mysql_fetch_assoc($cemresult);
mysql_free_result($cemresult);

$location = $cemetery[cemname];
if( $location && $cemetery[city] ) $location .= ", $cemetery[city]"; else $location = $cemetery[city];
if( $location && $cemetery[county] ) $location .= ", $cemetery[county]"; else $location = $cemetery[county];
if( $location && $cemetery[state] ) $location .= ", $cemetery[state]"; else $location = $cemetery[state];
if( $location && $cemetery[country] ) $location .= ", $cemetery[country]"; else $location = $cemetery[country];

$query = "SELECT gedcom, treename FROM $trees_table ORDER BY treename";
$treeresult = mysql_query($query) or die ("$admtext[cannotexecutequery]: $query");
$numtrees = mysql_num_rows($treeresult);

writelog( "<a href=\"$showmap_url" . "cemeteryID=$cemeteryID&tree=$tree\">$text[mapof] $location</a>" );
$size = GetImageSize( "$rootpath$headstonepath/$cemetery[maplink]" );

tng_header( "$text[mapof] $location", "" );
?>

<p class="header"><? echo "$text[mapof] $location"; ?></p>
<br>
<?
echo tng_menu( "", "", 1 );
if( $numtrees > 1 ) {
	$formstr = getFORM( "showmap", "GET", "form1", "form1" );
	echo $formstr;

	echo $text[tree]; ?>: 
	<select name="tree">
		<option value="-x--all--x-" <? if( !$tree ) echo "selected"; ?>><? echo $text[alltrees]; ?></option>
<?
	while( $row = mysql_fetch_assoc($treeresult) ) {
		echo "	<option value=\"$row[gedcom]\"";
		if( $tree && $row[gedcom] == $tree ) echo " selected";
		echo ">$row[treename]</option>\n";
	}
?>
	</select>
	<input type="hidden" name="cemeteryID" value="<? echo $cemeteryID; ?>">  <input type="submit" value="<? echo $text[go]; ?>">
</form>
<?
}

	if( $cemetery[maplink] ) {
		echo "<img src=\"$headstonepath/$cemetery[maplink]\" border=\"0\" $size[3] alt=\"$cemetery[cemname]\"><br><br>\n";
	}
?>

	<table border="0" cellspacing="1" cellpadding="2" width="100%">
	<tr>
		<td width="33%" class="fieldnameback"><span class="fieldname">&nbsp;<b><? echo $text[name]; ?></b></span></td>
		<td width="16%" class="fieldnameback"><span class="fieldname">&nbsp;<b><? echo $text[status]; ?></b></span></td>
		<td width="51%" class="fieldnameback"><span class="fieldname">&nbsp;<b><? echo $text[notes]; ?></b></span></td>
	</tr>
<?
	if( $tree )
		$wherestr = "AND $headstones_table.gedcom = \"$tree\"";
	else 
		$wherestr = "";

	$query = "SELECT $headstones_table.personID as personID, showmap, $headstones_table.notes as notes, status, path, lastname, firstname, $headstones_table.gedcom FROM $headstones_table, $people_table WHERE cemeteryID = \"$cemeteryID\" AND $headstones_table.personID = $people_table.personID AND $headstones_table.gedcom = $people_table.gedcom $wherestr ORDER BY lastname, firstname";
	$hsresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	while( $hs = mysql_fetch_assoc( $hsresult ) )
	{
		echo "<tr><td valign=\"top\" class=\"databack\"><span class=\"normal\">&nbsp;<a href=\"$getperson_url" . "personID=$hs[personID]&tree=$hs[gedcom]\">$hs[lastname], $hs[firstname]</a>&nbsp;</span></td>\n";
		if( $hs[path] )
			echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\"><nobr>&nbsp;<a href=\"$showheadstone_url" . "personID=$hs[personID]&tree=$hs[gedcom]\">$text[seephoto]</a>&nbsp;</nobr></span></td>\n";
		else {
			echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\">&nbsp;$hs[status]&nbsp;</span></td>\n";
		}
		echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\">&nbsp;$hs[notes]&nbsp;</span></td></tr>\n";
	}
	echo "</table><p></p>\n";

	echo tng_menu( "", "", 2 );
	tng_footer( "" );
?>
