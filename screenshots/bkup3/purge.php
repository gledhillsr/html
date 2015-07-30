<?
require("config.php");
    $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");

	$today = getdate();
	if($today[mon] < 10)
			$yr = $today[year] - 1;
	 else
			$yr = $today[year];    
    $endingTicks = strtotime("Oct 1, ". $yr);
    $endSeasonStr = date("l, F d, Y  H:i:s", $endingTicks);
?>
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<script language="JavaScript">

</script>
<title>Purge History</title>
</head>

<body background="images/ncmnthbk.jpg">

<h1>WARNING:  About to delete entire ski history from last season</h1>

<form name="myForm" method="POST" action="purge.php">
<br><br><br>
<?

    if($purgeYesBtn) {
	    $arrDate = getdate();
	    $today=mktime(0, 0, 0, $arrDate[mon], $arrDate[mday], $arrDate[year]);
		 $query_string = "DELETE FROM `skihistory` WHERE `date` <= $endingTicks"; 
	    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query 5");
		 $count = 0;
	    if ($result) { 
	    		echo "Old Ski History data has been deleted.";
	    } else {			  
	    		echo "Error, no records deleted";
		 }
    } else if($purgeHistoryBtn) {

    $arrDate = getdate();
    $today=mktime(0, 0, 0, $arrDate[mon], $arrDate[mday], $arrDate[year]);
	 $query_string = "SELECT COUNT(*) FROM `skihistory` WHERE `date` <= $endingTicks"; 
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query 5");
	 $count = 0;
   if (mysql_num_rows($result)==0)
       echo "Oops, no records";
   else
       $count = mysql_result($result,0);

		 if($count <= 0) {
			echo "No records to delete.";
		 } else {
		 	echo "You are about to delete $count records, are you sure.";
	    	echo "<br><br><input type=\"submit\" value=\"YES, delete them\" name=\"purgeYesBtn\">&nbsp;\n";
		}
    } else {
		 echo "This will delete all ski histories dated before <b>$endSeasonStr</b>.<br>";
		 echo "<br>PLEASE make a perminate backup of your data BEFORE proceding.  You will not be prompted again.";
	    echo "<br><br><input type=\"submit\" value=\"DELETE last season's ski history\" name=\"purgeHistoryBtn\">&nbsp;\n";
	 }
    @mysql_close($connect_string);
    @mysql_free_result($result);
?>
</form>

</body>

</html>
