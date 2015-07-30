<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "headstones";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");

$showmap_url = getURL( "showmap", 1 );
$getperson_url = getURL( "getperson", 1 );
$showheadstone_url = getURL( "showheadstone", 1 );

if( $country )
	$subquery = "WHERE country = '$country' ";
if( $state )
	$subquery .= "AND state = '$state' ";
if( $county ) 
	$subquery .= "AND county = '$county'";

$query = "SELECT * FROM $cemeteries_table $subquery ORDER BY country, state, county, city, cemname";
$cemresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");

$query = "SELECT gedcom, treename FROM $trees_table ORDER BY treename";
$treeresult = mysql_query($query) or die ("$admtext[cannotexecutequery]: $query");
$numtrees = mysql_num_rows($treeresult);

$location = $county;
if( $location && $state ) $location .= ", $state"; else $location = $state;
if( $location && $country ) $location .= ", $country"; else $location = $country;

tng_header( $text[cemeteriesheadstones], "" );
?>

<p class="header"><? echo $text[cemeteriesheadstones];  if( $location ) echo " $text[in] $location"; ?></p>
<BR>
<?
echo tng_menu( "", "", 1 );
if( $numtrees > 1 ) {
	$formstr = getFORM( "headstones", "GET", "form1", "form1" );
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
	</select> <input type="submit" value="<? echo $text[go]; ?>">
	<input type="hidden" name="country" value="<? echo $country; ?>">
	<input type="hidden" name="state" value="<? echo $state; ?>">
	<input type="hidden" name="county" value="<? echo $county; ?>">
</form>
<?
}

	if( $tree )
		$wherestr = "AND $headstones_table.gedcom = \"$tree\"";
	else 
		$wherestr = "";
	
	while( $cemetery = mysql_fetch_assoc( $cemresult ) )
	{
		$query = "SELECT $headstones_table.personID as personID, showmap, $headstones_table.notes as notes, status, path, lastname, firstname, $headstones_table.gedcom FROM $headstones_table, $people_table WHERE cemeteryID = \"$cemetery[cemeteryID]\" AND $headstones_table.personID = $people_table.personID AND $headstones_table.gedcom = $people_table.gedcom $wherestr ORDER BY lastname, firstname";
		$hsresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
		$numhs = mysql_num_rows( $hsresult );
		if( $numhs ) {
			echo "<i>&nbsp;\n";
			$location = $cemetery[cemname];
			if( $location && $cemetery[city] ) $location .= ", $cemetery[city]"; else $location = $cemetery[city];
			if( $location && $cemetery[county] ) $location .= ", $cemetery[county]"; else $location = $cemetery[county];
			if( $location && $cemetery[state] ) $location .= ", $cemetery[state]"; else $location = $cemetery[state];
			if( $location && $cemetery[country] ) $location .= ", $cemetery[country]"; else $location = $cemetery[country];
	
			echo $location;
			if( $cemetery[maplink] )
				echo "&nbsp;&nbsp;(<a href=\"$showmap_url" . "cemeteryID=$cemetery[cemeteryID]&tree=$tree\">$text[showmap]</a>)";
			echo "</i>\n<table border=\"0\" cellspacing=\"1\" cellpadding=\"2\" width=\"100%\">\n";
			echo "<tr><td width=\"33%\" class=\"fieldnameback\"><span class=\"fieldname\">&nbsp;<b>$text[name]</b></span></td><td width=\"16%\" class=\"fieldnameback\"><span class=\"fieldname\">&nbsp;<b>$text[status]</b></span></td><td width=\"51%\" class=\"fieldnameback\"><span class=\"fieldname\">&nbsp;<b>$text[notes]</b></span></td></tr>\n";
			while( $hs = mysql_fetch_assoc( $hsresult ) )
			{
				echo "<tr><td valign=\"top\" class=\"databack\"><span class=\"normal\">&nbsp;<a href=\"$getperson_url" . "personID=$hs[personID]&tree=$hs[gedcom]\">$hs[lastname], $hs[firstname]</a>&nbsp;</span></td>\n";
				if( $hs[path] ) {
					echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\"><nobr>&nbsp;<a href=\"$showheadstone_url" . "personID=$hs[personID]&tree=$hs[gedcom]\">$text[seephoto]";
					if( $hs[showmap] == 1 ) {
						echo " $text[andlocation]";
					}
					echo "</a>&nbsp;</nobr></span></td>\n";
				}
				else if( $hs[status] == $text[unmarked] && $cemetery[maplink] ) {
					echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\"><nobr>&nbsp;<a href=\"$showheadstone_url" . "personID=$hs[personID]&tree=$hs[gedcom]\">Unmarked</a>&nbsp;</nobr></span></td>\n";
				}
				else if( $hs[status] == $text[located] && $cemetery[maplink] ) {
					echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\"><nobr>&nbsp;<a href=\"$showheadstone_url" . "personID=$hs[personID]&tree=$hs[gedcom]\">See location</a>&nbsp;</nobr></span></td>\n";
				}
				else {
					echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\">&nbsp;$hs[status]&nbsp;</span></td>\n";
				}
				echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\">&nbsp;$hs[notes]&nbsp;</span>\n";
				echo "</td></tr>\n";
			}
			echo "</table><p></p>\n";
		}
	}
?>
</span>
<?
echo tng_menu( "", "", 2 );
tng_footer( "" );
?>
