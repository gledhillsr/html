<?
require("config.php");

	if($syncLoginBtn) {
//    header("Location: Synchronize.php?startPos=0"); /* Redirect browser */
    header("Location: sync2.php"); /* Redirect browser */

 }
	if($syncRosterBtn) {
//    header("Location: Synchronize.php?startPos=0"); /* Redirect browser */
    header("Location: sync4.php?startPatroller=0"); /* Redirect browser */

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
<meta http-equiv="Content-Language" content="en-us"/>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252"/>
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
<meta HTTP-EQUIV="Expires" CONTENT="-1"/>
<title>Sync Roster</title>
</head>

<body background="images/ncmnthbk.jpg">


<form name="myForm" method="POST" action="sync.php">
<?
//$now //current seconds
echo "<h1>Sync the SKIHISTORY on this machine, UP to the Internet</h1>\n";
    echo "<input type=submit value=\"Sync Login History UP to Web\" name=\"syncLoginBtn\">&nbsp;&nbsp;&nbsp;&nbsp;May take a few minutes.<br><br><br>\n";
echo "<h1>Sync the ROSTER from the Internet, DOWN to this machine</h1>";
    echo "<input type=submit value=\"Syncronize Roster Down from Web\" name=\"syncRosterBtn\">&nbsp;&nbsp;&nbsp;&nbsp;May take a few minutes.<br><br><br>\n";
//	if($enableSync) {
//		$strEnable = "Enabled";
//		$strBtn = "Disable Syncronization";
//	} else {
//		$strEnable = "Disabled";
//		$strBtn = "Enable Syncronization";
//	}
//	echo "<font size=4>Real-Time Synchronization is currently <b>$strEnable</b></font>&nbsp;&nbsp;&nbsp;";
//    echo "<input type=submit value=\"$strBtn\" name=\"doSyncBtn\">&nbsp;(Not working yet)<br>\n";
    mysql_close($connect_string);
//    mysql_free_result($result);
?>
</form>

</body>

</html>
