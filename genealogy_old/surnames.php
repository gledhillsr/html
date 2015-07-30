<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "surnames";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");

$search_url = getURL( "search", 1 );
$surnames_oneletter_url = getURL( "surnames-oneletter", 1 );
$surnames_all_url = getURL( "surnames-all", 1 );

$query = "SELECT gedcom, treename FROM $trees_table ORDER BY treename";
$treeresult = mysql_query($query) or die ("$admtext[cannotexecutequery]: $query");
$numtrees = mysql_num_rows($treeresult);

tng_header( $text[surnamelist], "" );
?>

<p class="header"><? echo $text[surnamelist]; ?></p>
<?
echo tng_menu( "", "", 1 );
if( $numtrees > 1 ) {
	$formstr = getFORM( "surnames", "GET", "form1", "form1" );
        echo $formstr;
	
	echo $text[tree]; ?>:
	<select name="tree">
		<option value="-x--all--x-" <? if( !$tree ) echo "selected"; ?>><? echo $text[alltrees]; ?></option>
<?
	while( $row = mysql_fetch_assoc($treeresult) ) {
		echo "  <option value=\"$row[gedcom]\"";
		if( $tree && $row[gedcom] == $tree ) echo " selected";
		echo ">$row[treename]</option>\n";
	}
?>
	</select> <input type="submit" value="<? echo $text[go]; ?>"><br>
	</form>
<?
}

$linkstr = "";
$linkstr2col1 = "";
$linkstr2col2 = "";
$linkstr3col1 = "";
$linkstr3col2 = "";
$nosurname = urlencode($text[nosurname]);

if( $tree )
	$wherestr = "WHERE gedcom = \"$tree\"";
else 
	$wherestr = "";
	
$query = "SELECT ucase(left(lastname,1)) as firstchar, ucase( binary(left(lastname,1) ) ) as binfirstchar FROM $people_table $wherestr GROUP BY binfirstchar ORDER by binfirstchar";
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
if( $result ) {
	$initialchar = 1;
	
	while( $surname = mysql_fetch_assoc( $result ) ) {
		if( $initialchar != 1 ) { 
			$linkstr .= " | ";
		}
		if( $surname[firstchar] == "" )
			$linkstr .= "<a href=\"$search_url" . "mylastname=$nosurname&lnqualify=equals&mybool=AND\">$text[nosurname]</a>";
		else {
			$urlfirstchar = urlencode($surname[firstchar]);
			$linkstr .= "<a href=\"$surnames_oneletter_url" . "firstchar=$urlfirstchar&tree=$tree\">$surname[firstchar]</a>";
		}
		$initialchar++;
	}
	mysql_free_result($result);
}

$query = "SELECT ucase(left(lastname,1)) as firstchar, ucase( binary(left(lastname,1) ) ) as binfirstchar, count( ucase( left( lastname,1) ) ) as lncount FROM $people_table $wherestr GROUP BY binfirstchar ORDER by lncount DESC";
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
if( $result ) {
	$namectr = 0;
	while( $surname = mysql_fetch_assoc( $result ) ) {
		if( $namectr <13 ) {
			if( $surname[firstchar] == "" )
				$linkstr2col1 .= "<a href=\"$search_url" . "mylastname=$nosurname&lnqualify=equals&mybool=AND&tree=$tree\">$text[nosurname]</a> ($surname[lncount])<br>\n";
			else {
				$urlfirstchar = urlencode($surname[firstchar]);
				$linkstr2col1 .= "<a href=\"$surnames_oneletter_url" . "firstchar=$urlfirstchar&tree=$tree\">$surname[firstchar]</a> ($surname[lncount])<br>\n";
			}
		}
		else {
			if( $surname[firstchar] == "" )
				$linkstr2col2 .= "<a href=\"$search_url" . "mylastname=$nosurname&lnqualify=equals&mybool=AND&tree=$tree\">$text[nosurname]</a> ($surname[lncount])<br>\n";
			else {
				$urlfirstchar = urlencode($surname[firstchar]);
				$linkstr2col2 .= "<a href=\"$surnames_oneletter_url" . "firstchar=$urlfirstchar&tree=$tree\">$surname[firstchar]</a> ($surname[lncount])<br>\n";
			}
		}
		$namectr++;
	}
	mysql_free_result($result);
}

$query = "SELECT ucase(lastname) as lastname, ucase( binary(lastname) ) as binlast, count( ucase( lastname ) ) as lncount FROM $people_table $wherestr GROUP BY binlast ORDER by lncount DESC LIMIT 25";
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
if( $result ) {
	$count = 1;
	while( $surname = mysql_fetch_assoc( $result ) ) {
		$surname2 = urlencode($surname[lastname]);
		if( $count <=13 ) {
			if( $surname[lastname] == "" )
				$linkstr3col1 .= "$count. <a href=\"$search_url" . "mylastname=$nosurname&lnqualify=equals&mybool=AND&tree=$tree\">$text[nosurname]</a> ($surname[lncount])<br>\n";
			else
				$linkstr3col1 .= "$count. <a href=\"$search_url" . "mylastname=$surname2&lnqualify=equals&mybool=AND&tree=$tree\">$surname[lastname]</a> ($surname[lncount])<br>";
		}
		else {
			if( $surname[lastname] == "" )
				$linkstr3col2 .= "$count. <a href=\"$search_url" . "mylastname=$nosurname&lnqualify=equals&mybool=AND&tree=$tree\">$text[nosurname]</a> ($surname[lncount])<br>\n";
			else
				$linkstr3col2 .= "$count. <a href=\"$search_url" . "mylastname=$surname2&lnqualify=equals&mybool=AND&tree=$tree\">$surname[lastname]</a> ($surname[lncount])<br>";
		}
		$count++;
	}
	mysql_free_result($result);
}
?>

<p><li><? echo $text[surnamesstarting]; ?>:<br>
<?
	echo $linkstr;
?></li>
</p>

<?
$formstr = getFORM( "surnames100", "POST", "", "" );
echo $formstr;
?>

<p><li><? echo $text[showtop]; ?> <input type="text" name="topnum" value="100" size="4" maxlength="4"> <? echo $text[byoccurrence]; ?>  <input type="hidden" name="tree" value="<? echo $tree; ?>"><input type="submit" value="<? echo $text[go]; ?>"></li><br>
<li><? echo "<a href=\"$surnames_all_url" . "tree=$tree\">$text[showallsurnames]</a> ($text[sortedalpha])</li>\n"; ?>

</form>

<table>
	<tr>
		<td valign="bottom" colspan="2"><span class="normal">
<li><? echo "$text[firstchars]<br>$text[byoccurrence] ($text[totalnames]):"; ?>&nbsp;&nbsp;&nbsp;&nbsp;
		</td>
		<td>&nbsp;&nbsp;</td>
		<td valign="bottom" colspan="2"><span class="normal">
<li><? echo "$text[top25] ($text[totalnames]):"; ?>
		</td>
	</tr>
	<tr>
		<td valign="top"><span class="normal">
<?
	echo $linkstr2col1;
?>
</span>
		</td>
		<td valign="top"><span class="normal">
<?
	echo $linkstr2col2;
?>
</span>
		</td>
		<td>&nbsp;&nbsp;</td>
		<td valign="top"><span class="normal">
<?
	echo $linkstr3col1;
?>
</span>
		</td>
		<td valign="top"><span class="normal">
<?
	echo $linkstr3col2;
?>
</span>
		</td>
	</tr>
</table>

<?
echo tng_menu( "", "", 2 );
tng_footer( "" );
?>
