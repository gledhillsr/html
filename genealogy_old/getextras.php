<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "getperson";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");
include($cms[tngpath] . "log.php" );
include($cms[tngpath] . "personlib.php" );

$showheadstone_url = getURL( "showheadstone", 1 );
$showphoto_url = getURL( "showphoto", 1 );
$getperson_url = getURL( "getperson", 1 );
$familygroup_url = getURL( "familygroup", 1 );
$showsource_url = getURL( "showsource", 1 );

$citations = array();
$citedisplay = array();
$citestring = "";
$citationctr = 0;
$citedispctr = 0;

$indnotes = getNotes( $personID, "I" );
getCitations( $personID );

$query = "SELECT *, DATE_FORMAT(changedate,\"%d %b %Y\") as changedate, $people_table.gedcom as gedcom, disallowgedcreate FROM $people_table, $trees_table WHERE personID = \"$personID\" AND $people_table.gedcom = \"$tree\" AND $people_table.gedcom = $trees_table.gedcom";
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
if( $result ) {
	$row = mysql_fetch_assoc($result);
	if( !$row[living] || $allow_living || !$nonames ) {
		$namestr = trim("$row[firstname] $row[lastname]");
		if( $row[suffix] ) $namestr .= ", $row[suffix]";
	}
	else
		$namestr = $text[living];
	$logname = $nonames && $row[living] ? $text[living] : $namestr;
	$disallowgedcreate = $row[disallowgedcreate];
	mysql_free_result($result);
}

writelog( "<a href=\"$getperson_url" . "personID=$personID&tree=$tree\">$text[indinfofor] $logname ($personID)</a>" );

tng_header( $namestr, "" );
?>

<p class="header">
<?php 
	if( !$row[living] || $allow_living ) {
		$photoref = $tree ? "$photopath/$tree.$personID.$photosext" : "$photopath/$personID.$photosext";
		echo showSmallPhoto( $photoref, $namestr );
	}
	echo $namestr;
	$citekey = $personID . "_";
	$cite = $citekey ? reorderCitation( $citekey ) : "";
	if( $cite )
		echo "<sup><span class=\"normal\">[$cite]</span></sup>";
?>
<br clear="left">
</p>
<?php
	echo tng_menu( "person", $personID, 1 );
?>
<table border="0" cellspacing="1" cellpadding="4">
<?php
	if( !$row[living] || $allow_living ) {
		//histories & documents
		$query = "SELECT * FROM $doclinks_table, $histories_table WHERE $doclinks_table.personID=\"$personID\" AND $histories_table.docID = $doclinks_table.docID AND gedcom = \"$tree\" ORDER BY ordernum";
		$doclinks = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
		if( mysql_num_rows($doclinks) ) { 
?>
	<tr>
		<td valign="top" class="fieldnameback"><span class="fieldname">&nbsp;<?php echo $text[historiesdocs]; ?>&nbsp;</span></td>
		<td valign="top" class="databack" colspan="2">
			<span class="normal"><ul>
<?php
			while( $doclink = mysql_fetch_assoc( $doclinks ) ) {
				if( $doclink[path] )
					echo "<LI><a href=\"$historypath/$doclink[path]\">$doclink[description]</a><br>". nl2br($doclink[notes]) ."</LI>\n";
				else
					echo "<LI>$doclink[description]<br>". nl2br($doclink[notes]) ."</LI>\n";
			}
			echo "</ul>\n</td>\n";
			echo "</tr>\n";
			showBreak();
		} 
		mysql_free_result($doclinks);
	
		//photos
		$query = "SELECT * FROM $photolinks_table, $photos_table WHERE $photolinks_table.personID=\"$personID\" AND $photos_table.photoID = $photolinks_table.photoID AND $photolinks_table.gedcom = \"$tree\" ORDER BY ordernum";
		$photolinks = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
		if( mysql_num_rows($photolinks) ) { 
?>
	<tr>
		<td valign="top" class="fieldnameback"><span class="fieldname"><nobr>&nbsp;<?php echo $text[photos]; ?>&nbsp;</nobr></span></td>
		<td valign="top" class="databack" colspan="2">
			<table border="0" class="databack" cellspacing="7" cellpadding="0">
<?php
			while( $photolink = mysql_fetch_assoc( $photolinks ) ) {
				if( $photolink[thumbpath] != "" && file_exists("$rootpath$photopath/$photolink[thumbpath]")) {
					$size = @GetImageSize( "$rootpath$photopath/$photolink[thumbpath]" );
					$imgsrc = "<a href=\"$showphoto_url" . "personID=$photolink[personID]&tree=$tree&ordernum=$photolink[ordernum]\"><img src=\"$photopath/$photolink[thumbpath]\" border=\"0\" $size[3]></a>";
				}
				else
					$imgsrc = "";
				echo "<tr><td>$imgsrc</td><td><span class=\"normal\"><a href=\"$showphoto_url" . "personID=$photolink[personID]&tree=$tree&ordernum=$photolink[ordernum]\">$photolink[description]</a><br>$photolink[notes]</span></td></tr>\n";
			}
			echo "</table>\n";
			echo "</td>\n";
			echo "</tr>\n";
			showBreak();
		}
		mysql_free_result($photolinks);
		
		if( $row[title] )
			showEvent( array( "text"=>$text[title], "fact"=>$row[title], "note"=>$indnotes[TITL] ) );
		if( $row[suffix] ) 
			showEvent( array( "text"=>$text[suffix], "fact"=>$row[suffix], "note"=>$indnotes[NSFX] ) );
		if( $row[nickname] ) 
			showEvent( array( "text"=>$text[nickname], "fact"=>$row[nickname], "note"=>$indnotes[NICK] ) );
		if( $row[birthdate] || $row[birthplace] ) 
			showEvent( array( "text"=>$text[born], "date"=>$row[birthdate], "place"=>$row[birthplace], "note"=>$indnotes[BIRT], "citekey"=>$personID . "_BIRT" ) );
		if( $row[altbirthdate] || $row[altbirthplace] ) 
			showEvent( array( "text"=>$text[christened], "date"=>$row[altbirthdate], "place"=>$row[altbirthplace], "note"=>$indnotes[CHR], "citekey"=>$personID . "_CHR" ) );
	}
	if ( $row[sex] == "M" ) { 
		$sex = $text[male]; $spouse = "wife"; $self = "husband"; $spouseorder = "husborder";
	}
	else if ($row[sex] == "F" ) { 
		$sex = $text[female]; $spouse = "husband"; $self = "wife"; $spouseorder = "wifeorder";
	}
	else { 
		$sex = $text[unknown];   $spouseorder = "";
	}
	showEvent( array( "text"=>$text[sex], "fact"=>$sex ) );

	if( !$row[living] || $allow_living ) {
		if( $allow_lds ) {
			if( $row[baptdate] || $row[baptplace] ) 
				showEvent( array( "text"=>$text[baptizedlds], "date"=>$row[baptdate], "place"=>$row[baptplace], "note"=>$indnotes[BAPL], "citekey"=>$personID . "_BAPL" ) );
			if( $row[endldate] || $row[endlplace] )
				showEvent( array( "text"=>$text[endowedlds], "date"=>$row[endldate], "place"=>$row[endlplace], "note"=>$indnotes[ENDL], "citekey"=>$personID . "_ENDL" ) );
		}
	
		//do custom events
		$query = "SELECT display, eventdate, eventplace, info, tag, description, eventID FROM $events_table, $eventtypes_table WHERE persfamID = \"$personID\" AND $events_table.eventtypeID = $eventtypes_table.eventtypeID AND gedcom = \"$tree\" AND keep = \"1\" ORDER BY ordernum, tag, description";
		$custevents = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
		while ( $custevent = mysql_fetch_assoc( $custevents ) )	{
			$eventID = $custevent[eventID];
			$key = "cust$eventID";
			$fact = $custevent[info] ? checkXnote( $custevent[info] ) : "";
			showEvent( array( "text"=>$custevent[display], "date"=>$custevent[eventdate], "place"=>$custevent[eventplace], "fact"=>$fact, "note"=>$indnotes[$key], "citekey"=>$personID . "_" . $eventID ) );
		}
	
		if( $row[deathdate] || $row[deathplace] ) 
			showEvent( array( "text"=>$text[died], "date"=>$row[deathdate], "place"=>$row[deathplace], "note"=>$indnotes[DEAT], "citekey"=>$personID . "_DEAT" ) );
		if( $row[burialdate] || $row[burialplace] ) 
			showEvent( array( "text"=>$text[buried], "date"=>$row[burialdate], "place"=>$row[burialplace], "note"=>$indnotes[BURI], "citekey"=>$personID . "_BURI" ) );
	}

	//headstone info
	$query = "SELECT path, status, showmap, notes, cemeteryID FROM $headstones_table WHERE personID = \"$personID\" AND gedcom = \"$tree\"";
	$hsresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	if( $hsresult && mysql_num_rows($hsresult) ) { 
		$hs = mysql_fetch_assoc($hsresult);
		mysql_free_result($hsresult);
		if( $hs[path] ) {
			$graveinfo = "<a href=\"$showheadstone_url" . "personID=$personID&tree=$tree\">$text[seephoto]";
			if( $hs[showmap] == 1 )
				$graveinfo .= " " . $text[andlocation];
			$graveinfo .= "</a>&nbsp;";
		}
		else
			$graveinfo = "$hs[status]. &nbsp;";

		if( $hs[notes] )
			$graveinfo .= "$text[notes]: $hs[notes]";

		showEvent( array( "text"=>$text[headstone], "fact"=>$graveinfo ) );
	} 
	
	showEvent( array( "text"=>$text[personid], "fact"=>$personID ) );
	if( $row[changedate] || $allow_edit ) {
		if( $allow_edit ) {
			if( $row[changedate] ) $row[changedate] .= " | ";
			$row[changedate] .= "<a href=\"$cms[tngpath]" . "admin/editperson.php?personID=$personID&tree=$tree\">$text[edit]</a>";
		}
		showEvent( array( "text"=>$text[lastmodified], "fact"=>$row[changedate] ) );
	}
	showBreak();

	//do parents
	$query = "SELECT personID, familyID, sealdate, sealplace FROM $children_table WHERE personID = \"$personID\" AND gedcom = \"$tree\"";
	$parents = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	
	if ( $parents && mysql_num_rows( $parents ) ) {
		while ( $parent = mysql_fetch_assoc( $parents ) )
		{
			$query = "SELECT personID, lastname, firstname, birthdate, birthplace, altbirthdate, altbirthplace, $people_table.living FROM $people_table, $families_table WHERE $people_table.personID = $families_table.husband AND $families_table.familyID = \"$parent[familyID]\" AND $people_table.gedcom = \"$tree\" AND $families_table.gedcom = \"$tree\"";
			$gotfather = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	
		     if( $gotfather ) { 		
				$fathrow =  mysql_fetch_assoc( $gotfather );
				$birthinfo = getBirthInfo( $fathrow );
				if( $fathrow[firstname] || $fathrow[lastname] ) {
					$fathname = !$fathrow[living] || $allow_living || !$nonames ? trim( "$fathrow[firstname] $fathrow[lastname]" ) : $text[living];
					$fatherlink = "<a href=\"$getperson_url" . "personID=$fathrow[personID]&tree=$tree\">$fathname</a>";
				}
				else
					$fatherlink = "";
				if( !$fathrow[living] || $allow_living ) $fatherlink .= $birthinfo;
				showEvent( array( "text"=>$text[father], "fact"=>$fatherlink ) );
				mysql_free_result( $gotfather );
			} 

			$query = "SELECT personID, lastname, firstname, birthdate, birthplace, altbirthdate, altbirthplace, $people_table.living FROM $people_table, $families_table WHERE $people_table.personID = $families_table.wife AND $families_table.familyID = \"$parent[familyID]\" AND $people_table.gedcom = \"$tree\" AND $families_table.gedcom = \"$tree\"";
			$gotmother = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	
			if( $gotmother ) { 
				$mothrow =  mysql_fetch_assoc( $gotmother );
				$birthinfo = getBirthInfo( $mothrow );
				if( $mothrow[firstname] || $mothrow[lastname] ) {
					$mothname = !$mothrow[living] || $allow_living || !$nonames ? trim( "$mothrow[firstname] $mothrow[lastname]" ) : $text[living];
					$motherlink = "<a href=\"$getperson_url" . "personID=$mothrow[personID]&tree=$tree\">$mothname</a>";
				}
				else
					$motherlink = "";
				if( !$mothrow[living] || $allow_living ) $motherlink .= $birthinfo;
				showEvent( array( "text"=>$text[mother], "fact"=>$motherlink ) );
				mysql_free_result( $gotmother );
			} 
			
			if( ( !$row[living] || $allow_living ) && $allow_lds ) {
				if( $parent[sealdate] || $parent[sealplace] ) 
					showEvent( array( "text"=>$text[sealedplds], "date"=>$parent[sealdate], "place"=>$parent[sealplace], "note"=>$indnotes[SLGC], "citekey"=>$personID . "_SLGC" ) );
			}
			showEvent( array( "text"=>$text[groupsheet], "fact"=>"<a href=\"$familygroup_url" . "familyID=$parent[familyID]&tree=$tree\">$parent[familyID]</a>" ) );
			showBreak();
		}
		mysql_free_result($parents);
	}

	//do marriages
	if( $spouseorder )
		$query = "SELECT $spouse, familyID, living, marrdate, marrplace, sealdate, sealplace, notes, DATE_FORMAT(changedate,\"%d %b %Y\") as changedate FROM $families_table WHERE $families_table.$self = \"$personID\" AND gedcom = \"$tree\" ORDER BY $spouseorder";
	else
		$query = "SELECT husband, wife, familyID, living, marrdate, marrplace, sealdate, sealplace, notes, DATE_FORMAT(changedate,\"%d %b %Y\") as changedate FROM $families_table WHERE ($families_table.husband = \"$personID\" OR $families_table.wife = \"$personID\") AND gedcom = \"$tree\"";
	$marriages= mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$marrcount = 1;
	
	while ( $marriagerow =  mysql_fetch_assoc( $marriages ) )
	{
		$famnotes = getNotes( $marriagerow[familyID], "F" );
		getCitations( $marriagerow[familyID] );
	
		if( !$spouseorder )
			$spouse = $marriagerow[husband] == $personID ? wife : husband;
		unset($spouserow);
		unset($birthinfo);
		if( $marriagerow[$spouse] ) {
			$query = "SELECT personID, lastname, firstname, birthdate, birthplace, altbirthdate, altbirthplace, living FROM $people_table WHERE personID = \"$marriagerow[$spouse]\" AND gedcom = \"$tree\"";
			$spouseresult= mysql_query($query) or die ("$text[cannotexecutequery]: $query");
			$spouserow =  mysql_fetch_assoc( $spouseresult );
			$birthinfo = getBirthInfo( $spouserow );
			if( $spouserow[firstname] || $spouserow[lastname] ) {
				$spousename = !$spouserow[living] || $allow_living || !$nonames ? trim( "$spouserow[firstname] $spouserow[lastname]" ) : $text[living];
				$spouselink = "<a href=\"$getperson_url" . "personID=$spouserow[personID]&tree=$tree\">$spousename</a>";
			}
			if( !$spouserow[living] || $allow_living ) $spouselink .= $birthinfo;
		}
		else
			$spouselink = "";
		showEvent( array( "text"=>"$text[family] $marrcount", "fact"=>$spouselink ) );
		
		if( !$marriagerow[living] || $allow_living ) {
			if( $marriagerow[marrdate] || $marriagerow[marrplace] ) 
				showEvent( array( "text"=>$text[married], "date"=>$marriagerow[marrdate], "place"=>$marriagerow[marrplace], "note"=>$famnotes[MARR], "citekey"=>$marriagerow[familyID] . "_MARR" ) );
	
			if( $allow_lds ) {
				if( $marriagerow[sealdate] || $marriagerow[sealplace] ) 
					showEvent( array( "text"=>$text[sealedslds], "date"=>$marriagerow[sealdate], "place"=>$marriagerow[sealplace], "note"=>$famnotes[SLGS], "citekey"=>$marriagerow[familyID] . "_SLGS" ) );
			}
		
			$query = "SELECT display, eventdate, eventplace, info, tag, description, eventID FROM $events_table, $eventtypes_table WHERE persfamID = \"$marriagerow[familyID]\" AND $events_table.eventtypeID = $eventtypes_table.eventtypeID AND gedcom = \"$tree\" AND keep = \"1\" ORDER BY ordernum, tag, description";
			$custevents = mysql_query($query) or die ("Cannot execute $query query");
			while ( $custevent = mysql_fetch_assoc( $custevents ) )
			{
				$eventID = $custevent[eventID];
				$fact = $custevent[info] ? checkXnote( $custevent[info] ) : "";
				showEvent( array( "text"=>$custevent[display], "date"=>$custevent[eventdate], "place"=>$custevent[eventplace], "fact"=>$fact, "note"=>$famnotes[$eventID], "citekey"=>$marriagerow[familyID] . "_" . $eventID ) );
			}
		
			if( !$notestogether ) {
				$famnotes2 = "";
				$addfamnotes2 = buildNotes( $famnotes );
				if( $marriagerow[notes] ) { 
					preg_match_all( "/@(\S+)@/", $marriagerow[notes], $matches );
					$marriagerow[notes] = preg_replace( "/@(\S+)@/", "[<a href=\"#sources\">\\1</a>]", $marriagerow[notes] );
					array_unique($matches[1]);
					$newsources = implode( ",", $matches[1]);
					if( $newsources) addSource( $newsources );
					$famnotes2 = nl2br($marriagerow[notes]); 
					if( $addfamnotes2 ) $famnotes2 .= "<br><br>\n";
				}
				$famnotes2 .= "$addfamnotes2";
				
				if( $famnotes2 ) { 
?>
		<tr>
			<td valign="top" class="fieldnameback" nowrap><span class="fieldname">&nbsp;<?php echo $text[notes]; ?>&nbsp;</span></td>
			<td valign="top" class="databack" colspan="2"><span class="normal"><?php  echo $famnotes2; ?></span></td>
		</tr>
<?php
				}
			}
		}
		$marrcount++;
	
		//do children
		$query = "SELECT $people_table.personID as pID, firstname, lastname, birthdate, birthplace, altbirthdate, altbirthplace, haskids, living FROM $people_table, $children_table WHERE $people_table.personID = $children_table.personID AND $children_table.familyID = \"$marriagerow[familyID]\" AND $people_table.gedcom = \"$tree\" AND $children_table.gedcom = \"$tree\" ORDER BY ordernum";
		$children= mysql_query($query) or die ("$text[cannotexecutequery]: $query");
		
		if( $children && mysql_num_rows( $children ) ) {
?>
	<tr>
		<td valign="top" class="fieldnameback"><span class="fieldname"><nobr>&nbsp;<?php echo $text[children]; ?>&nbsp;</nobr></span></td>
		<td colspan="2" valign="top" class="databack"><span class="normal">
<?php
			$kidcount = 1;
			echo "<table cellpadding = \"0\" cellspacing = \"0\">\n";
			while ( $child =  mysql_fetch_assoc( $children ) )
			{
				$ifkids = $child[haskids] ? "&gt" : "&nbsp;";
				$birthinfo = getBirthInfo( $child );
				if( $child[firstname] || $child[lastname] ) {
					$childname = !$child[living] || $allow_living || !$nonames ? trim( "$child[firstname] $child[lastname]" ) : $text[living];
					echo "<tr><td>$ifkids</td><td><span class=\"normal\">$kidcount. <a href=\"$getperson_url" . "personID=$child[pID]&tree=$tree\">$childname</a>";
					if( !$child[living] || $allow_living ) echo $birthinfo;
					echo "</span></td></tr>\n";
				}
				$kidcount++;
			}
			echo "</table>\n";
			echo "</td>\n";
			echo "</tr>\n";

			mysql_free_result( $children );
		}
		if( $marriagerow[changedate] || $allow_edit ) {
			if( $allow_edit ) {
				if( $marriagerow[changedate] ) $marriagerow[changedate] .= " | ";
				$marriagerow[changedate] .= "<a href=\"$cms[tngpath]" . "admin/editfamily.php?familyID=$marriagerow[familyID]&tree=$tree\">$text[edit]</a>";
			}
			showEvent( array( "text"=>$text[lastmodified], "fact"=>$marriagerow[changedate] ) );
		}
		showEvent( array( "text"=>$text[groupsheet], "fact"=>"<a href=\"$familygroup_url" . "familyID=$marriagerow[familyID]&tree=$tree\">$marriagerow[familyID]</a>" ) );
		showBreak();
	}
	mysql_free_result($marriages);
	
	if( !$row[living] || $allow_living ) {
		if( !$notestogether )
			$addnotes = buildNotes( $indnotes );
		else {
			$addnotes = "";
			$addnotes .= $indnotes['--x-general-x--'][text];
			$addnotes .= $indnotes[NAME][text];
			$addnotes = "<ul>\n" . $addnotes . "</ul>\n";
		}
		if( $row[notes] ) {
			$notes = nl2br($row[notes]); 
			if( $addnotes ) $notes .= "<br><br>\n";
		}
		$notes .= "$addnotes";
		
		if( $notes ) { 
?>
	<tr>
		<td valign="top" class="fieldnameback" nowrap><span class="fieldname">&nbsp;<?php echo $text[notes]; ?>&nbsp;</span></td>
		<td valign="top" class="databack" colspan="2"><span class="normal"><?php  echo $notes; ?></span></td>
	</tr>
<?php
			showBreak();
		}
	}

	if( $citedispctr ) { 
?>
	<tr>
		<td valign="top" class="fieldnameback"><a name="sources"><span class="fieldname">&nbsp;<?php echo $text[sources]; ?>&nbsp;</span></td>
		<td valign="top" class="databack" colspan="2"><span class="normal"><?php echo $citestring; ?></span></td>
	</tr>
<?php
		showBreak();
	}
?>

</table>
<?php
	echo tng_menu( "person", $personID, 2 );
tng_footer( "" );
?>
