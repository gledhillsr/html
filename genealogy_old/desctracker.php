<?php
include("begin.php");
include($cms[tngpath] . "pedconfig.php");
include($cms[tngpath] . "genlib.php");
$textpart = "descend";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");
include($cms[tngpath] . "log.php" );
include($cms[tngpath] . "browserinfo.php" );

$desctracker_url = getURL( "desctracker", 1 );
$getperson_url = getURL( "getperson", 1 );

function drawBox( $person, $box ) {
	global $tree, $pedigree, $photopath, $photosext, $browser, $childcount, $totkids, $more, $allow_living, $nonames, $text, $rootpath, $cms, $getperson_url;

	if( $box[lineoutof] )
		$bgcolor = $pedigree[boxcolor];
	else if( $box[lineinto] == 2 )  //spouse
		$bgcolor = getColor( 1 );
	else
		$bgcolor = $pedigree[emptycolor];
		
	//begin, entire square
	echo "<td><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr>";
	
	//box consists of three cells
	//left margin
	drawEmpty( $box[topleft], $box[middleleft], $box[bottomleft] );
	
	//main area
	echo "<td valign=\"top\" align=\"center\">";
	
	//top border
	if( $box[lineinto] ) {
		if( $box[topleft] )
			echo "<img src=\"$cms[tngpath]" . "black.gif\" width=\"$pedigree[halfwidth]\" height=\"1\" hspace=\"0\" vspace=\"0\" border=\"0\">";
		else
			echo "<img src=\"$cms[tngpath]" . "spacer.gif\" width=\"$pedigree[halfwidth]\" height=\"1\" hspace=\"0\" vspace=\"0\" border=\"0\">";
		if( $box[lineinto] == 1 || $box[topleft] || $box[topright] ) 
			echo "<img src=\"$cms[tngpath]" . "black.gif\" width=\"1\" height=\"1\" hspace=\"0\" vspace=\"0\" border=\"0\">";
		//line break after
		if( $box[topright] )
			echo "<img src=\"$cms[tngpath]" . "black.gif\" width=\"$pedigree[halfwidth]\" height=\"1\" hspace=\"0\" vspace=\"0\" border=\"0\"><br>";
		else
			echo "<img src=\"$cms[tngpath]" . "spacer.gif\" width=\"$pedigree[halfwidth]\" height=\"1\" hspace=\"0\" vspace=\"0\" border=\"0\"><br>";
		if( $box[lineinto] == 1 )
			echo "<img src=\"$cms[tngpath]" . "black.gif\" width=\"1\" height=\"20\" hspace=\"0\" vspace=\"0\" border=\"0\">";
		else
			echo "<img src=\"$cms[tngpath]" . "spacer.gif\" width=\"1\" height=\"20\" hspace=\"0\" vspace=\"0\" border=\"0\">";
	}
	else
		echo "<img src=\"$cms[tngpath]" . "spacer.gif\" width=\"1\" height=\"21\" hspace=\"0\" vspace=\"0\" border=\"0\">";

	//name section
	//outer table with border
	echo "<table width=\"$pedigree[puboxwidth]\" height=\"$pedigree[puboxheight]\" border=\"1\" cellspacing=\"0\" cellpadding=\"$pedigree[cellpad]\" bordercolor=\"#000000\" bgcolor=\"$bgcolor\" style=\"border-collapse : collapse;\"><tr><td>\n";
	
	//inner table
	echo "<table width=\"100%\" bgcolor=\"$bgcolor\" cellpadding=\"0\" cellspacing=\"0\"><tr><td align=left>";
	if( !$person[living] || $allow_living || !$nonames )
		$name = trim("$person[firstname] $person[lastname]");
	else
		$name = $text[living];
	$nameinfo = "<a href=\"$getperson_url" . "personID=$person[personID]&tree=$tree\">$name</a>";
	if( $allow_living || !$person[living] ) {
		if( $person[personID] && $pedigree[inclphotos] ) {
			$photoref = "$photopath/$pedigree[phototree]$person[personID].$photosext";
			if( file_exists( "$rootpath$photoref" ) ) {
				// proportionally size according to box height
				$photoinfo = getimagesize( "$rootpath$photoref" );
				$constoffset = $browser == "NS4" ? 8 : 0;
				$photohtouse = $pedigree[puboxheight] - $constoffset - ( $pedigree[cellpad] * 2 ); // take cellpadding into account
				if( $photoinfo[1] < $photohtouse ) {
					$photohtouse = $photoinfo[1];
					$photowtouse = $photoinfo[0];
				}
				else
					$photowtouse = intval( $photohtouse * $photoinfo[0] / $photoinfo[1] ) ;
				echo "<img src=\"$photoref\" width=\"$photowtouse\" height=\"$photohtouse\">";
			}
		}
		if( $person[birthyear] || $person[deathyear] ) {
			if( !$person[birthyear] ) $person[birthyear] = "";
			if( !$person[deathyear] ) $person[deathyear] = "";
			$nameinfo .= "<br>$person[birthyear] - $person[deathyear]";
		}
	}
	echo "</td><td align=\"center\"><span class=\"normal\">$nameinfo</span>";
	//end inner table
	echo "</td></tr></table>";
	
	//end outer table with border
	echo "</td></tr></table>";
	
	//bottom border
	if( $more && $box[lineoutof] )
		echo "<img src=\"$cms[tngpath]" . "black.gif\" width=\"1\" height=\"20\" hspace=\"0\" vspace=\"0\" border=\"0\"><br>";
	else
		echo "<img src=\"$cms[tngpath]" . "spacer.gif\" width=\"1\" height=\"20\" hspace=\"0\" vspace=\"0\" border=\"0\"><br>";

	if( $more ) {
		if( $box[bottomleft] )
			echo "<img src=\"$cms[tngpath]" . "black.gif\" width=\"$pedigree[halfwidth]\" height=\"1\" hspace=\"0\" vspace=\"0\" border=\"0\">";
		else
			echo "<img src=\"$cms[tngpath]" . "spacer.gif\" width=\"$pedigree[halfwidth]\" height=\"1\" hspace=\"0\" vspace=\"0\" border=\"0\">";
		if( $box[bottomleft] || $box[bottomright] || $box[lineoutof] ) 
			echo "<img src=\"$cms[tngpath]" . "black.gif\" width=\"1\" height=\"1\" hspace=\"0\" vspace=\"0\" border=\"0\">";
		if( $box[bottomright] )
			echo "<img src=\"$cms[tngpath]" . "black.gif\" width=\"$pedigree[halfwidth]\" height=\"1\" hspace=\"0\" vspace=\"0\" border=\"0\">";
		else
			echo "<img src=\"$cms[tngpath]" . "spacer.gif\" width=\"$pedigree[halfwidth]\" height=\"1\" hspace=\"0\" vspace=\"0\" border=\"0\">";
	}
	
	//end main area
	echo "</td>";
	
	//right margin
	drawEmpty( $box[topright], $box[middleright], $box[bottomright] );

	//end, entire square
	echo "</tr></table></td>";
}

function drawEmpty( $top, $middle, $bottom ) {
	global $pedigree, $more, $cms;

	echo "<td align=\"center\">";
	if( $top ) {
		echo "<img src=\"$cms[tngpath]" . "black.gif\" width=\"5\" height=\"1\" hspace=\"0\" vspace=\"0\" border=\"0\"><br>";
		echo "<img src=\"$cms[tngpath]" . "spacer.gif\" width=\"1\" height=\"20\" hspace=\"0\" vspace=\"0\" border=\"0\">";
	}
	else
		echo "<img src=\"$cms[tngpath]" . "spacer.gif\" width=\"1\" height=\"21\" hspace=\"0\" vspace=\"0\" border=\"0\">";

	echo "<table width=\"5\" height=\"$pedigree[puboxheight]\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>\n";
	if( $middle )
		echo "<img src=\"$cms[tngpath]" . "black.gif\" width=\"5\" height=\"1\" hspace=\"0\" vspace=\"0\" border=\"0\">";
	echo "</td></tr></table>";

	if( $bottom && $more ) {
		echo "<img src=\"$cms[tngpath]" . "spacer.gif\" width=\"1\" height=\"20\" hspace=\"0\" vspace=\"0\" border=\"0\"><br>";
		echo "<img src=\"$cms[tngpath]" . "black.gif\" width=\"5\" height=\"1\" hspace=\"0\" vspace=\"0\" border=\"0\">";
	}
	else
		echo "<img src=\"$cms[tngpath]" . "spacer.gif\" width=\"1\" height=\"21\" hspace=\"0\" vspace=\"0\" border=\"0\">";
	echo "</td>";
}

function doNextPerson( $row, $items, $nextperson, $box ) {
	global $people_table, $families_table, $tree, $text, $childcount, $pedigree, $totkids, $more;
	
	$nextnextfamily = $items[0];
	if( $row[personID] == $nextperson && $nextnextfamily ) {
		if( $row[sex] == "M" )
			$query = "SELECT personID, firstname, lastname, IF(birthdate!='',YEAR(birthdatetr),YEAR(altbirthdatetr)) as birthyear, IF(deathdate!='',YEAR(deathdatetr),YEAR(burialdatetr)) as deathyear, sex, $people_table.living FROM $families_table, $people_table WHERE husband = \"$row[personID]\" AND personID = wife AND $families_table.familyID = \"$nextnextfamily\" AND $families_table.gedcom = \"$tree\" AND $people_table.gedcom = \"$tree\"";
		else if( $row[sex] == "F" )
			$query = "SELECT personID, firstname, lastname, IF(birthdate!='',YEAR(birthdatetr),YEAR(altbirthdatetr)) as birthyear, IF(deathdate!='',YEAR(deathdatetr),YEAR(burialdatetr)) as deathyear, sex, $people_table.living FROM $families_table, $people_table WHERE wife = \"$row[personID]\" AND personID = husband AND $families_table.familyID = \"$nextnextfamily\" AND $families_table.gedcom = \"$tree\" AND $people_table.gedcom = \"$tree\"";
		if( $query ) {
			$result3 = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
			if( $result3 ) {
				$spouserow = mysql_fetch_assoc( $result3 );
				mysql_free_result( $result3 );
			}
		}
		else
			$spouserow = array();
		
		$savechildcount = $childcount;
		$childcount++;
		if( $box[lineinto] ) {
			$box[topright] = $childcount == $totkids ? 0 : 1;
			$box[topleft] = $childcount != $totkids ? 1 : 0;
			//if( ( $box[bottomleft] && !$box[bottomright] ) || $childcount == $totkids ) $box[bottomleft] = 0;
			//if( $childcount == $totkids ) $box[bottomright] = 0;
			//if( !$box[bottomleft] && $box[bottomright] ) $box[bottomleft] = 1;
			$box[bottomright] = $childcount > $totkids / 2 ? 0 : 1;
			$box[bottomleft] = $childcount > ($totkids + 1) / 2 ? 0 : 1;
		}	
		else {
			$box[bottomleft] = $box[bottomright] = 0;
		}
		$box[lineinto] = 2;
		$box[lineoutof] = 0;
		$box[middleleft] = 1;
		$box[middleright] = 0;
		//echo "tl=$box[topleft], tr=$box[topright], bl=$box[bottomleft], br=$box[bottomright]";
		drawBox( $spouserow, $box );  //yes, that's intentional
	}
}

function getBox( $childcount, $totkids, $thisisit, $gotnext ) {
	global $more;
	
	$box = array();
	
	$box[lineoutof] = $thisisit;
	$thisside = ( $childcount < ( ( $totkids / 2 ) + .5 ) ) && $gotnext ? 1 : 0;
	$thatside = ( $childcount > ( ( $totkids / 2 ) + .5 ) ) && ( !$gotnext || $box[lineoutof] ) ? 1 : 0;
	$middle = $childcount == ( $totkids / 2 ) + .5;
	//echo "this=$thisside, that=$thatside, mid=$middle, cc=$childcount, tk=$totkids, gn=$gotnext, ";
	$box[topright] = ( $childcount == $totkids ) || ( ( $childcount == $totkids - 1 ) && $thisisit && !$thisside && $more ) ? 0 : 1;
	$box[topleft] = $childcount != 1 ? 1 : 0;
	if( $thisside ) {
		if( $box[lineoutof] ) {
			$box[bottomright] = 1;
			$box[bottomleft] = 0;
		}
		else {
			$box[bottomright] = 1;
			$box[bottomleft] = 1;
		}
	}
	elseif( $thatside ) {
		if( $box[lineoutof] ) {
			$box[bottomright] = 0;
			$box[bottomleft] = 1;
		}
		else {
			$box[bottomright] = 1;
			$box[bottomleft] = 1;
		}
	}
	elseif( $middle ) {
		$box[bottomright] = $thisisit || $gotnext ? 0 : 1;
		$box[bottomleft] = $thisisit || !$gotnext ? 0 : 1;
	}
	$box[lineinto] = 1;
	
	return $box;
}

function getColor( $shifts ) {
	global $pedigree;
	
	$shiftval = $shifts * $pedigree[colorshift];
	$R = $pedigree[baseR] + $shiftval;
	$G = $pedigree[baseG] + $shiftval;
	$B = $pedigree[baseB] + $shiftval;
	if ( $R > 255 ) $R = 255; if ( $R < 0 ) $R = 0;
	if ( $G > 255 ) $G = 255; if ( $G < 0 ) $G = 0;
	if ( $B > 255 ) $B = 255; if ( $B < 0 ) $B = 0;
	$R = str_pad( dechex($R), 2, "0", STR_PAD_LEFT ); 
	$G = str_pad( dechex($G), 2, "0", STR_PAD_LEFT ); 
	$B = str_pad( dechex($B), 2, "0", STR_PAD_LEFT );
	return "#$R$G$B";
}

$pedigree[baseR] = hexdec( substr( $pedigree[boxcolor], 1, 2 ) );
$pedigree[baseG] = hexdec( substr( $pedigree[boxcolor], 3, 2 ) );
$pedigree[baseB] = hexdec( substr( $pedigree[boxcolor], 5, 2 ) );

if( $pedigree[colorshift] > 0 ) {
	$extreme = $pedigree[baseR] < $pedigree[baseG] ? $pedigree[baseR] : $pedigree[baseG];
	$extreme = $extreme < $pedigree[baseB] ? $extreme : $pedigree[baseB];
}
elseif( $pedigree[colorshift] < 0 ) {
	$extreme = $pedigree[baseR] > $pedigree[baseG] ? $pedigree[baseR] : $pedigree[baseG];
	$extreme = $extreme > $pedigree[baseB] ? $extreme : $pedigree[baseB];
}
$pedigree[colorshift] = 33;
$pedigree[cellpad] = 3;
$pedigree[puboxheight] += 12;
$pedigree[halfwidth] = floor($pedigree[puboxwidth] / 2);
$pedigree[phototree] = $tree;
if( $tree ) $pedigree[phototree] .= ".";

$items = explode( ",", $trail );
$personID = $nextperson = array_shift( $items );
if( $nextperson ) {
	$query = "SELECT personID, firstname, lastname, suffix, IF(birthdate!='',YEAR(birthdatetr),YEAR(altbirthdatetr)) as birthyear, IF(deathdate!='',YEAR(deathdatetr),YEAR(burialdatetr)) as deathyear, sex, living, disallowgedcreate FROM $people_table, $trees_table WHERE personID = \"$nextperson\" AND $people_table.gedcom = \"$tree\" AND $people_table.gedcom = $trees_table.gedcom";
	$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	if( $result ) {
		$row = mysql_fetch_assoc( $result );
		if( !$row[living] || $allow_living || !$nonames ) {
			$descname = "$row[firstname] $row[lastname]";
			if( $row[suffix] ) $descname .= ", $row[suffix]";
		}
		else
			$descname = $text[living];
		$logname = $nonames && $row[living] ? $text[living] : $descname;
		$disallowgedcreate = $row[disallowgedcreate];
	}
	writelog( "<a href=\"$desctracker_url" . "trail=$trail&tree=$tree\">$text[descendfor] $logname ($personID)</a>" );
}

tng_header( $descname, "" );
?>

<p class="header">
<? 
	if( !$row[living] || $allow_living ) {
		$photoref = $tree ? "$photopath/$tree.$personID.$photosext" : "$photopath/$personID.$photosext";
		echo showSmallPhoto( $photoref, $descname );
	}
	echo $descname; 
?>
<br clear="left">
</p>
<?
	echo tng_menu( "", $personID, 1 );
?>

<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="center">
<?
$more = count( $items );
if( $nextperson ) {
	echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tr>\n";
	$box = array();
	$box[lineinto] = 0;
	$box[lineoutof] = 1;
	$box[topleft] = $box[topright] = 0;
	$childcount = $totkids = 1;
	$box[bottomright] = $more ? 1 : 0;
	$box[bottomleft] = 0;
	$box[middleright] = 1;
	$box[middleleft] = 0;
	drawBox( $row, $box );
	doNextPerson( $row, $items, $nextperson, $box );
	echo "</tr>\n</table>\n";
	if( $more )
		echo "<img src=\"$cms[tngpath]" . "black.gif\" width=\"1\" height=\"20\" hspace=\"0\" vspace=\"0\" border=\"0\"><br>";
}
while( $more ) {
	$lineinfo = array();
	$linelength = 0;
	$gotnext = 0;
	$nextfamily = array_shift( $items );
	$nextperson = array_shift( $items );
	$more = count( $items );
		
	//get kids
	$query = "SELECT $children_table.personID as personID, firstname, lastname, IF(birthdate!='',YEAR(birthdatetr),YEAR(altbirthdatetr)) as birthyear, IF(deathdate!='',YEAR(deathdatetr),YEAR(burialdatetr)) as deathyear, sex, living FROM $children_table, $people_table WHERE familyID = \"$nextfamily\" AND $children_table.personID = $people_table.personID AND $children_table.gedcom = \"$tree\" AND $people_table.gedcom = \"$tree\" ORDER BY ordernum";
	$result2 = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	if( $result2 ) {
		//echo "<img src=\"$cms[tngpath]" . "black.gif\" width=\"1\" height=\"20\" hspace=\"0\" vspace=\"0\" border=\"0\">";
		echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tr>\n";
		$totkids = mysql_num_rows( $result2 );
		if( $more ) $totkids++;
		$childcount = 0;
		while( $row = mysql_fetch_assoc( $result2 ) ) {
			$childcount++;
			if( $row[personID] == $nextperson ) {
				$gotnext = 1;
				$firsthalf = $childcount < ( $totkids / 2 ) ? 1 : 0;
				$thisisit = 1;
			}
			else
				$thisisit = 0;

			$box = getBox( $childcount, $totkids, $thisisit, $gotnext );
			$box[middleleft] = 0;
			$box[middleright] = $thisisit && $more ? 1 : 0;
			//echo "tl=$box[topleft], tr=$box[topright], ml=$box[middleleft], mr=$box[middleright], bl=$box[bottomleft], br=$box[bottomright], cc=$childcount, tk=$totkids, gn=$gotnext";
			drawBox( $row, $box );
			doNextPerson( $row, $items, $nextperson, $box );
		}
		echo "</tr>\n</table>";
	}
	if( $more ) {
		echo "<img src=\"$cms[tngpath]" . "black.gif\" width=\"1\" height=\"20\" hspace=\"0\" vspace=\"0\" border=\"0\"><br>";
	}
	mysql_free_result( $result2 );
}
?>
		</td>
	</tr>
</table>
<?
	echo tng_menu( "", $personID, 2 );
	tng_footer( "" );
?>
