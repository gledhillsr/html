<?php
include("begin.php");
//session_cache_limiter('public');
include($cms[tngpath] . "genlib.php");
$textpart = "search";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");
include($cms[tngpath] . "log.php" );
include($cms[tngpath] . "functions.php");

$searchform_url = getURL( "searchform", 1 );
$search_url = getURL( "search", 1 );
$getperson_url = getURL( "getperson", 1 );
$showtree_url = getURL( "showtree", 1 );

@set_time_limit(0);
$query = "SELECT gedcom FROM $trees_table";
$treeresult = mysql_query($query) or die ("$admtext[cannotexecutequery]: $query");
$numtrees = mysql_num_rows($treeresult);

function buildCriteria( $column, $colvar, $qualifyvar, $qualifier, $value ) {
	global $text, $allwhere, $mybool, $querystring;
	
	$value = urldecode($value);
	if (get_magic_quotes_gpc() == 0)
		$value = addslashes( $value );

	$criteria = "";
	if( $criteria ) $criteria .= " $mybool ";
	switch ($qualifier) {
		case $text[equals]:
			$criteria .= "$column = \"$value\"";
			break;
		case $text[startswith]:
			$criteria .= "$column LIKE \"$value%\"";
			break;
		case $text[endswith]:
			$criteria .= "$column LIKE \"%$value\"";
			break;
		case $text[soundexof]:
			$criteria .= "SOUNDEX($column) = SOUNDEX(\"$value\")";
			break;
		case $text[metaphoneof]:
			$criteria .= "metaphone = \"" . metaphone($value) . "\"";
			break;
		default:
			$criteria .= "$column LIKE \"%$value%\"";
			$qualifier = $text[contains];
			break;
	}
	addtoQuery( $column, $colvar, $criteria, $qualifyvar, $qualifier, $qualifier, $value );
}

function buildYearCriteria( $column, $colvar, $qualifyvar, $altcolumn, $qualifier, $value ) {
	global $text;
	
	$value = urldecode($value);
	if (get_magic_quotes_gpc() == 0)
		$value = addslashes( $value );

	$criteria = "";
	switch ($qualifier) {
		case "pm2":
			$criteria = "(IF($column!='',YEAR($column),YEAR($altcolumn)) < $value + 2 AND IF($column,YEAR($column), YEAR($altcolumn)) > $value - 2)";
			$qualifystr = $text[plusminus2];
			break;
		case "pm10":
			$criteria = "(IF($column!='',YEAR($column),YEAR($altcolumn)) < $value + 10 AND IF($column,YEAR($column), YEAR($altcolumn)) > $value - 10)";
			$qualifystr = $text[plusminus10];
			break;
		case "lt":
			$criteria = "IF($column!='',YEAR($column),YEAR($altcolumn)) < \"$value\"";
			$qualifystr = $text[lessthan];
			break;
		case "gt":
			$criteria = "IF($column!='',YEAR($column),YEAR($altcolumn)) > \"$value\"";
			$qualifystr = $text[greaterthan];
			break;
		case "lte":
			$criteria = "IF($column!='',YEAR($column),YEAR($altcolumn)) <= \"$value\"";
			$qualifystr = $text[lessthanequal];
			break;
		case "gte":
			$criteria = "IF($column!='',YEAR($column),YEAR($altcolumn)) >= \"$value\"";
			$qualifystr = $text[greaterthanequal];
			break;
		default:
			$criteria = "IF($column!='',YEAR($column),YEAR($altcolumn)) = \"$value\"";
			$qualifystr = $text[equalto];
			break;
	}
	addtoQuery( $column, $colvar, $criteria, $qualifyvar, $qualifier, $qualifystr, $value );
}

function addtoQuery( $column, $colvar, $criteria, $qualifyvar, $qualifier, $qualifystr, $value ) {
	global $text, $allwhere, $mybool, $querystring, $urlstring;

	if( $urlstring )
		$urlstring .= "&";
	$urlstring .= "$colvar=" . urlencode($value) . "&$qualifyvar=$qualifier";
	
	if( $querystring )
		$querystring .= "$mybool ";
	$querystring .= "$text[$column] $qualifystr " . stripslashes($value) . " ";
	
	if( $criteria ) {
		if( $allwhere )  $allwhere .= " " . $mybool;
		$allwhere .= " " . $criteria;
	}
}

$querystring = "";
$allwhere = "";

if( $mylastname )  {
	if( $mylastname == $text[nosurname] )
		addtoQuery( "lastname", "mylastname", "lastname = \"\"", "lnqualify", $text[equals], $text[equals], $mylastname );
	else {
		buildCriteria( "lastname", "mylastname", "lnqualify", $lnqualify, $mylastname );
	}
}
if( $myfirstname ) {
	buildCriteria( "firstname", "myfirstname", "fnqualify", $fnqualify, $myfirstname );
}
if( $mytitle ) {
	buildCriteria( "title", "mytitle", "tqualify", $tqualify, $mytitle );
}
if( $mysuffix ) {
	buildCriteria( "suffix", "mysuffix", "sfqualify", $sfqualify, $mysuffix );
}
if( $mynickname ) {
	buildCriteria( "nickname", "mynickname", "nnqualify", $nnqualify, $mynickname );
}
if( $mybirthplace ) {
	buildCriteria( "birthplace", "mybirthplace", "bpqualify", $bpqualify, $mybirthplace );
}
if( $mydeathplace ) {
	buildCriteria( "deathplace", "mydeathplace", "dpqualify", $dpqualify, $mydeathplace );
}

if( $mybirthyear ) {
	buildYearCriteria( "birthdatetr", "mybirthyear", "byqualify", "altbirthdatetr", $byqualify, $mybirthyear );
}
if( $mydeathyear ) {
	buildYearCriteria( "deathdatetr", "mydeathyear", "dyqualify", "burialdatetr", $dyqualify, $mydeathyear );
}
if( $tree ) {
	if( $urlstring )
		$urlstring .= "&";
	$urlstring .= "tree=$tree";
	
	if( $querystring )
		$querystring .= "AND ";
	$querystring .= "tree $text[equals] " . stripslashes($tree) . " ";
		
	if( $allwhere ) $allwhere = "($allwhere) AND";
	$allwhere .= " $people_table.gedcom=\"$tree\"";
}

if( ( !$allow_living_db || $assignedtree ) && $nonames && ( $mytitle || $mysuffix || $mynickname || $mybirthplace || $mydeathplace || $mybirthyear || $mydeathyear ) ) {
	if( $allwhere ) $allwhere = $tree ? "$allwhere AND " : "($allwhere) AND ";
	if( $allow_living_db )
		$allwhere .= "($people_table.living != 1 OR $people_table.gedcom = \"$assignedtree\")";
	else
		$allwhere .= "$people_table.living != 1";
}

if( $allwhere ) {
	$allwhere = "WHERE " . $allwhere;
	$querystring = "$text[text_for] <b>" . $querystring . "</b>";
}

$max_browsesearch_pages = 5;
if( $offset ) {
	$newoffset = "$offset, ";
	$offsetplus = $offset + 1;
}
else {
	$offsetplus = 1;
	$page = 1;
}

if( $showspouse == "yes" ) {
	$families_join = "LEFT JOIN $families_table AS families1 ON ($people_table.gedcom = families1.gedcom AND $people_table.personID = families1.husband ) LEFT JOIN $families_table AS families2 ON ($people_table.gedcom = families2.gedcom AND $people_table.personID = families2.wife ) ";  // added IDF Apr 03
	$huswife = ", families1.wife as wife, families2.husband as husband";																													   // added IDF Apr 03
}
else {
	$families_join = "";
	$huswife = "";
}

$query = "SELECT $people_table.personID, lastname, firstname, $people_table.living, nickname, suffix, title, birthplace, birthdate, deathplace, deathdate, LPAD(SUBSTRING_INDEX(birthdate, ' ', -1),4,'0') as birthyear, birthplace, altbirthdate, LPAD(SUBSTRING_INDEX(altbirthdate, ' ', -1),4,'0') as altbirthyear, altbirthplace, $people_table.gedcom, treename $huswife FROM $people_table $families_join LEFT JOIN $trees_table on $people_table.gedcom = $trees_table.gedcom $allwhere ORDER BY lastname, firstname, birthyear, altbirthyear LIMIT $newoffset" . ($maxsearchresults + 1);
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
//echo "urlstring=($urlstring)<br>"; //hack
//echo "querystring=($querystring)<br>"; //hack
//echo "allwhere=($allwhere)<br>"; //hack
//echo "query=($query)<br>"; //hack

$numrows = mysql_num_rows( $result );

if( $numrows == $maxsearchresults + 1 || $offsetplus > 1 ) {
	$query = "SELECT count(personID) as pcount FROM $people_table $families_join LEFT JOIN $trees_table on $people_table.gedcom = $trees_table.gedcom $allwhere";
	$result2 = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$countrow = mysql_fetch_assoc($result2);
	$totrows = $countrow[pcount];
}
else
	$totrows = $numrows;

if ( !$numrows ) {
	$msg = "$text[noresults] $querystring. $text[tryagain].";
	header( "Location: " . "$searchform_url" . "msg=" . urlencode( $msg ) );
	exit;
}
else if ( $numrows == $maxsearchresults + 1 ) {
	$more = 1;
	$numrows = $maxsearchresults;
}
else
	$more = 0;
	
tng_header( $text[searchresults], "" );
?>

<p class="header"><? echo $text[searchresults]; ?></p>

<?
echo tng_menu( "", "", 1 );
$numrowsplus = $numrows + $offset;

writelog( "<a href=\"$search_url" . "lnqualify=$lnqualify&mylastname=$mylastname&fnqualify=$fnqualify&myfirstname=$myfirstname&mytitle=$mytitle&tqualify=$tqualify&mysuffix=$mysuffix&sfqualify=$sfqualify&mynickname=$mynickname&nnqualify=$nnqualify&mybirthplace=$mybirthplace&bpqualify=$bpqualify&mydeathplace=$mydeathplace&dpqualify=$dpqualify&mydeathyear=$mydeathyear&dyqualify=$dyqualify&mybirthyear=$mybirthyear&byqualify=$byqualify&mybool=$mybool&tree=$tree\">$text[searchresults] $querystring</a>" );

echo "<p>$text[matches] $offsetplus $text[to] $numrowsplus $text[of] $totrows $querystring</p>";

$pagenav = get_browseitems_nav( $totrows, "$search_url" . "$urlstring&mybool=$mybool&showspouse=$showspouse&tree=$tree&offset", $maxsearchresults, $max_browsesearch_pages );
echo "<p>$pagenav</p>";
?>

<table cellpadding="3" cellspacing="1" border="0">
	<tr>
		<td class="fieldnameback"><span class="fieldname"><nobr>&nbsp;<b><? echo $text[lastfirst]; ?></b>&nbsp;</nobr></span></td>
		<? if( $mysuffix ) { ?><td class="fieldnameback"><span class="fieldname">&nbsp;<b><? echo $text[suffix]; ?></b>&nbsp;</span></td><? } ?>
		<? if( $mytitle ) { ?><td class="fieldnameback"><span class="fieldname">&nbsp;<b><? echo $text[title]; ?></b>&nbsp;</span></td><? } ?>
		<? if( $mynickname ) { ?><td class="fieldnameback"><span class="fieldname">&nbsp;<b><? echo $text[nickname]; ?></b>&nbsp;</span></td><? } ?>
		<td class="fieldnameback" colspan="2"><span class="fieldname">&nbsp;<b><? echo $text[bornchr]; ?></b>&nbsp;</span></td>
		<? if( $mydeathyear || $mydeathplace ) { ?><td class="fieldnameback" colspan="2"><span class="fieldname">&nbsp;<b><? echo $text[diedburied]; ?></b>&nbsp;</span></td><? } ?>
		<? if( $showspouse) { ?><td class="fieldnameback"><span class="fieldname">&nbsp;<b><? echo $text[spouse]; ?></b>&nbsp;</span></td><? } ?>
		<td class="fieldnameback"><span class="fieldname"><nobr>&nbsp;<b><? echo $text[personid]; ?></b>&nbsp;</nobr></span></td>
		<? if( $numtrees > 1 ) { ?><td class="fieldnameback"><span class="fieldname">&nbsp;<b><? echo $text[tree]; ?></b>&nbsp;</span></td><? } ?>
	</tr>
	
<?
$i = $offsetplus;
$j = $offsetplus + $maxsearchresults;
while( $i < $j && $row = mysql_fetch_assoc($result))
{
	if( !$row[living] || ( $allow_living_db && (!$assignedtree || $assignedtree == $row[gedcom]) ) ) {
		if ( $row[birthdate] || ( $row[birthplace] && !$row[altbirthdate] ) ) {
			$birthdate = "$text[birthabbr] $row[birthdate]";
			$birthplace = $row[birthplace];
		}
		else if ( $row[altbirthdate] || $row[altbirthplace] ) {
			$birthdate = "$text[chrabbr] $row[altbirthdate]";
			$birthplace = $row[altbirthplace];
		}
		else {
			$birthdate = "";
			$birthplace = "";
		}
		if ( $row[deathdate] || ( $row[deathplace] && !$row[burialdate] ) ) {
			$deathdate = "$text[deathabbr] $row[deathdate]";
			$deathplace = $row[deathplace];
		}
		else if ( $row[burialdate] || $row[burialplace] ) {
			$deathdate = "$text[burialabbr] $row[burialdate]";
			$deathplace = $row[burialplace];
		}
		else {
			$deathdate = "";
			$deathplace = "";
		}
		$suffix = $row[suffix];
		$title = $row[title];
		$nickname = $row[nickname];
		$livingOK = 1;
	}
	else
		$suffix = $title = $nickname = $birthdate = $birthplace = $deathdate = $deathplace = $livingOK = "";
	$i++;
	echo "<tr>";
	if( $livingOK || !$nonames ) {
		$name = trim( "$row[lastname], $row[firstname]" );
		if( $suffix && !$mysuffix )
			$name .= " $suffix";
	}
	else
		$name = $text[living];
	echo "<td class=\"databack\"><span class=\"normal\">&nbsp;<a href=\"$getperson_url" . "personID=$row[personID]&tree=$row[gedcom]\">$name</a>&nbsp;</span></td>";
	if( $mysuffix ) echo "<td class=\"databack\"><span class=\"normal\">&nbsp;$suffix </span></td>";
	if( $mytitle ) echo "<td class=\"databack\"><span class=\"normal\">&nbsp;$title </span></td>";
	if( $mynickname ) echo "<td class=\"databack\"><span class=\"normal\">&nbsp;$nickname </span></td>";
	echo "<td class=\"databack\"><span class=\"normal\">&nbsp;$birthdate </span></td><td class=\"databack\"><span class=\"normal\">&nbsp;$birthplace </span></td>";
	if( $mydeathyear || $mydeathplace) echo "<td class=\"databack\"><span class=\"normal\">&nbsp;$deathdate </span></td><td class=\"databack\"><span class=\"normal\">&nbsp;$deathplace </span></td>";
	
	if( $showspouse ) {
		$spouse = "";
		$spouseID = $row[husband] ? $row[husband] : $row[wife];
		if( $spouseID ) {
			$query = "SELECT lastname, firstname FROM $people_table WHERE personID = \"$spouseID\" AND gedcom = \"$row[gedcom]\"";
			$spresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
			if( $spresult ) {
				$sprow = mysql_fetch_assoc($spresult);
				$spouse = "$sprow[firstname] $sprow[lastname]";
				mysql_free_result($spresult);
			}
		}
		echo "<td class=\"databack\"><span class=\"normal\">&nbsp;<a href=\"$getperson_url" . "personID=$spouseID&tree=$row[gedcom]\">$spouse</a>&nbsp;</span></td>";
	}
	echo "<td class=\"databack\"><span class=\"normal\">&nbsp;$row[personID] </span></td>";
	if( $numtrees > 1 )
		echo "<td class=\"databack\"><span class=\"normal\">&nbsp;<a href=\"$showtree_url" . "tree=$row[gedcom]\">$row[treename]</a>&nbsp;</span></td>";
	echo "</tr>\n";
}
mysql_free_result($result);

?>

</table>

<?
echo "<p>$pagenav</p>";

echo tng_menu( "", "", 2 );
tng_footer( "" );
?>
