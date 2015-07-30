<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "sources";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");
include($cms[tngpath] . "functions.php");

$browsesources_url = getURL( "browsesources", 1 );
$showsource_url = getURL( "showsource", 1 );

function doSourceSearch( $instance, $pagenav ) {
	global $text, $photosearch;

	$browsesources_noargs_url = getURL( "browsesources", 0 );
	
	$str = "<span class=\"normal\">\n";
	$str .= getFORM( "browsesources", "GET", "SourceSearch$instance", "" );
	$str .= "<input type=\"text\" name=\"sourcesearch\" value=\"$sourcesearch\"> <input type=\"submit\" value=\"$text[search]\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	$str .= $pagenav;
	if( $docsearch )
		$str .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$browsesources_noargs_url\">$text[browseallsources]</a>";
	$str .= "</form></span>\n";
	
	return $str;
}

$max_browsesource_pages = 5;
if( $offset ) {
	$newoffset = "$offset, ";
	$offsetplus = $offset + 1;
}
else {
	$offsetplus = 1;
	$page = 1;
}

if( $tree ) {
	$wherestr = "WHERE $sources_table.gedcom = \"$tree\"";
	if( $sourcesearch ) $wherestr .= " AND title LIKE \"%$sourcesearch%\"";
}
else if( $sourcesearch ) 
	$wherestr = "WHERE title LIKE \"%$sourcesearch%\"";
else
	$wherestr = "";

$query = "SELECT sourceID, title, author, $sources_table.gedcom as gedcom, treename FROM $sources_table LEFT JOIN $trees_table on $sources_table.gedcom = $trees_table.gedcom $wherestr ORDER BY title LIMIT $newoffset" . ($maxsearchresults + 1);
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");

$numrows = mysql_num_rows( $result );

if( $numrows == $maxsearchresults + 1 || $offsetplus > 1 ) {
	$query = "SELECT count(sourceID) as scount FROM $sources_table LEFT JOIN $trees_table on $sources_table.gedcom = $trees_table.gedcom $wherestr";
	$result2 = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$row = mysql_fetch_assoc( $result2 );
	$totrows = $row[scount];
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

tng_header( $text[browseallsources], "" );
?>

<p class="header"><?php echo $text[browseallsources]; ?></span></p>

<?php 
echo tng_menu( "", "", 1 );
$query = "SELECT gedcom, treename FROM $trees_table ORDER BY treename";
$treeresult = mysql_query($query) or die ("$admtext[cannotexecutequery]: $query");
$numtrees = mysql_num_rows($treeresult);

if( $numtrees > 1 ) {
	$formstr = getFORM( "browsesources", "GET", "form1", "form1" );
	echo $formstr;

	echo $text[tree]; ?>: 
	<select name="tree">
		<option value="-x--all--x-" <?php if( !$tree ) echo "selected"; ?>><?php echo $text[alltrees]; ?></option>
<?php
	while( $row = mysql_fetch_assoc($treeresult) ) {
		echo "	<option value=\"$row[gedcom]\"";
		if( $tree && $row[gedcom] == $tree ) echo " selected";
		echo ">$row[treename]</option>\n";
	}
?>
	</select> <input type="submit" value="<?php echo $text[go]; ?>"><br>
</form>
<?php
}
if( $totrows )
	echo "<p>$text[matches] $offsetplus $text[to] $numrowsplus $text[of] $totrows</p>";

$pagenav = get_browseitems_nav( $totrows, $browsesources_url . "sourcesearch=$sourcesearch&offset", $maxsearchresults, $max_browsesource_pages );
if( $pagenav || $sourcesearch )
	echo doSourceSearch( 1, $pagenav );
?>
<table cellpadding="3" cellspacing="1" border="0">
	<tr>
		<td class="fieldnameback">&nbsp;</td>
		<td class="fieldnameback"><span class="fieldname"><nobr>&nbsp;<strong><?php echo $text[sourceid]; ?></strong>&nbsp;</nobr></span></td>
		<td class="fieldnameback"><span class="fieldname">&nbsp;<strong><?php echo $text[title]; ?></strong>&nbsp;</span></td>
		<td class="fieldnameback"><span class="fieldname"><nobr>&nbsp;<strong><?php echo $text[author]; ?></strong>&nbsp;</nobr></span></td>
		<?php if( $numtrees > 1 ) { ?><td class="fieldnameback"><span class="fieldname">&nbsp;<strong><?php echo $text[tree]; ?></strong>&nbsp;</span></td><?php } ?>
	</tr>
<?php
$i = 1;
while( $row = mysql_fetch_assoc( $result ) )
{
	echo "<tr><td valign=\"top\" class=\"databack\"><span class=\"normal\">$i</span></td>\n";
	echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\"><a href=\"$showsource_url" . "sourceID=$row[sourceID]&tree=$row[gedcom]\">$row[sourceID]</a>&nbsp;</span></td>";
	echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\"><a href=\"$showsource_url" . "sourceID=$row[sourceID]&tree=$row[gedcom]\">$row[title]</a>&nbsp;</span></td>";
	echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\">$row[author]&nbsp;</span></td>";
	if( $numtrees > 1 )
		echo "<td valign=\"top\" class=\"databack\" nowrap><span class=\"normal\">$row[treename]&nbsp;</span></td>";
	echo "</tr>\n";
	$i++;
}
mysql_free_result($result);
?>
</table>

<?php
if( $pagenav || $sourcesearch )
	echo doSourceSearch( 2, $pagenav );

echo tng_menu( "", "", 2 );
tng_footer( "" );
?>
