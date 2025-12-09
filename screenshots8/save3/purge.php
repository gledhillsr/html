<?php
require("config.php");
include("runningFromWeb.php");
    $connect_string = @mysqli_connect($mysqli_host, $mysqli_username, $mysqli_password) or die ("Could not connect to the database.");
	mysqli_select_db($connect_string, $mysqli_db);

	$today = getdate();
	if($today[\MON] < 10)
			$yr = $today[\YEAR] - 1;
	 else
			$yr = $today[\YEAR];    
    $endingTicks = strtotime("Oct 1, ". $yr);
    $endSeasonStr = date("l, F d, Y  H:i:s", $endingTicks);
?>
<html>

<head>
<meta http-equiv="Content-Language" content="en-us"/>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252"/>
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
<meta HTTP-EQUIV="Expires" CONTENT="-1"/>
<script language="JavaScript">

</script>
<title>Purge History</title>
</head>

<body background="images/ncmnthbk.jpg">

<h1>WARNING:  About to delete entire ski history from last season</h1>

<form name="myForm" method="POST" action="purge.php">
<br><br><br>
<?php 
    if($runningFromWeb) {
        $strEnable = " Disabled ";
    }

    if($purgeYesBtn) {
        $arrDate = getdate();
        $today=mktime(0, 0, 0, $arrDate[\MON], $arrDate[\MDAY], $arrDate[\YEAR]);
        $query_string = "DELETE FROM `skihistory` WHERE `date` <= $endingTicks";
        $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query 5");
        $count = 0;
        if ($result) {
            echo "Old Ski History data has been deleted.";
        } else {
            echo "Error, no records deleted";
        }
    } else if($purgeHistoryBtn) {
        $arrDate = getdate();
        $today=mktime(0, 0, 0, $arrDate[\MON], $arrDate[\MDAY], $arrDate[\YEAR]);
        $query_string = "SELECT COUNT(*) FROM `skihistory` WHERE `date` <= $endingTicks";
        $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query 5");
        $count = 0;
        if (mysqli_num_rows($result)==0) {
            echo "Oops, no records";
        } else {
            $count = mysqli_result($result,0);
        }
        if($count <= 0) {
            echo "No records to delete.";
        } else {
            echo "You are about to delete $count records, are you sure.";
            echo "<br><br><input type=\"submit\" $strEnable value=\"YES, delete them\" name=\"purgeYesBtn\">&nbsp;\n";
        }
    } else {
        echo "This will delete all ski histories dated before <b>$endSeasonStr</b>.<br>";
        echo "<br>PLEASE make a perminate backup of your data BEFORE proceding.  You will not be prompted again.";
        echo "<br><br><input type=\"submit\" $strEnable value=\"DELETE last season's ski history\" name=\"purgeHistoryBtn\">&nbsp;\n";
    }
    @mysqli_close($connect_string);
    @mysqli_free_result($result);
    if($runningFromWeb) {
        echo "<br><br><font color='red' size=4>Purging data is <b>Disabled</b> while viewing from web</font><br>";
    }
?>
</form>

</body>

</html>
