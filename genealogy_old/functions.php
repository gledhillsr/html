<?php

function get_photo_id( $result, $offset ){
    if( !mysql_data_seek($result, $offset ) )
		return(0);
	$row = mysql_fetch_assoc( $result );
	return( $row[photoID] );
}

function get_photo_offsets( $result, $photoID ) {
	mysql_data_seek ($result, 0);
	for( $i = 0; $i < mysql_num_rows($result); $i++ ) {
    	$row = mysql_fetch_assoc( $result );
        if( $row[photoID] == $photoID ) {
        	break;
        }
    }
	$nexttolast = mysql_num_rows( $result ) - 1;
	$prev = $i ? $i - 1 : $nexttolast;
	$next = $i < $nexttolast ? $i + 1 : 0;
	
    return array( $i, $prev, $next, $nexttolast );
}

function get_showphoto_nav( $result, $address, $perpage, $pagenavpages ) {
	global $page, $totalpages, $personID, $tree, $text;

	$total = mysql_num_rows($result);

	if( !$page ) $page = 1;
	if( $total <= $perpage )
		return "";

	$totalpages = ceil($total/$perpage);
	if ($page > $totalpages ) $page = $totalpages;

	if( $page > 1 ) {
		$prevpage = $page - 1;
		$photoID = get_photo_id( $result, $prevpage - 1 );
		$prevlink = " <a href=\"$address=$photoID&personID=$personID&tree=$tree&ordernum=$prevpage&page=$prevpage\" title=\"$text[text_prev]\">&laquo;$text[text_prev]</a> ";
	}
	if( $page < $totalpages ) {
		$nextpage = $page + 1;
		$photoID = get_photo_id( $result, $nextpage - 1 );
		$nextlink = "<a href=\"$address=$photoID&personID=$personID&tree=$tree&ordernum=$nextpage&page=$nextpage\" title=\"$text[text_next]\">$text[text_next]&raquo;</a>";
	}
	while( $curpage++ < $totalpages ) {
		$photoID = get_photo_id( $result, $curpage - 1 );
		if( ( $curpage <= $page-$pagenavpages || $curpage >= $page+$pagenavpages ) && $pagenavpages!=0 ) {
			if( $curpage == 1 ) {
				$photoID = get_photo_id( $result, $curpage );
				$firstlink = " <a href=\"$address=$photoID&personID=$personID&tree=$tree&ordernum=$curpage&page=$curpage\" title=\"$text[firstpage]\">&laquo;1</a> ... ";
			}
		    if( $curpage == $totalpages )
				$lastlink = "... <a href=\"$address=$photoID&personID=$personID&tree=$tree&ordernum=$curpage&page=$curpage\" title=\"$text[lastpage]\">$totalpages&raquo;</a>";
		} 
		else {
			if ($curpage==$page)
				$pagenav .= " <font size=\"2\">[$curpage]</font> ";
			else
				$pagenav .= " <a href=\"$address=$photoID&personID=$personID&tree=$tree&ordernum=$curpage&page=$curpage\">$curpage</a> ";
		}
	}
	$pagenav = "$firstlink $prevlink $pagenav $nextlink $lastlink";
	
	return $pagenav;
}

function get_browseitems_nav( $total, $address, $perpage, $pagenavpages ) {
	global $page, $totalpages, $tree, $text;

	if ( !$page ) $page = 1;

	if( $total <= $perpage )
		return "";

	$totalpages = ceil( $total / $perpage );
	if ( $page > $totalpages ) $page = $totalpages;

	if( $page > 1 ) {
		$prevpage = $page-1;
		$navoffset = ( ( $prevpage * $perpage ) - $perpage );
		$prevlink = " <a href=\"$address=$navoffset&tree=$tree&page=$prevpage\" title=\"$text[text_prev]\">&laquo;$text[text_prev]</a> ";
	}
	if ($page<$totalpages) {
		$nextpage = $page+1;
		$navoffset = (($nextpage * $perpage) - $perpage);
		$nextlink = "<a href=\"$address=$navoffset&tree=$tree&page=$nextpage\" title=\"$text[text_next]\">$text[text_next]&raquo;</a>";
	}
	while( $curpage++ < $totalpages ) {
   	$navoffset = ( ($curpage - 1 ) * $perpage );
		if( ( $curpage <= $page - $pagenavpages || $curpage >= $page + $pagenavpages ) && $pagenavpages ) {
			if( $curpage == 1 )
				$firstlink = " <a href=\"$address=$navoffset&tree=$tree&page=$curpage\" title=\"$text[firstpage]\">&laquo;1</a> ... ";
		    if( $curpage == $totalpages )
				$lastlink = "... <a href=\"$address=$navoffset&tree=$tree&page=$curpage\" title=\"$text[lastpage]\">$totalpages&raquo;</a>";
		}
		else {
			if( $curpage == $page )
				$pagenav .= " <font size=\"2\">[$curpage]</font> ";
			else
				$pagenav .= " <a href=\"$address=$navoffset&tree=$tree&page=$curpage\">$curpage</a> ";
		}
	}
	$pagenav = "$firstlink $prevlink $pagenav $nextlink $lastlink";
	
	return $pagenav;
}
?>
