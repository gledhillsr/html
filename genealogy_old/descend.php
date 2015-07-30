<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "descend";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");
include($cms[tngpath] . "log.php" );

$getperson_url = getURL( "getperson", 1 );
$descend_url = getURL( "descend", 1 );
$descend_noargs_url = getURL( "descend", 0 );
$desctracker_url = getURL( "desctracker", 1 );
$register_noargs_url = getURL( "register", 0 );

function getIndividual( $key, $sex, $level, $trail ) {
	global $tree, $maxdesc, $families_table, $people_table, $children_table, $text, $allow_living, $nonames, $cms, $getperson_url, $desctracker_url;

	if( $sex == "M" ) {
		$self = "husband";
		$spouse = "wife";
		$spouseorder = "husborder";
	}
	else {
		$self = "wife";
		$spouse = "husband";
		$spouseorder = "wifeorder";
	}
	
	$query = "SELECT $spouse, familyID FROM $families_table WHERE $families_table.$self = \"$key\" AND gedcom = \"$tree\" ORDER BY $spouseorder";
	$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	if( $result ) {
		while( $row = mysql_fetch_assoc( $result ) ) {
			if( $row[$spouse] ) {
				$query = "SELECT personID, firstname, lastname, birthdate, altbirthdate, deathdate, burialdate, living FROM $people_table WHERE personID = \"$row[$spouse]\" AND gedcom = \"$tree\"";
				$spouseresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
				if( $spouseresult ) {
					$spouserow = mysql_fetch_assoc( $spouseresult );
					$vitalinfo = getVitalDates( $spouserow );
					if( !$spouserow[living] || $allow_living || !$nonames )
						$spousename = trim("$spouserow[firstname] $spouserow[lastname]");
					else
						$spousename = $text[living];
					echo str_repeat( "&nbsp;", ($level - 1) * 8 - 4 ) . "+ &nbsp;<a href=\"$getperson_url" . "personID=$spouserow[personID]&tree=$tree\">$spousename</a>&nbsp; $vitalinfo<br>\n";
				}
			}
			else {
				echo str_repeat( "&nbsp;", ($level - 1) * 8 - 4 ) . "+ <br>\n";
			}
			$query = "SELECT $children_table.personID as cpersonID, firstname, lastname, birthdate, altbirthdate, deathdate, burialdate, sex, living FROM $children_table, $people_table WHERE familyID = \"$row[familyID]\" AND $children_table.personID = $people_table.personID AND $children_table.gedcom = \"$tree\" AND $people_table.gedcom = \"$tree\" ORDER BY ordernum";
			$result2 = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
			if( $result2 ) {
				while( $crow = mysql_fetch_assoc( $result2 ) ) {
					$newtrail = "$trail,$row[familyID],$crow[cpersonID]";
					$vitalinfo = getVitalDates( $crow );
					if( !$crow[living] || $allow_living || !$nonames )
						$cname = trim("$crow[firstname] $crow[lastname]");
					else
						$cname = $text[living];
					echo str_repeat( "&nbsp;", ($level - 1) * 8 ) . "$level &nbsp;<a href=\"$getperson_url" . "personID=$crow[cpersonID]&tree=$tree\">$cname</a>&nbsp;<a href=\"$desctracker_url" . "trail=$newtrail&tree=$tree\"><img src=\"$cms[tngpath]" . "dchart.gif\" width=\"10\" height=\"9\" border=\"0\"></a> $vitalinfo<br>\n";
					if( $level < $maxdesc ) {
						getIndividual( $crow[cpersonID], $crow[sex], $level + 1, $newtrail );
					}
				}
			}
			mysql_free_result( $result2 );
		}
	}
	mysql_free_result( $result );
}

function getVitalDates( $row ) {
	global $text, $allow_living;
	
	$vitalinfo = "";
	
	if( !$row[living] || $allow_living ) {
		if( $row[birthdate] ) {
			$vitalinfo = "$text[birthabbr] $row[birthdate] ";
		}
		else if( $row[altbirthdate] ){
			$vitalinfo = "$text[chrabbr] $row[altbirthdate] ";
		}
		else {
			$vitalinfo .= " ";
		}
		if( $row[deathdate] ) {
			$vitalinfo .= "$text[deathabbr] $row[deathdate]";
		}
		else if( $row[burialdate] ){
			$vitalinfo .= "$text[burialabbr] $row[burialdate]";
		}
		else {
			$vitalinfo .= " ";
		}
	}
	return $vitalinfo;
}

$level = 1;
$key = $personID;

$query = "SELECT firstname, lastname, suffix, birthdate, altbirthdate, deathdate, burialdate, sex, living, disallowgedcreate FROM $people_table, $trees_table WHERE personID = \"$key\" AND $people_table.gedcom = \"$tree\" AND $people_table.gedcom = $trees_table.gedcom";
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
if( $result ) {
	$row = mysql_fetch_assoc( $result );
	if( !$row[living] || $allow_living || !$nonames ) {
		$namestr = trim("$row[firstname] $row[lastname]");
		if( $row[suffix] ) $namestr .= ", $row[suffix]";
	}
	else
		$namestr = $text[living];
		$logname = $nonames && $row[living] ? $text[living] : $namestr;
	$disallowgedcreate = $row[disallowgedcreate];
}

writelog( "<a href=\"$descend_url" . "personID=$personID&tree=$tree\">$text[descendfor] $logname ($personID)</a>" );

tng_header( $namestr, "" );
?>

<p class="header">
<? 
	if( !$row[living] || $allow_living ) {
		$photoref = $tree ? "$photopath/$tree.$personID.$photosext" : "$photopath/$personID.$photosext";
		echo showSmallPhoto( $photoref, $namestr );
	}
	echo $namestr; 
?>
<br clear="left">
</p>
<?
echo tng_menu( "descend", $personID, 1 );
	$formstr = getFORM( "register", "GET", "selection", "" );
	echo $formstr;
?>
<span class="normal">
<select name="formatsel">
	<? echo "<option value=\"$descend_noargs_url\" selected>$text[stdformat]</option>\n"; ?>
	<? echo "<option value=\"$register_noargs_url\">$text[regformat]</option>\n"; ?>
</select> <input type="submit" value="<? echo $text[go]; ?>" onClick="document.selection.action = document.selection.formatsel.options[document.selection.formatsel.selectedIndex].value;">
<br>(<? echo "$text[maxof] $maxdesc $text[gensatonce] <img src=\"$cms[tngpath]" . "dchart.gif\" width=\"10\" height=\"9\" border=\"0\"> = $text[graphdesc]"; ?>)</span>
<input type="hidden" name="personID" value="<? echo $personID; ?>">
<input type="hidden" name="tree" value="<? echo $tree; ?>">
</form>
<div align="left">
<span class="normal">
<?
$vitalinfo = getVitalDates( $row );
echo "$level &nbsp;<a href=\"$getperson_url" . "personID=$personID&tree=$tree\">$namestr</a>&nbsp; $vitalinfo<br>\n";
getIndividual( $key, $row[sex], $level + 1, $personID );

?>
</span>
</div>
<?
echo tng_menu( "descend", $personID, 2 );
tng_footer( "" );
?>
