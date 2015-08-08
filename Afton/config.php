<?php
/*****************************************************************************
 * resort specific variables                                                 *
 ****************************************************************************/
$resortFull = "Afton Alps";
$resort     = "Afton";
$resortURL  = "http://www.aftonalpsskipatrol.org";
$resortImg  = "AftonLogo.jpg";
$imgHeight  = 80;

/*****************************************************************************
 * register cookies, POST's, & GET's                                         *
 *****************************************************************************/

//if(!ini_get('register_globals'))
{
	$__am = array('COOKIE','POST','GET');
	while(list(,$__m) = each($__am)){
//var_dump($__m);
//echo "---" . ${"HTTP_".$__m."_VARS"} . "---<br>";
		$__ah = &${"HTTP_".$__m."_VARS"};
//var_dump($__ah);
		if(!is_array($__ah)) continue;
		while(list($__n, $__v) = each ($__ah)) {
//echo "yyy(".$__n."--".$__v.")<br>";
			$$__n = $__v;
		}
	}
}
?>
