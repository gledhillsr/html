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
$register_url = getURL( "register", 1 );
$descend_url = getURL( "descend", 1 );
$familygroup_url = getURL( "familygroup", 1 );

function getSpouses( $personID, $sex ) {
	global $tree, $families_table, $people_table, $children_table, $text, $allow_living, $nonames;

	$spouses = array();
	if( $sex == "M" ) {
		$self = "husband";
		$spouse = "wife";
		$spouseorder = "husborder";
	}
	else if( $sex == "F" ){
		$self = "wife";
		$spouse = "husband";
		$spouseorder = "wifeorder";
	}
	else {
		$self = $spouse = $spouseorder = "";
	}

	if( $spouse ) 
		$query = "SELECT $spouse, familyID, marrdate, marrplace, living FROM $families_table WHERE $families_table.$self = \"$personID\" AND gedcom = \"$tree\" ORDER BY $spouseorder";
	else
		$query = "SELECT husband, wife, familyID, marrdate, marrplace, living FROM $families_table WHERE ($families_table.wife = \"$personID\" OR $families_table.husband = \"$personID\") AND gedcom = \"$tree\"";
	$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	if( $result ) {
		while( $row = mysql_fetch_assoc( $result ) ) {
			if( !$spouse )
				$spouse = $row[husband] == $personID ? "wife" : "husband";
			$query = "SELECT personID, firstname, lastname, suffix, birthdate, birthplace, altbirthdate, altbirthplace, deathdate, deathplace, burialdate, burialplace, sex, living FROM $people_table WHERE personID = \"$row[$spouse]\" AND gedcom = \"$tree\"";
			$result2 = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
			$spouserow =  mysql_fetch_assoc( $result2 );
			$spouserow[familyID] = $row[familyID];
			$spouserow[marrdate] = $row[marrdate];
			$spouserow[marrplace] = $row[marrplace];
			$spouserow[fliving] = $row[living];
			if( !$spouserow[living] || $allow_living || !$nonames ) {
				$spouserow[name] = trim("$spouserow[firstname] $spouserow[lastname]");
				if( $spouserow[suffix] ) $spouserow[name] .= ", $spouserow[suffix]";
			}
			else
				$spouserow[name] = $spouserow[firstname] = $text[living];
			
			array_push( $spouses, $spouserow );
		}
	}
	mysql_free_result( $result );
	
	return $spouses;
}

function getSpouseParents( $personID, $sex) {
	global $tree, $families_table, $people_table, $children_table, $text, $allow_living, $nonames, $getperson_url;

	if( $sex == "M" ) {
		$childtext = $text[sonof];
	}
	else if( $sex == "F" ){
		$childtext = $text[daughterof];
	}
	else {
		$childtext = $text[childof];
	}

	$allparents = "";
	$query = "SELECT familyID FROM $children_table WHERE personID = \"$personID\" AND gedcom = \"$tree\" ORDER BY ordernum";
	$parents = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	
	if ( $parents && mysql_num_rows( $parents ) ) {
		while ( $parent = mysql_fetch_assoc( $parents ) )
		{
			$parentstr = "";
			$query = "SELECT personID, lastname, firstname, $people_table.living FROM $people_table, $families_table WHERE $people_table.personID = $families_table.husband AND $families_table.familyID = \"$parent[familyID]\" AND $people_table.gedcom = \"$tree\" AND $families_table.gedcom = \"$tree\"";
			$gotfather = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	
		     if( $gotfather ) { 		
				$fathrow =  mysql_fetch_assoc( $gotfather );
				if( $fathrow[firstname] || $fathrow[lastname] ) {
					if( !$fathrow[living] || $allow_living || !$nonames )
						$fathname = trim("$fathrow[firstname] $fathrow[lastname]");
					else
						$fathname = $text[living];
					$parentstr .= "<a href=\"$getperson_url" . "personID=$fathrow[personID]&tree=$tree\">$fathname</a>";
				}
				mysql_free_result( $gotfather );
			} 

			$query = "SELECT personID, lastname, firstname, $people_table.living FROM $people_table, $families_table WHERE $people_table.personID = $families_table.wife AND $families_table.familyID = \"$parent[familyID]\" AND $people_table.gedcom = \"$tree\" AND $families_table.gedcom = \"$tree\"";
			$gotmother = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	
			if( $gotmother ) { 
				$mothrow =  mysql_fetch_assoc( $gotmother );
				if( $mothrow[firstname] || $mothrow[lastname] ) {
					if( !$mothrow[living] || $allow_living || !$nonames )
						$mothname = trim("$mothrow[firstname] $mothrow[lastname]");
					else
						$mothname = $text[living];
					if( $parentstr ) $parentstr .= " $text[text_and] ";
					$parentstr .= "<a href=\"$getperson_url" . "personID=$mothrow[personID]&tree=$tree\">$mothname</a>";
				}
				mysql_free_result( $gotmother );
			} 
			if( $parentstr ) {
				$parentstr = "$childtext $parentstr";
				$allparents .= $allparents ? ", $parentstr" : $parentstr;
			}
		}
		mysql_free_result($parents);
	}
	if( $allparents ) $allparents = "($allparents)";
	
	return $allparents;
}

function getVitalDates( $row ) {
	global $text, $allow_living;
	
	$vitalinfo = "";
	
	if( !$row[living] || $allow_living ) {
		if( $row[birthdate] || $row[birthplace] ) {
			$vitalinfo .= " $text[birthabbr] $row[birthdate]";
			if( $row[birthdate] && $row[birthplace] )
				$vitalinfo .= ", ";
			$vitalinfo .= $row[birthplace];
		}
		if( $row[altbirthdate] || $row[altbirthplace] ){
			if( $vitalinfo ) $vitalinfo .= ";";
			$vitalinfo .= " $text[chrabbr] $row[altbirthdate]";
			if( $row[altbirthdate] && $row[altbirthplace] )
				$vitalinfo .= ", ";
			$vitalinfo .= $row[altbirthplace];
		}

		if( $row[deathdate] ) {
			if( $vitalinfo ) $vitalinfo .= ";";
			$vitalinfo .= " $text[deathabbr] $row[deathdate]";
			if( $row[deathdate] && $row[deathplace] )
				$vitalinfo .= ", ";
			$vitalinfo .= $row[deathplace];
		}
		if( $row[burialdate] ){
			if( $vitalinfo ) $vitalinfo .= ";";
			$vitalinfo .= " $text[burialabbr] $row[burialdate]";
			if( $row[burialdate] && $row[burialplace] )
				$vitalinfo .= ", ";
			$vitalinfo .= $row[burialplace];
		}
	}
	if( $vitalinfo ) $vitalinfo .= ".";
	return $vitalinfo;
}

function getSpouseDates( $row ) {
	global $text, $allow_living;
	
	$spouseinfo = "";
	
	if( !$row[fliving] || $allow_living ) {
		if( $row[marrdate] || $row[marrplace] ) {
			$spouseinfo .= " $row[marrdate]";
			if( $row[marrdate] && $row[marrplace] )
				$spouseinfo .= ", ";
			$spouseinfo .= $row[marrplace];
		}
	}
	if( $spouseinfo ) $spouseinfo .= ".";
	return $spouseinfo;
}

$generation = 1;
$personcount = 1;

$currgen = array();
$nextgen = array();

$query = "SELECT personID, firstname, lastname, suffix, birthdate, birthplace, altbirthdate, altbirthplace, deathdate, deathplace, burialdate, burialplace, sex, living, disallowgedcreate FROM $people_table, $trees_table WHERE personID = \"$personID\" AND $people_table.gedcom = \"$tree\" AND $people_table.gedcom = $trees_table.gedcom";
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
if( $result ) {
	$row = mysql_fetch_assoc( $result );
	if( !$row[living] || $allow_living || !$nonames ) {
		$row[name] = trim("$row[firstname] $row[lastname]");
		if( $row[suffix] ) $row[name] .= ", $row[suffix]";
	}
	else
		$row[name] = $row[firstname] = $text[living];
	$logname = $nonames && $row[living] ? $text[living] : $row[name];
	$row[genlist] = "";
	$row[number] = 1;
	$row[spouses] = getSpouses( $personID, $row[sex] );
	$disallowgedcreate = $row[disallowgedcreate];
	array_push( $currgen, $row );
}

writelog( "<a href=\"$register_url" . "personID=$personID&tree=$tree\">$text[descendfor] $logname ($personID)</a>" );

tng_header( $row[name], "" );
?>

<p class="header">
<? 
	if( !$row[living] || $allow_living ) {
		$photoref = $tree ? "$photopath/$tree.$personID.$photosext" : "$photopath/$personID.$photosext";
		echo showSmallPhoto( $photoref, $row[name] );
	}
	echo $row[name]; 
?>
<br clear="left">
</p>
<?
	echo tng_menu( "descend", $personID, 1 );
?>

<script language="JavaScript">
	function checkSelection() {
		document.form1.action = document.form1.formatsel.options[document.form1.formatsel.selectedIndex].value;
		alert(document.form1.action);
	}
</script>

<?
$formstr = getFORM( "descend", "GET", "selection", "" );
echo $formstr;
?>

<span class="normal">
<select name="formatsel">
	<? echo "<option value=\"$register_url\" selected>$text[regformat]</option>\n"; ?>
	<? echo "<option value=\"$descend_url\">$text[stdformat]</option>\n"; ?>
</select> <input type="submit" value="<? echo $text[go]; ?>" onClick="document.selection.action = document.selection.formatsel.options[document.selection.formatsel.selectedIndex].value;">
<br>(<? echo "$text[maxof] $maxdesc $text[gensatonce]"; ?>)</span>
<input type="hidden" name="personID" value="<? echo $personID; ?>">
<input type="hidden" name="tree" value="<? echo $tree; ?>">
</form>

<span class="normal">
<ol>
<?
while( count( $currgen ) && $generation <= $maxdesc ) {
	echo "<span class=\"subhead\"><strong>$text[generation]: $generation</strong></span><br>\n";
	while( $row = array_shift( $currgen ) ) {
		echo "<li><a href=\"$getperson_url" . "personID=$row[personID]&tree=$tree\">$row[name]</a>";
		if( $row[genlist] )
			echo " ($row[genlist])";
		echo getVitalDates( $row );
		echo "<br><br>\n";
		
		$firstfirstname = strtok($row[firstname]," ");
		$newlist = "$row[number].$firstfirstname<sup>$generation</sup>";
		if( $row[genlist] ) $newlist .= ", $row[genlist]";
		while( $spouserow = array_shift( $row[spouses] ) ) {
			echo "$firstfirstname $text[marrabbr] <a href=\"$getperson_url" . "personID=$spouserow[personID]&tree=$tree\">$spouserow[name]</a>";
			echo getSpouseDates( $spouserow );
			$spouseinfo = getVitalDates( $spouserow );
			if( $spouseinfo ) {
				$spfirstfirstname = strtok( $spouserow[firstname], " " );
				$spparents = getSpouseParents( $spouserow[personID], $spouserow[sex] );
				echo " $spfirstfirstname $spparents $spouseinfo";
			}
			echo " [<a href=\"$familygroup_url" . "familyID=$spouserow[familyID]&tree=$tree\">$text[groupsheet]</a>]<br>\n";
			$query = "SELECT $children_table.personID as personID, firstname, lastname, suffix, birthdate, birthplace, altbirthdate, altbirthplace, deathdate, deathplace, burialdate, burialplace, sex, living FROM $children_table, $people_table WHERE familyID = \"$spouserow[familyID]\" AND $children_table.personID = $people_table.personID AND $children_table.gedcom = \"$tree\" AND $people_table.gedcom = \"$tree\" ORDER BY ordernum";
			$result2 = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
			if( $result2 && mysql_num_rows( $result2 ) ) {
				echo "<ol>$text[children]:<br>\n";
				while( $childrow = mysql_fetch_assoc( $result2 ) ) {
					$personcount++;
					$childrow[spouses] = getSpouses( $childrow[personID], $childrow[sex] );
					$childrow[genlist] = $newlist;
					$childrow[number] = $personcount;
					if( !$childrow[living] || $allow_living || !$nonames ) {
						$childrow[name] = trim("$childrow[firstname] $childrow[lastname]");
						if( $childrow[suffix] ) $childrow[name] .= ", $childrow[suffix]";
					}
					else
						$childrow[name] = $childrow[firstname] = $text[living];
					echo "<li type=\"i\">$personcount. <a href=\"$getperson_url" . "personID=$childrow[personID]&tree=$tree\">$childrow[name]</a>";
					echo getVitalDates( $childrow );
					echo "</li>\n";
					array_push( $nextgen, $childrow );
				}
				echo "</ol>\n";
				mysql_free_result( $result2 );
			}
			echo "<br>\n";
		}
		echo "</li>\n";
	}
	$currgen = $nextgen;
	unset( $nextgen );
	$nextgen = array();
	$generation++;
	echo "<br>\n";
}
?>
</ol>
</span>
<?
	echo tng_menu( "descend", $personID, 2 );
	tng_footer( "" );
?>
