<?php
function getBirthInfo( $thisperson ) {
	global $text;
	
	$birthstring = "";
	if( $thisperson[birthdate] ) {
		$birthstring .= ", $text[birthabbr] $thisperson[birthdate]";
		if( $thisperson[birthplace] )
			$birthstring .= ", $thisperson[birthplace]";
	}
	else if( $thisperson[altbirthdate] ) {
		$birthstring .= ", $text[chrabbr] $thisperson[altbirthdate]";
		if( $thisperson[altbirthplace] ) 
			$birthstring .= ", $thisperson[altbirthplace]";
	}
	return $birthstring;
}

function getCitations( $persfamID ) {
	global $sources_table, $text, $tree, $citations_table, $citations, $citationsctr, $citedisplay, $cms, $showsource_url;

	$citquery = "SELECT citationID, title, shorttitle, author, other, publisher, callnum, page, quay, citedate, citetext, $citations_table.note as note, $citations_table.sourceID, description, eventID FROM $citations_table LEFT JOIN $sources_table on $citations_table.sourceID = $sources_table.sourceID AND $sources_table.gedcom = $citations_table.gedcom WHERE persfamID = \"$persfamID\" AND $citations_table.gedcom = \"$tree\" ORDER BY citationID";
	$citresult = mysql_query($citquery) or die ("$text[cannotexecutequery]: $citquery");
		
	while( $citrow = mysql_fetch_assoc($citresult) ) {
		$source = $citrow[sourceID] ? "[<a href=\"$showsource_url" . "sourceID=$citrow[sourceID]&tree=$tree\">$citrow[sourceID]</a>] &nbsp;" : "";
		$newstring = $source ? "" : "<a href=\"$showsource_url" . "sourceID=$citrow[sourceID]&tree=$tree\">$citrow[description]</a>";
		$key = $persfamID . "_" . $citrow[eventID];
		$citationsctr++;
		$citations[$key] .= $citations[$key] ? ",$citationsctr" : $citationsctr;

		if( $citrow[shorttitle] ) {
			if( $newstring ) $newstring .= ", ";
			$newstring .= $citrow[shorttitle];
		}
		else if( $citrow[title] ) {
			if( $newstring ) $newstring .= ", ";
			$newstring .= $citrow[title];
		}
		if( $citrow[author] ) {
			if( $newstring ) $newstring .= ", ";
			$newstring .= $citrow[author];
		}
		if( $citrow[publisher] ) {
			if( $newstring ) $newstring .= ", ";
			$newstring .= "($citrow[publisher])";
		}
		if( $citrow[callnum] ) {
			if( $newstring ) $newstring .= ", ";
			$newstring .= "$citrow[callnum].";
		}
		if( $citrow[other] ) {
			if( $newstring ) $newstring .= ", ";
			$newstring .= $citrow[other];
		}
		if( $citrow[page] ) {
			if( $newstring ) $newstring .= ", ";
			$newstring .= $citrow[page];
		}
		if( $citrow[quay] ) {
			if( $newstring ) $newstring .= " ";
			$newstring .= "($text[reliability]: $citrow[quay])";
		}
		if( $citrow[citedate] ) {
			if( $newstring ) $newstring .= ", ";
			$newstring .= $citrow[citedate];
		}
		if( $citrow[citetext] ) {
			if( $newstring ) $newstring .= "<br>\n";
			$newstring .= "\"" . nl2br($citrow[citetext]) . "\"";
		}
		if( $citrow[note] ) {
			if( $newstring ) $newstring .= "<br>\n";
			$newstring .= "\"" . nl2br($citrow[note]) . "\"";
		}
		$citedisplay[$citationsctr] = "$source $newstring";
	}
	mysql_free_result($citresult);
}

function reorderCitation( $citekey ) {
	global $citedispctr, $citestring, $citations, $citedisplay;

	$newstring = "";
	if( $citations[$citekey] ) {
		$citationlist = explode( ',', $citations[$citekey] );
		foreach( $citationlist as $citation ) {
			$citedispctr++;
			$citestring .= "$citedispctr. $citedisplay[$citation]<br />\n";
			$newstring .= $newstring ? ",<a href=\"#sources\">$citedispctr</a>" : "<a href=\"#sources\">$citedispctr</a>";
		}
		$citations[$citekey] = "";
	}
	return $newstring;
}

function getNotes( $persfamID, $flag ) {
	global $notelinks_table, $xnotes_table, $tree, $eventtypes_table, $events_table, $text, $eventswithnotes;
	
	$custnotes = array();
	$precustnotes = array();
	$postcustnotes = array();
	$finalnotesarray = array();
	
	if( $flag == "I" ) {
		$precusttitles = array( "BIRT"=>$text[born], "CHR"=>$text[christened], "NAME"=>$text[name], "TITL"=>$text[title], "NSFX"=>$text[suffix], "NICK"=>$text[nickname], "BAPL"=>$text[baptizedlds], "ENDL"=>$text[endowedlds] );
		$postcusttitles = array( "DEAT"=>$text[died], "BURI"=>$text[buried], "SLGC"=>$text[sealedplds] );
	}
	else {
		$precusttitles = array( "MARR"=>$text[married], "SLGS"=>$text[sealedslds] );
		$postcusttitles = array();
	}
		
	$query = "SELECT display, $notelinks_table.note as note1, $xnotes_table.note as note2, $notelinks_table.eventID as eventID FROM $notelinks_table LEFT JOIN  $xnotes_table on $notelinks_table.noteID = $xnotes_table.noteID AND $notelinks_table.gedcom = $xnotes_table.gedcom LEFT JOIN $events_table ON $notelinks_table.eventID = $events_table.eventID LEFT JOIN $eventtypes_table on $eventtypes_table.eventtypeID = $events_table.eventtypeID WHERE $notelinks_table.persfamID=\"$persfamID\" AND $notelinks_table.gedcom =\"$tree\" ORDER BY ordernum, tag, description";
	$notelinks = mysql_query($query) or die ("$text[cannotexecutequery]: $query");

	$currevent = "";
	$type = 0;
	while( $note = mysql_fetch_assoc( $notelinks ) ) {
		if( !$note[eventID] ) $note[eventID] = "--x-general-x--";
		if( $note[eventID] != $currevent ) {
			$currevent = $note[eventID];
			$currtitle = "";
		}
		if( !$currtitle ) {
			if( $note[display] ) {
				$currtitle = $note[display];
				$key = "cust$currevent";
				$custnotes[$key] = array( "title"=>$currtitle, "text"=>"");
				$type = 2;
			}
			else {
				if( $postcusttitles[$currevent] ) {
					$currtitle = $postcusttitles[$currevent];
					$postcustnotes[$currevent] = array( "title"=>$postcusttitles[$currevent], "text"=>"");
					$type = 3;
				}
				else {
					$currtitle = $precusttitles[$currevent] ? $precusttitles[$currevent] : " ";
					$precustnotes[$currevent] = array( "title"=>$precusttitles[$currevent], "text"=>"");
					$type = 1;
				}
			}
		}
		switch( $type ) {
			case 1:
				$precustnotes[$currevent][text] .= "<li>" . nl2br($note[note1] . $note[note2]) . "</li>\n";
				break;
			case 2:
				$custnotes[$key][text] .= "<li>" . nl2br($note[note1] . $note[note2]) . "</li>\n";
				break;
			case 3:
				$postcustnotes[$currevent][text] .= "<li>" . nl2br($note[note1] . $note[note2]) . "</li>\n";
				break;
		}
	}
	$finalnotesarray = array_merge( $precustnotes, $custnotes, $postcustnotes );	
	
	return $finalnotesarray;
}

function buildNotes( $notearray ) {
	$notes = "";
	foreach( $notearray as $key => $note ) {
		if( $notes )
			$notes .= "<br>\n";
		if( $note[title] )
			$notes .= "<a name=\"$key\">$note[title]</a>:<br>\n";
		$notes .= "<ul>\n" . $note[text] . "</ul>\n";
	}
	return $notes;
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

function showEvent( $data ) {
	global $citations, $notestogether;

	$dateplace = $data[date] || $data[place] ? 1 : 0;
	$rows = $dateplace;
	if( $data[fact] ) $rows++;
	if( $data[note] && $notestogether ) $rows++;
	$output = "";
	$cite = $data[citekey] ? reorderCitation( $data[citekey] ) : "";
	
	$preoutput = "<tr>\n";
	$preoutput .= "<td valign=\"top\" class=\"fieldnameback\" nowrap rowspan=\"$rows\"><span class=\"fieldname\">$data[text]&nbsp;</span></td>\n";
	if( $dateplace ) {
		$output .= "<td valign=\"top\" class=\"databack\"><span class=\"normal\"><nobr>$data[date]</nobr>&nbsp;</span></td>\n";
		$output .= "<td valign=\"top\" width=\"80%\" class=\"databack\"><span class=\"normal\">$data[place]";
		if( $cite ) {
			$output .= "&nbsp; [$cite]";
			$cite = "";
		}
		$output .= "&nbsp;</span></td>\n";
		$output .= "</tr>\n";
	}
	if( $data[fact] ) {
		if( $output ) $output .= "<tr>\n";
		$output .= "<td valign=\"top\" colspan=\"2\" class=\"databack\"><span class=\"normal\">" . nl2br( $data[fact] );
		if( $cite ) {
			$output .= "&nbsp; [$cite]";
			$cite = "";
		}
		$output .= "&nbsp;</span></td>\n";
		$output .= "</tr>\n";
	}
	if( $data[note] && $notestogether ) {
		if( $output ) $output .= "<tr>\n";
		$output .= "<td valign=\"top\" colspan=\"2\" class=\"databack\"><span class=\"normal\">" . $data[note][text];
		if( $cite ) {
			$output .= "&nbsp; [$cite]";
			$cite = "";
		}
		$output .= "&nbsp;</span></td>\n";
		$output .= "</tr>\n";
	}
	if( $output )
		echo $preoutput . $output;
}

function showBreak( ) {
	echo "<tr><td height=\"3\" colspan=\"3\"><font size=\"1\">&nbsp;</font></td></tr>\n";
}
?>