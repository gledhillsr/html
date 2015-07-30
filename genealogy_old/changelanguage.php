<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "language";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");

$query = "SELECT display, folder FROM $languages_table ORDER BY display";
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");

$numrows = mysql_num_rows( $result );

tng_header( $text[changelanguage], "" );
?>

<p class="header"><? echo $text[changelanguage]; ?></span></p>

<? 
echo tng_menu( "", "", 1 );
if( $numrows ) {
	$str .= getFORM( "savelanguage", "POST", "", "" );
	echo "$str";

	echo "$text[language]: \n";
?>
	<select name="newlanguage">
<?
	while( $row = mysql_fetch_assoc($result)) {
		echo "<option value=\"$row[folder]\"";
		if( $row[folder] == $mylanguage )
			echo " selected";
		echo ">$row[display]</option>\n";
	}
	mysql_free_result($result);
?>
	</select>
	<br><br>
	<input type="submit" value="<? echo $text[savechanges]; ?>">
</form>
<?
}
else
	echo "$text[language]: $mylanguage\n";

echo tng_menu( "", "", 2 );
tng_footer( "" );
?>
