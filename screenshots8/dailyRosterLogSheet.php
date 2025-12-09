<?php 
require("config.php");
//    $arrDate =  date_default_timezone_set("America/Denver");
    $arrDate = getdate();
    $today=mktime(0, 0, 0, $arrDate[\MON], $arrDate[\MDAY], $arrDate[\YEAR]);
    $strToday = date("l F-d-Y", $today);
	$simpleCredits = 0;
//    echo("xyzzy<br/>");
//    echo("$mysqli_host<br/>");
//    echo("$mysqli_username<br/>");
//    echo("$mysqli_password<br/>");
    $connect_string = @mysqli_connect($mysqli_host, $mysqli_username, $mysqli_password);
    mysqli_select_db($connect_string, $mysqli_db);

     mysqli_select_db($connect_string, $mysqli_db);
     if (!$connect_string) {
        die ("Could not connect to the database. (" . mysqli_error() . ")");
     }
 //    echo("Connected successfully");
	$getClassification = ["SR" => "Senior", "BAS" => "Basic", "AUX" => "Auxilary", "SRA" => "Sr. Aux.",  "CAN" => "Candidate",  "PRO" => "Pro", "TRA" => "Transfer", "OTH" => "Other", "" => "&nbsp;"];

?>

<html>
<head>
<meta http-equiv="Content-Language" content="en-us"/>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252"/>
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
<meta HTTP-EQUIV="Expires" CONTENT="-1"/>
<title>Daily Roster</title>
<script language="JavaScript">
<!--
function validateKeyPress(evt) {
var id = 0;
    var index=document.myForm.pname.selectedIndex;
    if(index >= 0)
        id = document.myForm.pname.options[index].value;
//alert ("show history for patroller ID=" + id);

    if(id > 0 && (evt == null || (evt.keyCode == 13 || evt.keyCode == 32))) {
        window.location.href="history.php?admin=1&ID="+id;
    }
}

function showHistory() {
var id = 0;
    var index=document.myForm.pname.selectedIndex;
    if(index >= 0)
        id = document.myForm.pname.options[index].value;
//alert ("show history for patroller ID=" + id);
  if(id > 0 && (evt == null || (evt.keyCode == 13 || evt.keyCode == 32))) {
        window.location.href="history.php?admin=1&ID="+id;
  }
}
//-->
</script>
</head>

<body background="images/ncmnthbk.jpg">
<script>
function printWindow(){
   bV = parseInt(navigator.appVersion)
   if (bV >= 4) window.print()
}
</script>
<p align=center>
<font size=5>
Brighton Ski Patrol:
<?php 
  echo "<b>$strToday</b>\n"; 
?>

<font size=1>
&nbsp;&nbsp;<a href="javascript:printWindow()">Print This Page</a><br>
</font>
<!-- <br><br><br> -->
<form action="morning_login.php" method="get">
  <input type="submit" value="Go back to: Morning Login screen" />
</form>

</p>
<br>
<p align=center>
Volunteer Patrol Daily Log Sheet
</p>
</font>
<div align=center>
<center>
<table $extra1 border=1 cellpadding=0 cellspacing=0 style="border-collapse: collapse" bordercolor="#111111" id="AutoNumber1" bgcolor="#E9E9E9">
<!-- print table header -->
  <tr>
    <td width="250" align="center">Name</td>
    <td width="100" align="center">Date</td>
    <td width="50"  align="center">Check-in Time</td>
    <td width="100" align="center">Classification</td>
    <td width="50"  align="center">Shift</td>
    <td width=50 align=center>Credit<br>Value</td>
    <td width=50 align=center>Multiplier</td>
    <td width=50 align=center>Final Credit Value</td>
  </tr>
<?php 

//debug     $query_string = "SELECT * FROM skihistory WHERE true ORDER BY name, date, checkin";
    $query_string = "SELECT * FROM skihistory WHERE date=\"$today\" ORDER BY name, date, checkin";
//echo $query_string . "<br>";
    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result)");
    while ($row = @mysqli_fetch_array($result)) {
    	$name     = $row[ \NAME ];
        $patroller_id = $row[ \PATROLLER_ID ];
    	$date     = $row[ \DATE ];
    	$date     = date("M-d-Y", $date);
	$checkin  = $row[ \CHECKIN ];
    	$time     = secondsToTime($checkin);
	$areaID   = $row[ \AREAID ] + 0;
    	$shift    = $row[ \SHIFT ];
    	$value    = $row[ \VALUE ];  //day, swing and night before 4 pm = 4, night before 5:30=3, night=2
    	switch ($shift) {
          case 0:
            $shift = "Day";
            break;
          case 1:
            $shift = "Swing";
            break;
          default:
            if($value == 4) {
              $shift = "Full Night";
            } else if($value == 3) {
              $shift = "3/4 Night";
            } else {
              $shift = "Night";
			}
            break;
    	} // end "switch" 
        $value  = $value / 2;  //day, swing and night before 4 pm = 4, night before 5:30=3, night=2
	$simpleCredits += $value;
        $multiplier  = $row[ \MULTIPLIER ];    //o=none, 1=basic, 2=senior
        $classification = "&nbsp;";
        $query_string = "SELECT ClassificationCode, Commitment FROM roster WHERE  IDNumber=\"$patroller_id\"";
//echo $query_string . "<br>";
	$result2 = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result)");
	$ClassificationCode = "";
	while ($row2 = @mysqli_fetch_array($result2)) {
		$ClassificationCode = $row2[ \CLASSIFICATIONCODE ];
		$classification = $getClassification[ $ClassificationCode ];
		$Commitment = $row2[ \COMMITMENT ];  //0=inactive, 1=part time, 2=Full Time
        }

    // multiplier 0 = no credits at all
    //            1 = credits of a Basic
    //            2 = credits of a Senior
    //            3 = credits of a Senior on the family plan
        $multiplierString = "Error";
 	$baseValue = $value;
        if($multiplier == 0) {
            $baseValue = 0;
            $multiplierString = "Zero&nbsp;/&nbsp;1st&nbsp;yr&nbsp;or<br>(Basic/Family)";
            if ($Commitment == 1) { //if Part Time
                $multiplierString = "Zero&nbsp;/&nbsp;Part&nbsp;Time";
            } else if ($ClassificationCode == "TRA") {
                $multiplierString = "Zero&nbsp;/&nbsp;Transfer";
            } else if ($ClassificationCode == "CAN") {
                $multiplierString = "Zero&nbsp;/&nbsp;Candidate";
            }
        } else if($multiplier == 1) {  //Basic
            $baseValue = $value;
            $multiplierString = $classification;  
        } else if($multiplier == 2) {  //Senior
            $multiplier = 4/3;         //1.33334;
            $multiplierString = $classification; 
	} else if($multiplier == 3) {  //Senior family plan 
            $multiplier = 1/3;         // 0.33334;
            $baseValue = 0;
            if ($Commitment == 1) { //if Part Time
                $multiplierString = "SR&nbsp;/&nbsp;Part&nbsp;Time";
            } else {
                $multiplierString = "$ClassificationCode/Family";
            }
	} 
 //$multiplierString = "$multiplierString($Commitment/$multiplier/$foo)";

        $history_id  = $row[\HISTORY_ID ];
        $credit = $value * $multiplier;

	  echo "<tr>\n";
	  echo "  <td align=\"center\">$name</td>\n";     //Name 
	  echo "  <td align=\"center\">$date</td>\n";     //Date
	  echo "  <td align=\"center\">$time</td>\n";     //Check-in time
	  echo "  <td align=\"center\">$classification</td>\n";
	  echo "  <td align=\"center\">$shift</td>\n";    //Day,Swing/Night
	  echo "  <td align=\"center\">$baseValue</td>\n";    //credits without Sr 
	  echo "  <td align=\"center\">$multiplierString</td>\n";
	  echo "  <td align=\"center\">" . number_format($credit, 2, '.', ',') . "</td>\n";
	  echo "</tr>\n";
    } //end "while" loop for each patroller in query
?>
        </tr>
      </table>
    </center>
  </div>
<br><br><br>
Signed:_________________________________________

</body>
</html>

<?php 
    @mysqli_close($connect_string);
    if($result)
        @mysqli_free_result($result);
?>
