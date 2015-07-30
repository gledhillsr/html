<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "headstones";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");

$headstones_url = getURL( "headstones", 1 );

$query = "SELECT * FROM $cemeteries_table ORDER BY country, state, county, city, cemname";
$cemresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");

$query = "SELECT gedcom, treename FROM $trees_table ORDER BY treename";
$treeresult = mysql_query($query) or die ("$admtext[cannotexecutequery]: $query");
$numtrees = mysql_num_rows($treeresult);

tng_header( $text[cemeteriesheadstones], "" );
?>

<p class="header"><? echo $text[cemeteriesheadstones]; ?></p>

<BR>
<?
echo tng_menu( "", "", 1 );
if( $numtrees > 1 ) {
	$formstr = getFORM( "cemeteries", "GET", "form1", "form1" );
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
</form>
<?
}
?>

<ul>
	<li><span class="normal">
	<? echo "<a href=\"$headstones_url" . "tree=$tree\">";
	   echo $text[showallhsr];
	?>
	</a></span></li>
</ul><br>
<div align="center">
<table width="95%" border="0" cellspacing="0" cellpadding="0">
<?
	if( $tree )
		$wherestr = "AND gedcom = \"$tree\"";
	else 
		$wherestr = "";
	
	$lastcountry = "";
	$cellcount = 0;
	$linecount = 0;
	while( $cemetery = mysql_fetch_assoc( $cemresult ) )
	{
		$query = "SELECT $headstones_table.personID as personID FROM $headstones_table WHERE cemeteryID = \"$cemetery[cemeteryID]\" $wherestr";
		$hsresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
		$numhs = mysql_num_rows( $hsresult );
		if( $numhs ) {
			if( $cemetery[country] != $lastcountry ) {
				if( $cellcount == 0 ) {
					echo "<tr><td valign=\"top\"><span class=\"normal\">";
					$cellcount++;
				}
				elseif( $cellcount % 5 == 0 ) {
					$cellcount = 0;
					echo "</span></td></tr><tr><td valign=\"top\"><span class=\"normal\">\n";
					$linecount = 0;
				}
				elseif( $linecount >35 ) {
					$cellcount++;
					echo "</span></td><td valign=\"top\"><span class=\"normal\">";
					$linecount = 0;
				}
				else {
					echo "<br>";
					$linecount++;
				}
				$linecount++;
				echo "<font size=\"4\"><strong><a href=\"$headstones_url" . "country=" . urlencode($cemetery[country]) . "&tree=$tree\">$cemetery[country]</a></strong></font><br>\n";
				$lastcountry = $cemetery[country];
				$laststate = "";
			}
	
			if( $cemetery[state] != $laststate ) {
				if( $linecount >35 ) {
					$cellcount++;
					echo "</span></td><td valign=\"top\"><span class=\"normal\"><em>($cemetery[country] cont.)</em><br>";
					$linecount = 1;
				}
				else {
					echo "<br>";
					$linecount++;
				}
				
				$linecount++;
				echo "<strong><a href=\"$headstones_url" . "country=" . urlencode($cemetery[country]) . "&state=" . urlencode($cemetery[state]) . "&tree=$tree\">$cemetery[state]</a></strong><br>\n";
				$laststate = $cemetery[state];
				$lastcounty = "";
			}
	
			if( $cemetery[county] != $lastcounty ) {
				$linecount++;
				echo "<a href=\"$headstones_url" . "country=" . urlencode($cemetery[country]) . "&state=" . urlencode($cemetery[state]) . "&county=" . urlencode($cemetery[county]) . "&tree=$tree\">$cemetery[county]</a><br>\n";
				$lastcounty = $cemetery[county];
			}
		}
	}
?>
	</span></td></tr>
</table>
</div><br><br>
<?
echo tng_menu( "", "", 2 );
tng_footer( "" );
?>
