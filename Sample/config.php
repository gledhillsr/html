<?php
/*****************************************************************************
 * resort specific variables                                                 *
 ****************************************************************************/
$resort="Sample";
$resortFull = "Sample Resort";
$resortURL  = "www.nspOnline.org";
$resortImg  = "/images/Brighton.gif";
$ImgWidth  = "261";

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
