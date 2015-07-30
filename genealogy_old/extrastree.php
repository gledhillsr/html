<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "extras";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");
include($cms[tngpath] . "log.php" );

$pedigree_url = getURL( "pedigree", 1 );
$getextras_url = getURL( "getextras", 1 );

$generations = 12;

function displayIndividual( $key, $generation, $slot, $column ) {
	global $columns, $tree, $generations, $pedmax, $people_table, $families_table, $personID, $text, $photolinks_table, $doclinks_table, $col1fam, $col2fam;
	global $allow_living, $cms, $getextras_url;

	$nextslot = $slot * 2;
	$name = "";
	
	if( $key ) {
		$query = "SELECT firstname, lastname, living, famc, IF(birthdate!='',YEAR(birthdatetr),YEAR(altbirthdatetr)) as birthyear, IF(deathdate!='',YEAR(deathdatetr),YEAR(burialdatetr)) as deathyear FROM $people_table WHERE personID = \"$key\" AND gedcom = \"$tree\"";
		$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
		if( $result ) {
			$row = mysql_fetch_assoc( $result );

			if( $generation == 2 ) {
				if( $slot == 2 )
					$col1fam = $row[lastname];
				else
					$col2fam = $row[lastname];
			}
			
			if( !$row[living] || $allow_living ) {
				$photoquery = "SELECT count(ID) as photocount FROM $photolinks_table WHERE personID = \"$key\" AND gedcom = \"$tree\"";
				$photoresult = mysql_query($photoquery) or die ("$text[cannotexecutequery]: $photoquery");
				if( $photoresult ) {
					$photorow = mysql_fetch_assoc( $photoresult );
					mysql_free_result( $photoresult );
				}
				else
					$photorow[photocount] = 0;
				
				$docquery = "SELECT count(ID) as doccount FROM $doclinks_table WHERE personID = \"$key\" AND gedcom = \"$tree\"";
				$docresult = mysql_query($docquery) or die ("$text[cannotexecutequery]: $docquery");
				if( $docresult ) {
					$docrow = mysql_fetch_assoc( $docresult );
					mysql_free_result( $docresult );
				}
				else
					$docrow[doccount] = 0;
	
				if( $photorow[photocount] || $docrow[doccount] ) {
					if( !isset($columns[$column][$generation]) )
						$columns[$column][$generation] = "$text[generation]: $generation<br>";
					$birthyear = $row[birthyear] ? $row[birthyear] : "?";
					$deathyear = $row[deathyear] ? $row[deathyear] : "?";
					$columns[$column][$generation] .= "<li><a href=\"$getextras_url" . "personID=$key&tree=$tree\">$row[lastname], $row[firstname]</a> ($birthyear - $deathyear)";
					if( $photorow[photocount] )
						$columns[$column][$generation] .= " <img src=\"$cms[tngpath]" . "photo.gif\" width=\"14\" height=\"13\" border=\"0\" alt=\"photo available\">";
					if( $docrow[doccount] )
						$columns[$column][$generation] .= " <img src=\"$cms[tngpath]" . "doc.gif\" width=\"11\" height=\"13\" border=\"0\" alt=\"history available\">";
					$columns[$column][$generation] .= "</li><br>\n";
				}
			}
			mysql_free_result($result);
		}
	}

	$generation++;
	if( $nextslot < $pedmax ) {
		$husband = "";
		$wife = "";

		if( $key ) {
			$query = "SELECT husband, wife FROM $families_table WHERE familyID = \"$row[famc]\" AND gedcom = \"$tree\"";
			$result2 = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
			if( $result2 ) {
				$newrow = mysql_fetch_assoc( $result2 );
				$husband = $newrow[husband];
				$wife = $newrow[wife];
				mysql_free_result($result2);
			}
		}
		if( !$column ) {
			$leftcolumn = 1;
			$rightcolumn = 2;
		}
		else
			$leftcolumn = $rightcolumn = $column;
		displayIndividual( $husband, $generation, $nextslot, $leftcolumn );
		$nextslot++;
		displayIndividual( $wife, $generation, $nextslot, $rightcolumn );
	}
}

$query = "SELECT firstname, lastname, living, disallowgedcreate FROM $people_table, $trees_table WHERE personID = \"$personID\" AND $people_table.gedcom = \"$tree\" AND $people_table.gedcom = $trees_table.gedcom";
$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
if( $result ) {
	$row = mysql_fetch_assoc( $result );
	$pedname = trim("$row[firstname] $row[lastname]");
	$logname = $nonames && $row[living] ? $text[living] : $pedname;
	$disallowgedcreate = $row[disallowgedcreate];
	mysql_free_result($result);
}

$columns = array();

$pedmax = pow( 2, intval($generations) );
$key = $personID;

writelog( "<a href=\"$pedigree_url" . "personID=$personID&tree=$tree&generations=$generations&display=textonly\">$text[pedigreefor] $logname ($personID)</a> $generations $text[generations]" );

tng_header( $text[extras], "" );
?>

<p class="header"><? echo $text[extras]; ?></p>
<p class="subhead">
<? 
	if( !$row[living] || $allow_living ) {
		$photoref = $tree ? "$photopath/$tree.$personID.$photosext" : "$photopath/$personID.$photosext";
		echo showSmallPhoto( $photoref, $pedname );
	}
	echo "<strong>$text[familyof]<br>$pedname</strong>";
?>
<br clear="left">
</p>
<?
echo tng_menu( "", $personID, 1 );
$slot = 1;
displayIndividual( $personID, 1, $slot, 0 );

//echo $columns[0][1];
?>
<table border=0 cellspacing="0" cellpadding="0">
<tr>
	<td valign="top" nowrap>
		<p class="subhead"><strong><? echo "$col1fam $text[side]"; ?></strong></p>
		<span class="normal">
<?
	for( $nextgen = 2; $nextgen <= $generations; $nextgen++ ) {
		if( $columns[1][$nextgen] ) {
			echo $columns[1][$nextgen];
			echo "<br>\n";
		}
	}
?>
		</span>
	</td>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td valign="top" nowrap>
		<p class="subhead"><strong><? echo "$col2fam $text[side]"; ?></strong></p>
		<span class="normal">
<?
	for( $nextgen = 2; $nextgen <= $generations; $nextgen++ ) {
		if( $columns[2][$nextgen] ) {
			echo $columns[2][$nextgen];
			echo "<br>\n";
		}
	}
?>
		</span>
	</td>
</tr>
</table>

<?
echo tng_menu( "", $personID, 2 );
tng_footer( "" );
?>
