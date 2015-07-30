<?
session_register('session_language');
if( $session_language )
	$mylanguage = $session_language;
else {
	$mylanguage = $language;
	$session_language = $language;
}

//	$mylanguage = "English";	//added (sg)

?>