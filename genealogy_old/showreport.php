<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "showreport";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");
include($cms[tngpath] . "functions.php");

function processfield( $field ) {
	global $need_families;
	
	if( $field == "marrdate" || $field == "marrdatetr" || $field == "marrplace" ) {
		$newfield = "if(sex='M',families1." . $field . ",families2." . $field . ")";
		$need_families = 1;
	}
	else if( substr($field,0,2) == "ss" ) {
		$newfield = "if(sex='M',families1." . substr($field,1) . ",families2." . substr($field,1) . ")";
		$need_families = 1;
	}
	else
		$newfield = $field;
	return $newfield;
}

$showreport_url = getURL( "showreport", 1 );
$getperson_url = getURL( "getperson", 1 );

$query = "SELECT gedcom, treename FROM $trees_table ORDER BY treename";
$treeresult = mysql_query($query) or die ("$admtext[cannotexecutequery]: $query");
$numtrees = mysql_num_rows($treeresult);

if( $tree ) {
	$peopletreestr = "$people_table.gedcom = \"$tree\"";
	$familytreestr = "if(sex='M',families1.gedcom = \"$tree\",families2.gedcom = \"$tree\")";
	$childrentreestr = "$children_table.gedcom = \"$tree\"";
}
else {
	$peopletreestr = "";
	$familytreestr = "";
	$childrentreestr = "";
}
$treestr = $peopletreestr;
$trees_join = "";

$need_families = 0;
$need_children = 0;

$query = "SELECT * FROM $reports_table WHERE reportID = $reportID";
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
$rrow = mysql_fetch_assoc( $result );
mysql_free_result($result);

$ldsfields = array("baptdate","baptplace","endldate","endlplace","ssealdate","ssealplace","psealdate","psealplace");
$truedates = array("birthdatetr","altbirthdatetr","deathdatetr","burialdatetr","baptdatetr","endldatetr","ssealdatetr","psealdatetr","marrdatetr","changedate");
$familyfields = array("marrdate","marrdatetr","marrplace","ssealdate","ssealdatetr","ssealplace");
$displaystr = "$people_table.living";
$displayfields = explode( $lineending, $rrow[display] );
for( $i = 0; $i < count( $displayfields ) - 1; $i++ ) {
	if( $displayfields[$i] != "personID" && $displayfields[$i] != "gedcom" && ( $allow_lds || !in_array( $displayfields[$i], $ldsfields ) ) ) {
		if( $displaystr ) $displaystr .= ",";
		if( $displayfields[$i] == "marrdate" || $displayfields[$i] == "marrdatetr" || $displayfields[$i] == "marrplace" ) {
			$need_families = 1;
			$displayfields[$i] = "if(sex='M',families1." . $displayfields[$i] . ",families2." . $displayfields[$i] . ")";
		}
		if( substr($displayfields[$i],0,2) == "ss" ) {
			$need_families = 1;
			$displayfields[$i] = "if(sex='M',families1." . substr($displayfields[$i],1) . ",families2." . substr($displayfields[$i],1) . ")";
		}
		if( substr($displayfields[$i],0,2) == "ps" ) {
			$displayfields[$i] = "$children_table." . substr($displayfields[$i],1);
			$need_children = 1;
		}
		if(  substr( $displayfields[$i], 0, 6 ) == "spouse" ) {
			$need_families = 1;
			$displaystr .= "(if(sex='M',families1.wife,families2.husband)) as spouse";
		}
		elseif( in_array( $displayfields[$i], $truedates ) )
			$displaystr .= "DATE_FORMAT($people_table.$displayfields[$i],'%d %b %Y') as $displayfields[$i]";
		elseif( $displayfields[$i] == "gedcom" ) {
			$trees_join = ", $trees_table";
			$treestr .= " AND $people_table.gedcom = $trees_table.gedcom";
			$displaystr .= "treename";
			$displayfields[$i] = "treename";
		}
		else
			$displaystr .= $displayfields[$i];
	}
}
$displaystr .= ", $people_table.personID, $people_table.gedcom";

$criteriastr = "";
$criteriafields = explode( $lineending, $rrow[criteria] );
for( $i = 0; $i < count( $criteriafields ) - 1; $i++ ) {
	if( $criteriastr ) $criteriastr .= " ";
	if( in_array( $criteriafields[$i], $familyfields ) ) 
		$need_families = 1;

	if( $criteriafields[$i] == "currmonth" )
		$criteriafields[$i] = "\"" . strtoupper( date( "M" ) ) . "\"";
	else if( $criteriafields[$i] == "currmonthnum" )
		$criteriafields[$i] = "\"" . date( "m" ) . "\"";
	else if( $criteriafields[$i] == "curryear" )
		$criteriafields[$i] = "\"" . date( "Y" ) . "\"";
	else if( $criteriafields[$i] == "currday" )
		$criteriafields[$i] = "\"" . date( "d" ) . "\"";
	else if( $criteriafields[$i] == "personID" )
		$criteriafields[$i] = "$people_table.personID";
	else if( $criteriafields[$i] == "today" ) {
		$criteriafields[$i] = "NOW()";
		$truedate = 1;
	}
	else if( in_array($criteriafields[$i],$truedates) ) {
		$truedate = 1;
	}
		
	switch( $criteriafields[$i] ) {
		case "dayonly":
		case "monthonly":
		case "yearonly":
		case "to_days":
			$criteriastr .= "";
			break;
		case "contains":
		case "starts with":
		case "ends with":
			$criteriastr .= "LIKE";
			break;
		default:
			switch( $criteriafields[$i-1] ) {
				case "dayonly":
					if( $truedate )
						$criteriastr .= "DAYOFMONTH($criteriafields[$i])";
					else
						$criteriastr .= "LPAD(SUBSTRING_INDEX($criteriafields[$i], ' ', 1),2,'0')";
					break;
				case "monthonly":
					if( $truedate )
						$criteriastr .= "MONTH($criteriafields[$i])";
					else
						$criteriastr .= "substring(SUBSTRING_INDEX($criteriafields[$i], ' ', -2),1,3)";
					break;
				case "yearonly":
					if( $truedate )
						$criteriastr .= "YEAR($criteriafields[$i])";
					else
						$criteriastr .= "LPAD(SUBSTRING_INDEX($criteriafields[$i], ' ', -1),4,'0')";
					break;
				case "to_days":
					if( $truedate )
						$criteriastr .= "TO_DAYS($criteriafields[$i])";
					else
						$criteriastr .= "LPAD(SUBSTRING_INDEX($criteriafields[$i], ' ', -1),4,'0')";
					break;
				case "contains":
					if( substr( $criteriafields[$i], 0, 1 ) == "\"" )
						$criteriastr .= "\"%" . substr( $criteriafields[$i], 1, -1) . "%\"";
					else
						$criteriastr .= "\"%" . $criterafields[$i] . "%\"";
					break;
				case "starts with":
					if( substr( $criteriafields[$i], 0, 1 ) == "\"" )
						$criteriastr .= "\"" . substr( $criteriafields[$i], 1, -1) . "%\"";
					else
						$criteriastr .= "\"" . $criteriafields[$i] . "%\"";
					break;
				case "ends with":
					if( substr( $criteriafields[$i], 0, 1 ) == "\"" )
						$criteriastr .= "\"%" . substr( $criteriafields[$i], 1, -1) . "\"";
					else
						$criteriastr .= "\"%" . $criteriafields[$i] . "\"";
					break;
				default:
					if( substr($criteriafields[$i],0,2) == "ps" ) {
						$criteriastr .= "$children_table." . substr($criteriafields[$i],1);
						$need_children = 1;
					}
					else if( $criteriafields[$i] == "marrdate" || $criteriafields[$i] == "marrdatetr" || $criteriafields[$i] == "marrplace" ) {
						$criteriastr .= "if(sex='M',families1." . $criteriafields[$i] . ",families2." . $criteriafields[$i] . ")";
						$need_families = 1;
					}
					else if( substr($criteriafields[$i],0,2) == "ss" ) {
						$criteriastr .= "if(sex='M',families1." . substr($criteriafields[$i],1) . ",families2." . substr($criteriafields[$i],1) . ")";
						$need_families = 1;
					}
					else
						$criteriastr .= $criteriafields[$i];
					break;
			}
			break;
	}
}
if( ( !$allow_living_db || $assignedtree ) && $nonames ) {
	if( $criteriastr ) $criteriastr = "($criteriastr) AND ";
	if( $allow_living_db )
		$criteriastr .= "($people_table.living != 1 OR $people_table.gedcom = \"$assignedtree\")";
	else
		$criteriastr .= "$people_table.living != 1";
}
if( $criteriastr )
	$criteriastr = "WHERE $criteriastr";

$orderbystr = "";
$orderbyfields = explode( $lineending, $rrow[orderby] );
for( $i = 0; $i < count( $orderbyfields ) - 1; $i++ ) {
	if( $orderbystr ) {
		if( $orderbyfields[$i] == "desc" )
			$orderbystr .= " ";
		else
			$orderbystr .= ",";
	}
	$prefix = "";
	if( $orderbyfields[$i] == "dayonly" ) {
		$i++;
		$newfield = processfield( $orderbyfields[$i] );
		if( in_array($orderfields[$i],$truedates) )
			$newfield = "DAYOFMONTH($newfield)";
		else
			$newfield = "LPAD(SUBSTRING_INDEX($newfield, ' ', 1),2,'0')";
		$displaystr .= ", $newfield as dayonly$orderbyfields[$i]";
		$orderbystr .= "dayonly$orderbyfields[$i]";
	}
	else if( $orderbyfields[$i] == "yearonly" ) {
		$i++;
		$newfield = processfield( $orderbyfields[$i] );
		if( in_array($orderfields[$i],$truedates) )
			$newfield = "YEAR($newfield)";
		else
			$newfield = "LPAD(SUBSTRING_INDEX($newfield, ' ', -1),4,'0')";
		$displaystr .= ", $newfield as yearonly$orderbyfields[$i]";
		$orderbystr .= "yearonly$orderbyfields[$i]";
	}
	else if( $orderbyfields[$i] == "personID" )
		$orderbystr .= "$people_table.personID";
	else if( substr($orderbyfields[$i],0,2) == "ps" ) {
		$orderbystr .= "$children_table." . substr($orderbyfields[$i],1);
		$need_children = 1;
	}
	else
		$orderbystr .= processfield( $orderbyfields[$i] );
}
if( $orderbystr )
	$orderbystr = "ORDER BY $orderbystr";

$max_browsesearch_pages = 5;
if( $offset ) {
	$newoffset = "$offset, ";
	$offsetplus = $offset + 1;
}
else {
	$offsetplus = 1;
	$page = 1;
}

if( $need_families ) {
	$families_join = "LEFT JOIN $families_table AS families1 ON ($people_table.gedcom = families1.gedcom AND $people_table.personID = families1.husband ) LEFT JOIN $families_table AS families2 ON ($people_table.gedcom = families2.gedcom AND $people_table.personID = families2.wife ) "; 
	//$families_join = "LEFT JOIN $families_table ON ($people_table.personID = $families_table.husband OR $people_table.personID = $families_table.wife) AND $people_table.gedcom = $families_table.gedcom";
	if( $familytreestr ) $treestr .= " AND $familytreestr";
}
else
	$families_join = "";
if( $need_children ) {
	$children_join = "LEFT JOIN $children_table ON $people_table.personID = $children_table.personID AND $people_table.gedcom = $children_table.gedcom";
	if( $childrentreestr ) $treestr .= " AND $childrentreestr";
}
else
	$children_join = "";

if( $treestr )
       $treestr = $criteriastr ? "AND $treestr" : "WHERE $treestr";

$query = "SELECT $displaystr FROM $people_table $trees_join $families_join $children_join $criteriastr $treestr $orderbystr LIMIT $newoffset" . ($maxsearchresults + 1);
$result = @mysql_query($query);

tng_header( $rrow[reportname], "" );
?>

<p class="header"><?php echo "$text[report]: $rrow[reportname]"; ?></p>

<p class="normal"><?php echo "$text[description]: $rrow[reportdesc]"; ?></p>

<?php
echo tng_menu( "", "", 1 );
if( $numtrees > 1 ) {
	$formstr = getFORM( "showreport", "GET", "form1", "form1" );
	echo $formstr;

	echo $text[tree]; ?>: 
	<select name="tree">
		<option value="-x--all--x-" <?php if( !$tree ) echo "selected"; ?>><?php echo $text[alltrees]; ?></option>
<?php
	while( $row = mysql_fetch_assoc($treeresult) ) {
		echo "	<option value=\"$row[gedcom]\"";
		if( $tree && $row[gedcom] == $tree ) echo " selected";
		echo ">$row[treename]</option>\n";
	}
?>
	</select> <input type="hidden" name="reportID" value="<?php echo $reportID; ?>"><input type="submit" value="<?php echo $text[go]; ?>">
</form>
<?php
}

if( !$result ) {
?>

<p class="normal"><?php echo "<b>$text[error]:</b> $text[reportsyntax] (ID: $rrow[reportID]) $text[wasincorrect] "; ?>
<?php echo "<a href=\"mailto:$emailaddr\">$emailaddr</a>"; ?>.</p>
<p><?php echo "$text[query]: $query <br>$text[errormessage]:"; ?>
<?php echo mysql_error(); ?></p>

<?php
}
else {
	$numrows = mysql_num_rows( $result );
	if( $numrows == $maxsearchresults + 1 || $offsetplus > 1 ) {
		$query = "SELECT count($people_table.personID) as rcount FROM $people_table $trees_join $families_join $children_join $criteriastr $treestr";
		$result2 = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
		$countrow = mysql_fetch_assoc($result2);
		$totrows = $countrow[rcount];
	}
	else
		$totrows = $numrows;
	
	if ( $numrows == $maxsearchresults + 1 ) {
		$more = 1;
		$numrows = $maxsearchresults;
	}
	else
		$more = 0;
	$numrowsplus = $numrows + $offset;
	if( $totrows )
		echo "<p>$text[matches] $offsetplus $text[to] $numrowsplus $text[of] $totrows</p>";

	$pagenav = get_browseitems_nav( $totrows, "$showreport_url" . "reportID=$reportID&tree=$tree&offset", $maxsearchresults, $max_browsesearch_pages );
	echo $pagenav;
?>

<table cellpadding="3" cellspacing="1" border="0">
	<tr>
<?php
	$displaytext = explode( $lineending, $rrow[displaytext] );
	for( $i = 0; $i < count( $displayfields ) - 1; $i++ ) {
		echo "<td class=\"fieldnameback\"><span class=\"fieldname\"><strong>$displaytext[$i]</strong></span></td>\n";
	}
?>
	</tr>
<?php
	while( $row = mysql_fetch_assoc($result)) {
		echo "<tr>\n";
		for( $i = 0; $i < count( $displayfields ) - 1; $i++ ) {
			$thisfield = $displayfields[$i];
			if( $thisfield == "lastname, firstname" ){
				$data = !$row[living] || !$nonames || ( $allow_living_db && (!$assignedtree || $assignedtree == $row[gedcom])) ? trim("$row[lastname], $row[firstname]") : $text[living];
			}
			else if( $thisfield == "firstname, lastname" ){
				$data = !$row[living] || !$nonames || ( $allow_living_db && (!$assignedtree || $assignedtree == $row[gedcom])) ? trim("$row[firstname] $row[lastname]") : $text[living];
			}
			else if( substr( $thisfield, 0, 6 ) == "spouse" ) {
				$spouseID = $row[spouse];
				if( $thisfield == "spousename" ) { 
					$query = "SELECT lastname, firstname, gedcom, living FROM $people_table WHERE personID = \"$spouseID\" AND gedcom = \"$row[gedcom]\"";
					$spresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
					if( $spresult ) {
						$sprow = mysql_fetch_assoc($spresult);
						$data = !$sprow[living] || !$nonames || ( $allow_living_db && (!$assignedtree || $assignedtree == $row[gedcom])) ? trim("$sprow[firstname] $sprow[lastname]") : $text[living];
						mysql_free_result($spresult);
					}
					else
						$data = "";
				}
				else
					$data = $spouseID;
			}
			else if( !$row[living] || $allow_living )
				$data = $row[$thisfield];
			else
				$data = "&nbsp;";
			if( $thisfield == $rrow[linkfield] )
				$data = "<a href=\"$getperson_url" . "personID=$row[personID]&tree=$row[gedcom]\">$data</a>";
			echo "<td class=\"databack\"><span class=\"normal\">$data</span></td>\n";
		}
		echo "</tr>\n";
	}
	mysql_free_result($result);
?>
</table>
<br><br>
<?php
	echo $pagenav;
}

echo tng_menu( "", "", 2 );
tng_footer( "" );
?>

