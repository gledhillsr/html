<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "reports";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");

$showreport_url = getURL( "showreport", 1 );

$query = "SELECT reportname, reportdesc, reportID FROM $reports_table WHERE active = 1 ORDER BY rank, reportname";
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");

$numrows = mysql_num_rows( $result );

tng_header( $text[reports], "" );
?>

<p class="header"><? echo $text[reports]; ?></p>

<?
echo tng_menu( "", "", 1 );
if ( !$numrows ) {
	echo $text[noreports];
}
else {
?>
<table cellpadding="3" cellspacing="1" border="0" width="100%">
	<tr>
		<td class="fieldnameback"><span class="fieldname">&nbsp;</span></td>
		<td class="fieldnameback" width="35%"><span class="fieldname"><nobr>&nbsp;<b><? echo $text[reportname]; ?></b>&nbsp;</nobr></span></td>
		<td class="fieldnameback" width="65%"><span class="fieldname"><nobr>&nbsp;<b><? echo $text[description]; ?></b>&nbsp;</nobr></span></td>
	</tr>

<?
$count = 1;
while( $row = mysql_fetch_assoc($result)) {
	echo "<tr><td class=\"databack\"><span class=\"normal\">$count.</span></td><td class=\"databack\"><span class=\"normal\">&nbsp;<a href=\"$showreport_url" . "reportID=$row[reportID]\">$row[reportname]</a>&nbsp;</span></td><td class=\"databack\"><span class=\"normal\">&nbsp;$row[reportdesc]&nbsp;</span></td></tr>\n";
	$count++;
}
mysql_free_result($result);
?>
</table>

<? 
}

echo tng_menu( "", "", 2 );
tng_footer( "" );
?>
