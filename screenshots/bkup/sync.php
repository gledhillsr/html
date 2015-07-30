<?
require("config.php");

	if($syncNowBtn) {
//    header("Location: Synchronize.php?startPos=0"); /* Redirect browser */
    header("Location: Synchronize.php?startPatroller=0"); /* Redirect browser */

 }

    $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");

   $query_string = "SELECT syncEnabled FROM directorsettings WHERE 1";
//echo "$query_string ";
	$result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query.  Cannot query syncEnabled");
    if ($row = @mysql_fetch_array($result)) {
		$enableSync = $row[syncEnabled];
//echo "enableSync = $enableSync<br>";
//echo "doSyncBtn = $doSyncBtn<br>";
		if($doSyncBtn) {
		   $query_string = "SELECT syncEnabled FROM directorsettings WHERE 1";
//echo "$query_string ";
			if($enableSync)
				$query_string = "UPDATE directorsettings set syncEnabled=0 WHERE 1";
			else
				$query_string = "UPDATE directorsettings set syncEnabled=1 WHERE 1";
			$result = @mysql_db_query($mysql_db, $query_string) or die ("SYNC change NOT made");
			$enableSync = !$enableSync;

		}
	}
?>
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<title>Syncronize</title>
</head>

<body background="images/ncmnthbk.jpg">

<h1>Synchronize the ROSTER on the Internet to this machine</h1>
<h1>Synchronize the SKIHISTORY on this machine to the Internet</h1>

<form name="myForm" method="POST" action="sync.php">
<?
//$now //current seconds
    echo "<input type=submit value=\"Syncronize Now\" name=\"syncNowBtn\">&nbsp;<br><br><br>\n";
	if($enableSync) {
		$strEnable = "Enabled";
		$strBtn = "Disable Syncronization";
	} else {
		$strEnable = "Disabled";
		$strBtn = "Enable Syncronization";
	}
	echo "<font size=4>Real-Time Synchronization is currently <b>$strEnable</b></font>&nbsp;&nbsp;&nbsp;";
    echo "<input type=submit value=\"$strBtn\" name=\"doSyncBtn\">&nbsp;(Not working yet)<br>\n";
    mysql_close($connect_string);
//    mysql_free_result($result);
?>
</form>

</body>

</html>
