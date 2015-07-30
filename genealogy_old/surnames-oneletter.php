<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "surnames";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
@set_time_limit(0);
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");

$search_url = getURL( "search", 1 );
$surnames_url = getURL( "surnames", 1 );

$query = "SELECT gedcom, treename FROM $trees_table ORDER BY treename";
$treeresult = mysql_query($query) or die ("$admtext[cannotexecutequery]: $query");
$numtrees = mysql_num_rows($treeresult);

$decodedfirstchar = stripslashes(urldecode($firstchar));

tng_header( "$text[surnamelist]: $text[beginswith] $decodedfirstchar", "" );
?>

<p class="header"><? echo "$text[surnamelist]: $text[beginswith] $decodedfirstchar"; ?></p>
<? 
echo tng_menu( "", "", 1 );
if( $numtrees > 1 ) {
	$formstr = getFORM( "surnames-oneletter", "GET", "form1", "form1" );
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
	</select> <input type="hidden" name="firstchar" value="<? echo $firstchar; ?>"><input type="submit" value="<? echo $text[go]; ?>"><br>
</form>
<?
}
$formstr = getFORM( "surnames100", "GET", "", "" );
echo $formstr;
?>

<p>
<li><? echo $text[showtop]; ?> <input type="text" name="topnum" value="100" size="4" maxlength="4"> <? echo $text[byoccurrence]; ?> <input type="hidden" name="tree" value="<? echo $tree; ?>"><input type="submit" value="<? echo $text[go]; ?>"><br>
<li><? echo "<a href=\"$surnames_url" . "tree=$tree\">$text[mainsurnamepage]</a>"; ?></li></p>
</form>
<p><? echo "$text[allbeginningwith] $decodedfirstchar, $text[sortedalpha] ($text[numoccurrences]):"; ?><br>
</p>
<p><b><? echo $text[showmatchingsurnames]; ?></b></p>

<table border="0" cellspacing="0" cellpadding="0">
	<tr><td valign="top"><span class="normal">
<?
if( $tree )
	$wherestr = "AND gedcom = \"$tree\"";
else 
	$wherestr = "";
	
$query = "SELECT ucase(lastname) as lastname, ucase( binary(lastname) ) as binlast, count( ucase( lastname ) ) as lncount FROM $people_table WHERE ucase(binary(lastname)) LIKE \"$firstchar%\" $wherestr GROUP BY binlast ORDER by binlast";
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
if( $result ) {
	$snnum = 1;
	$num_in_col = 20;
	$numrows = mysql_num_rows($result);
	$numcols = floor($numrows / $num_in_col);
	if( $numcols > 4 ) {
		$numcols = 4;
		$num_in_col = ceil($numrows / 4 );
	}
	
	$num_in_col_ctr = 0;
	while( $surname = mysql_fetch_assoc( $result ) ) {
		$surname2 = urlencode( $surname[lastname] );
		$name = $surname[lastname] ? "<a href=\"$search_url" . "mylastname=$surname2&lnqualify=equals&mybool=AND&tree=$tree\">$surname[lastname]</a>" : "$text[nosurname]";
		echo "$snnum. $name ($surname[lncount])<br>\n";
		$snnum++;
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

