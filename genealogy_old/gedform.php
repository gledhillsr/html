<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "gedcom";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");

$query = "SELECT firstname, lastname, living, disallowgedcreate FROM $people_table, $trees_table WHERE personID = \"$personID\" AND $people_table.gedcom = \"$tree\" AND $people_table.gedcom = $trees_table.gedcom";
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
if( $result ) {
	$row = mysql_fetch_assoc($result);
	$firstname=$row[firstname];
	$lastname=$row[lastname];
	$disallowgedcreate = $row[disallowgedcreate];
	$name = !$row[living] || $allow_living || !$nonames ? "$row[firstname] $row[lastname]" : $text[living];
	mysql_free_result($result);
}
if( $disallowgedcreate ) exit;

tng_header( "$text[creategedfor] $name", "" );
?>

<p class="header">
<? 
	if( !$row[living] || $allow_living ) {
		$photoref = $tree ? "$photopath/$tree.$personID.$photosext" : "$photopath/$personID.$photosext";
		echo showSmallPhoto( $photoref, $name );
	}
	echo $name; 
?>
<br clear="left">
</p>
<?
	echo tng_menu( "gedcom", $personID, 1 );

$formstr = getFORM( "gedcom", "GET", "", "" );
echo $formstr;
?>
<INPUT TYPE="HIDDEN" NAME="personID" VALUE="<? echo $personID; ?>">
<INPUT TYPE="HIDDEN" NAME="tree" VALUE="<? echo $tree; ?>">
<table border="0" cellspacing="2" cellpadding="0">
<tr><td><span class="normal"><? echo $text[gedstartfrom]; ?>:&nbsp; </span></td><td><span class="normal"><? echo $name; ?></span></td></tr>
<tr><td><span class="normal"><? echo $text[email]; ?>:&nbsp; </span></td><td><span class="normal"><INPUT TYPE="TEXT" NAME="email" SIZE="20"></span></td></tr>
<tr><td><span class="normal"><? echo $text[producegedfrom]; ?>:&nbsp; </span></td><td><span class="normal"><SELECT NAME="type"><OPTION value="<? echo $text[ancestors]; ?>" selected><? echo $text[ancestors]; ?></option><OPTION value="<? echo $text[descendants]; ?>"><? echo $text[descendants]; ?></option></SELECT></span></td></tr>
<tr><td><span class="normal"><? echo $text[numgens]; ?>:&nbsp; </span></td><td><span class="normal"><input type="text" name="maxgcgen" value="6" size="3"></span></td></tr>
<? if( $allow_lds ) { ?>
<tr><td></td><td><span class="normal"><input type="checkbox" name="lds" value="yes"> <? echo $text[includelds]; ?></span></td></tr>
<? } ?>
<tr><td></td><td><span class="normal"><INPUT TYPE="submit" VALUE="<? echo $text[buildged]; ?>"></span></td></tr>
</table></form>

<?
	echo tng_menu( "gedcom", $personID, 2 );
	tng_footer( "" );
?>
