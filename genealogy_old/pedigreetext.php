<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "pedigree";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
@set_time_limit(0);
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");
include($cms[tngpath] . "log.php" );

$getperson_url = getURL( "getperson", 1 );
$pedigree_url = getURL( "pedigree", 1 );
$searchform_noargs_url = getURL( "searchform", 0 );
$descend_url = getURL( "descend", 1 );
$gedform_url = getURL( "gedform", 1 );

function showBlank() {
	echo "<TD NOWRAP><span class=\"normal\">&nbsp;</span></td>\n";
	echo "<TD NOWRAP width=\"100%\"><span class=\"normal\">&nbsp;</span></td>\n</tr>\n";
	echo "<tr>\n<TD NOWRAP><span class=\"normal\">&nbsp;</span></td>\n";
	echo "<TD NOWRAP><span class=\"normal\">&nbsp;</span></td>\n</tr>\n";
}

function displayIndividual( $key, $generation, $slot ) {
	global $tree, $generations, $marrdate, $marrplace, $pedmax, $people_table, $families_table, $personID, $text, $allow_living, $nonames, $cms, $getperson_url, $pedigree_url;

	$nextslot = $slot * 2;
	$name = "";
	$row[birthdate] = "";
	$row[birthplace] = "";
	$row[altbirthdate] = "";
	$row[altbirthplace] = "";
	$row[deathdate] = "";
	$row[deathplace] = "";
	$row[burialdate] = "";
	$row[burialplace] = "";
	
	if( $key ) {
		$query = "SELECT firstname, lastname, living, famc, birthdate, birthplace, altbirthdate, altbirthplace, deathdate, deathplace, burialdate, burialplace FROM $people_table WHERE personID = \"$key\" AND gedcom = \"$tree\"";
		$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
		if( $result ) {
			$row = mysql_fetch_assoc( $result );
			if( !$row[living] || $allow_living || !$nonames )
				$name = trim("$row[firstname] $row[lastname]");
			else
				$name = $text[living];
			mysql_free_result($result);
		}
	}

	if( $slot > 1 && $slot % 2 != 0 )
		echo "</tr>\n<tr>\n";
	
	$rowspan = pow( 2, $generations - $generation );
	if( $rowspan == 1 )
		$vertfill = 8;
	else
		$vertfill = ($rowspan - 1) * 53 + 1;
		
	if( $slot > 1 && $slot % 2 != 0 )
		echo "<td rowspan=\"$rowspan\" valign=\"top\">\n";
	elseif( $slot % 2 == 0 )
		echo "<td rowspan=\"$rowspan\" valign=\"bottom\">\n";
	else
		echo "<td rowspan=\"$rowspan\">\n";

	if( $slot > 1 && $slot % 2 != 0 ) {
		echo "<table border=0 cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n<tr>\n";
		echo "<td width=1><IMG SRC=\"$cms[tngpath]" . "black.gif\" HEIGHT=$vertfill WIDTH=1 VSPACE=0 HSPACE=0 BORDER=0></td>\n";
		echo "<td width=\"100%\"></td>\n</tr>\n</table>\n";
	}
	else {
		echo "<table border=0 cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n<tr>\n";
		echo "<td colspan=\"2\" width=\"100%\"><IMG SRC=\"$cms[tngpath]" . "spacer.gif\" HEIGHT=$vertfill WIDTH=1 VSPACE=0 HSPACE=0 BORDER=0></td>\n</tr>\n</table>\n";
	}

	echo "<table border=0 cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
	echo "<tr>\n";
	if( $slot > 1 && $slot % 2 != 0 ) {
		echo "<td><IMG SRC=\"$cms[tngpath]" . "black.gif\" HEIGHT=15 WIDTH=1 VSPACE=0 HSPACE=0 BORDER=0></td>\n";
	}
	else {
		echo "<td><IMG SRC=\"$cms[tngpath]" . "spacer.gif\" WIDTH=1 HEIGHT=1 BORDER=0 HSPACE=0 VSPACE=0></td>\n";
	}
	echo "<TD NOWRAP colspan=\"2\"><span class=\"normal\">&nbsp;$slot. <a href=\"$getperson_url" . "personID=$key&tree=$tree\">$name</a>&nbsp;</span></td>\n";

	//arrow goes here in own cell
	if( $nextslot >= $pedmax && $row[famc] )
		echo "<td><span class=\"normal\"><a href=\"$pedigree_url" . "personID=$key&tree=$tree&display=textonly\">=&gt</a></span></td>\n";

	echo "</tr>\n";
	echo "<tr>\n<TD NOWRAP colspan=\"3\"><IMG SRC=\"$cms[tngpath]" . "black.gif\" WIDTH=\"100%\" HEIGHT=1 VSPACE=0 HSPACE=0 BORDER=0></TD>\n</tr>\n";
	echo "<tr>\n";
	if( $slot % 2 == 0 ) {
		echo "<td rowspan=\"6\"><IMG SRC=\"$cms[tngpath]" . "black.gif\" HEIGHT=90 WIDTH=1 VSPACE=0 HSPACE=0 BORDER=0></td>\n";
	}
	else {
		echo "<td rowspan=\"4\"><IMG SRC=\"$cms[tngpath]" . "spacer.gif\" WIDTH=1 HEIGHT=1 BORDER=0 HSPACE=0 VSPACE=0></td>\n";
	}
	if( !$row[living] || $allow_living ) {
		if( $row[birthdate] || $row[altbirthdate] || $row[altbirthplace] || $row[deathdate] || $row[burialdate] || $row[burialplace] || ( $slot % 2 == 0 && ( $marrdate[$slot] || $marrplace[$slot] ) ) )
			$dataflag = 1;
		else
			$dataflag = 0;
		if( $row[altbirthdate] && !$row[birthdate] ) {
			echo "<TD NOWRAP><span class=\"normal\">&nbsp;$text[capaltbirthabbr]:</span></td>\n";
			echo "<TD NOWRAP width=\"100%\"><span class=\"normal\">$row[altbirthdate]&nbsp;</span></td>\n</tr>\n";
			echo "<tr>\n<TD NOWRAP><span class=\"normal\">&nbsp;$text[capplaceabbr]:&nbsp;</span></td>\n";
			echo "<TD NOWRAP><span class=\"normal\">$row[altbirthplace]&nbsp;</span></td>\n</tr>\n";
		}
		elseif( $dataflag ) {
			echo "<TD NOWRAP><span class=\"normal\">&nbsp;$text[capbirthabbr]:</span></td>\n";
			echo "<TD NOWRAP width=\"100%\"><span class=\"normal\">$row[birthdate]&nbsp;</span></td></tr>\n";
			echo "<tr>\n<TD NOWRAP><span class=\"normal\">&nbsp;$text[capplaceabbr]:&nbsp;</span></td>\n";
			echo "<TD NOWRAP><span class=\"normal\">$row[birthplace]&nbsp;</span></td>\n</tr>\n";
		}
		else 
			showBlank();
		if( $slot % 2 == 0 ) {
			if( $dataflag ) {
				echo "<tr>\n<TD NOWRAP><span class=\"normal\">&nbsp;$text[capmarrabbr]:</span></td>\n";
				echo "<TD NOWRAP><span class=\"normal\">$marrdate[$slot]&nbsp;</span></td>\n</tr>\n";
				echo "<tr>\n<TD NOWRAP><span class=\"normal\">&nbsp;$text[capplaceabbr]:&nbsp;</span></td>\n";
				echo "<TD NOWRAP><span class=\"normal\">$marrplace[$slot]&nbsp;</span></td>\n</tr>\n";
			}
			else 
				showBlank();
		}
		if( $row[burialdate] && !$row[deathdate] ) {
			echo "<tr>\n<TD NOWRAP><span class=\"normal\">&nbsp;$text[capburialabbr]:</span></td>\n";
			echo "<TD NOWRAP><span class=\"normal\">$row[burialdate]&nbsp;</span></td>\n</tr>\n";
			echo "<tr>\n<TD NOWRAP><span class=\"normal\">&nbsp;$text[capplaceabbr]:&nbsp;</span></td>\n";
			echo "<TD NOWRAP><span class=\"normal\">$row[burialplace]&nbsp;</span></td>\n</tr>\n</table>\n";
		}
		elseif( $dataflag ) {
			echo "<tr>\n<TD NOWRAP><span class=\"normal\">&nbsp;$text[capdeathabbr]:</span></td>\n";
			echo "<TD NOWRAP><span class=\"normal\">$row[deathdate]&nbsp;</span></td></tr>\n";
			echo "<tr>\n<TD NOWRAP><span class=\"normal\">&nbsp;$text[capplaceabbr]:&nbsp;</span></td>\n";
			echo "<TD NOWRAP><span class=\"normal\">$row[deathplace]&nbsp;</span></td>\n</tr>\n</table>\n";
		}
		else
			showBlank();
	}
	else {
		showBlank();
		if( $slot % 2 == 0 )
			showBlank();
		showBlank();
	}
	
	if( $slot % 2 == 0 ) {
		echo "<table border=0 cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n<tr>\n";
		echo "<td width=1><IMG SRC=\"$cms[tngpath]" . "black.gif\" HEIGHT=$vertfill WIDTH=1 VSPACE=0 HSPACE=0 BORDER=0></td>\n";
		echo "<td width=\"100%\"></td>\n</tr>\n</table>\n";
	}
	else {
		echo "<table border=0 cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n<tr>\n";
		echo "<td colspan=\"2\" width=\"100%\"><IMG SRC=\"$cms[tngpath]" . "spacer.gif\" HEIGHT=$vertfill WIDTH=1 VSPACE=0 HSPACE=0 BORDER=0></td>\n</tr>\n</table>\n";
	}
	echo "</td>\n";
	
	$generation++;
	if( $nextslot < $pedmax ) {
		$husband = "";
		$wife = "";
		$marrdate[ $nextslot ] = "";
		$marrplace[ $nextslot ] = "";

		if( $key ) {
			$query = "SELECT husband, wife, marrdate, marrplace, living FROM $families_table WHERE familyID = \"$row[famc]\" AND gedcom = \"$tree\"";
			$result2 = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
			if( $result2 ) {
				$newrow = mysql_fetch_assoc( $result2 );
				$husband = $newrow[husband];
				$wife = $newrow[wife];
				if( !$newrow[living] || $allow_living ) {
					$marrdate[ $nextslot ] = $newrow[marrdate];
					$marrplace[ $nextslot ] = $newrow[marrplace];
				}
				else {
					$marrdate[ $nextslot ] = "";
					$marrplace[ $nextslot ] = "";
				}
				mysql_free_result($result2);
			}
		}
		displayIndividual( $husband, $generation, $nextslot );
		$nextslot++;
		displayIndividual( $wife, $generation, $nextslot );
	}
}

$query = "SELECT firstname, lastname, living, disallowgedcreate FROM $people_table, $trees_table WHERE personID = \"$personID\" AND $people_table.gedcom = \"$tree\" AND $people_table.gedcom = $trees_table.gedcom";
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
}

if( !$maxgen ) $maxgen = 6;
if( $generations > $maxgen ) 
	$generations = $maxgen;
else if( !$generations ) 
	$generations = 4;

$pedmax = pow( 2, intval($generations) );
$key = $personID;

writelog( "<a href=\"$pedigree_url" . "personID=$personID&tree=$tree&generations=$generations&display=textonly\">$text[pedigreefor] $logname ($personID)</a> $generations $text[generations]" );

tng_header( "$text[pedigreefor] $pedname", "" );
?>

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
	if( $maxgen != 4 ) { 

$formstr = getFORM( "pedigree", "", "", "" );
echo $formstr;

echo $text[generations]; ?>: 
<select name="generations">
<?
    for( $i = 1; $i <= $maxgen; $i++ ) {
        echo "<option value=\"$i\"";
        if( $i == $generations ) echo " selected";
        echo ">$i</option>\n";
    }
?>
</select>
&nbsp;<input type="radio" name="display" value="compact"> <? echo $text[pedcompact]; ?>
&nbsp;<input type="radio" name="display" value="standard"> <? echo $text[pedstandard]; ?> 
&nbsp;<input type="radio" name="display" value="textonly" checked> <? echo $text[pedtextonly]; ?>
&nbsp;<input type="submit" value="<? echo $text[go]; ?>"><br>(<? echo $text[scrollnote]; ?>)
<input type="hidden" name="personID" value="<? echo $personID; ?>">
<input type="hidden" name="tree" value="<? echo $tree; ?>">
</form>
<? } ?>

<table border=0 cellspacing="0" cellpadding="0">
<tr>
<?
$slot = 1;
displayIndividual( $personID, 1, $slot );
?>
</tr>
</table>

<p class="normal">
<? 
	echo "<a href=\"$searchform_noargs_url\">$text[newsearch]</a> | <a href=\"$getperson_url" . "personID=$personID&tree=$tree\">$text[indinfo]</a> | <a href=\"$descend_url" . "personID=$personID&tree=$tree\">$text[descendchart]</a> "; 
	if( !$disallowgedcreate )
		echo "| <a href=\"$gedform_url" . "personID=$personID&tree=$tree\">$text[extractgedcom]</a> ";
	if( $allow_edit )
		echo "| <a href=\"$cms[tngpath]" . "admin/editperson.php?personID=$personID&tree=$tree\">$text[edit]</a> ";
?>
| <a href="<? echo $homepage; ?>" target="<? echo $target; ?>"><? echo $text[homepage]; ?></a></p>
<?
	echo tng_menu( "pedigree", $personID, 2 );
	tng_footer( "" ); 
?>
