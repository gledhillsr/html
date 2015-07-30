<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "surnames";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");

$search_url = getURL( "search", 1 );
$surnames_all_url = getURL( "surnames-all", 1 );
$surnames_url = getURL( "surnames", 1 );

$query = "SELECT gedcom, treename FROM $trees_table ORDER BY treename";
$treeresult = mysql_query($query) or die ("$admtext[cannotexecutequery]: $query");
$numtrees = mysql_num_rows($treeresult);

tng_header( "$text[surnamelist] &#151; $text[top] $topnum", "" );
?>

<p class="header"><?echo "$text[surnamelist] &#151; $text[top] $topnum"; ?></p>
<? 
echo tng_menu( "", "", 1 );

$formstr = getFORM( "surnames100", "GET", "form1", "" );
echo $formstr;
?>

<p>
<?
if( $numtrees > 1 ) {
?>
<li><? echo $text[tree]; ?>: 
	<select name="tree">
		<option value="-x--all--x-" <? if( !$tree ) echo "selected"; ?>><? echo $text[alltrees]; ?></option>
<?
	while( $row = mysql_fetch_assoc($treeresult) ) {
		echo "	<option value=\"$row[gedcom]\"";
		if( $tree && $row[gedcom] == $tree ) echo " selected";
		echo ">$row[treename]</option>\n";
	}
?>
	</select> <input type="submit" value="<? echo $text[go]; ?>"><br><br>
<?
}
?>
<li><? echo $text[showtop]; ?> <input type="text" name="topnum" value="<? echo $topnum; ?>" size="4" maxlength="4"> <? echo $text[byoccurrence]; ?> <input type="submit" value="<? echo $text[go]; ?>"></li><br>
 <li><? echo "<a href=\"$surnames_all_url" . "tree=$tree\">$text[showallsurnames]</a> ($text[sortedalpha])"; ?></li><br>
 <li><? echo "<a href=\"$surnames_url" . "tree=$tree\">$text[mainsurnamepage]</a>"; ?></li>

</form>

<table border="0" cellspacing="0" cellpadding="0">
	<tr><td valign="top"><span class="normal">
<?
if( $tree )
	$wherestr = "WHERE gedcom = \"$tree\"";
else 
	$wherestr = "";
	
$topnum = $topnum ? $topnum : 100;
$query = "SELECT ucase(lastname) as lastname, ucase( binary(lastname) ) as binlast, count( ucase( lastname ) ) as lncount FROM $people_table $wherestr GROUP BY binlast ORDER by lncount DESC, binlast LIMIT $topnum";

$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
$topnum = mysql_num_rows($result);
if( $result ) {
	$counter = 1;
	$num_in_col = 20;
	$numcols = floor($topnum / $num_in_col);
	if( $numcols > 4 ) {
		$numcols = 4;
		$num_in_col = ceil($topnum / 4 );
	}
	
	$num_in_col_ctr = 0;
	$nosurname = urlencode($text[nosurname]);
	while( $surname = mysql_fetch_assoc( $result ) ) {
		$surname2 = urlencode( $surname[lastname] );
		$name = $surname[lastname] ? "<a href=\"$search_url" . "mylastname=$surname2&lnqualify=equals&mybool=AND&tree=$tree\">$surname[lastname]</a>" : "<a href=\"$search_url" . "mylastname=$nosurname&lnqualify=equals&mybool=AND&tree=$tree\">$text[nosurname]</a>";
		echo "$counter. $name ($surname[lncount])<br>\n";
		$counter++;
		$num_in_col_ctr++;
		if( $num_in_col_ctr == $num_in_col ) {
			echo "</span></td>\n<td>&nbsp;&nbsp;</td>\n<td valign=\"top\"><span class=\"normal\">";
			$num_in_col_ctr = 0;
		}
	}
	mysql_free_result($result);
}
?>
	</span></td></tr>
</table>

<?
echo tng_menu( "", "", 2 );
tng_footer( "" );
?>
