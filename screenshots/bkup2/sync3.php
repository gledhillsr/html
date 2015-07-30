<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<title>Synchronize with www.BrightonNSP.org</title>
</head>
<body>
zzz111222<br>
</body>
</html>
<?php
exit;
//NOTE TO BRIAN(me): Only thing changed here is $showdebug was changed to its current constant
//	and indenting.  
//	config.php now has PEAR::DB functionality and has been tested


require("config.php");
$arrDate = getdate();
$today=mktime(0, 0, 0, $arrDate['mon'], $arrDate['mday'], $arrDate['year']);
//    $strToday = date("F-d-Y h:m", $today);
$strToday = date("F-d-Y h:i:s a");
//echo "$mysql_host at: $strToday<br>";
$roster = array();
$skihistory = array();

//Contants
$SHOW_DEBUG = true;
$startHistoryID = 0;    //another hack sg
?>
