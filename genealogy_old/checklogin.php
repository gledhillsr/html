<?php
	session_start();
	session_register('logged_in');
	session_register('assignedtree');
	session_register('postvars');
	session_register('destinationpage');
	session_register('currentuser');
	session_register('session_rp');
	
	if( $logged_in && $session_rp == $rootpath ) {
		if( $postvars ) {
			foreach( $postvars as $key=>$value ) {
				${$key} = $value;
			}
			$postvars = "";
		}
		else {
			$postvars = $_POST;
			$destinationpage = "http://" . $HTTP_HOST;
			$destinationpage .= $REQUEST_URI ? $REQUEST_URI : $SCRIPT_NAME . "?" . $QUERY_STRING;
		}
		if( $assignedtree && $assignedtree != $tree )
			$notrighttree = 1;
		else {
			session_register('allow_admin_db');
			session_register('allow_edit_db');
			session_register('allow_add_db');
			session_register('allow_delete_db');
			session_register('allow_living_db');
			session_register('allow_lds_db');
			$allow_admin = $allow_admin_db;
			$allow_edit = $allow_edit_db;
			$allow_add = $allow_edit_db;
			$allow_delete = $allow_delete_db;
			$allow_living = $allow_living_db;
			$allow_lds = $allow_lds_db;
			$notrighttree = 0;
		}
	}
	else {
		$postvars = $_POST;
		$destinationpage = sprintf("%s%s%s","http://",$HTTP_HOST,$REQUEST_URI); ;
		if( $requirelogin ) {
			$login_noargs_url = getURL( "login", 0 );
			header( "Location: $login_noargs_url" );
		}
		else {
			$logged_in = 1;
			$notrighttree = 1;
			$assignedtree = "-x-guest-x-";
			$currentuser = "";
			$session_rp = $rootpath;
		}
	}
	if( $notrighttree ) {
		$allow_admin = 0;
		$allow_edit = 0;
		$allow_add = 0;
		$allow_delete = 0;
		$allow_living = 0;
		$allow_lds = $ldsdefault ? 0 : 1;
	}
?>
