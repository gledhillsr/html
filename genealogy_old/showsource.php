<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "sources";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");
include($cms[tngpath] . "log.php" );

$showsource_url = getURL( "showsource", 1 );

function showEvent( $data ) {
	$dateplace = $data[date] || $data[place] ? 1 : 0;
	$rows = $dateplace;
	if( $data[fact] ) $rows++;
	$output = "";
	
	$preoutput = "<tr>\n";
	$preoutput .= "<td valign=\"top\" class=\"fieldnameback\" nowrap rowspan=\"$rows\"><span class=\"fieldname\">$data[text]&nbsp;</span></td>\n";
	if( $dateplace ) {
		$output .= "<td valign=\"top\" class=\"databack\"><span class=\"normal\"><nobr>$data[date]</nobr>&nbsp;</span></td>\n";
		$output .= "<td valign=\"top\" width=\"80%\" class=\"databack\"><span class=\"normal\">$data[place]";
		$output .= "&nbsp;</span></td>\n";
		$output .= "</tr>\n";
	}
	if( $data[fact] ) {
		if( $output ) $output .= "<tr>\n";
		$output .= "<td valign=\"top\" colspan=\"2\" class=\"databack\"><span class=\"normal\">" . nl2br( $data[fact] );
		$output .= "&nbsp;</span></td>\n";
		$output .= "</tr>\n";
	}
	if( $output )
		echo $preoutput . $output;
}

function checkXnote( $fact ) {
	global $xnotes_table;
	
	preg_match( "/^@(\S+)@/", $fact, $matches );
	if( $matches[1] ) {
		$query = "SELECT note from $xnotes_table WHERE noteID = \"$matches[1]\"";
		$xnoteres = @mysql_query( $query );
		if( $xnoteres ) {
			$xnote = mysql_fetch_assoc( $xnoteres );
			$fact = nl2br($xnote[note]);
		}
	}
	return $fact;
}

$query = "SELECT * FROM $sources_table WHERE $sources_table.sourceID = \"$sourceID\" AND $sources_table.gedcom = \"$tree\"";
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
$srcrow = mysql_fetch_assoc($result);
mysql_free_result($result);

writelog( "<a href=\"$showsource_url" . "sourceID=$sourceID&tree=$tree\">$text[source] $srcrow[title] ($sourceID)</a>" );

tng_header( "$srcrow[title] ($srcrow[sourceID])", "" );
?>

<p class="header"><?php echo "$srcrow[title] ($srcrow[sourceID])"?></p>
<br>
<?php
echo tng_menu( "", "", 1 );
?>	
	<table border="0" cellspacing="1" cellpadding="4">
<?php
	if( $srcrow[title] )
		showEvent( array( "text"=>$text[title], "fact"=>$srcrow[title] ) );
	if( $srcrow[shorttitle] )
		showEvent( array( "text"=>$text[shorttitle], "fact"=>$srcrow[shorttitle] ) );
	//if( $srcrow[type] ) showEvent( array( "text"=>$text[type], "fact"=>$srcrow[type] ) );
	if( $srcrow[author] )
		showEvent( array( "text"=>$text[author], "fact"=>$srcrow[author] ) );
	if( $srcrow[publisher] )
		showEvent( array( "text"=>$text[publisher], "fact"=>$srcrow[publisher] ) );
	if( $srcrow[callnum] )
		showEvent( array( "text"=>$text[callnum], "fact"=>$srcrow[callnum] ) );
	if( $srcrow[other] )
		showEvent( array( "text"=>$text[other], "fact"=>$srcrow[other] ) );

	//do custom events
	$query = "SELECT display, eventdate, eventplace, info, tag, description, eventID FROM $events_table, $eventtypes_table WHERE persfamID = \"$sourceID\" AND $events_table.eventtypeID = $eventtypes_table.eventtypeID AND gedcom = \"$tree\" AND keep = \"1\" ORDER BY ordernum, tag, description";
	$custevents = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	while ( $custevent = mysql_fetch_assoc( $custevents ) )	{
		$fact = $custevent[info] ? checkXnote( $custevent[info] ) : "";
		showEvent( array( "text"=>$custevent[display], "date"=>$custevent[eventdate], "place"=>$custevent[eventplace], "fact"=>$fact ) );
	}

	if( $srcrow[comments] )
		showEvent( array( "text"=>$text[notes], "fact"=>$srcrow[comments] ) );
	if( $srcrow[actualtext] )
		showEvent( array( "text"=>$text[text], "fact"=>$srcrow[actualtext] ) );
?>
	</table>
	<br>

<?php
echo tng_menu( "", "", 2 );
tng_footer( "" );
?>

