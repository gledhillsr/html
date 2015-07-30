<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "showphoto";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");
include($cms[tngpath] . "functions.php");

$browsephotos_url = getURL( "browsephotos", 1 );
$getperson_url = getURL( "getperson", 1 );
$showphoto_url = getURL( "showphoto", 1 );

function doPhotoSearch( $instance, $pagenav ) {
	global $text, $photosearch;

	$browsephotos_noargs_url = getURL( "browsephotos", 0 );
	
	$str = "<span class=\"normal\">\n";
	$str .= getFORM( "browsephotos", "GET", "PhotoSearch$instance", "" );
	$str .= "<input type=\"text\" name=\"photosearch\" value=\"$photosearch\"> <input type=\"submit\" value=\"$text[search]\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	$str .= $pagenav;
	if( $photosearch )
		$str .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$browsephotos_noargs_url\">$text[browseallphotos]</a>";
	$str .= "</form></span>\n";
	
	return $str;
}

$max_browsephoto_pages = 5;
if( $offset ) {
	$newoffset = "$offset, ";
	$offsetplus = $offset + 1;
	//$page = ceil( $offset / $maxsearchresults );
}
else {
	$offsetplus = 1;
	$page = 1;
}

if( $tree ) {
	$wherestr = "WHERE $photolinks_table.gedcom = \"$tree\"";
	if( $photosearch ) $wherestr .= " AND $photos_table.description LIKE \"%$photosearch%\"";
}
else if( $photosearch ) 
	$wherestr = "WHERE $photos_table.description LIKE \"%$photosearch%\"";
else
	$wherestr = "";
	
$query = "SELECT DISTINCT $photos_table.photoID, $photos_table.description, $photos_table.notes, $photos_table.thumbpath FROM $photos_table LEFT JOIN $photolinks_table";
$query .= " on $photos_table.photoID = $photolinks_table.photoID $wherestr ORDER BY description LIMIT $newoffset" . ($maxsearchresults + 1);
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
$numrows = mysql_num_rows( $result );

if( $numrows == $maxsearchresults + 1 || $offsetplus > 1 ) {
	$query = "SELECT count(DISTINCT $photos_table.photoID) as phcount FROM $photos_table LEFT JOIN $photolinks_table";
	$query .= " on $photos_table.photoID = $photolinks_table.photoID $wherestr";
	$result2 = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$row = mysql_fetch_assoc( $result2 );
	$totrows = $row[phcount];
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

tng_header( $text[browseallphotos], "" );
?>

<p class="header"><? echo $text[browseallphotos]; ?></span></p>
<? 
echo tng_menu( "", "", 1 );
$query = "SELECT gedcom, treename FROM $trees_table ORDER BY treename";
$treeresult = mysql_query($query) or die ("$admtext[cannotexecutequery]: $query");
$numtrees = mysql_num_rows($treeresult);

if( $numtrees > 1 ) {
	$formstr = getFORM( "browsephotos", "GET", "form1", "form1" );
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
	</select> <input type="submit" value="<? echo $text[go]; ?>"><br>
</form>
<?
}
if( $totrows )
	echo "<p>$text[matches] $offsetplus $text[to] $numrowsplus $text[of] $totrows</p>";

$pagenav = get_browseitems_nav( $totrows, $browsephotos_url . "photosearch=$photosearch&offset", $maxsearchresults, $max_browsephoto_pages );
if( $pagenav || $photosearch )
	echo doPhotoSearch( 1, $pagenav );
?>
<table cellpadding="3" cellspacing="1" border="0">
	<tr><td class="fieldnameback">&nbsp;</td>
	<td class="fieldnameback"><span class="fieldname">&nbsp;<strong><? echo $text[thumb]; ?></strong>&nbsp;</span></td>
	<td class="fieldnameback"><span class="fieldname">&nbsp;<strong><? echo $text[description]; ?></strong>&nbsp;</span></td>
	<td class="fieldnameback"><span class="fieldname">&nbsp;<strong><? echo $text[notes]; ?></strong>&nbsp;</span></td>
	<td class="fieldnameback"><span class="fieldname">&nbsp;<strong><? echo $text[people]; ?></strong>&nbsp;</span></td></tr>
<?
$i = $offsetplus;
$j = $offsetplus + $maxsearchresults;
while( $i < $j && $row = mysql_fetch_assoc( $result ) )
{
 	$query = "SELECT $photolinks_table.personID, $photolinks_table.gedcom, firstname, lastname, living FROM $photolinks_table, $people_table WHERE $photolinks_table.personID = $people_table.personID AND $photolinks_table.gedcom = $people_table.gedcom AND $photolinks_table.photoID = '$row[photoID]' ORDER BY lastname, firstname";
	$presult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$photolinktext = "";
	$noneliving = 1;
	while( $prow = mysql_fetch_assoc( $presult ) )
	{
		if( $prow[living] && ( !$allow_living_db || ($assignedtree && $assignedtree != $prow[gedcom]) ) ) {
			$noneliving = 0;
			$thisliving = 1;
		}
		else
			$thisliving = 0;
		$photolinktext .= "<a href=\"$getperson_url" . "personID=$prow[personID]&tree=$prow[gedcom]\">";
		$photolinktext .= !$thisliving || !$nonames ? "$prow[firstname] $prow[lastname]" : $text[living];
		$photolinktext .= "</a>\n<br>\n";
	}
	mysql_free_result( $presult );

	if( $noneliving || !$nonames ) {
		$description = $row[description];
		$notes = $row[notes];
	}
	else
		$description = $notes = $text[living];

	echo "<tr><td valign=\"top\" class=\"databack\"><span class=\"normal\">$i</span></td>";
	echo "<td valign=\"top\" class=\"databack\">";
	if( $noneliving && $row[thumbpath] && file_exists("$photopath/$row[thumbpath]")) {
		$photoinfo = getimagesize( "$photopath/$row[thumbpath]" );
		if( $photoinfo[1] < 50 ) {
			$photohtouse = $photoinfo[1];
			$photowtouse = $photoinfo[0];
		}
		else {
			$photohtouse = 50;
			$photowtouse = intval( 50 * $photoinfo[0] / $photoinfo[1] ) ;
		}
		echo "<a href=\"$showphoto_url" . "photoID=$row[photoID]\">";
		echo "<span class=\"normal\">";
		echo "<img border=0 src=\"$photopath/$row[thumbpath]\" width=\"$photowtouse\" height=\"$photohtouse\"></span></a>\n";
	}
	else 
		echo "&nbsp;";
	echo "</td>\n";
	echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\">";
	echo "<a href=\"$showphoto_url" . "photoID=$row[photoID]\">$description</a>&nbsp;</span></td>\n";
	echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\">$notes&nbsp;</span></td>";
	echo "<td valign=\"top\" class=\"databack\" nowrap><span class=\"normal\">\n";
	echo $photolinktext;
	echo "</span></td></tr>\n";
	$i++;
}
mysql_free_result($result);
?>
</table>

<?
if( $pagenav || $photosearch )
	echo doPhotoSearch( 2, $pagenav );

echo tng_menu( "", "", 2 );
tng_footer( "" );
?>
