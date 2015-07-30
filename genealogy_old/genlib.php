<?
if( !$tree && $defaulttree ) 
	$tree = $defaulttree;
elseif( $tree == "-x--all--x-" ) 
	$tree = "";

function db_connect($dbhost,$dbname,$dbusername,$dbpassword) {
	$link = @mysql_pconnect($dbhost, $dbusername, $dbpassword);
	if( $link && mysql_select_db($dbname))
		return $link;
	return( FALSE );
}

function tng_header( $title, $flags ) {
	global $custommeta, $customheader, $cms;
	include($cms[tngpath] . "version.php");
	
	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n\n";
	echo "<!-- $tng_title, v.$tng_version ($tng_date), Written by Darrin Lythgoe, $tng_copyright -->\n";
	if( !$cms[support] ) 
		echo "<html>\n<head>\n";
	else
		echo $cms[credits];
	echo "<title>$title</title>\n";
	include( $custommeta );
	if( isset( $flags[norobots] ) )
		echo "<meta name=\"robots\" content=\"noindex,nofollow\">\n";
	if( !$cms[support] ) {
		echo "</head>\n";
		include( $customheader );
	}
}

function tng_footer( $flags ) {
	global $customfooter, $cms;
	
	include( $customfooter ); 
	if( isset( $flags[more] ) ) 
		echo $flags[more];
	include($cms[tngpath] . "end.php" );
}

function showSmallPhoto( $photoref, $alttext ) {
	global $rootpath;
	
	if( file_exists( "$rootpath$photoref" ) ) {
		$photoinfo = getimagesize( "$rootpath$photoref" );
		if( $photoinfo[1] < 50 ) {
			$photohtouse = $photoinfo[1];
			$photowtouse = $photoinfo[0];
		}
		else {
			$photohtouse = 50;
			$photowtouse = intval( 50 * $photoinfo[0] / $photoinfo[1] ) ;
		}
		$photo = "<img src=\"$photoref\" border=\"1\" alt=\"$alttext\" width=\"$photowtouse\" height=\"$photohtouse\" align=\"left\">";
	}
	else
		$photo = "";
	
	return $photo;
}

function tng_menu( $currpage, $personID, $instance ) {
	global $tree, $text, $disallowgedcreate, $target, $allow_admin, $allow_edit, $target, $homepage, $chooselang, $languages_table, $mylanguage, $currentuser;
	global $REQUEST_URI, $HTTP_HOST, $SCRIPT_NAME, $QUERY_STRING, $cms;

	$getperson_url = getURL( "getperson", 1 );
	$pedigree_url = getURL( "pedigree", 1 );
	$descend_url = getURL( "descend", 1 );
	$gedform_url = getURL( "gedform", 1 );
	$logout_url = getURL( "logout", 1 );
	$login_noargs_url = getURL( "login", 0 );
	$searchform_noargs_url = getURL( "searchform", 0 );

	$returnpage = "http://" . $HTTP_HOST;
	$returnpage .= $REQUEST_URI ? $REQUEST_URI : $SCRIPT_NAME . "?" . $QUERY_STRING;

	$menu = "<span class=\"normal\">";
	$menu .= getFORM( "savelanguage2", "GET", "menu$instance", "" );
	$menu .= "<input type=\"hidden\" name=\"returnpage\" value=\"$returnpage\">";
	$menu .= "<a href=\"$homepage\" target=\"$target\">$text[homepage]</a>";
	$menu .= $currpage == "search" ? " | $text[newsearch]" : " | <a href=\"$searchform_noargs_url\">$text[newsearch]</a>";
	if( $personID && substr($personID,0,1) == "I" ) {
		$menu .= $currpage == "person" ? " | $text[indinfo]" : " | <a href=\"$getperson_url" . "personID=$personID&tree=$tree\">$text[indinfo]</a>";
		$menu .= $currpage == "pedigree" ? " | $text[pedigree]" : " | <a href=\"$pedigree_url" . "personID=$personID&tree=$tree\">$text[pedigree]</a>";
		$menu .= $currpage == "descend" ? " | $text[descendchart]" : " | <a href=\"$descend_url" . "personID=$personID&tree=$tree\">$text[descendchart]</a>";
		if( !$disallowgedcreate )
			$menu .= $currpage == "gedcom" ? " | $text[extractgedcom]" : " | <a href=\"$gedform_url" . "personID=$personID&tree=$tree\">$text[extractgedcom]</a>";
		$editstr = "person";
	}
	else
		$editstr = "family";

	if( $currentuser ) {
		if( $personID && $allow_admin && $allow_edit )
			$menu .= " | <a href=\"$cms[tngpath]" . "admin/edit$editstr.php?$editstr" . "ID=$personID&tree=$tree\">$text[edit]</a>";
		if( !$cms[cloaklogin] || $cms[cloaklogin] == "both" ) 
			$menu .= " | <a href=\"$logout_url" . "session=" . session_name() . "\" target=\"$target\">$text[logout]</a>";
		if( $cms[support] ) $menu .= " | User: $currentuser ";
	}
	else {
		if( !$cms[cloaklogin] || $cms[cloaklogin] == "both" ) {
			$changefield = !$cms[support] ? "action='login.php'" : "file.value='login'";
			$menu .= " | <a href=\"#\" onClick=\"document.menu$instance.$changefield;document.menu$instance.submit();\">$text[login]</a>";
		}
		elseif( $cms[support] ) $menu .= " | User: anonymous ";
	}
	if( $chooselang ) {
		$query = "SELECT display, folder FROM $languages_table ORDER BY display";
		$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
		
		if( mysql_num_rows( $result ) ) {
			$menu .= " &nbsp;<select name=\"newlanguage$instance\" style=\"font-size: 10px;\" onChange=\"document.menu$instance.submit();\">";

			while( $row = mysql_fetch_assoc($result)) {
				$menu .= "<option value=\"$row[folder]\"";
				if( $row[folder] == $mylanguage )
					$menu .= " selected";
				$menu .= ">$row[display]</option>\n";
			}
			$menu .= "</select>\n";
			$menu .= "<input type=\"hidden\" name=\"instance\" value=\"$instance\">";
		}
		mysql_free_result($result);
	}
	$menu .= "</form></span>\n";
	
	return $menu;
}

function getURL( $destination, $args ) {
        global $cms;

	if( $cms[support] )
		$url = $args ? "$cms[url]=$destination&" : "$cms[url]=$destination";
	else
		$url = $args ? $destination . ".php?" : $destination . ".php";

	return $url;
}

function getFORM( $action, $method, $name, $id ) {
	global $cms;

	if( !$cms[support] ) 
		$url = $action . ".php";
	else 
		$url = "modules.php";

	$formstr = "<form action=\"$url\"";
	if( $method )
		$formstr .= " method=\"$method\"";
	if( $name ) 
		$formstr .= " name=\"$name\"";
	if( $id ) 
		$formstr .= " id=\"$id\"";

	$formstr .= ">\n";

	if( $cms[support] ) {
		$formstr .= "<input type=\"hidden\" name=\"op\" value=\"modload\">\n";
		$formstr .= "<input type=\"hidden\" name=\"name\" value=\"$cms[module]\">\n";
		$formstr .= "<input type=\"hidden\" name=\"file\" value=\"$action\">\n";
	}

	return $formstr;
}
?>
