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
$surnames_noargs_url = getURL( "surnames", 0 );

$query = "SELECT gedcom, treename FROM $trees_table ORDER BY treename";
$treeresult = mysql_query($query) or die ("$admtext[cannotexecutequery]: $query");
$numtrees = mysql_num_rows($treeresult);

tng_header( "$text[surnamelist]; &#151; $text[allsurnames]", "" );
?>

<p class="header"><? echo $text[surnamelist]; ?></p>
<? 
echo tng_menu( "", "", 1 );
if( $numtrees > 1 ) {

	$formstr = getFORM( "surnames-all", "GET", "form1", "form1" );
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
$formstr = getFORM( "surnames100", "GET", "", "" );
echo $formstr;
?>

<p>
<li><? echo $text[showtop]; ?> <input type="text" name="topnum" value="100" size="4" maxlength="4"> <? echo $text[byoccurrence]; ?>  <input type="hidden" name="tree" value="<? echo $tree; ?>"><input type="submit" value="<? echo $text[go]; ?>"><br>
<li><? echo "<a href=\"$surnames_noargs_url\">$text[mainsurnamepage]</a>"; ?></li></p>
</form>
<?
if( $tree ) {
	$wherestr = "WHERE gedcom = \"$tree\"";
	$wherestr2 = "AND gedcom = \"$tree\"";
}
else {
	$wherestr = "";
	$wherestr2 = "";
}
	
$linkstr = "";
$nosurname = urlencode($text[nosurname]);
$query = "SELECT ucase(left(lastname,1)) as firstchar, ucase( binary(left(lastname,1) ) ) as binfirstchar FROM $people_table $wherestr GROUP BY binfirstchar ORDER by binfirstchar";
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
if( $result ) {
	$initialchar = 1;
	
	while( $surname = mysql_fetch_assoc( $result ) ) {
		if( $initialchar != 1 ) { 
			$linkstr .= " | ";
		}
		if( $surname[firstchar] == "" ) {
			$surname[firstchar] = $text[nosurname];
			$linkstr .= "<a href=\"$search_url" . "mylastname=$nosurname&lnqualify=equals&mybool=AND&tree=$tree\">$text[nosurname]</a> | ";
		}
		else {
			$linkstr .= "<a href=\"#char$initialchar\">$surname[firstchar]</a>";
			$firstchars[$initialchar] = $surname[firstchar];
			$initialchar++;
		}
	}
	mysql_free_result($result);
}
?>
<p><li><? echo $text[surnamesstarting]; ?>:<br>
<?
	echo $linkstr;
?>
</li></p>
<p><b><? echo $text[showmatchingsurnames]; ?></b></p>

<?
for( $scount = 1; $scount < $initialchar; $scount++ ) {
	echo "<A name=\"char$scount\">\n";
	$urlfirstchar = addslashes($firstchars[$scount]);
?>
<span class="header"><? echo $firstchars[$scount]; ?></span>
<table border="0" cellspacing="0" cellpadding="0">
	<tr><td valign="top"><span class="normal">
<?
$query = "SELECT ucase(lastname) as lastname, ucase( binary(lastname) ) as binlast, count( ucase( lastname ) ) as lncount FROM $people_table WHERE ucase(binary(lastname)) LIKE \"$urlfirstchar%\" $wherestr2 GROUP BY binlast ORDER by binlast";
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
		$name = $surname[lastname] ? "<a href=\"$search_url" . "mylastname=$surname2&lnqualify=equals&mybool=AND&tree=$tree\">$surname[lastname]</a>" : "<a href=\"search.php?mylastname=$nosurname&lnqualify=equals&mybool=AND&tree=$tree\">$text[nosurname]</a>";
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
</table><br><p><a href="#top"><? echo $text[backtotop]; ?></a></p><br>
<?
}
echo tng_menu( "", "", 2 );
tng_footer( "" );
?>

