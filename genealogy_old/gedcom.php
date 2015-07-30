<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "gedcom";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");
include($cms[tngpath] . "log.php" );

$gedcom_url = getURL( "gedcom", 1 );

@set_time_limit(0);
$allsources = array();
$xnotes = array();

$query = "SELECT disallowgedcreate FROM $trees_table WHERE gedcom = \"$tree\"";
$treeresult = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
$treerow = mysql_fetch_assoc($treeresult);
if( $treerow[disallowgedcreate] ) exit;
mysql_free_result( $treeresult );

function getAncestor ( $person, $generation ) {
	global $tree, $maxgcgen, $indarray, $people_table, $text;

	$query = "SELECT personID, famc FROM $people_table WHERE personID = \"$person\" AND gedcom = \"$tree\"";
	$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	if( $result ) {
		$ind = mysql_fetch_assoc( $result );
		//do individual, but only do spouse if this is the first generation (all others covered as children of succeeding generations)
		if( $generation == 1 ) {
			$indarray[$ind[personID]] = writeIndividual( $ind[personID] );
			getDescendant( $ind[personID], 0 );
		}
		else {
			$indarray[$ind[personID]] = writeIndividual( $ind[personID] );
		}
		if( $ind[famc] && ( $generation < $maxgcgen ) ) {
			getFamily( $person, $ind[famc], $generation + 1 );
		}
		mysql_free_result( $result );
	}
}

function getCitations( $persfamID ) {
	global $citations_table, $text, $tree;

	$citations = array();
	$citquery = "SELECT citationID, page, quay, citedate, citetext, note, sourceID, description, eventID FROM $citations_table WHERE persfamID = \"$persfamID\" AND gedcom = \"$tree\" ORDER BY eventID";
	$citresult = mysql_query($citquery) or die ("$text[cannotexecutequery]: $query");
	
	$lasteventID = "";
	while( $cite = mysql_fetch_assoc( $citresult ) ) {
		if( $cite[eventID] != $lasteventID ) {
			$citectr = 1;
			$lasteventID = $cite[eventID];
		}
		else
			$citectr++;
		$eventID = $lasteventID ? $lasteventID : "NAME";
		$citations[$eventID][$citectr] = array( "page" => $cite[page], "quay" => $cite[quay], "citedate" => $cite[citedate], "citetext" => $cite[citetext], "note" => $cite[note], "sourceID" => $cite[sourceID], "description" => $cite[description] );
	}	
	return $citations;
}

function writeCitation( $citelist, $level ) {
	global $allsources;
	
	$levelplus1 = $level + 1;
	$citestr = "";
	
	$citecount = count( $citelist );
	foreach( $citelist as $cite ) {
		if( $cite[sourceID] ) {
			array_push( $allsources, $cite[sourceID] );
			$citestr .= "$level SOUR @$cite[sourceID]@\r\n";
			if( $cite[citedate] || $cite[TEXT] ) {
				$levelplus2 = $level + 2;
				$citestr .= "$levelplus1 DATA\r\n";
				if( $cite[citedate] )
					$citestr .= "$levelplus2 DATE $cite[citedate]\r\n";
				if( $cite[citetext] )
					$citestr .= writeNote( $levelplus2, "TEXT", $cite[citetext] );
			}
		}
		else {
			$citestr = "$level SOUR $cite[description]\r\n";
			if( $cite[citetext] )
				$citestr .= writeNote( $levelplus1, "TEXT", $cite[citetext] );
		}
		if( $cite[page] ) $citestr .= "$levelplus1 PAGE $cite[page]\r\n";
		if( $cite[quay] ) $citestr .= "$levelplus1 QUAY $cite[quay]\r\n";
		if( $cite[note] ) $citestr .= writeNote( $levelplus1, "NOTE", $cite[note] );
	}
	
	return $citestr;
}

function getNotes( $id ) {
	global $notelinks_table, $xnotes_table, $tree, $eventtypes_table, $text, $xnotes;
	
	$query = "SELECT $notelinks_table.ID as ID, $notelinks_table.note as note, $xnotes_table.noteID as noteID, $notelinks_table.eventID FROM $notelinks_table LEFT JOIN $xnotes_table on $notelinks_table.noteID = $xnotes_table.noteID AND $notelinks_table.gedcom = $xnotes_table.gedcom WHERE $notelinks_table.persfamID=\"$id\" AND $notelinks_table.gedcom =\"$tree\" ORDER BY eventID";
	$notelinks = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$notearray = array();
	while( $notelink = mysql_fetch_assoc( $notelinks ) ) {
		$noteid = $notelink[eventID] ? $notelink[eventID] : "-x--general--x-";
		$newnote = $notelink[note] ? $notelink[note] : "@$notelink[noteID]@";
		if( $notearray[$noteid] ) 
			$notearray[$noteid] .= "\n$newnote";
		else
			$notearray[$noteid] = $newnote;
		if( $notelink[noteID] && !in_array( $notelink[noteID], $xnotes ) )
			array_push( $xnotes, $notelink[noteID] );
	}
	mysql_free_result( $notelinks );
	
	return $notearray;
}

function writeNote( $level, $label, $notetxt ) {
	$noteinfo = "";
	$notes = split ( chr(10), $notetxt );
	$note = array_shift( $notes );
	$noteinfo .= "$level $label $note\r\n";
	$level++;
	foreach ( $notes as $note ) {
		$noteinfo .= "$level CONT $note\r\n";
	}
	return $noteinfo;
}

function doXNotes( ) {
	global $xnotes_table, $tree, $text, $xnotes;
	
	if( $xnotes ) {
		$xnoteinfo = "";
		foreach ( $xnotes as $xnote ) {
			$query = "SELECT note FROM $xnotes_table WHERE gedcom =\"$tree\" AND noteID = \"$xnote\" ORDER BY noteID";
			$xnotearray = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
			$xnotetxt = mysql_fetch_assoc( $xnotearray );
			echo "0 @$xnote@ NOTE\r\n"; 
			
			$notes = split ( chr(10), $xnotetxt[note] );
			foreach ( $notes as $note ) {
				echo "1 CONT $note\r\n";
			}
			mysql_free_result( $xnotearray );
		}
	}
}

function getFamily ( $person, $parents, $generation ) {
	global $tree, $famarray, $indarray, $families_table, $children_table, $people_table, $text;
	
	$query = "SELECT * FROM $families_table WHERE familyID = \"$parents\" AND gedcom = \"$tree\"";
	$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	if( $result ) {
		$family = mysql_fetch_assoc( $result );
		mysql_free_result( $result );

		$famarray[$parents] = writeFamily( $family );

		if( $family[husband] ) {
			getAncestor( $family[husband], $generation );
			$query = "SELECT wife, familyID FROM $families_table WHERE husband = \"$family[husband]\" AND gedcom = \"$tree\"";
			$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
			if( $result ) {
				while( $spouse = mysql_fetch_assoc( $result ) ) {
					if( $spouse[wife] != $family[wife] ) {
						$indarray[$spouse[wife]] = writeIndividual( $spouse[wife] );
						$indarray[$spouse[wife]] .= "1 FAMS @$spouse[familyID]@\r\n";
					}
					$indarray[$family[husband]] .= "1 FAMS @$spouse[familyID]@\r\n";
				}
				mysql_free_result( $result );
			}
		}
		
		if( $family[wife] ) {
			getAncestor( $family[wife], $generation );
			$query = "SELECT husband, familyID FROM $families_table WHERE wife = \"$family[wife]\" AND gedcom = \"$tree\"";
			$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
			if( $result ) {
				while( $spouse = mysql_fetch_assoc( $result ) ) {
					if( $spouse[husband] != $family[husband] ) {
						$indarray[$spouse[husband]] = writeIndividual( $spouse[husband] );
						$indarray[$spouse[husband]] .= "1 FAMS @$spouse[familyID]@\r\n";
					}
					$indarray[$family[wife]] .= "1 FAMS @$spouse[familyID]@\r\n";
				}
				mysql_free_result( $result );
			}
		}
		if( $generation > 1 ) {
			$query = "SELECT familyID, $children_table.personID as personID, sealdate, sealplace, living FROM $children_table, $people_table WHERE familyID = \"$parents\" AND $children_table.gedcom = \"$tree\" AND $children_table.personID = $people_table.personID AND $children_table.gedcom = $people_table.gedcom";
			$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
			if( $result ) {
				while( $child = mysql_fetch_assoc( $result ) ) {
					if( $child[personID] != $person ) {
						$indarray[$child[personID]] = writeIndividual( $child[personID] );
						getDescendant( $child[personID], 0 );
					}
					$indarray[$child[personID]] .= appendParents( $child );
					$famarray[$parents] .= "1 CHIL @$child[personID]@\r\n";
				}
				mysql_free_result( $result );
			}
		}
	}
}

function appendParents( $child ) {
	global $lds, $allow_lds, $allow_living;
	
	$info = "1 FAMC @$child[familyID]@\r\n";
	if( ( !$child[living] || $allow_living ) && $allow_living && $lds == "yes" ) { 
		if( $child[sealdate] || $child[sealplace] ) {
			$childnotes = getNotes( $child[personID] );
			$citations = getCitations( $child[personID] . $child[familyID] );

			$info .= "1 SLGC\r\n";
			$info .= "2 FAMC @$child[familyID]@\r\n";
			if( $child[sealdate] ) { $info .= "2 DATE $child[sealdate]\r\n"; }
			if( $child[sealplace] ) { 
				$tok = strtok ($child[sealplace]," ");
				if( strlen( $tok ) == 5 ) {
					$info .= "2 TEMP $tok\r\n"; 
					$tok = strtok( " " );
					if( $tok )
						$info .= "2 PLAC $tok\r\n"; 
				}
				else
					$info .= "2 PLAC $child[sealplace]\r\n"; 
			}
			if( $childnotes[SLGC] )
				$info .= writeNote( 2, "NOTE", $childnotes[SLGC] );
			if( $citations[SLGC] ) { 
				$info .= writeCitation( $citations[SLGC] );
			}
		}
	}
	
	return $info;
}

function writeIndividual( $person ) {
	global $tree, $lds, $people_table, $events_table, $eventtypes_table, $text, $allow_living, $allow_lds, $nonames;
	
	$query = "SELECT lastname, firstname, sex, title, suffix, nickname, birthdate, birthplace, altbirthdate, altbirthplace, deathdate, deathplace, burialdate, burialplace, baptdate, baptplace, endldate, endlplace, famc, notes, living FROM $people_table WHERE personID = \"$person\" AND gedcom = \"$tree\"";
	$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	if( $result ) {
		$ind = mysql_fetch_assoc( $result );
		if( !$ind[living] || $allow_living )
			$indnotes = getNotes( $person );
		else
			$indnotes = array();
		
		$citations = getCitations( $person );
		
		$info = "0 @$person@ INDI\r\n";
		if( !$ind[living] || $allow_living || !$nonames ) {
			$info .= "1 NAME $ind[firstname] /$ind[lastname]/\r\n";
			if( $indnotes[NAME] )
				$info .= writeNote( 2, "NOTE", $indnotes[NAME] );
			if( $ind[suffix] ) {
				$info .= "2 NSFX $ind[suffix]\r\n";
				if( $indnotes[NSFX] )
					$info .= writeNote( 3, "NOTE", $indnotes[NSFX] );
			}
			if( $ind[nickname] ) {
				$info .= "2 NICK $ind[nickname]\r\n";
				if( $indnotes[NICK] )
					$info .= writeNote( 3, "NOTE", $indnotes[NICK] );
			}
			if( $ind[title] ) {
				$info .= "1 TITL $ind[title]\r\n";
				if( $indnotes[TITL] )
					$info .= writeNote( 2, "NOTE", $indnotes[TITL] );
			}
			$info .= "1 SEX $ind[sex]\r\n";
			if( $citations[NAME] )
				$info .= writeCitation( $citations[NAME], 1 );
		}
		else
			$info .= "1 NAME $text[living] //\r\n";

		if( !$ind[living] || $allow_living ) {
			if( $ind[birthdate] || $ind[birthplace] ) {
				$info .= "1 BIRT\r\n";
				if( $ind[birthdate] ) { $info .= "2 DATE $ind[birthdate]\r\n"; }
				if( $ind[birthplace] ) { $info .= "2 PLAC $ind[birthplace]\r\n"; }
				if( $indnotes[BIRT] )
					$info .= writeNote( 2, "NOTE", $indnotes[BIRT] );
				if( $citations[BIRT] )
					$info .= writeCitation( $citations[BIRT], 2 );
			}
			if( $ind[altbirthdate] || $ind[altbirthplace] ) {
				$info .= "1 CHR\r\n";
				if( $ind[altbirthdate] ) { $info .= "2 DATE $ind[altbirthdate]\r\n"; }
				if( $ind[altbirthplace] ) { $info .= "2 PLAC $ind[altbirthplace]\r\n"; }
				if( $indnotes[CHR] )
					$info .= writeNote( 2, "NOTE", $indnotes[CHR] );
				if( $citations[CHR] ) { 
					$info .= writeCitation( $citations[CHR], 2 );
				}
			}
		}
		if( $ind[deathdate] || $ind[deathplace] ) {
			$info .= "1 DEAT\r\n";
			if( $ind[deathdate] ) { $info .= "2 DATE $ind[deathdate]\r\n"; }
			if( $ind[deathplace] ) { $info .= "2 PLAC $ind[deathplace]\r\n"; }
			if( $indnotes[DEAT] )
				$info .= writeNote( 2, "NOTE", $indnotes[DEAT] );
			if( $citations[DEAT] ) { 
				$info .= writeCitation( $citations[DEAT], 2 );
			}
		}
		if( $ind[burialdate] || $ind[burialplace] ) {
			$info .= "1 BURI\r\n";
			if( $ind[burialdate] ) { $info .= "2 DATE $ind[burialdate]\r\n"; }
			if( $ind[burialplace] ) { $info .= "2 PLAC $ind[burialplace]\r\n"; }
			if( $indnotes[BURI] )
				$info .= writeNote( 2, "NOTE", $indnotes[BURI] );
			if( $citations[BURI] ) { 
				$info .= writeCitation( $citations[BURI], 2 );
			}
		}
		if( $ind[famc] ) {
			$info .= "1 FAMC @$ind[famc]@\r\n";
		}
		
		if( !$ind[living] || $allow_living ) {
			$query = "SELECT tag, description, eventdate, eventplace, info, eventID FROM $events_table, $eventtypes_table WHERE persfamID = \"$person\" AND $events_table.eventtypeID = $eventtypes_table.eventtypeID AND type = \"I\" AND gedcom = \"$tree\" AND keep = \"1\" ORDER BY ordernum";
			$custevents = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
			while ( $custevent = mysql_fetch_assoc( $custevents ) ) {
				$info .= "1 $custevent[tag]";
				if( $custevent[info] ) { $info .= " $custevent[info]"; }
				$info .= "\r\n";
				if( $custevent[description] ) $info .= "2 TYPE $custevent[description]\r\n";
				if( $custevent[eventdate] )  $info .= "2 DATE $custevent[eventdate]\r\n"; 
				if( $custevent[eventplace] )  $info .= "2 PLAC $custevent[eventplace]\r\n"; 
				$eventID = $custevent[eventID];
				if( $indnotes[$eventID] )
					$info .= writeNote( 2, "NOTE", $indnotes[$eventID] );
				if( $citations[$eventID] )
					$info .= writeCitation( $citations[$eventID], 2 );
			}
			
			if( $allow_lds && $lds == "yes" ) {
				if( $ind[baptdate] || $ind[baptplace] ) {
					$info .= "1 BAPL\r\n";
					if( $ind[baptdate] ) { $info .= "2 DATE $ind[baptdate]\r\n"; }
					if( $ind[baptplace] ) { 
						$tok = strtok ($ind[baptplace]," ");
						if( strlen( $tok ) == 5 ) {
							$info .= "2 TEMP $tok\r\n"; 
							$tok = strtok( " " );
							if( $tok )
								$info .= "2 PLAC $tok\r\n"; 
						}
						else
							$info .= "2 PLAC $ind[baptplace]\r\n"; 
					}
					if( $indnotes[BAPL] )
						$info .= writeNote( 2, "NOTE", $indnotes[BAPL] );
					if( $citations[BAPL] ) { 
						$info .= writeCitation( $citations[BAPL], 2 );
					}
				}
				if( $ind[endldate] || $ind[endlplace] ) {
					$info .= "1 ENDL\r\n";
					if( $ind[endldate] ) { $info .= "2 DATE $ind[endldate]\r\n"; }
					if( $ind[endlplace] ) { 
						$tok = strtok ($ind[endlplace]," ");
						if( strlen( $tok ) == 5 ) {
							$info .= "2 TEMP $tok\r\n"; 
							$tok = strtok( " " );
							if( $tok )
								$info .= "2 PLAC $tok\r\n"; 
						}
						else
							$info .= "2 PLAC $ind[endlplace]\r\n"; 
					}
					if( $indnotes[ENDL] )
						$info .= writeNote( 2, "NOTE", $indnotes[ENDL] );
					if( $citations[ENDL] ) { 
						$info .= writeCitation( $citations[ENDL], 2 );
					}
				}
			}
			if( $ind[notes] )
				$info .= writeNote( 1, "NOTE", $ind[notes] );
			if( $indnotes['-x--general--x-'] )
				$info .= writeNote( 1, "NOTE", $indnotes['-x--general--x-'] );
		}
		mysql_free_result( $result );
	}
	return $info;
}

function writeFamily( $family ) {
	global $tree, $lds, $events_table, $eventtypes_table, $text, $allow_living, $allow_lds;
	
	$familyID = $family[familyID];
	$famnotes = getNotes( $familyID );
	
	$citations = getCitations( $familyID );
	
	$info = "0 @$familyID@ FAM\r\n";
	if( $family[status] ) { $info .= "1 _STAT $family[status]\r\n"; }
	if( $family[husband] )
		$info .= "1 HUSB @$family[husband]@\r\n"; 
	if( $family[wife] )
		$info .= "1 WIFE @$family[wife]@\r\n"; 
	
	//look up husband, look up wife, get living
	
	if( !$family[living] || $allow_living ) {
		if( $family[marrdate] || $family[marrplace] ) {
			$info .= "1 MARR\r\n";
			if( $family[marrdate] ) { $info .= "2 DATE $family[marrdate]\r\n"; }
			if( $family[marrplace] ) { $info .= "2 PLAC $family[marrplace]\r\n"; }
			if( $famnotes[MARR] )
				$info .= writeNote( 2, "NOTE", $famnotes[MARR] );
			if( $citations[MARR] ) { 
				$info .= writeCitation( $citations[MARR], 2 );
			}
		}
	
		$query = "SELECT tag, description, eventdate, eventplace, info, eventID FROM $events_table, $eventtypes_table WHERE persfamID = \"$person\" AND $events_table.eventtypeID = $eventtypes_table.eventtypeID AND type = \"F\" AND gedcom = \"$tree\" AND keep = \"1\" ORDER BY ordernum";
		$custevents = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
		while ( $custevent = mysql_fetch_assoc( $custevents ) ) {
			$info .= "1 $custevent[tag]";
			if( $custevents[info] ) { $info .= " $custevent[info]"; }
			$info .= "\r\n";
			if( $custevent[description] ) $info .= "2 TYPE $custevent[description]\r\n";
			if( $custevent[eventdate] )  $info .= "2 DATE $custevent[eventdate]\r\n"; 
			if( $custevent[eventplace] )  $info .= "2 PLAC $custevent[eventplace]\r\n"; 
			$eventID = $custevent[eventID];
			if( $famnotes[$eventID] )
				$info .= writeNote( 2, "NOTE", $famnotes[$eventID] );
			if( $citations[$eventID] ) { 
				$info .= writeCitation( $citations[$eventID] );
			}
		}
			
		if( $allow_lds && $lds == "yes" ) {
			if( $family[sealdate] || $family[sealplace] ) {
				$info .= "1 SLGS\r\n";
				if( $family[sealdate] ) { $info .= "2 DATE $family[sealdate]\r\n"; }
				if( $family[sealplace] ) { 
					$tok = strtok ($family[sealplace]," ");
					if( strlen( $tok ) == 5 ) {
						$info .= "2 TEMP $tok\r\n"; 
						$tok = strtok( " " );
						if( $tok )
							$info .= "2 PLAC $tok\r\n"; 
					}
					else
						$info .= "2 PLAC $fam[sealplace]\r\n"; 
				}
				if( $famnotes[SLGS] )
					$info .= writeNote( 2, "NOTE", $famnotes[SLGS] );
				if( $citations[SLGS] ) { 
					$info .= writeCitation( $citations[SLGS] );
				}
			}
		}
		if( $famnotes[$familyID] )
			$info .= writeNote( 1, "NOTE", $famnotes[$familyID] );
		if( $family[notes] )
			$info .= writeNote( 1, "NOTE", $family[notes] );
	}

	return $info;
}

function processEntities ( $entarray ) {
	foreach( $entarray as $thisent ) {
		echo $thisent;
	}
}

function getDescendant( $person, $generation ) {
	global $tree, $maxgcgen, $famarray, $indarray, $families_table, $children_table, $people_table, $text;
	
	$query = "SELECT * FROM $families_table WHERE (husband = \"$person\" OR wife = \"$person\") AND gedcom = \"$tree\"";
	$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	if( $result ) {
		while( $family = mysql_fetch_assoc( $result ) ) {
			if( $family[husband] == $person ) {
				$self = "husband";
				$spouse = "wife";
			}
			else {
				$self = "wife";
				$spouse = "husband";
			}
			$famarray[$family[familyID]] = writeFamily( $family );
			$indarray[$family[$spouse]] = writeIndividual( $family[$spouse] );
			$indarray[$family[$spouse]] .= "1 FAMS @$family[familyID]@\r\n";
			$indarray[$person] .= "1 FAMS @$family[familyID]@\r\n";
			
			if( $generation > 0 ) {
				$query = "SELECT $children_table.personID as personID, living FROM $children_table, $people_table WHERE familyID = \"$family[familyID]\" AND $children_table.personID = $people_table.personID AND $children_table.gedcom = \"$tree\" AND $people_table.gedcom = \"$tree\" ORDER BY ordernum";
				$result2 = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
				if( $result2 ) {
					while( $child = mysql_fetch_assoc( $result2 ) ) {
						$indarray[$child[personID]] = writeIndividual( $child[personID] );
						$indarray[$child[personID]] .= appendParents( $child );
						$famarray[$family[familyID]] .= "1 CHIL @$child[personID]@\r\n";
						if( $generation < $maxgcgen ) {
							getDescendant( $child[personID], $generation + 1 );
						}
					}
				}
				mysql_free_result( $result2 );
			}
		}
	}
	mysql_free_result( $result );
}

function doSources( ) {
	global $tree, $sources_table, $events_table, $eventtypes_table, $allsources, $text;
	
	$newsources = array_unique( $allsources );
	if( $newsources ) {
		foreach( $newsources as $nextsource ) {
			$srcquery = "SELECT * FROM $sources_table WHERE sourceID = \"$nextsource\" AND gedcom = \"$tree\"";
			$srcresult = mysql_query($srcquery) or die ("$text[cannotexecutequery]: $query");
			if( $srcresult ) {
				$source = mysql_fetch_assoc( $srcresult );
				echo "0 @$source[sourceID]@ SOUR\r\n"; 
				if( $source[callnum] ) { echo "1 CALN $source[callnum]\r\n"; }
				if( $source[title] ) { echo "1 TITL $source[title]\r\n"; }
				if( $source[shorttitle] ) { echo "1 ABBR $source[shorttitle]\r\n"; }
				if( $source[author] ) { echo "1 AUTH $source[author]\r\n"; }
				if( $source[publisher] ) { echo "1 PUBL $source[publisher]\r\n"; }

				$query = "SELECT tag, description, eventdate, eventplace, info FROM $events_table, $eventtypes_table WHERE persfamID = \"$source[sourceID]\" AND $events_table.eventtypeID = $eventtypes_table.eventtypeID AND type = \"S\" AND gedcom = \"$tree\" AND keep = \"1\" ORDER BY ordernum";
				$custevents = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
				while ( $custevent = mysql_fetch_assoc( $custevents ) ) {
					$info .= "1 $custevent[tag]";
					if( $custevents[info] ) { $info .= " $custevent[info]"; }
					$info .= "\r\n";
					if( $custevent[description] ) $info .= "2 TYPE $custevent[description]\r\n";
					if( $custevent[eventdate] )  $info .= "2 DATE $custevent[eventdate]\r\n"; 
					if( $custevent[eventplace] )  $info .= "2 PLAC $custevent[eventplace]\r\n"; 
				}

				if( $source[actualtext] ) {
					$srcnote = writeNote( 1, "TEXT", $source[actualtext] );
					echo $srcnote;
				}
			}
		}
	}
}

if( $maxgcgen > 0 || $type == "all" ) {
	if( $maxgcgen > 999 ) {
		$maxgcgen = 999;
	}

	$query = "SELECT firstname, lastname, living FROM $people_table WHERE personID = \"$personID\" AND $people_table.gedcom = \"$tree\"";
	$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	if( $result ) {
		$row = mysql_fetch_assoc($result);
		if( !$row[living] || $allow_living || !$nonames )
			$namestr = trim("$row[firstname]$row[lastname]"); //no space intentional
		else
			$namestr = $text[living];
		$logname = $nonames && $row[living] ? $text[living] : $namestr;
		mysql_free_result($result);
	}

	writelog( "<a href=\"$gedcom_url" . "personID=$personID&tree=$tree&name=$logname&email=REPEAT&type=$type&maxgen=$maxgcgen&lds=$lds\">$text[gedcreatedfrom] $logname ($personID), $maxgcgen $text[generations] ($type)</a> $text[gedcreatedfor] $email." );

	header("Content-type: application/ged"); 
	header("Content-Disposition: attachment; filename=$namestr.ged\n\n");
	$firstpart = "0 HEAD\r\n"
	. "1 FILE $namestr.ged\r\n"
	. "1 GEDC\r\n"
	. "2 VERS 5.5\r\n"
	. "2 FORM LINEAGE-LINKED\r\n"
	. "1 CHAR ANSI\r\n"
	. "1 SUBM @SUB1@\r\n"
	. "0 @SUB1@ SUBM\r\n"
	. "1 NAME $dbowner\r\n"
	. "1 _EMAIL $emailaddr\r\n";
	
	echo $firstpart;

	$generation = 1;
	
	if( $type == $text[ancestors] ) {
		getAncestor( $personID, $generation );
	}
	else if( $type == $text[descendants] ) {
		$indarray[$personID] = writeIndividual( $personID );
		getDescendant( $personID, $generation );
	}
	else if( $type == "all" ) {
		$query = "SELECT personID, sex FROM $people_table WHERE gedcom = \"$tree\"";
		$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
		while( $ind = mysql_fetch_assoc( $result ) ) {
			$indarray[$ind[personID]] = writeIndividual( $ind[personID] );
			$query = "";
			if( $ind[sex] == "M" )
				$query = "SELECT familyID FROM $families_table WHERE husband = \"$ind[personID]\" AND gedcom = \"$tree\" ORDER BY wifeorder";
			else if( $ind[sex] == "F" )
				$query = "SELECT familyID FROM $families_table WHERE wife = \"$ind[personID]\" AND gedcom = \"$tree\" ORDER BY husborder";
			if( $query ) {
				$result2 = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
				while( $spouse = mysql_fetch_assoc( $result2 ) )
					$indarray[$ind[personID]] .= "1 FAMS @$spouse[familyID]@\r\n";
				mysql_free_result( $result2 );
			}
			echo $indarray[$ind[personID]];
		}
		mysql_free_result( $result );
		
		$query = "SELECT * FROM $families_table WHERE gedcom = \"$tree\"";
		$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
		while( $fam = mysql_fetch_assoc( $result ) ) {
			$famarray[$fam[familyID]] = writeFamily( $fam );
			
			$query = "SELECT personID as personID FROM $children_table WHERE familyID = \"$fam[familyID]\" AND gedcom = \"$tree\" ORDER BY ordernum";
			$result2 = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
			if( $result2 ) {
				while( $child = mysql_fetch_assoc( $result2 ) )
					$famarray[$fam[familyID]] .= "1 CHIL @$child[personID]@\r\n";
			}
			mysql_free_result( $result2 );
			echo $famarray[$fam[familyID]];
		}
		mysql_free_result( $result );
	}
	else {
		echo "error - no type.\n";
	}
	if( $type != "all" ) {
		processEntities( $indarray );
		processEntities( $famarray );
	}
	
	doSources();
	doXNotes();
	
	echo "0 TRLR\r\n";
}
else {
	tng_header( "Error", "" );
	echo "<h1>Error</h1>\n<p>maxgen = $maxgcgen. $text[nomaxgen]</p>\n";
	echo tng_menu( "", "", 1 );
	tng_footer( "" );
}
?>
