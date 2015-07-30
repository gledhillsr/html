<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "browsedocs";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");
include($cms[tngpath] . "functions.php");

$browsedocs_url = getURL( "browsedocs", 1 );
$getperson_url = getURL( "getperson", 1 );

function doDocSearch( $instance, $pagenav ) {
	global $text, $photosearch;

	$browsedocs_noargs_url = getURL( "browsedocs", 0 );
	
	$str = "<span class=\"normal\">\n";
	$str .= getFORM( "browsedocs", "GET", "DocSearch$instance", "" );
	$str .= "<input type=\"text\" name=\"docsearch\" value=\"$docsearch\"> <input type=\"submit\" value=\"$text[search]\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	$str .= $pagenav;
	if( $docsearch ) {
		$str .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$browsedocs_noargs_url\">$text[browsealldocs]</a>";
	}
	$str .= "</form></span>\n";
	
	return $str;
}

$max_browsedoc_pages = 5;
if( $offset ) {
	$newoffset = "$offset, ";
	$offsetplus = $offset + 1;
}
else {
	$offsetplus = 1;
	$page = 1;
}

if( $tree ) {
	$wherestr = "WHERE $doclinks_table.gedcom = \"$tree\"";
	if( $docsearch ) $wherestr .= " AND $histories_table.description LIKE \"%$docsearch%\"";
}
else if( $docsearch ) 
	$wherestr = "WHERE $histories_table.description LIKE \"%$docsearch%\"";
else
	$wherestr = "";

$query = "SELECT DISTINCT $histories_table.docID, description, notes, path FROM $histories_table LEFT JOIN $doclinks_table on $histories_table.docID = $doclinks_table.docID $wherestr ORDER BY description LIMIT $newoffset" . ($maxsearchresults + 1);
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");

$numrows = mysql_num_rows( $result );

if( $numrows == $maxsearchresults + 1 || $offsetplus > 1 ) {
	$query = "SELECT count(DISTINCT $histories_table.docID) as dcount FROM $histories_table LEFT JOIN $doclinks_table on $histories_table.docID = $doclinks_table.docID $wherestr";
	$result2 = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$row = mysql_fetch_assoc( $result2 );
	$totrows = $row[dcount];
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

tng_header( $text[browsealldocs], "" );
?>

<p class="header"><? echo $text[browsealldocs]; ?></span></p>

<? 
echo tng_menu( "", "", 1 );
$query = "SELECT gedcom, treename FROM $trees_table ORDER BY treename";
$treeresult = mysql_query($query) or die ("$admtext[cannotexecutequery]: $query");
$numtrees = mysql_num_rows($treeresult);

if( $numtrees > 1 ) {
	$formstr = getFORM( "browsedocs", "GET", "form1", "form1" );
	echo $formstr;

	echo $text[tree]; ?>: 
	<select name="tree">
		<option value="" <? if( !$tree ) echo "selected"; ?>><? echo $text[alltrees]; ?></option>
<?
	while( $row = mysql_fetch_assoc($treeresult) ) {
		echo "	<option value=\"$row[gedcom]\"";
		if( $tree && $row[gedcom] == $tree ) echo " selected";
		echo ">$row[treename]</option>\n";
	}
?>
	</select> <input type="submit" value="<? echo $text[go]; ?>"><br>
</form>
<?
}
if( $totrows )
	echo "<p>$text[matches] $offsetplus $text[to] $numrowsplus $text[of] $totrows</p>";

$pagenav = get_browseitems_nav( $totrows, $browsedocs_url . "docsearch=$docsearch&offset", $maxsearchresults, $max_browsedoc_pages );
if( $pagenav || $docsearch )
	echo doDocSearch( 1, $pagenav );
?>
<table cellpadding="3" cellspacing="1" border="0">
	<tr><td class="fieldnameback">&nbsp;</td>
	<td class="fieldnameback"><span class="fieldname">&nbsp;<strong><? echo $text[description]; ?></strong>&nbsp;</span></td>
	<td class="fieldnameback"><span class="fieldname">&nbsp;<strong><? echo $text[notes]; ?></strong>&nbsp;</span></td>
	<td class="fieldnameback"><span class="fieldname">&nbsp;<strong><? echo $text[people]; ?></strong>&nbsp;</span></td></tr>
<?
$i = 1;
while( $row = mysql_fetch_assoc( $result ) )
{
	$query = "SELECT $doclinks_table.personID, $doclinks_table.gedcom, firstname, lastname, living FROM $doclinks_table, $people_table WHERE $doclinks_table.personID = $people_table.personID AND $doclinks_table.gedcom = $people_table.gedcom AND $doclinks_table.docID = '$row[docID]' ORDER BY lastname, firstname";
	$presult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$doclinktext = "";
	$noneliving = 1;
	while( $prow = mysql_fetch_assoc( $presult ) )
	{
		if( $prow[living] && ( !$allow_living_db || ($assignedtree && $assignedtree != $prow[gedcom]) ) ) {
			$noneliving = 0;
			$thisliving = 1;
		}
		else
			$thisliving = 0;
		$doclinktext .= "<a href=\"$getperson_url" . "personID=$prow[personID]&tree=$prow[gedcom]\">";
		$doclinktext .= !$thisliving || !$nonames ? "$prow[firstname] $prow[lastname]" : $text[living];
		$doclinktext .= "</a>\n<br>\n";
	}
	mysql_free_result( $presult );

	if( $row[path] && $noneliving ) {
		$description = "<a href=\"$historypath/$row[path]\">$row[description]</a>";
		$notes = $row[notes];
	}
	elseif( $noneliving || !$nonames ) {
		$description = $row[description];
		$notes = $row[notes];
	}
	else
		$description = $notes = $text[living];

	echo "<tr><td valign=\"top\" class=\"databack\"><span class=\"normal\">$i</span></td>\n";
	echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\">$description&nbsp;</span></td>\n";
	echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\">$notes&nbsp;</span></td>";
	echo "<td valign=\"top\" class=\"databack\" nowrap><span class=\"normal\">\n";
	echo $doclinktext; 
 	echo "</span></td></tr>";
	$i++;
}
mysql_free_result($result);
?>
</table>

<?
if( $pagenav || $docsearch )
	echo doDocSearch( 2, $pagenav );

echo tng_menu( "", "", 2 );
tng_footer( "" );
?>
