<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "trees";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");
include($cms[tngpath] . "functions.php");

$browsetrees_url = getURL( "browsetrees", 1 );
$showtree_url = getURL( "showtree", 1 );

function doTreeSearch( $instance, $pagenav ) {
	global $text, $photosearch;

	$browsetrees_noargs_url = getURL( "browsetrees", 0 );
	
	$str = "<span class=\"normal\">\n";
	$str .= getFORM( "browsetrees", "GET", "TreeSearch$instance", "" );
	$str .= "<input type=\"text\" name=\"treesearch\" value=\"$treesearch\"> <input type=\"submit\" value=\"$text[search]\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	$str .= $pagenav;
	if( $docsearch )
		$str .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$browsetrees_noargs_url\">$text[browsealltrees]</a>";
	$str .= "</form></span>\n";
	
	return $str;
}

$max_browsetree_pages = 5;
if( $offset ) {
	$newoffset = "$offset, ";
	$offsetplus = $offset + 1;
}
else {
	$offsetplus = 1;
	$page = 1;
}

if( $treesearch ) 
	$wherestr = "WHERE treename LIKE \"%$sourcesearch%\" || description LIKE \"$sourcesearch%\"";
else
	$wherestr = "";

$query = "SELECT count(personID) as pcount, $trees_table.gedcom, treename, description FROM $trees_table LEFT JOIN $people_table on $trees_table.gedcom = $people_table.gedcom GROUP BY $trees_table.gedcom ORDER BY treename LIMIT $newoffset" . ($maxsearchresults + 1);
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");

$numrows = mysql_num_rows( $result );

if( $numrows == $maxsearchresults + 1 || $offsetplus > 1 ) {
	$query = "SELECT count(gedcom) as treecount FROM $trees_table";
	$result2 = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$countrow = mysql_fetch_assoc( $result2 );
	$totrows = $countrow[treecount];
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

tng_header( $text[browsealltrees], "" );
?>

<p class="header"><?php echo $text[browsealltrees]; ?></span></p>

<?php 
echo tng_menu( "", "", 1 );
if( $totrows )
	echo "<p>$text[matches] $offsetplus $text[to] $numrowsplus $text[of] $totrows</p>";

$pagenav = get_browseitems_nav( $totrows, $browsetrees_url . "treesearch=$treesearch&offset", $maxsearchresults, $max_browsetree_pages );
if( $pagenav || $treesearch )
	echo doTreeSearch( 1, $pagenav );
?>
<table cellpadding="3" cellspacing="1" border="0">
	<tr>
		<td class="fieldnameback">&nbsp;</td>
		<td class="fieldnameback" nowrap><span class="fieldname">&nbsp;<strong><?php echo $text[treename]; ?></strong>&nbsp;</span></td>
		<td class="fieldnameback"><span class="fieldname"><nobr>&nbsp;<strong><?php echo $text[description]; ?></strong>&nbsp;</nobr></span></td>
		<td class="fieldnameback" nowrap><span class="fieldname">&nbsp;<strong><?php echo $text[individuals]; ?></strong>&nbsp;</span></td>
	</tr>
<?php
$i = 1;
while( $row = mysql_fetch_assoc( $result ) )
{
	echo "<tr><td valign=\"top\" class=\"databack\"><span class=\"normal\">$i</span></td>\n";
	echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\"><a href=\"$showtree_url" . "tree=$row[gedcom]\">$row[treename]</a>&nbsp;</span></td>";
	echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\">$row[description]&nbsp;</span></td>";
	echo "<td valign=\"top\" class=\"databack\" align=\"right\"><span class=\"normal\">$row[pcount]&nbsp;</span></td>";
	echo "</tr>\n";
	$i++;
}
mysql_free_result($result);
?>
</table>

<?php
if( $pagenav || $treesearch )
	echo doTreeSearch( 2, $pagenav );

echo tng_menu( "", "", 2 );
tng_footer( "" );
?>
