<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "showphoto";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");
include($cms[tngpath] . "log.php" );
include($cms[tngpath] . "functions.php" );

$showphoto_url = getURL( "showphoto", 1 );
$browsephotos_url = getURL( "browsephotos", 1 );
$getextras_url = getURL( "getextras", 1 );

$photosperpage = 1;
$max_showphoto_pages = 5;
if( !$personID ) {
   $query = "SELECT path, photoID, description, notes FROM $photos_table ORDER BY description";
	$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$offsets = get_photo_offsets( $result, $photoID );
	$page = $offsets[0] + 1;
	mysql_data_seek ( $result, $offsets[0] );
	
	$row = mysql_fetch_assoc($result);
	
	writelog( "<a href=\"$showphoto_url" . "photoID=$row[photoID]\">$text[photoof] $row[description] ($row[photoID])</a>" );
}
else {
	$query = "SELECT path, description, notes, ordernum, $photos_table.photoID as photoID FROM $photos_table, $photolinks_table WHERE personID = \"$personID\" AND $photolinks_table.gedcom = \"$tree\" AND $photos_table.photoID = $photolinks_table.photoID ORDER by ordernum";
	$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$page = $ordernum;
	mysql_data_seek ( $result, $ordernum - 1 );
	
	$row = mysql_fetch_assoc($result);
	$photoID = $row[photoID];
	$ordernum = $row[ordernum];
	
	$query = "SELECT disallowgedcreate FROM $trees_table WHERE gedcom = \"$tree\"";
	$treeresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$treerow = mysql_fetch_assoc($treeresult);
	$disallowgedcreate = $treerow[disallowgedcreate];
	mysql_free_result( $treeresult );

	writelog( "<a href=\"$showphoto_url" . "personID=$personID&tree=$tree&ordernum=$ordernum\">$text[photoof] $row[description] ($row[photoID])</a>" );
}

//select all photolinks for this photoID, joined with people
//loop through looking for living
//if any are living, don't show photo
$query = "SELECT $photolinks_table.gedcom, living FROM $photolinks_table, $people_table WHERE $photolinks_table.personID = $people_table.personID AND $photolinks_table.gedcom = $people_table.gedcom AND $photolinks_table.photoID = '$row[photoID]'";
$presult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
$noneliving = 1;
while( $prow = mysql_fetch_assoc( $presult ) )
{
       if( $prow[living] && ( !$allow_living_db || ($assignedtree && $assignedtree != $prow[gedcom]) ) )
               $noneliving = 0;
}
mysql_free_result( $presult );

if( $noneliving || !$nonames ) {
       $description = $row[description];
       $notes = nl2br($row[notes]);
}
else
       $description = $notes = $text[living];

$size = GetImageSize( "$rootpath$photopath/$row[path]" );
$adjheight = $size[1] - 1;

tng_header( $description, "" );
?>

<p class="header"><? echo $description; ?></p>
<?
echo tng_menu( "", $personID, 1 );
?>
<span class="normal">

<? 
if( !$personID ) {
	$offset = floor( $page / $maxsearchresults ) * $maxsearchresults;
	$pagenav = "<a href=\"$browsephotos_url" . "offset=$offset\">$text[browseallphotos]</a>  &nbsp;&nbsp;&nbsp;";
}
else
	$pagenav = "<a href=\"$getextras_url" . "personID=$personID&tree=$tree\">$text[photoshistories]</a>  &nbsp;&nbsp;&nbsp;";

$pagenav .= get_showphoto_nav( $result, $showphoto_url . "photoID", $photosperpage, $max_showphoto_pages );
mysql_free_result( $result );

echo $pagenav;
?>

<p><? echo $notes; ?>
<br><br>
<?
if( $noneliving ) {
?>
<img src="<? echo "$photopath/$row[path]"; ?>" border="0" <? echo "$size[3]"; ?> alt="<? echo "$description"; ?>"></p>
<? 
$filename = basename( $row[path] );
echo "<p>$pagenav</p><b>$text[filename]: </b>$filename &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>$text[photosize]: </b>$size[0] x $size[1]";
}
else {
?>
<table border="1" cellspacing="0" cellpadding="5"><tr><td>
<img src="<? echo $cms[tngpath]; ?>spacer.gif" alt="" width="<? echo $size[0]; ?>" height="1" border="0"><br>
<img src="<? echo $cms[tngpath]; ?>spacer.gif" alt="" width="1" height="<? echo $adjheight; ?>" border="0" align="left">
<strong><span class="normal"><? echo $text[living]; ?></span></strong>
</td></tr></table>
<?
}
?>
 <br><br>
</span>
<?
echo tng_menu( "", $personID, 2 );
tng_footer( "" );
?>
