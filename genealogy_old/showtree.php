<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "trees";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");
include($cms[tngpath] . "log.php" );

$showtree_url = getURL( "showtree", 1 );

function showFact( $text, $fact, $space ) {
	echo "<tr>\n";
	echo "<td valign=\"top\" class=\"fieldnameback\" nowrap><span class=\"fieldname\">&nbsp;" . $text . "&nbsp;</span></td>\n";
	echo "<td valign=\"top\" colspan=\"2\" class=\"databack\"><span class=\"normal\">$space$fact&nbsp;</span></td>\n";
	echo "</tr>\n";
}

$query = "SELECT count(personID) as pcount, $trees_table.gedcom, treename, description, owner, private, address, email, city, state, zip, country, phone FROM $trees_table LEFT JOIN $people_table on $trees_table.gedcom = $people_table.gedcom WHERE $trees_table.gedcom = \"$tree\" GROUP BY $trees_table.gedcom";
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
$row = mysql_fetch_assoc($result);
mysql_free_result($result);

writelog( "<a href=\"$showtree_url" . "tree=$tree\">$text[tree]: $row[treename]</a>" );

tng_header( "$text[tree]: $row[treename]", "" );
?>

<p class="header"><?php echo "$text[tree]: $row[treename]"?></p>
<br>
<?php
echo tng_menu( "", "", 1 );
?>	
	<table border="0" cellspacing="1" cellpadding="4">
<?php
	if( $row[treename] ) 	showFact( $text[treename], 		$row[treename], "&nbsp;" );
	if( $row[description] ) showFact( $text[description], 	$row[description], "&nbsp;" );

	echo "<tr><td colspan=\"2\"><font size=\"1\">&nbsp;</font></td></tr>\n";

	showFact( $text[individuals], $row[pcount], "&nbsp;" );

	$query = "SELECT count(familyID) as fcount FROM $families_table WHERE gedcom = \"$row[gedcom]\"";
	$famresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$famrow = mysql_fetch_assoc($famresult);
	mysql_free_result($famresult);
	showFact( $text[families], $famrow[fcount], "&nbsp;" );
	
	$query = "SELECT count(sourceID) as scount FROM $sources_table WHERE gedcom = \"$row[gedcom]\"";
	$srcresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$srcrow = mysql_fetch_assoc($srcresult);
	mysql_free_result($srcresult);
	showFact( $text[sources], $srcrow[scount], "&nbsp;" );

	echo "<tr><td colspan=\"2\"><font size=\"1\">&nbsp;</font></td></tr>\n";
	
	if( $row[owner] ) {
		if( !$row[private] && $row[email] )
			$row[owner] = "<a href=\"mailto:$row[email]\">$row[owner]</a>";
		showFact( $text[owner], 		$row[owner], "&nbsp;" );
	}
	if( !$row[private] ) {
		if( $row[address] ) 	showFact( $text[address], 		$row[address], "&nbsp;" );
		if( $row[city] ) 		showFact( $text[city], 			$row[city], "&nbsp;" );
		if( $row[state] ) 		showFact( $text[state], 		$row[state], "&nbsp;" );
		if( $row[zip] ) 		showFact( $text[zip], 			$row[zip], "&nbsp;" );
		if( $row[country] ) 	showFact( $text[country], 		$row[country], "&nbsp;" );
		if( !$row[owner] && $row[email] ) 		showFact( $text[email], 		"<a href=\"$row[email]\">$row[email]</a>", "&nbsp;" );
		if( $row[phone] ) 		showFact( $text[phone], 		$row[phone], "&nbsp;" );
	}
?>
	</table>
	<br>

<?php
	echo tng_menu( "", "", 2 );
	tng_footer( "" );
?>

