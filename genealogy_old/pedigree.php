<?php
include("begin.php");
include($cms[tngpath] . "pedconfig.php");
include($cms[tngpath] . "genlib.php");

$pedigreetext_url = getURL( "pedigreetext", 1 );

if( $display == "textonly" || ( !$display && $pedigree[usepopups] == -1 ) )
        header( "Location: $pedigreetext_url" . "personID=$personID&tree=$tree&generations=$generations");

$textpart = "pedigree";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
@set_time_limit(0);
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");
include($cms[tngpath] . "log.php");
include($cms[tngpath] . "browserinfo.php");

$pedigree_url = getURL( "pedigree", 1 );
$getperson_url = getURL( "getperson", 1 );
$pedigree_url = getURL( "pedigree", 1 );
$familygroup_url = getURL( "familygroup", 1 );

$query = "SELECT firstname, lastname, suffix, living, disallowgedcreate FROM $people_table, $trees_table WHERE personID = \"$personID\" AND $people_table.gedcom = \"$tree\" AND $people_table.gedcom = $trees_table.gedcom";
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
if( $result ) {
	$row = mysql_fetch_assoc( $result );
	if( !$row[living] || $allow_living || !$nonames ) {
		$pedname = trim("$row[firstname] $row[lastname]");
		if( $row[suffix] ) $pedname .= ", $row[suffix]";
	}
	else
		$pedname = $text[living];
	$logname = $nonames && $row[living] ? $text[living] : $pedname;
	$disallowgedcreate = $row[disallowgedcreate];
	mysql_free_result($result);

	$query = "SELECT marrdate, marrplace, living FROM $families_table WHERE (husband = \"$personID\" OR wife = \"$personID\") AND gedcom = \"$tree\" ORDER BY husborder, wifeorder";
	$result2 = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	if( $result2 ) {
		$newrow = mysql_fetch_assoc( $result2 );
		if( !$newrow[living] || $allow_living ) {
			$marrdate[ 1 ] = $newrow[marrdate];
			$marrplace[ 1 ] = $newrow[marrplace];
		}
		else {
			$marrdate[ 1 ] = "";
			$marrplace[ 1 ] = "";
		}
		mysql_free_result($result2);
	}
}

if( $display == "compact" )
	$pedigree[usepopups] = 1;
elseif( $display == "standard" )
	$pedigree[usepopups] = 0;
else {
	if( $pedigree[usepopups] )
		$display = "compact";
	else
		$display = "standard";
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

function displayIndividual( $key, $generation, $slot, $spousekey, $childkey) {
	global $pedigree, $generations, $parentset, $marrdate, $marrplace, $pedmax, $people_table, $families_table, $children_table, $personID;
	global $text, $pedoptions, $tree, $browser, $photopath, $display, $photosext, $allow_living, $nonames, $rootpath, $cms, $getperson_url, $pedigree_url, $familygroup_url;
	
	// set pointer to next father/mother pair
	$nextslot = $slot * 2;
    
	if ( $key ) {
		$query = "SELECT firstname, lastname, living, famc, sex, birthdate, birthplace, altbirthdate, altbirthplace, deathdate, deathplace, burialdate, burialplace FROM $people_table WHERE personID = \"$key\" AND gedcom = \"$tree\"";
		$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
		if( $result ) {
			$row = mysql_fetch_assoc( $result );
			if( !$row[living] || $allow_living || !$nonames )
				$nameinfo = trim("$row[firstname] $row[lastname]");
			else
				$nameinfo = $text[living];
			mysql_free_result($result);
		}
		
		// get parent info
		$parentfamID = "";
		$locparentset = 0;
		$parentscount = 0;
		$parentfamIDs = array();
		$query = "SELECT familyID FROM $children_table WHERE personID = \"$key\" AND gedcom = \"$tree\" ORDER BY ordernum";
		$parents = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
		if ( $parents ) {
			$parentscount = mysql_num_rows( $parents );
			if ( $parentscount > 0 ) {
				if ( $parentset > $parentscount ) 
					$parentset = $parentscount;
				$i = 0;
				while ( $parentrow = mysql_fetch_assoc( $parents ) ) {
					$i++;
					if( !$locparentset && $parentrow[familyID] == $row[famc] ) {
						$parentfamID = $row[famc];
						$locparentset = $i;
					}
					elseif ( $i == $parentset ) {
						$parentfamID = $parentrow[familyID];
						$locparentset = $i;
					}
					$parentfamIDs[$i] = $parentrow[familyID];
				}
			}
			mysql_free_result($parents);
		}
	}
	$name = $nameinfo ? $nameinfo : "$text[unknownlit]";
	$namelink = $nameinfo ? "<a href=\"$getperson_url" . "personID=$key&tree=$tree\">$name</a>" : "$text[unknownlit]";

	// do we want spouse &/or kids info in popups?
	$spiceNames = array(); $spiceIDs = array();
	$kidsNames = array();  $kidsIDs = array();
	$spicekidcount = array();
	$spousecount = 1;
	
	if( $pedigree[popupspouses] ) {
		if( $row[sex] ) { 
			if ( $row[sex] == "M" ) { 
		  		$spouse = "wife"; $self = "husband"; $spouseorder = "husborder"; 
			}
			elseif( $row[sex] == "F" ) { 
				$spouse = "husband"; $self = "wife"; $spouseorder = "wifeorder"; 
			}
			else
				$spouseorder = "";
		}
		if( $spouseorder ) { 
			$query = "SELECT $spouse, familyID FROM $families_table WHERE $families_table.$self = \"$key\" AND $families_table.gedcom = \"$tree\" ORDER BY $spouseorder";
			$spouses = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
			while ( $spouserow =  mysql_fetch_assoc( $spouses ) ) {
				if( $spouserow[$spouse] ) {
					$query = "SELECT lastname, firstname, living FROM $people_table WHERE personID = \"$spouserow[$spouse]\" AND gedcom = \"$tree\"";
					$spouseIDresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
					$spouseIDrow =  mysql_fetch_assoc( $spouseIDresult );
					if( !$spouseIDrow[living] || $allow_living || !$nonames )
						$spiceNames[$spousecount] = trim("$spouseIDrow[firstname] $spouseIDrow[lastname]");
					else
						$spiceNames[$spousecount] = $text[living];
					$spiceIDs[$spousecount] = $spouserow[$spouse];
					$spicefamilyIDs[$spousecount] = $spouserow[familyID];
					mysql_free_result($spouseIDresult);
				}
				else {
					$spiceNames[$spousecount] = "";
					$spiceIDs[$spousecount] = "";
				}
					
				$spicekidcount[$spousecount] = 0;
		  
				if ($pedigree[popupkids]) {
					$kidcount = 1;
					$query = "SELECT $people_table.personID as pID, firstname, lastname, living FROM $people_table, $children_table WHERE $people_table.personID = $children_table.personID AND $children_table.familyID = \"$spouserow[familyID]\" AND $people_table.gedcom = \"$tree\" AND $children_table.gedcom = \"$tree\" ORDER BY ordernum";
					$children = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
					if( $children && mysql_num_rows( $children ) ) {
						while ( $child =  mysql_fetch_assoc( $children ) ) {
							$spicekidcount[$spousecount]++;
							$kidsIDs[$spousecount][$kidcount] = $child[pID];
							if( !$child[living] || $allow_living || !$nonames )
								$kidsNames[$spousecount][$kidcount] = trim("$child[firstname] $child[lastname]");
							else
								$kidsNames[$spousecount][$kidcount] = $text[living];
							$kidcount++;
						}
					}
					mysql_free_result($children);
				}
				$spousecount++;
			}
			mysql_free_result($spouses);
		}
	}

	// compute box height to use
	// -  first box height is defined by config parm [$pedigree[boxheight]].
	// -  boxes of each subsequent generation shrunk according to config parm [$pedigree[boxheightshift]] (which may be zero, in which case all boxes will be the same height).
	// -  some minimums and defaults are enforced so that we don't get into trouble shrinking the heights to negative numbers (which would be a bad thing).
	$boxheighttouse = $pedigree[boxheight] + ( $pedigree[boxheightshift] * ( $generation - 1) ) ;
    
	// if any of the data we need is present, set the flag
	if( !$row[living] || $allow_living ) {
		if( $row[birthdate] || $row[altbirthdate] || $row[altbirthplace] || $row[deathdate] || $row[burialdate] || $row[burialplace] )
			$dataflag = 1;
		else
			$dataflag = 0;
			
		// get birthdate info
		if ( $row[altbirthdate] && !$row[birthdate] ) {
			$bd = $row[altbirthdate]; 
			$bp = $row[altbirthplace];
			$birthabbr = $text[capaltbirthabbr];
		}
	  	elseif( $dataflag ) {
			$bd = $row[birthdate];
			$bp = $row[birthplace];
			$birthabbr = $text[capbirthabbr];
		}
		else {
			$bd = ""; 
			$bp = "";
			$birthabbr = "";
		}
	
		// get death/burial date info   
		if( $row[burialdate] && !$row[deathdate] ) {
			$dd = $row[burialdate]; 
			$dp = $row[burialplace];
			$deathabbr = $text[capburialabbr];
		}
		elseif( $dataflag ) {
			$dd = $row[deathdate] ;
			$dp = $row[deathplace];
			$deathabbr = $text[capdeathabbr];
		}
		else {
			$dd = "";
			$dp = "";
			$deathabbr = "";
		}
	    
		// get marriage info
		if( $marrdate[$slot] || $marrplace[$slot] ) {
			$md = $marrdate[$slot];
			$mp = $marrplace[$slot];
			$marrabbr = $text[capmarrabbr];
		}
	  	else {
			$md = "";
			$mp = "";
			$marrabbr = "";
		}
    
		// get photo info (if to be included)
		if( $key && $pedigree[inclphotos] && $pedigree[usepopups]) {
			$photoref = "$photopath/$pedigree[phototree]$key.$photosext";
			$photoinfo = null;
			if( file_exists( "$rootpath$photoref" ) ) {
				// proportionally size according to box height
				$photoinfo = getimagesize( "$rootpath$photoref" );
				$photohtouse = $boxheighttouse - ( $pedigree[cellpad] * 2 ); // take cellpadding into account
				if( $photoinfo[1] < $photohtouse ) {
					$photohtouse = $photoinfo[1];
					$photowtouse = $photoinfo[0];
				}
				else
					$photowtouse = intval( $photohtouse * $photoinfo[0] / $photoinfo[1] ) ;
			}
		}
	}
	else {
		$bd = $bp = $birthabbr = $dd = $dp = $deathabbr = $md = $mp = $marrabbr = "";
	}

	// will we have any popup info?
	$popupinfo = ( ( trim( $bd.$bp.$md.$mp.$dd.$dp ) == "" ) && !count( $spiceIDs ) && !count( $kidsIDs ) ) ? false : true;
    
	// compute horizontal box offset
	// -  first box horizontal offset is defined by config parm [$pedigree[leftindent]].
	// -  boxes for each subsequent generation are offset horizontally according to config parms [$pedigree[boxwidth] and [$pedigree[boxHsep]]. The latter value has a minimum setting enforced in the earlier idiot checks so that we don't get negative offsets and so there's at least *some* room for connectors.
	$offsetH = $pedigree[leftindent] + ( $generation - 1 ) * ( $pedigree[boxwidth] + $pedigree[boxHsep] ) ;

	// compute vertical separation
	// -  the vertical separation between boxes of each generation are different because the box height for each generation may be different, and the boxes need to line up according to father/mother pair of the subsequent generation
	// -  we can back into the vertical separation because we can know, for the *last* generation to be displayed, the box size (computed above) and the vertical separation of those boxes (via config parm [$sepV]). This allows us to  calculate the height of the space to be used for the *last* generation
	//    display (computed as $pedigree[maxheight]). Given this, and the height of *this*  generation's boxes, we can do the following math to derive the amount of space that must exist between *this* generation's boxes to result in their being properly aligned vis-a-vis the *next* generation's boxes
	$sepV = intval ( $pedigree[maxheight] - ( pow( 2, ( $generation - 1 ) ) * $boxheighttouse ) ) / pow( 2, ( $generation - 1 ) ) ;

	// compute vertical offset for first box per generation
	// -  now we need to calculate the 'base" offset vertically for *this* generation's first (or, top) box.  We computed the separation required above so support proper alignment. This calulation is also necessary to obtain proper vertical alignment
	$offsetV = ( $pedigree[maxheight] - $pedigree[boxVsep] - ( pow( 2, ( $generation - 1 ) ) * ( $boxheighttouse + $sepV ) - $sepV ) ) / 2;

	// finally, compute the offset for the box we're to build
	// -  finally, we need to figure out where the specific box for *this* generation needs to be placed. This math isn't so bad, since it's a linear equaltion based upon slot # ($slot), initial offset ($offsetV), box height ($boxheighttouse), and vertical separation ($sepV).
	$offsetV = intval ( $pedigree[borderwidth] + ( $slot - pow( 2, ( $generation - 1 ) ) ) * ( $boxheighttouse + $sepV ) + $offsetV ) ;

	// compute box color
	// -  if the config parm [$pedigree[colorshift]] is anything other than zero this math will reduce each primary color value (red,green,blue),  respectively, but the color shift value
	// -  if $pedigree[colorshift] = 0, all this code spits out the same value as  defined by the config parm [$pedigree[boxcolor]]
	// -  otherwise the color will "shift" up or down (closer to white or closer to black)
	$boxcolortouse = $nameinfo ? getColor( $generation - 1 ) : $pedigree[emptycolor];

	// compute font sizes
	// -  this will adjust font size values for subsequent generation box data
	// -  note that the shift can be different for the names portion and for the dates portion.  (Dates portion is either displayed in the box or in the popup box, depending upon the config parm [$pedigree[usepopups]].)
	// -  while decimal values are allowed for the config parms [$pedigree[namesizeshift]] and [$pedigree[datessizeshift]], rounding is done here so that only integer values will be used in the HTML strings. This means that some side-by-side generations' boxes will have the same font sizes.
	// -  Notwithstanding, the font sizes are never permitted to be less than 6 points
	$namefontsztouse    = intval( $pedigree[boxnamesize]   + ($generation - 1) * $pedigree[namesizeshift] );
	$datesfontsztouse   = intval( $pedigree[boxdatessize]  + ($generation - 1) * $pedigree[datessizeshift] );
	$popupinfosizetouse = intval( $pedigree[popupinfosize] + ($generation - 1) * $pedigree[popupinfosizeshift] );
	if ($namefontsztouse    < 7) $namefontsztouse    = 7;
	if ($datesfontsztouse   < 7) $datesfontsztouse   = 7;
	if ($popupinfosizetouse < 7) $popupinfosizetouse = 7;
    if( $browser == "NS4" ) {
		$begnamefont = "<font point-size=$namefontsztouse>";
		$begdatefont = "<font point-size=$datesfontsztouse>";
		$endfont = "</font>";
	}
	else {
		$begnamefont = "<span style=\"font-size:$namefontsztouse"."pt\">";
		$begdatefont = "<span style=\"font-size:$datesfontsztouse"."pt\">";
		$endfont = "</span>";
	}
	
	// set true to show trace comments in HTML
	$demarkelements = false;
    
	// build the person's main display box
	//... include trace (maybe)
	if ($demarkelements) 
		echo "\n<!-- box for $name -->\n\n";
		
	//start box 
	if ($browser == "NS4") {
		echo "<layer bgcolor=\"$boxcolortouse\" top=$offsetV left=$offsetH height=$boxheighttouse width=$pedigree[boxwidth] z-index=5 clip=\"$pedigree[boxwidth],$boxheighttouse\">";
		$tableheight = $boxheighttouse -  (2 * $pedigree[cellpad]);
	}
	else {
		echo "<div style=\"position:absolute; background-color:$boxcolortouse; top:$offsetV; left:$offsetH; height:$boxheighttouse; width:$pedigree[boxwidth]; z-index:5; clip:rect(auto, auto, " . $boxheighttouse . "px, auto)$pedigree[overflowtxt]\">\n";
		$tableheight = $boxheighttouse;
	}
    echo "<table border=\"0\" cellpadding=\"$pedigree[cellpad]\" cellspacing=\"0\" align=\"$pedigree[boxalign]\"><tr>";
	
    // implant a picture (maybe)
    if ( $pedigree[inclphotos] && $photoinfo && $pedigree[usepopups]) 
		echo "<td align=left valign=top><img src=\"$photoref\" width=\"$photowtouse\" height=\"$photohtouse\"></td>";
		
    // name info
    echo "<td align=\"$pedigree[boxalign]\" class=pboxname height=\"$tableheight\">$begnamefont$namelink$endfont";
	if( $key && $row[famc] && $pedigree[popupchartlinks] && $slot != 1)
		echo " <a href=\"$pedigree_url" . "personID=$key&tree=$tree&&display=$display\">$pedigree[chartlink]</a>";
	
	//... if including dates/places in the box itself ...
	if (!$pedigree[usepopups]) {
		$datastr = "";
		if( $bd || $bp ) {
			$datastr .= "<tr><td valign=\"top\"><span class=pboxdates>$begdatefont$birthabbr:&nbsp;$endfont</span></td>";
			if( $bd ) {
				$datastr .= "<td><span class=pboxdates>$begdatefont$bd$endfont</span></td></tr>\n";
				if( $bp ) $datastr .= "<tr><td valign=\"top\"><span class=pboxdates>$begdatefont&nbsp;$endfont</span></td><td><span class=pboxdates>$begdatefont$bp$endfont</span></td>";
			}
			elseif( $bp ) $datastr .= "<td><span class=pboxdates>$begdatefont&nbsp;$endfont</span></td><td><span class=pboxdates>$begdatefont$bp$endfont</span></td>";
			$datastr .= "</tr>\n";
		}
		if( $md || $mp ) {
			$datastr .= "<tr><td valign=\"top\"><span class=pboxdates>$begdatefont$text[capmarrabbr]:&nbsp;$endfont</span></td>";
			if( $md ) {
				$datastr .= "<td><span class=pboxdates>$begdatefont$md$endfont</span></td></tr>\n";
				if( $mp ) $datastr .= "<tr><td valign=\"top\"><span class=pboxdates>$begdatefont&nbsp;$endfont</span></td><td><span class=pboxdates>$begdatefont$mp$endfont</span></td>";
			}
			elseif( $mp ) $datastr .= "<td><span class=pboxdates>$begdatefont&nbsp;$endfont</span></td><td><span class=pboxdates>$begdatefont$mp$endfont</span></td>";
			$datastr .= "</tr>\n";
		}
		if( $dd || $dp ) {
			$datastr .= "<tr><td valign=\"top\"><span class=pboxdates>$begdatefont$deathabbr:&nbsp;$endfont</span></td>";
			if( $dd ) {
				$datastr .= "<td><span class=pboxdates>$begdatefont$dd$endfont</span></td></tr>\n";
				if( $dp ) $datastr .= "<tr><td valign=\"top\"><span class=pboxdates>$begdatefont&nbsp;$endfont</span></td><td><span class=pboxdates>$begdatefont$dp$endfont</span></td>";
			}
			elseif( $dp ) $datastr .= "<td><span class=pboxdates>$begdatefont&nbsp;$endfont</span></td><td><span class=pboxdates>$begdatefont$dp$endfont</span></td>";
			$datastr .= "</tr>\n";
		}
    }
	if( $datastr )
		echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n$datastr\n</table>\n";

	echo "</td></tr></table>" . ($browser=="NS4"?"</layer>":"</div>") . "\n";
	//end box
    
	// build the pop-up information box
	if( $pedigree[usepopups] && $name && $popupinfo ) {
		$popupcolor = $pedigree[popupcolor] ? $pedigree[popupcolor] : getColor( $generation );
		//... include trace (maybe)
		if( $demarkelements ) 
			echo "\n<!-- popup for $name -->\n\n";
		
		// lay a down arrow below the box to indicate a drop-down has data
		if( $pedigree[downarrow] ) {
			if( $browser == "NS4" )
				echo "<layer top=" . ($offsetV + $boxheighttouse + $pedigree[borderwidth] + $pedigree[shadowoffset] + 1) . " left=" . ($offsetH + intval(($pedigree[boxwidth] - $pedigree[downarroww])/2) - 1) . " z-index=7>";
			else
				echo "<div style=\"position:absolute; top:" . ($offsetV + $boxheighttouse + $pedigree[borderwidth] + $pedigree[shadowoffset] + 1) . ";left:" . ($offsetH + intval(($pedigree[boxwidth] - $pedigree[downarroww])/2) - 1) . ";z-index:7;cursor:pointer\">";
			echo "<a href=\"#\" onmouse$pedigree[event]=\"showPopup($slot, $offsetV,$boxheighttouse)\"><img src=\"$cms[tngpath]" . "ArrowDown.gif\" border=\"0\" width=\"$pedigree[downarroww]\" height=\"$pedigree[downarrowh]\"></a>";
		}
		else {
			if( $browser == "NS4" ) 
				echo "<layer top=" . ($offsetV + $boxheighttouse + $pedigree[borderwidth] + $pedigree[shadowoffset] - 2) . " left=" . ($offsetH + intval($pedigree[boxwidth]/2) - 1) . " z-index=7>";
			else
				echo "<div style=\"position:absolute; top:" . ($offsetV + $boxheighttouse + $pedigree[borderwidth] + $pedigree[shadowoffset] - 2) . ";left:" . ($offsetH + intval($pedigree[boxwidth]/2) - 1) . ";z-index:7;cursor:pointer\">";
			echo "<a href=\"javascript:showPopup($slot, $offsetV, $boxheighttouse)\"><font face=\"sans-serif\" size=1><B>V</B></font></a>";
		}		
		echo "$pedigree[enddiv]\n";
		
		//start the block
		if( $browser == "NS4" ) 
			echo "<layer name=\"popup$slot\" visibility=\"hide\" bgcolor=\"$popupcolor\" left=" . ($offsetH - $pedigree[borderwidth] + round($pedigree[shadowoffset]/2)) . " width=" . ($pedigree[boxwidth] + (2*$pedigree[borderwidth])) . " z-index=8 onmouseover=\"cancelTimer($slot)\" onmouseout=\"setTimer($slot)\">\n";
		else
			echo "<div id=\"popup$slot\" style=\"position:absolute; visibility:hidden; background-color:$popupcolor; left:" . ($offsetH - $pedigree[borderwidth] + round($pedigree[shadowoffset]/2)) . ";z-index:8\" onmouseover=\"cancelTimer($slot)\" onmouseout=\"setTimer($slot)\">\n";
		echo "<table border=\"1\" bordercolor=\"$pedigree[bordercolor]\" cellpadding=\"1\" cellspacing=\"0\"><tr><td bordercolor=\"$popupcolor\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n";
		buildInfoEntry ( array( $birthabbr, $bd ), $popupinfosizetouse );
		buildInfoEntry ( array( "", $bp ), $popupinfosizetouse );
		buildInfoEntry ( array( $marrabbr, $md ), $popupinfosizetouse );
		buildInfoEntry ( array( "", $mp ), $popupinfosizetouse );
		buildInfoEntry ( array( $deathabbr, $dd ), $popupinfosizetouse );
		buildInfoEntry ( array( "", $dp ), $popupinfosizetouse );
		
		//other parents
		if ($parentscount > 1) {
			buildInfoEntry ( array( "<B>$text[parents]:</B>" ), $popupinfosizetouse );
			for ( $i = 1; $i <= $parentscount ; $i++ ) {
				$parentinfo = getParentInfo($parentfamIDs[$i]);
				$parentlink = ( $parentinfo[fathname] ? "<a href=\"$getperson_url" . "personID=$parentinfo[fathID]&tree=$tree\">$parentinfo[fathname]</a>" : "" ) . ( ( $parentinfo[fathname] . $parentinfo[mothname] ) ? ", " : "" ) . ( $parentinfo[mothname] ? "<a href=\"$getperson_url" . "personID=$parentinfo[mothID]&tree=$tree\">$parentinfo[mothname]</a>" : "" );
				if( $i != $locparentset && $pedigree[popupchartlinks] )
					buildInfoEntry( array( $i, $parentlink, "<a href=\"$pedigree_url" . "personID=$key&tree=$tree&parentset=$i&display=$display\">$pedigree[chartlink]</a>" ), $popupinfosizetouse );
				else 
					buildInfoEntry( array( $i, $parentlink, "" ), $popupinfosizetouse );
			}
		}
		
		//spouse info
		if( $pedigree[popupspouses] && (count($spiceIDs) > 0) ) {
			for ( $i = 1; $i <= count($spiceIDs); $i++ ) {
				buildInfoEntry ( array( "<B>$text[family]:</B> [<a href=\"$familygroup_url" . "familyID=$spicefamilyIDs[$i]&tree=$tree\">$text[groupsheet]</a>]" ), $popupinfosizetouse );
				$spouselink = $spiceIDs[$i] ? "<a href=\"$getperson_url" . "personID=" . $spiceIDs[$i] . "&tree=$tree\">" . $spiceNames[$i] . "</a>" : "$text[unknownlit]";
				if( $pedigree[popupchartlinks] && $spiceIDs[$i] )
					buildInfoEntry( array ( $i, $spouselink, "<a href=\"$pedigree_url" . "personID=" . $spiceIDs[$i] . "&tree=$tree&display=$display\">$pedigree[chartlink]</a>" ), $popupinfosizetouse );
				else
					buildInfoEntry( array ( $i, $spouselink, "" ), $popupinfosizetouse );

				// children
				if( $pedigree[popupkids] && $spicekidcount[$i] ) {
					buildInfoEntry ( array( "", "<B>$text[children]</B>"), $popupinfosizetouse );
					for ( $j = 1; $j <= $spicekidcount[$i]; $j++ ) {
						$kidlink = "<a href=\"$getperson_url" . "personID=" . $kidsIDs[$i][$j] . "&tree=$tree\">" . $kidsNames[$i][$j] . "</a>";
						if( $pedigree[popupchartlinks] )
							buildInfoEntry( array ( "&nbsp;", $pedigree[bullet], $kidlink, "<a href=\"$pedigree_url" . "personID=" . $kidsIDs[$i][$j] . "&tree=$tree&display=$display\">$pedigree[chartlink]</a>" ), $popupinfosizetouse );
						else
							buildInfoEntry( array ( "&nbsp;", $pedigree[bullet], $kidlink, "" ), $popupinfosizetouse );
					}
				}
			}
		}
		
		//end popup
		echo "</table></td></tr></table>$pedigree[enddiv]\n";		
	}

	//include trace (maybe)
	if ($demarkelements) 
		echo "\n<!-- box outline and shadow for $name -->\n\n";
		
	//line & shadow
	if ($browser == "NS4") { 
		echo "<layer bgcolor=\"" . $pedigree[bordercolor] . "\" top=" . ($offsetV-$pedigree[borderwidth]) . " left=" . ($offsetH-$pedigree[borderwidth]) . " height=" . ($boxheighttouse+(2*$pedigree[borderwidth])) . " width=" . ($pedigree[boxwidth]+(2*$pedigree[borderwidth])) . " z-index=4 clip=\"" . ($pedigree[boxwidth]+(2*$pedigree[borderwidth])) . "," . ($boxheighttouse+(2*$pedigree[borderwidth])) . "\">&nbsp;</layer>\n"; 
		echo "<layer bgcolor=\"" . $pedigree[shadowcolor] . "\" top=" . ($offsetV-$pedigree[borderwidth]+$pedigree[shadowoffset]) . " left=" . ($offsetH-$pedigree[borderwidth]+$pedigree[shadowoffset]) . " height=" . ($boxheighttouse+(2*$pedigree[borderwidth])) . " width=" . ($pedigree[boxwidth]+(2*$pedigree[borderwidth])) . " z-index=1 clip=\"" . ($pedigree[boxwidth]+(2*$pedigree[borderwidth])) . "," . ($boxheighttouse+(2*$pedigree[borderwidth])) . "\">&nbsp;</layer>\n";
	}
	else {
		echo "<div style=\"position:absolute; background-color:$pedigree[bordercolor]; top:" . ($offsetV-$pedigree[borderwidth]) . ";left:" . ($offsetH-$pedigree[borderwidth]) . ";height:" . ($boxheighttouse+(2*$pedigree[borderwidth])) . ";width:" . ($pedigree[boxwidth]+(2*$pedigree[borderwidth])) . ";z-index:4\"></div>\n";
		echo "<div style=\"position:absolute; background-color:$pedigree[shadowcolor]; top:" . ($offsetV-$pedigree[borderwidth]+$pedigree[shadowoffset]) . ";left:" . ($offsetH-$pedigree[borderwidth]+$pedigree[shadowoffset]) . ";height:" . ($boxheighttouse+(2*$pedigree[borderwidth])) . ";width:" . ($pedigree[boxwidth]+(2*$pedigree[borderwidth])) . ";z-index:1\"></div>\n";
	}

	// build left horizontal lines & shadows (except for first generation)
	if ($generation != 1) {
		if ($browser == "NS4") {
			echo "<layer bgcolor=\"" . $pedigree[bordercolor] . "\" top=" . ($offsetV + intval($boxheighttouse/2) - intval($pedigree[linewidth]/2)) . " left=" . ($offsetH - intval($pedigree[boxHsep]/2)) . " height=$pedigree[linewidth] width=" . (intval($pedigree[boxHsep]/2) + 2) . " z-index=3 clip=\"" . (intval($pedigree[boxHsep]/2) + 2) . ",$pedigree[linewidth]\">&nbsp;</layer>\n";
			echo "<layer bgcolor=\"" . $pedigree[shadowcolor] . "\" top=" . ($offsetV + intval($boxheighttouse/2) - intval($pedigree[linewidth]/2) + $pedigree[shadowoffset] + 1) . " left=" . (($offsetH - intval($pedigree[boxHsep]/2)) + $pedigree[shadowoffset] + 1) . " height=$pedigree[linewidth] width=" . (intval($pedigree[boxHsep]/2) + 2) . " z-index=1 clip=\"" . (intval($pedigree[boxHsep]/2) + 2) . ",$pedigree[linewidth]\">&nbsp;</layer>\n";
		}
		else {
			echo "<div style=\"position:absolute;background-color:$pedigree[bordercolor]; top:" . ($offsetV + intval($boxheighttouse/2) - intval($pedigree[linewidth]/2)) . ";left:" . ($offsetH - intval($pedigree[boxHsep]/2)) . ";height:$pedigree[linewidth];width:" . (intval($pedigree[boxHsep]/2) + 2) . ";z-index:3;clip:rect(auto auto " . $pedigree[linewidth] . "px auto)\"></div>\n";
			echo "<div style=\"position:absolute;background-color:$pedigree[shadowcolor]; top:" . ($offsetV + intval($boxheighttouse/2) - intval($pedigree[linewidth]/2) + $pedigree[shadowoffset] + 1) . ";left:" . (($offsetH - intval($pedigree[boxHsep]/2)) + $pedigree[shadowoffset] + 1) . ";height:$pedigree[linewidth];width:" . (intval($pedigree[boxHsep]/2) + 2) . ";z-index:1;clip:rect(auto auto " . $pedigree[linewidth] . "px auto)\"></div>\n";
		}
	}

	// build right horizontal line & shadow (except for last generation)
	if ($generation != $generations) {
		if ($browser == "NS4") {
			echo "<layer bgcolor=\"" . $pedigree[bordercolor] . "\" top=" . ($offsetV + intval($boxheighttouse/2) - intval($pedigree[linewidth]/2)) . " left=" . ($offsetH + $pedigree[boxwidth]) . " height=$pedigree[linewidth] width=" . (intval($pedigree[boxHsep]/2) + 1) . " z-index=3 clip=\"" . (intval($pedigree[boxHsep]/2) + 1) . ",$pedigree[linewidth]\">&nbsp;</layer>\n";
			echo "<layer bgcolor=\"" . $pedigree[shadowcolor] . "\" top=" . ($offsetV + intval($boxheighttouse/2) - intval($pedigree[linewidth]/2) + $pedigree[shadowoffset] + 1) . " left=" . ($offsetH + $pedigree[boxwidth] + $pedigree[shadowoffset] + 1) . " height=$pedigree[linewidth] width=" . (intval($pedigree[boxHsep]/2) + 1) . " z-index=1 clip=\"" . (intval($pedigree[boxHsep]/2) + 1) . ",$pedigree[linewidth]\">&nbsp;</layer>\n";
		}
		else {
			echo "<div style=\"position:absolute;background-color:$pedigree[bordercolor]; top:" . ($offsetV + intval($boxheighttouse/2) - intval($pedigree[linewidth]/2)) . ";left:" . ($offsetH + $pedigree[boxwidth]) . ";height:$pedigree[linewidth];width:" . (intval($pedigree[boxHsep]/2) + 1) . ";z-index:3;clip:rect(auto auto " . $pedigree[linewidth] . "px auto)\"></div>\n";
			echo "<div style=\"position:absolute;background-color:$pedigree[shadowcolor]; top:" . ($offsetV + intval($boxheighttouse/2) - intval($pedigree[linewidth]/2) + $pedigree[shadowoffset] + 1) . ";left:" . ($offsetH + $pedigree[boxwidth] + $pedigree[shadowoffset] + 1) . ";height:$pedigree[linewidth];width:" . (intval($pedigree[boxHsep]/2) + 1) . ";z-index:1;clip:rect(auto auto " . $pedigree[linewidth] . "px auto)\"></div>\n";
		}
	}

	// build vertical line & shadow
	if ($generation != 1  && $slot % 2 ==0 ) {
		if ($browser == "NS4") {
			echo "<layer bgcolor=\"" . $pedigree[bordercolor] . "\" top=" . ($offsetV + intval($boxheighttouse/2) - intval($pedigree[linewidth]/2)). " left=" . ($offsetH - intval($pedigree[boxHsep]/2)) . " height=" . ($sepV + $boxheighttouse) . " width=$pedigree[linewidth] z-index=3 clip=\"$pedigree[linewidth]," . ($sepV + $boxheighttouse) . "\">&nbsp;</layer>\n";
			echo "<layer bgcolor=\"" . $pedigree[shadowcolor] . "\" top=" . ($offsetV + intval($boxheighttouse/2) - intval($pedigree[linewidth]/2) + $pedigree[shadowoffset] + 1) . " left=" . ($offsetH - intval($pedigree[boxHsep]/2) + $pedigree[shadowoffset] + 1) . " height=" . ($sepV + $boxheighttouse) . " width=$pedigree[linewidth] z-index=1 clip=\"$pedigree[linewidth]," . ($sepV + $boxheighttouse) . "\">&nbsp;</layer>\n";
		}
		else {
			echo "<div style=\"position:absolute;background-color:$pedigree[bordercolor]; font-size:0;top:" . ($offsetV + intval($boxheighttouse/2) - intval($pedigree[linewidth]/2)). ";left:" . ($offsetH - intval($pedigree[boxHsep]/2)) . ";height:" . ($sepV + $boxheighttouse) . ";width:$pedigree[linewidth];z-index:3\"></div>\n";
			echo "<div style=\"position:absolute;background-color:$pedigree[shadowcolor]; font-size:0;top:" . ($offsetV + intval($boxheighttouse/2) - intval($pedigree[linewidth]/2) + $pedigree[shadowoffset] + 1) . ";left:" . ($offsetH - intval($pedigree[boxHsep]/2) + $pedigree[shadowoffset] + 1) . ";height:" . ($sepV + $boxheighttouse) . ";width:$pedigree[linewidth];z-index:1\"></div>\n";
		}
	}

	// see if we should include off-page connector
	if ( ( $nextslot >= $pedmax ) && $row[famc] ) { 
		if ($browser == "NS4") 
			echo "<layer top=" .  ( $offsetV + intval( ($boxheighttouse - $offpageimgh) / 2) + 1 ) . " left=" . ($offsetH + $pedigree[boxwidth] + $pedigree[borderwidth] + $pedigree[shadowoffset] + 3 ) . " z-index=5>\n";
		else 
			echo "<div style=\"position:absolute;top:" . ( $offsetV + intval( ($boxheighttouse - $offpageimgh) / 2) + 1 ) . ";left:" . ($offsetH + $pedigree[boxwidth] + $pedigree[borderwidth] + $pedigree[shadowoffset] + 3 ) . ";z-index:5\">\n";
		echo "<a href=\"$pedigree_url" . "personID=$key&tree=$tree&display=$display\">$pedigree[offpagelink]</a>$pedigree[enddiv]\n";
	}

	// do the look-ahead

	$generation++;
	if( $nextslot < $pedmax ) {
		$husband = ""; $wife = "";
		$marrdate[ $nextslot ] = "";
		$marrplace[ $nextslot ] = "";

		// get next parents pair

		if( $key ) {
			$query = "SELECT husband, wife, marrdate, marrplace, living FROM $families_table WHERE familyID = \"$parentfamID\" AND gedcom = \"$tree\"";
			$result2 = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
			if( $result2 ) {
				$newrow = mysql_fetch_assoc( $result2 );
				$husband = $newrow[husband];
				$wife = $newrow[wife];
				if( !$newrow[living] || $allow_living ) {
					$marrdate[ $nextslot ] = $newrow[marrdate];
					$marrplace[ $nextslot ] = $newrow[marrplace];
					$marrdate[ $nextslot + 1 ] = $newrow[marrdate];
					$marrplace[ $nextslot + 1 ] = $newrow[marrplace];
				}
				else {
					$marrdate[ $nextslot ] = "";
					$marrplace[ $nextslot ] = "";
					$marrdate[ $nextslot + 1 ] = "";
					$marrplace[ $nextslot + 1 ] = "";
				}
				mysql_free_result($result2);
			}
		}

		displayIndividual ( $husband, $generation, $nextslot, $wife, $key );
		$nextslot++;
		displayIndividual ( $wife, $generation, $nextslot, $husband, $key );
	}
}

// subroutine to get father/mother ids and names
function getParentInfo( $famid ) {
 	global $people_table, $families_table, $tree, $text, $nonames, $allow_living;

 	$parentarray = array();
	$query = "SELECT personID, lastname, firstname, $people_table.living FROM $people_table, $families_table WHERE $people_table.personID = $families_table.husband AND $families_table.familyID = \"$famid\" AND $families_table.gedcom = \"$tree\" AND $people_table.gedcom = \"$tree\"";
	$parentresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	if( $parentresult ) { 		
		$row =  mysql_fetch_assoc( $parentresult );
		$parentarray[fathID] = $row[personID];
		if( !$row[living] || $allow_living || !$nonames )
			$parentarray[fathname] = trim("$row[firstname] $row[lastname]");
		else
			$parentarray[fathname] = $text[living];
		mysql_free_result( $parentresult );
	}
	
	$query = "SELECT personID, lastname, firstname, $people_table.living FROM $people_table, $families_table WHERE $people_table.personID = $families_table.wife AND $families_table.familyID = \"$famid\" AND $families_table.gedcom = \"$tree\" AND $people_table.gedcom = \"$tree\"";
	$parentresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	if( $parentresult ) { 		
		$row =  mysql_fetch_assoc( $parentresult );
		$parentarray[mothID] = $row[personID];
		if( !$row[living] || $allow_living || !$nonames )
			$parentarray[mothname] = trim("$row[firstname] $row[lastname]");
		else
			$parentarray[mothname] = $text[living];
		mysql_free_result( $parentresult );
	}
	return $parentarray;
}

// subroutines used to single line of popup info box
function buildInfoEntry ( $columndata, $fontsize ){
	global $browser;
	
    if( $browser == "NS4" ) {
		$begfont = "<font point-size=$fontsize>";
		$endfont = "</font>";
	}
	else {
		$begfont = "<span style=\"font-size:$fontsize"."pt\">";
		$endfont = "</span>";
	}
	switch ( count($columndata) ) {
		case 1: // family heading .. 1 column of data
	    	echo "<tr>\n<td class=pboxpopup valign=top colspan=4>$begfont$columndata[0]</span></td></tr>\n";	
			break;
		
		case 2: // date/place or child/children heading ... 2 columns of data
	    	if ($columndata[0].$columndata[1] != "") {
				echo "<tr>\n<td class=pboxpopup align=right valign=top>$begfont";
				echo ( ( $columndata[0] == "" ? "&nbsp;" : ($columndata[0] . ":") ) . "&nbsp;" );
				echo "</span></td>\n";
				echo "<td class=pboxpopup valign=top colspan=3>$begfont$columndata[1]$endfont</td></tr>\n";
			}
		    break;
		
		case 3: // spouse entry ... 3 columns of data
		    echo "<tr>\n<td class=pboxpopup valign=top nowrap>$begfont<b>$columndata[0]</b>$endfont</td>\n";
			echo "<td class=pboxpopup valign=top nowrap colspan=2>$begfont$columndata[1]$endfont</td>\n";
			echo "<td class=pboxpopup valign=top align=right nowrap>$begfont&nbsp;$columndata[2]$endfont</td></tr>\n";
			break;
		
		case 4: // child entry ... 4 columns of data
		    echo "<tr>\n<td valign=top nowrap>$begfont$columndata[0]$endfont</td>\n";
			echo "<td class=pboxpopup valign=top nowrap>$begfont$columndata[1]&nbsp;$endfont</td>\n";
			echo "<td class=pboxpopup valign=top nowrap>$begfont$columndata[2]$endfont</td>\n";
			echo "<td class=pboxpopup valign=top align=right nowrap>$begfont&nbsp;$columndata[3]$endfont</td></tr>\n";
			break;
	}
}

// see if "popup info present" arrow is present
if (file_exists($cms[tngpath] . "ArrowDown.gif")) {
	$downarrow = getimagesize($cms[tngpath] . "ArrowDown.gif");
	$pedigree[downarroww] = $downarrow[0];
	$pedigree[downarrowh] = $downarrow[1];
	$pedigree[downarrow] = true;
}
else
	$pedigree[downarrow] = false;
	
// see if off-page connector arrow is present
if (file_exists($cms[tngpath] . "ArrowRight.gif")) {
	$offpageimg = getimagesize($cms[tngpath] . "ArrowRight.gif");
	$offpageimgw = $offpageimg[0];
	$offpageimgh = $offpageimg[1];
	$pedigree[offpagelink] = "<img border=\"0\" src=\"$cms[tngpath]" . "ArrowRight.gif\" width=\"$offpageimgw\" height=\"$offpageimgh\">";
}
else
	$pedigree[offpagelink] = "<b>&gt;</b>";
	
// see if chart link image is present
if (file_exists($cms[tngpath] . "Chart.gif")) {
	$chartlinkimg = getimagesize($cms[tngpath] . "Chart.gif");
	$chartlinkimgw = $chartlinkimg[0];
	$chartlinkimgh = $chartlinkimg[1];
	$pedigree[chartlink] = "<img src=\"$cms[tngpath]" . "Chart.gif\" border=\"0\" width=\"$chartlinkimgw\" height=\"$chartlinkimgh\" title=\"$text[popupnote2]\">";
}
else
	$pedigree[chartlink] = "<span class=\"normal\"><b>P</b></span>";
	
if( $pedigree[usepopups] ) {
	$pedigree[boxheight] = $pedigree[puboxheight];
	$pedigree[boxwidth] = $pedigree[puboxwidth];
	$pedigree[boxalign] = $pedigree[puboxalign];
	$pedigree[boxheightshift] = $pedigree[puboxheightshift];
}

// MOST OF THIS COULD BE HANDLED WITH JAVASCRIPT VALIDATION IN editpedconfig.php
// set boundary values if needed    
if( $pedigree[leftindent] < 0  ) $pedigree[leftindent]  = 0 ;
if( $pedigree[boxwidth] < 21 ) $pedigree[boxwidth]   = 21;
if( $pedigree[boxheight] < 21 ) $pedigree[boxheight]  = 21;
if( $pedigree[boxheightshift] > 0  )  $pedigree[boxheightshift] = -1 * $pedigree[boxheightshift];
if( $pedigree[boxHsep] < 7  ) $pedigree[boxHsep] = 7 ;
if( $pedigree[boxVsep] < 3 + $pedigree[shadowoffset] + (2*$pedigree[borderwidth]) + ($pedigree[downarrow] ? $pedigree[downarrowh] : 15) ) $pedigree[boxVsep] = 3 + $pedigree[shadowoffset] + (2*$pedigree[borderwidth]) + ($pedigree[downarrow] ? $pedigree[downarrowh] : 15) ;
if( $pedigree[borderwidth] < 1  ) $pedigree[borderwidth] = 1 ;
if( $pedigree[linewidth] < 1  ) $pedigree[linewidth] = 1 ;

// negative numbers ok for $pedigree[shadowoffset], $pedigree[colorshift], $fontshift)
// some values should be odd numbers ...    
if ($pedigree[boxwidth]  % 2 == 0) $pedigree[boxwidth]++;
if ($pedigree[boxheight] % 2 == 0) $pedigree[boxheight]++;
if ($pedigree[boxHsep]   % 2 == 0) $pedigree[boxHsep]++;
if ($pedigree[boxVsep]   % 2 == 0) $pedigree[boxVsep]++;
// and some even ...
if ($pedigree[boxheightshift] % 2 != 0) $pedigree[boxheightshift]++;

// if we are going to include photos, do we have what we need?
if ($pedigree[inclphotos] && (trim($photopath) == "" || trim($photosext) == "" )) $pedigree[inclphotos] = false;

// let's not shrink a box into nothingness
// boxheight must support at least 16 generations and not shrink below 16 pixels
if ($pedigree[boxheightshift] && ( $pedigree[boxheight] < -16 * $pedigree[boxheightshift] + 16 ) ) { $pedigree[boxheight] = -16 * $pedigree[boxheightshift] + 16 ; }

// how many generations to show?
if( !$pedigree[maxgen] ) $pedigree[maxgen] = 6;
if( $generations > $pedigree[maxgen] ) 
    $generations = $pedigree[maxgen];
else if( !$generations ) 
    $generations = 4;
else
	$generations = intval( $generations );
$pedmax = pow( 2, $generations );

// alternate parent display?
$parentset = intval($parentset);
    
// how much vertical real estate will we need?
$pedigree[maxheight] = pow( 2, ( $generations - 1 ) ) * ( ( $pedigree[boxheight] + ( $pedigree[boxheightshift] * ( $generations - 1 ) ) ) + $pedigree[boxVsep] );

// how much horizontal real estate will we need?
$pedigree[maxwidth] = $generations * ( $pedigree[boxwidth] + $pedigree[boxHsep] );

$key = $personID;
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
$pedigree[colorshift] = round( $pedigree[colorshift] / 100 * $extreme / ($generations + 1) );
$pedigree[phototree] = $tree;
if( $tree ) $pedigree[phototree] .= ".";

$pedigree[overflowtxt] = $pedigree[overflow] ? ";overflow:auto" : "";
$pedigree[cellpad] = 5;
if( $browser == "NS4" ) {
	$pedigree[bullet] = "-";
	$pedigree[enddiv] = "</layer>";
}
else {
	$pedigree[bullet] = "&bull;";
	$pedigree[enddiv] = "</div>";
}

writelog( "<a href=\"$pedigree_url" . "personID=$personID&tree=$tree&generations=$generations&display=$display\">$text[pedigreefor] $logname ($personID)</a> $generations $text[generations]" );

tng_header( "$text[pedigreefor] $pedname", "" );
?>

<SCRIPT language="JavaScript">
<!--
// define possible timers globally
for( var h = 1; h < <? echo pow( 2, $generations ); ?>; h++ ) {
	eval( 'var timer' + h + '=false' );
}

function showPopup( slot, tall, high ){
// hide any other currently visible popups
  for ( var i = 1; i < <? echo ( pow( 2, $generations ) ); ?>; i++ ) {
    if ( i != slot ) { 
		cancelTimer(i); 
		hidePopup(i);
	}
  }
  
// show current
  <? if ($browser == "NS4") { ?>	
  var ref = document.layers[0].layers["popup" + slot];
  <? } else { ?>
  var ref = document.all ? document.all["popup" + slot] : document.getElementById ? document.getElementById("popup" + slot) : null;
  
  if ( ref ) {ref = ref.style;}
  <? } ?>

  if ( ref.visibility != "show" && ref.visibility != "visible" ) {
    ref.top = ( tall + high ) < 0 ? 0 : ( tall + high + <? echo $pedigree[borderwidth]; ?> );
    ref.zIndex = 8;
    ref.visibility = <? echo( $browser == "NS4" ? "\"show\";\n" : "\"visible\";\n" ) ; ?>
  }
}

function setTimer(slot) {
	eval( "timer" + slot + "=setTimeout(\"hidePopup(" + slot + ")\",<? echo $pedigree[popuptimer]; ?>);");
}

function cancelTimer(slot) {
	eval( "clearTimeout(timer" + slot + ");" ); 
	eval( "timer" + slot + "=false;" );
}

function hidePopup(slot) {
<? if ($browser == "NS4") { ?>
  var ref = document.layers[0].layers["popup" + slot];
  if (ref) { ref.visibility = "hide"; }
<? } else { ?>
  var ref = document.all ? document.all["popup" + slot] : document.getElementById ? document.getElementById("popup" + slot) : null ;
  if (ref) { ref.style.visibility = "hidden"; }
<? } ?>
  eval("timer" + slot + "=false;");
}
//-->
</SCRIPT>

<p class="header">
<? 
	if( !$row[living] || $allow_living ) {
		$photoref = $tree ? "$photopath/$tree.$personID.$photosext" : "$photopath/$personID.$photosext";
		echo showSmallPhoto( $photoref, $pedname );
	}
	echo $pedname;
?>
<br clear="left">
</p>

<?
	echo tng_menu( "pedigree", $personID, 1 );
?>

<? if( $pedigree[maxgen] != 4 ) { ?>

<?
$formstr = getFORM( "pedigree", "", "", "" );
echo $formstr;

echo $text[generations]; ?>: 
<select name="generations">
<?
    for( $i = 1; $i <= $pedigree[maxgen]; $i++ ) {
        echo "<option value=\"$i\"";
        if( $i == $generations ) echo " selected";
        echo ">$i</option>\n";
    }
?>
</select>
&nbsp;<input type="radio" name="display" value="compact"<? if( $pedigree[usepopups] ) echo " checked"; ?>> <? echo $text[pedcompact]; ?>
&nbsp;<input type="radio" name="display" value="standard"<? if( !$pedigree[usepopups] ) echo " checked"; ?>> <? echo $text[pedstandard]; ?>
&nbsp;<input type="radio" name="display" value="textonly"> <? echo $text[pedtextonly]; ?>
&nbsp;<input type="submit" value="<? echo $text[go]; ?>"><br>(<? echo $text[scrollnote]; 
	if ( $pedigree[usepopups] ) {
		echo ( $pedigree[downarrow] ? " <img src=\"$cms[tngpath]" . "ArrowDown.gif\" width=\"$pedigree[downarroww]\" height=\"$pedigree[downarrowh]\">" : " <a href=\"#\"><span class=\"normal\"><B>V</B></span></a>" ) . $text[popupnote1];
		if ( $pedigree[popupchartlinks] ) 
			echo "&nbsp;&nbsp;$pedigree[chartlink] $text[popupnote2]"; 
	}
?>)
<input type="hidden" name="personID" value="<? echo $personID; ?>">
<input type="hidden" name="tree" value="<? echo $tree; ?>">
<input type="hidden" name="parentset" value=<? echo $parentset; ?>>
</form>
<? 
} 

echo ( $browser == "NS4" ? "<layer>" : "<div align=\"left\" style=\"position:relative;\">" ); 

$slot = 1;
displayIndividual( $personID, 1, $slot, 0, 0 );
?>

<table border="0" cellspacing="0" cellpadding="0" width="<? echo ($pedigree[borderwidth] + ($pedigree[maxwidth] - $pedigree[boxHsep]) + $pedigree[shadowoffset] + $pedigree[leftindent] + $offpageimgw + 3); ?>" height="<? echo ($pedigree[borderwidth] + ($pedigree[maxheight] - $pedigree[boxVsep]) + $pedigree[shadowoffset]); ?>">
<tr><td></td></tr></table>

<? 
	$flags[more] = "$pedigree[enddiv]\n";
	echo tng_menu( "pedigree", $personID, 2 );
	tng_footer( $flags ); 
?>
