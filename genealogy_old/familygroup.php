<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "familygroup";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");
include($cms[tngpath] . "log.php" );

$familygroup_url = getURL( "familygroup", 1 );
$getperson_url = getURL( "getperson", 1 );

function showFact( $text, $fact ) {
	echo "<tr>\n";
	echo "<td valign=\"top\" class=\"fieldnameback\" nowrap><span class=\"fieldname\">&nbsp;" . $text . "&nbsp;</span></td>\n";
	echo "<td valign=\"top\" colspan=\"5\" class=\"databack\"><span class=\"normal\">$fact&nbsp;</span></td>\n";
	echo "</tr>\n";
}

function showDatePlace( $event ) {
	global $allow_lds;
	
	echo "<tr>\n";
	echo "<td valign=\"top\" class=\"fieldnameback\" nowrap><span class=\"fieldname\">&nbsp;" . $event[text] . "&nbsp;</span></td>\n";
	echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\"><nobr>" . $event[date] . "</nobr>&nbsp;</span></td>\n";
	echo "<td valign=\"top\" class=\"databack\"";
	if( $allow_lds )
		echo " width=\"50%\"";
	else
		echo " colspan=\"4\" width=\"80%\"";
	echo "><span class=\"normal\">$event[place]&nbsp;</span></td>\n";
	if( $allow_lds ) {
		echo "<td valign=\"top\" class=\"fieldnameback\" nowrap><span class=\"fieldname\">&nbsp;" . $event[ldstext] . "&nbsp;</span></td>\n";
		echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\"><nobr>" . $event[ldsdate] . "&nbsp;</nobr></span></td>\n";
		echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\"><nobr>$event[ldsplace]&nbsp;</nobr></span></td>\n";
	}
	echo "</tr>\n";
}

function showBreak( ) {
	echo "<tr><td height=\"3\" colspan=\"6\"><font size=\"1\">&nbsp;</font></td></tr>\n";
}

function displayIndividual( $ind, $label, $familyID, $showmarriage ) {
	global $tree, $text, $photopath, $photosext, $allow_living, $allow_lds, $allow_edit, $families_table, $people_table, $nonames, $cms, $getperson_url, $familygroup_url;

	$haskids = $ind[haskids] ? "X" : "&nbsp;";
	$restriction = $familyID ? "AND familyID != \"$familyID\"" : "";
	if( $ind[sex] == "M" ) $sex = $text[male];
	else if( $ind[sex] == "F" ) $sex = $text[female];
	else $sex = $text[unknown];

	if( !$ind[living] || $allow_living || !$nonames ) {
		$namestr = trim("$ind[firstname] $ind[lastname]");
		if( $ind[suffix] ) $namestr .= ", $ind[suffix]";
	}
	else
		$namestr = $text[living];
	
	//show photo & name
	echo "<tr><td colspan=\"6\">";
	if( !$ind[living] || $allow_living ) {
		$photoref = $tree ? "$photopath/$tree.$ind[personID].$photosext" : "$photopath/$ind[personID].$photosext";
		echo showSmallPhoto( $photoref, $namestr );
	}
	echo "<span class=\"normal\"><br>$label | $sex</span><br><span class=\"subhead\"><b>";
	if( $ind[haskids] ) 
		echo "> ";
	echo "<a href=\"$getperson_url" . "personID=$ind[personID]&tree=$tree\">$namestr</a></b>";
	
	if( $allow_edit )
		echo " | <a href=\"$cms[tngpath]" . "admin/editperson.php?personID=$ind[personID]&tree=$tree\">$text[edit]</a>";
	echo "<br></span>\n";
	echo "</td></tr>\n";

	$event = array();
	$event = "";
	
	$event[text] = $text[born];
	if( !$ind[living] || $allow_living ) {
		$event[date] = $ind[birthdate];
		$event[place] = $ind[birthplace];
		if( $allow_lds ) {
			$event[ldstext] = $text[ldsords];
			$event[ldsdate] = $text[date];
			$event[ldsplace] = $text[place];
		}
	}
	showDatePlace( $event );
	
	$event = "";
	if( !$ind[living] || $allow_living ) {
		$event[date] = $ind[altbirthdate];
		$event[place] = $ind[altbirthplace];
		if( $allow_lds ) {
			$event[ldsdate] = $ind[baptdate];
			$event[ldsplace] = $ind[baptplace];
			$event[ldstext] = $text[baptizedlds];
		}
	}
	if( (isset( $event[date]) && $event[date]) || (isset( $event[place]) && $event[place]) || isset($event[ldsdate]) || isset($event[ldsplace]) ) {
		$event[text] = $text[christened];
		showDatePlace( $event );
	}
	
	$event = "";
	$event[text] = $text[died];
	if( !$ind[living] || $allow_living ) {
		$event[date] = $ind[deathdate];
		$event[place] = $ind[deathplace];
		if( $allow_lds ) {
			$event[ldstext] = $text[endowedlds];
			$event[ldsdate] = $ind[endldate];
			$event[ldsplace] = $ind[endlplace];
		}
	}
	showDatePlace( $event );
	
	$event = "";
	$event[text] = $text[buried];
	if( !$ind[living] || $allow_living ) {
		$event[date] = $ind[burialdate];
		$event[place] = $ind[burialplace];
		if( $allow_lds ) {
			$event[ldstext] = $text[sealedplds];
			$event[ldsdate] = $ind[sealdate];
			$event[ldsplace] = $ind[sealplace];
		}
	}
	showDatePlace( $event );
	
	//show marriage & sealing if $showmarriage
	if( $familyID ) {
		if( $showmarriage ) {
			$query = "SELECT marrdate, marrplace, sealdate, sealplace, living FROM $families_table WHERE familyID = \"$familyID\" AND gedcom = \"$tree\"";
			$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
			$fam = mysql_fetch_assoc($result);
			mysql_free_result($result);
			
			$event = "";
			$event[text] = $text[married];
			if( !$fam[living] || $allow_living ) {
				$event[date] = $fam[marrdate];
				$event[place] = $fam[marrplace];
				if( $allow_lds ) {
					$event[ldstext] = $text[sealedslds];
					$event[ldsdate] = $fam[sealdate];
					$event[ldsplace] = $fam[sealplace];
				}
			}
			showDatePlace( $event );
		}
		$spousetext = $text[otherspouse];
	}
	else
		$spousetext = $text[spouse];

	//show other spouses
	if( $ind[sex] == "M" ) 
		$query = "SELECT familyID, personID, firstname, lastname, suffix, $families_table.living as living, $people_table.living as iliving, marrdate, marrplace, sealdate, sealplace FROM $families_table LEFT JOIN $people_table on $families_table.wife = $people_table.personID AND $families_table.gedcom = $people_table.gedcom WHERE husband = \"$ind[personID]\" AND $people_table.gedcom = \"$tree\" $restriction ORDER BY husborder";
	else if( $ind[sex] = "F" )
		$query = "SELECT familyID, personID, firstname, lastname, suffix, $families_table.living as living, $people_table.living as iliving, marrdate, marrplace, sealdate, sealplace FROM $families_table LEFT JOIN $people_table on $families_table.husband = $people_table.personID AND $families_table.gedcom = $people_table.gedcom WHERE wife = \"$ind[personID]\" AND $people_table.gedcom = \"$tree\" $restriction ORDER BY wifeorder";
	else
		$query = "SELECT familyID, personID, firstname, lastname, suffix, $families_table.living as living, $people_table.living as iliving, marrdate, marrplace, sealdate, sealplace FROM $families_table LEFT JOIN $people_table on ($families_table.husband = $people_table.personID OR $families_table.wife = $people_table.personID) AND $families_table.gedcom = $people_table.gedcom WHERE (wife = \"$ind[personID]\" && husband = \"$ind[personID]\") AND $people_table.gedcom = \"$tree\"";
	$spresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	
	while( $fam = mysql_fetch_assoc($spresult) ) {
		if( !$fam[iliving] || $allow_living || !$nonames ) {
			$spousename = trim("$fam[firstname] $fam[lastname]");
			if( $fam[suffix] ) $spousename .= ", $fam[suffix]";
		}
		else
			$spousename = $text[living];
		$spouselink = $spousename ? "<a href=\"$getperson_url" . "personID=$fam[personID]&tree=$tree\">$spousename</a> | " : "";
		$spouselink .= "<a href=\"$familygroup_url" . "familyID=$fam[familyID]&tree=$tree\">$fam[familyID]</a>";
		showFact( $spousetext, $spouselink );
		
		$event = "";
		$event[text] = $text[married];
		if( !$fam[living] || $allow_living ) {
			$event[date] = $fam[marrdate];
			$event[place] = $fam[marrplace];
			if( $allow_lds ) {
				$event[ldstext] = $text[sealedslds];
				$event[ldsdate] = $fam[sealdate];
				$event[ldsplace] = $fam[sealplace];
			}
		}
		showDatePlace( $event );
	}
	
	//show parents (for hus&wif)
	if( $familyID ) {
		$query = "SELECT familyID, personID, firstname, lastname, suffix, $people_table.living FROM $families_table, $people_table WHERE $families_table.familyID = \"$ind[famc]\" AND $families_table.gedcom = \"$tree\" AND $people_table.personID = $families_table.husband AND $people_table.gedcom = \"$tree\"";
		$presult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
		$parent = mysql_fetch_assoc($presult);
		if( !$parent[living] || $allow_living || !$nonames ) {
			$fathername = trim("$parent[firstname] $parent[lastname]");
			if( $parent[suffix] ) $fathername .= ", $parent[suffix]";
		}
		else
			$fathername = $text[living];
		mysql_free_result($presult);
		$fatherlink = $fathername ? "<a href=\"$getperson_url" . "personID=$parent[personID]&tree=$tree\">$fathername</a> | " : "";
		$fatherlink .= "<a href=\"$familygroup_url" . "familyID=$parent[familyID]&tree=$tree\">$parent[familyID]</a>";
		showFact( $text[father], $fatherlink );
		
		$query = "SELECT familyID, personID, firstname, lastname, suffix, $people_table.living FROM $families_table, $people_table WHERE $families_table.familyID = \"$ind[famc]\" AND $families_table.gedcom = \"$tree\" AND $people_table.personID = $families_table.wife AND $people_table.gedcom = \"$tree\"";
		$presult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
		$parent = mysql_fetch_assoc($presult);
		if( !$parent[living] || $allow_living || !$nonames ) {
			$mothername = trim("$parent[firstname] $parent[lastname]");
			if( $parent[suffix] ) $mothername .= ", $parent[suffix]";
		}
		else
			$mothername = $text[living];
		mysql_free_result($presult);
		$motherlink = $mothername ? "<a href=\"$getperson_url" . "personID=$parent[personID]&tree=$tree\">$mothername</a> | " : "";
		$motherlink .= "<a href=\"$familygroup_url" . "familyID=$parent[familyID]&tree=$tree\">$parent[familyID]</a>";
		showFact( $text[mother], $motherlink );
	}
}

//get family
$query = "SELECT familyID, husband, wife, living FROM $families_table WHERE familyID = \"$familyID\" AND gedcom = \"$tree\"";
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
$famrow = mysql_fetch_assoc($result);
mysql_free_result($result);

writelog( "<a href=\"$familygroup_url" . "familyID=$familyID&tree=$tree\">$text[familygroupfor] $text[family] $familyID</a>" );

tng_header( "$text[familygroupfor] $text[family] $familyID", "" );
?>

<p class="header">
<? 
	echo "$text[familygroupfor] $text[family] $familyID";
	
	$personID = $famrow[husband] ? $famrow[husband] : $famrow[wife];
?>
<br clear="left">
</p>
<?
	echo tng_menu( "", $familyID, 1 );
?>

<table border="0" cellspacing="1" cellpadding="4">
<?
	echo "<tr><td height=\"3\" colspan=\"6\"><span class=\"subhead\"><b>$text[parents]</b></span></td></tr>\n";

	//get husband & spouses
	if( $famrow[husband] ) {
		$query = "SELECT * FROM $people_table WHERE personID = \"$famrow[husband]\" AND gedcom = \"$tree\"";
		$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
		$husbrow = mysql_fetch_assoc($result);
		mysql_free_result($result);
		displayIndividual($husbrow, $text[husband], $familyID, 1);
	}
	
	//get wife & spouses
	if( $famrow[wife] ) {
		$query = "SELECT * FROM $people_table WHERE personID = \"$famrow[wife]\" AND gedcom = \"$tree\"";
		$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
		$wiferow = mysql_fetch_assoc($result);
		mysql_free_result($result);
		displayIndividual($wiferow, $text[wife], $familyID, 0);
	}
	
	//put a break here, title "Children"
	showBreak();
	echo "<tr><td height=\"3\" colspan=\"6\"><span class=\"subhead\"><b>$text[children]</b></span></td></tr>\n";
	
	//for each child
	$query = "SELECT $people_table.personID as personID, firstname, lastname, living, famc, sex, birthdate, birthplace, altbirthdate, altbirthplace, haskids, deathdate, deathplace, burialdate, burialplace, baptdate, baptplace, endldate, endlplace, sealdate, sealplace, notes FROM $people_table, $children_table WHERE $people_table.personID = $children_table.personID AND $children_table.familyID = \"$famrow[familyID]\" AND $people_table.gedcom = \"$tree\" AND $children_table.gedcom = \"$tree\" ORDER BY ordernum";
	$children= mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	
	
	if( $children && mysql_num_rows( $children ) ) {
		$childcount = 0;
		while( $childrow = mysql_fetch_assoc($children) ) {
			$childcount++;
			displayIndividual($childrow, "$text[child] $childcount", "", 1);
		}
	}
	mysql_free_result($children);
	
	//put a break here, title "Sources"
	showBreak();
	if( count($sources) ) { 
		echo "<tr><td height=\"3\" colspan=\"6\"><span class=\"subhead\"><b>$text[sources]</b><br></span></td></tr>\n";
?>
	<tr>
		<td valign="top" class="fieldnameback"><a name="sources"><span class="fieldname">&nbsp;<? echo $text[sources]; ?>&nbsp;</span></td>
		<td valign="top" class="databack" colspan="5"><span class="normal"><? echo getSource( $sources ); ?></span></td>
	</tr>
<?
		showBreak();
	}
?>	
</table>

<?
echo tng_menu( "", $familyID, 2 );
tng_footer( "" );
?>
