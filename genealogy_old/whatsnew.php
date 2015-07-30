<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "whatsnew";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");

$getperson_url = getURL( "getperson", 1 );
$showphoto_url = getURL( "showphoto", 1 );
$showheadstone_url = getURL( "showheadstone", 1);

$query = "SELECT * FROM $cemeteries_table ORDER BY country, state, county, city, cemname";
$cemresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");

$query = "SELECT gedcom, treename FROM $trees_table ORDER BY treename";
$treeresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
$numtrees = mysql_num_rows($treeresult);

$text[pastxdays] = ereg_replace( "xx", "$change_cutoff", $text[pastxdays] );

tng_header( "$text[whatsnew] $text[pastxdays]", "" );
?>

<p class="header"><? echo "$text[whatsnew] $text[pastxdays]"; ?></p>

<?
echo tng_menu( "", "", 1 );
if( $numtrees > 1 ) {
	$formstr = getFORM( "whatsnew", "GET", "form1", "form1" );
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
	</select> <input type="submit" value="<? echo $text[go]; ?>">
</form>
<?
}

	if( $tree )
		$wherestr = "AND (gedcom = \"$tree\" || gedcom = \"\")";
	else 
		$wherestr = "";
	
	if( !$change_cutoff ) $change_cutoff = 0;
	if( !$change_limit ) $change_limit = 10;
		
//select from photos where date later than cutoff, order by changedate descending, limit = 10
	$query = "SELECT photoID, description, notes, thumbpath, gedcom, DATE_FORMAT(changedate,'%d %b %Y') as changedatef FROM $photos_table WHERE TO_DAYS(NOW()) - TO_DAYS(changedate) <= $change_cutoff $wherestr ORDER BY changedate DESC, description LIMIT $change_limit";
	$photoresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$numrows = mysql_num_rows( $photoresult );
	if( $numrows ) {
?>
<span class="subhead"><b><? echo $text[photos]; ?></b></span><br>
<table cellpadding="3" cellspacing="1" border="0" width="100%">
	<tr>
		<td class="fieldnameback"><span class="fieldname">&nbsp;<strong><? echo $text[thumb]; ?></strong>&nbsp;</span></td>
		<td class="fieldnameback"><span class="fieldname">&nbsp;<strong><? echo $text[description]; ?></strong>&nbsp;</span></td><td class="fieldnameback"><span class="fieldname">&nbsp;<strong><? echo $text[notes]; ?></strong>&nbsp;</span></td>
		<? if( $numtrees > 1 ) { ?><td class="fieldnameback"><span class="fieldname">&nbsp;<b><? echo $text[tree]; ?></b>&nbsp;</span></td><? } ?>
		<td class="fieldnameback" nowrap><span class="fieldname">&nbsp;<b><? echo $text[lastmodified]; ?></b>&nbsp;</span></td>
	</tr>
<?
		while( $row = mysql_fetch_assoc( $photoresult ) )
		{
			$query = "SELECT living, $people_table.gedcom as gedcom FROM $photolinks_table, $people_table WHERE $photolinks_table.personID = $people_table.personID AND $photolinks_table.gedcom = $people_table.gedcom AND $photolinks_table.photoID = '$row[photoID]'";
			$presult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
			$noneliving = 1;
			while( $prow = mysql_fetch_assoc( $presult ) )
			{
				if( $prow[living] && ( !$allow_living_db || ($assignedtree && $assignedtree != $prow[gedcom]) ) )
					$noneliving = 0;
			}
			mysql_free_result( $presult );

			if( $noneliving || !$nonames ) {
				$description = $row[description];
				$notes = $row[notes];
			}
			else
				$description = $notes = $text[living];
		
			if( $noneliving && $row[thumbpath] && file_exists("$photopath/$row[thumbpath]")) {
				$size = @GetImageSize( "$rootpath$photopath/$row[thumbpath]" );
				$imgsrc = "<a href=\"$showphoto_url" . "photoID=$row[photoID]\"><img src=\"$photopath/$row[thumbpath]\" border=\"0\" $size[3]></a>";
			}
			else
				$imgsrc = "&nbsp;";
			echo "<tr><td  class=\"databack\"><span class=\"normal\">$imgsrc &nbsp;</span></td>";
			echo "<td class=\"databack\"><span class=\"normal\"><a href=\"$showphoto_url" . "photoID=$row[photoID]\">$description</a>&nbsp;</span></td>";
			echo "<td class=\"databack\"><span class=\"normal\">$notes&nbsp;</span></td>";
			if( $numtrees > 1 ) {
				echo "<td class=\"databack\"><span class=\"normal\">&nbsp;$row[gedcom]&nbsp;</span></td>";
			}
			echo "<td class=\"databack\" nowrap><span class=\"normal\">$row[changedatef]&nbsp;</span></td></tr>\n";
		}
		mysql_free_result($photoresult);
?>
</table>
<br><br>
<?
	}

//select from histories where date later than cutoff, order by changedate descending, limit = 10
	$query = "SELECT docID, description, notes, path, DATE_FORMAT(changedate,'%d %b %Y') as changedatef FROM $histories_table WHERE TO_DAYS(NOW()) - TO_DAYS(changedate) <= $change_cutoff ORDER BY changedate DESC, description LIMIT $change_limit";
	$docresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$numrows = mysql_num_rows( $docresult );
	if( $numrows ) {
?>
<span class="subhead"><b><? echo $text[historiesdocs]; ?></b></span><br>
<table cellpadding="3" cellspacing="1" border="0" width="100%">
	<tr>
		<td class="fieldnameback"><span class="fieldname">&nbsp;<strong><? echo $text[description]; ?></strong>&nbsp;</span></td><td class="fieldnameback"><span class="fieldname">&nbsp;<strong><? echo $text[notes]; ?></strong>&nbsp;</span></td>
		<td class="fieldnameback" nowrap><span class="fieldname">&nbsp;<b><? echo $text[lastmodified]; ?></b>&nbsp;</span></td>
	</tr>
<?
		while( $row = mysql_fetch_assoc( $docresult ) )
		{
			$query = "SELECT living, $people_table.gedcom as gedcom FROM $doclinks_table, $people_table WHERE $doclinks_table.personID = $people_table.personID AND $doclinks_table.gedcom = $people_table.gedcom AND $doclinks_table.docID = '$row[docID]'";
			$presult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
			$noneliving = 1;
			while( $prow = mysql_fetch_assoc( $presult ) )
			{
				if( $prow[living] && ( !$allow_living_db || ($assignedtree && $assignedtree != $prow[gedcom]) ) )
					$noneliving = 0;
			}
			mysql_free_result( $presult );
			
			if( $row[path] && $noneliving ) {
				$description = "<a href=\"$historypath/$row[path]\">$row[description]</a>";
				$notes = $row[notes];
			}
			elseif( $noneliving || !$nonames ) {
				$description = $row[description];
				$notes = $row[notes];
			}
			else
				$description = $notes = $text[living];
			echo "<tr><td valign=\"top\" class=\"databack\"><span class=\"normal\">$description&nbsp;</span></td>\n";
			echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\">$notes&nbsp;</span></td>";
			echo "<td class=\"databack\" nowrap><span class=\"normal\">$row[changedatef]&nbsp;</span></td></tr>\n";
		}
		mysql_free_result($docresult);
?>
</table>
<br><br>
<?
	}

	if( $tree )
		$wherestr = "AND gedcom = \"$tree\"";
	else 
		$wherestr = "";
	
//select from people where date later than cutoff, order by changedate descending, limit = 10
	$query = "SELECT $people_table.personID, lastname, firstname, birthdate, living, DATE_FORMAT(changedate,'%d %b %Y') as changedatef, LPAD(SUBSTRING_INDEX(birthdate, ' ', -1),4,'0') as birthyear, birthplace, altbirthdate, LPAD(SUBSTRING_INDEX(altbirthdate, ' ', -1),4,'0') as altbirthyear, altbirthplace, $people_table.gedcom as gedcom FROM $people_table WHERE TO_DAYS(NOW()) - TO_DAYS(changedate) <= $change_cutoff $wherestr ORDER BY changedate DESC, lastname, firstname, birthyear, altbirthyear LIMIT $change_limit";
	$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$numrows = mysql_num_rows( $result );
	if( $numrows ) {
?>
<span class="subhead"><b><? echo $text[individuals]; ?></b></span><br>
<table cellpadding="3" cellspacing="1" border="0" width="100%">
	<tr>
		<td class="fieldnameback"><span class="fieldname">&nbsp;<b><? echo $text[id]; ?></b>&nbsp;</span></td>
		<td class="fieldnameback"><span class="fieldname">&nbsp;<b><? echo $text[lastfirst]; ?></b>&nbsp;</span></td>
		<td class="fieldnameback" colspan="2"><span class="fieldname">&nbsp;<b><? echo $text[bornchr]; ?></b>&nbsp;</span></td>
		<? if( $numtrees > 1 ) { ?><td class="fieldnameback"><span class="fieldname">&nbsp;<b><? echo $text[tree]; ?></b>&nbsp;</span></td><? } ?>
		<td class="fieldnameback" nowrap><span class="fieldname">&nbsp;<b><? echo $text[lastmodified]; ?></b>&nbsp;</span></td>
	</tr>
	
<?
		while( $row = mysql_fetch_assoc($result))
		{
			if( !$row[living] || ( $allow_living_db && (!$assignedtree || $assignedtree == $row[gedcom]) ) ) {
				if ( $row[birthdate] ) {
					$birthdate = "$text[birthabbr] $row[birthdate]";
					$birthplace = $row[birthplace];
				}
				else if ( $row[altbirthdate] ) {
					$birthdate = "$text[chrabbr] $row[altbirthdate]";
					$birthplace = $row[altbirthplace];
				}
				else {
					$birthdate = "";
					$birthplace = "";
				}
				$living = 0;
			}
			else {
				$birthdate = $birthplace = "";
				$living = 1;
			}
			$namestr = !$living || !$nonames ? "$row[lastname], $row[firstname]" : $text[living];
			echo "<tr><td class=\"databack\"><span class=\"normal\"><a href=\"$getperson_url" . "personID=$row[personID]&tree=$row[gedcom]\">$row[personID]</a>&nbsp;</span></td>";
			echo "<td class=\"databack\"><span class=\"normal\"><a href=\"$getperson_url" . "personID=$row[personID]&tree=$row[gedcom]\">$namestr</a>&nbsp;</span></td>\n";
			echo "<td class=\"databack\" nowrap><span class=\"normal\">$birthdate&nbsp;</span></td><td class=\"databack\"><span class=\"normal\">&nbsp;$birthplace&nbsp;</span></td>";
			if( $numtrees > 1 ) {
				echo "<td class=\"databack\"><span class=\"normal\">$row[gedcom]&nbsp;</span></td>";
			}
			echo "<td class=\"databack\" nowrap><span class=\"normal\">$row[changedatef]&nbsp;</span></td></tr>\n";
		}
		mysql_free_result($result);
?>
</table>
<br><br>
<?
	}

//select husband, wife from families where date later than cutoff, order by changedate descending, limit = 10
	if( $tree )
		$allwhere = "AND $people_table.gedcom = \"$tree\" AND $families_table.gedcom = \"$tree\"";
	else
		$allwhere = " AND $people_table.gedcom = $families_table.gedcom";

	$query = "SELECT familyID, husband, wife, marrdate, $families_table.gedcom as gedcom, firstname, lastname, $families_table.living as fliving, $people_table.living as living, $families_table.gedcom as gedcom, DATE_FORMAT($families_table.changedate,'%d %b %Y') as changedatef FROM $families_table, $people_table WHERE TO_DAYS(NOW()) - TO_DAYS($families_table.changedate) <= $change_cutoff AND $people_table.personID = husband $allwhere ORDER BY $families_table.changedate DESC, lastname LIMIT $change_limit";
	$famresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$numrows = mysql_num_rows( $famresult );
	if( $numrows ) {
?>
<span class="subhead"><b><? echo $text[families]; ?></b></span><br>
<table cellpadding="3" cellspacing="1" border="0" width="100%">
	<tr>
		<td class="fieldnameback"><span class="fieldname"><nobr>&nbsp;<b><? echo $text[id]; ?></b>&nbsp;</nobr></span></td>
		<td class="fieldnameback"><span class="fieldname"><nobr>&nbsp;<b><? echo $text[husbid]; ?></b>&nbsp;</nobr></span></td>
		<td class="fieldnameback"><span class="fieldname"><nobr>&nbsp;<b><? echo $text[husbname]; ?></b>&nbsp;</nobr></span></td>
		<td class="fieldnameback"><span class="fieldname"><nobr>&nbsp;<b><? echo $text[wifeid]; ?></b>&nbsp;</nobr></span></td>
		<td class="fieldnameback"><span class="fieldname"><nobr>&nbsp;<b><? echo $text[married]; ?></b>&nbsp;</nobr></span></td>
		<? if( $numtrees > 1 ) { ?><td class="fieldnameback"><span class="fieldname">&nbsp;<b><? echo $text[tree]; ?></b>&nbsp;</span></td><? } ?>
		<td class="fieldnameback" nowrap><span class="fieldname">&nbsp;<b><? echo $text[lastmodified]; ?></b>&nbsp;</span></td>
	</tr>
	
<?
		while( $row = mysql_fetch_assoc($famresult))
		{
			$query = "SELECT living FROM $people_table WHERE $people_table.personID = \"$row[wife]\"";
			$wiferesult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
			$wiferow = mysql_fetch_assoc($wiferesult);
			$living = !$row[living] || ( $allow_living_db && (!$assignedtree || $assignedtree == $row[gedcom]) ) ? 0 : 1;
			//look up wife
			echo "<tr><td class=\"databack\" valign=\"top\"><span class=\"normal\">&nbsp;$row[familyID]&nbsp;</span></td><td class=\"databack\" valign=\"top\"><span class=\"normal\">&nbsp;<a href=\"$getperson_url" . "personID=$row[husband]&tree=$row[gedcom]\">$row[husband]</a>&nbsp;</span></td>\n";
			echo "<td class=\"databack\" valign=\"top\"><span class=\"normal\"><a href=\"$getperson_url" . "personID=$row[husband]&tree=$row[gedcom]\">";
			if( !$living || !$nonames )
				echo "$row[lastname], $row[firstname]";
			else
				echo $text[living];
			echo "</a>&nbsp;</span></td>\n";
			echo "<td class=\"databack\" valign=\"top\"><span class=\"normal\"><a href=\"$getperson_url" . "personID=$row[wife]&tree=$row[gedcom]\">$row[wife]</a>&nbsp;</span></td>\n";
			echo "<td class=\"databack\" valign=\"top\"><span class=\"normal\">";
			if( !$row[fliving] || ( $allow_living_db && (!$assignedtree || $assignedtree == $row[gedcom]) ) )
				echo $row[marrdate];
			echo "&nbsp;</span></td>\n";
			if( $numtrees > 1 ) {
				echo "<td class=\"databack\"><span class=\"normal\">$row[gedcom]&nbsp;</span></td>";
			}
			echo "<td class=\"databack\" nowrap><span class=\"normal\">$row[changedatef]&nbsp;</span></td></tr>\n";
		}
		mysql_free_result($famresult);
?>
</table>
<br><br>
<?
	}

	if( $tree )
		$wherestr = "AND $headstones_table.gedcom = \"$tree\"";

//select from headstones where date later than cutoff, order by changedate descending, limit = 10
	$query = "SELECT $headstones_table.personID as personID, DATE_FORMAT($headstones_table.changedate,'%d %b %Y') as changedatef, showmap, $headstones_table.notes as notes, status, path, lastname, firstname, $headstones_table.gedcom FROM $headstones_table, $people_table WHERE TO_DAYS(NOW()) - TO_DAYS($headstones_table.changedate) <= $change_cutoff AND $headstones_table.personID = $people_table.personID AND $headstones_table.gedcom = $people_table.gedcom $wherestr ORDER BY $headstones_table.changedate DESC, lastname, firstname LIMIT $change_limit";
	$hsresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$numrows = mysql_num_rows( $hsresult );
	if( $numrows ) {
?>
<span class="subhead"><b><? echo $text[headstones]; ?></b></span><br>
<table border="0" cellspacing="1" cellpadding="2" width="100%">
	<tr>
		<td width="33%" class="fieldnameback"><span class="fieldname">&nbsp;<b><? echo $text[name]; ?></b></span></td><td width="16%" class="fieldnameback"><span class="fieldname">&nbsp;<b><? echo $text[status]; ?></b></span></td><td width="51%" class="fieldnameback"><span class="fieldname">&nbsp;<b><? echo $text[notes]; ?></b></span></td>
		<? if( $numtrees > 1 ) { ?><td class="fieldnameback"><span class="fieldname">&nbsp;<b><? echo $text[tree]; ?></b>&nbsp;</span></td><? } ?>
		<td class="fieldnameback" nowrap><span class="fieldname">&nbsp;<b><? echo $text[lastmodified]; ?></b>&nbsp;</span></td>
	</tr>			
<?		
		while( $hs = mysql_fetch_assoc( $hsresult ) )
		{
			 echo "<tr><td valign=\"top\" class=\"databack\"><span class=\"normal\">&nbsp;<a href=\"$getperson_url" . "personID=$hs[personID]&tree=$hs[gedcom]\">$hs[lastname], $hs[firstname]</a>&nbsp;</span></td>\n";
			if( $hs[path] ) {
				echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\"><nobr>&nbsp;<a href=\"$showheadstone_url" . "personID=$hs[personID]&tree=$hs[gedcom]\">$text[seephoto]";
				if( $hs[showmap] == 1 ) {
					echo " $text[andlocation]";
				}
				echo "</a>&nbsp;</nobr></span></td>\n";
			}
			else {
				echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\">$hs[status]&nbsp;</span></td>\n";
			}
			echo "<td valign=\"top\" class=\"databack\"><span class=\"normal\">$hs[notes]&nbsp;</span></td>\n";
			if( $numtrees > 1 ) {
				echo "<td class=\"databack\"><span class=\"normal\">$row[gedcom]&nbsp;</span></td>";
			}
			echo "<td class=\"databack\" nowrap><span class=\"normal\">$hs[changedatef]&nbsp;</span></td></tr>\n";
		}
		echo "</table><p></p>\n";
	}

echo tng_menu( "", "", 2 );
tng_footer( "" );
?>
