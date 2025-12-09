<?php
require("config.php");
    $connect_string = @mysqli_connect($mysqli_host, $mysqli_username, $mysqli_password) or die ("Could not connect to the database.");
    mysqli_select_db($connect_string, $mysqli_db);

    $arrDate = getdate();
    $today=mktime(0, 0, 0, $arrDate[\MON], $arrDate[\MDAY], $arrDate[\YEAR]);
?>
<html>

<head>
<meta http-equiv="Content-Language" content="en-us"/>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252"/>
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
<meta HTTP-EQUIV="Expires" CONTENT="-1"/>
<title>Aid Room Summary</title>
</head>

<body background="images/ncmnthbk.jpg">
<script>
function printWindow(){
   bV = parseInt(navigator.appVersion)
   if (bV >= 4) window.print()
}
</script>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:printWindow()">Print This Page</a><br>
<br>
<?php for($i=0; $i < 2; $i++) {
	if($i == 0)
		$loc="Aid Room 1";
	else
		$loc="Aid Room 2";

  echo "<table border=\"1\" width=\"410\" cellspacing=\"0\" cellpadding=\"0\">\n";
  echo "<tr>\n";
  echo "<td colspan=\"4\" bgcolor=\"#E1E1E1\" align=center width=\"406\">$loc Summary</td>\n";
  echo "</tr>\n";
//
// loop for each sweep of name $loc
//	  
	$query_string = "SELECT * FROM sweepdefinitions	WHERE location=\"$loc\" ORDER BY start_time";
//echo "$query_string<br>";
    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result)");
    while ($row = @mysqli_fetch_array($result)) {
		$start = secondsToTime($row[\START_TIME]);
		$end   = secondsToTime($row[\START_TIME]);
		$currSweepID = $row[\ID];
//echo "looking for id=$currSweepID<br>";
		//loop through ski history for today, and this find Aid Room
		$query_string = "SELECT * FROM skihistory WHERE DATE=$today AND shift=0";
//echo "$query_string<br>";
	    $result2 = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result)");
		$found = false;
		$patroller_id = "192443";
		//
		// loop thru each skihistory from today and this morning and find the current sweep id
    	while (!$found && $row2 = @mysqli_fetch_array($result2)) {
			$patroller_id = $row2[\PATROLLER_ID];
			$sweep_ids = $row2[\SWEEP_IDS];
			$foo = trim($sweep_ids); //hack
			if($foo != "") {
			    $tok = strtok($foo, " ");
			    while ($tok) {
					if($tok == $currSweepID) {
						$found = true;
						break;
					}
			        $tok = strtok(" ");
			    }
			}
		}
		
		//get patroller ID
		if($found) {
			$query_string = "SELECT * FROM roster WHERE IDNumber=\"$patroller_id\"";
	    	$result2 = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result)");
			$class = "xx";
			$name = "xx";
			$area  = "Crest";
		    if ($row2 = @mysqli_fetch_array($result2)) {
				 $class = $row2[\CLASSIFICATIONCODE];
				 $name = $row2[\FIRSTNAME] . " " . $row2[\LASTNAME];
			}
		} else {
			$class = "&nbsp;";
			$name = "&nbsp;";
			$area = "&nbsp;";
		}
			
	  echo "<tr>\n";
	  echo "  <td width=\"47\"  bgcolor=\"#EBEBEB\" align=center><font size=2>$class</font></td>\n";
	  echo "  <td width=\"144\" bgcolor=\"#EBEBEB\"><font size=2>$name</font></td>\n";
	  echo "  <td width=\"80\"  bgcolor=\"#EBEBEB\"><font size=2>$start - $end</font></td>\n";
	  echo "  <td width=\"75\"  bgcolor=\"#EBEBEB\"><font size=2>$area</font></td>\n";
	  echo "</tr>\n";
	}

  echo "</table>\n";
  echo "<br>\n";
}

    @mysqli_close($connect_string);
    @mysqli_free_result($result);
?>
</body>

</html>
