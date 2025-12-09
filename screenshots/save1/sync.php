<?php
require("config.php");

if (file_exists ( "runningFromWeb.php" )) {
    include("runningFromWeb.php");  # should have $runningFromWeb = "true"; 
}

	if($syncLoginBtn) {
//    header("Location: syncSkiHistory.php?startPos=0"); /* Redirect browser process records in blocks */
    header("Location: syncSkiHistory.php"); /* Redirect browser all records in one pass */
    exit;

 }
	if($syncRosterBtn) {
    header("Location: syncRoster.php?startPatroller=0"); /* Redirect browser */
    exit;
 }

    //setup local DB connection
//    $connect_string = @mysqli_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
//-------------------------------------------------------------------------------------------------------
// I NO LONGER CHECK THE DATABASE TO SEE IF SYNCING IS ENABLED, I LOOK FOR A FILE runningFromWeb.php
//-------------------------------------------------------------------------------------------------------
//   $query_string = "SELECT syncEnabled FROM directorsettings WHERE 1";
////echo "$query_string ";
//	$result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query.  Cannot query syncEnabled");
//    if ($row = @mysql_fetch_array($result)) {
//		$enableSync = $row[syncEnabled];
////echo "enableSync = $enableSync<br>";
////echo "doSyncBtn = $doSyncBtn<br>";
//		if($doSyncBtn) {
//		   $query_string = "SELECT syncEnabled FROM directorsettings WHERE 1";
////echo "$query_string ";
//			if($enableSync)
//				$query_string = "UPDATE directorsettings set syncEnabled=0 WHERE 1";
//			else
//				$query_string = "UPDATE directorsettings set syncEnabled=1 WHERE 1";
//			$result = @mysql_db_query($mysql_db, $query_string) or die ("SYNC change NOT made");
//			$enableSync = !$enableSync;
//
//		}
//	}
//    mysql_close($connect_string);
?>
<html>

<head>
<meta http-equiv="Content-Language" content="en-us"/>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252"/>
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
<meta HTTP-EQUIV="Expires" CONTENT="-1"/>
<title>Syncronize</title>
</head>

<body background="images/ncmnthbk.jpg">


<form name="myForm" method="POST" action="sync.php">
<?php 
    if($runningFromWeb) {
        $strEnable = " Disabled ";
    }
    echo "<h1>Sync the SKIHISTORY on this machine, UP to the Internet</h1>\n";
    echo "<input type=submit $strEnable value=\"Sync Login History UP to Web\" name=\"syncLoginBtn\">&nbsp;&nbsp;&nbsp;&nbsp;May take a few minutes.<br><br><br>\n";
    echo "<h1>Sync the ROSTER from the Internet, DOWN to this machine</h1>";
    echo "<input  type=submit $strEnable value=\"Syncronize Roster Down from Web\" name=\"syncRosterBtn\">&nbsp;&nbsp;&nbsp;&nbsp;May take a few minutes.<br><br><br>\n";
    if($runningFromWeb) {
        echo "<font color='red' size=4>Synchronization is <b>Disabled</b> while viewing from web</font><br>";
    }
?>
</form>

</body>

</html>
