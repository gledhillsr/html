<?php
require("config.php");
if($delete) {
    $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
    $query_string = "DELETE FROM skihistory WHERE history_id=\"" . $delete . "\"";
//echo $query_string . "<br>";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");
    @mysql_close($connect_string);
    @mysql_free_result($result);

     header("Location: history.php?admin=1&ID=" . $ID); /* Redirect browser */

    exit;
}
if($ID) {
//
// get info from ROSTER
//
    $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
    $query_string = "SELECT LastName, FirstName, ClassificationCode, canEarnCredits FROM roster WHERE IDNumber=\"" . $ID . "\"";
//echo $query_string . "<br>";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");
    if ($row = @mysql_fetch_array($result)) {
        $name = $row[ FirstName ] . " " . $row[ LastName ];
        $class = $row[ClassificationCode];
        $canEarnCredits = $row[canEarnCredits];
    }
//echo "name=$name";
if($addBtn || $saveBtn) {
    if($addBtn)
        $query_string = "INSERT INTO skihistory SET ";
    else
        $query_string = "UPDATE skihistory SET ";
//    $date =
//$shift = 1;//0 = day, 1 = swing, 2 = night
//$value=4;  //day, swing and night before 4 pm = 4, night before 5:30 = 3, night after 5:30 = 2
//dialog 0=day,1=swing,2=fullnight,3=3/4night,4=night
switch($shiftSel) {
    case 0:   $shift=0;   $value=4;    break;  //day
    case 1:   $shift=1;   $value=4;    break;  //swing
    case 2:   $shift=2;   $value=4;    break;  //full night
    case 3:   $shift=2;   $value=3;    break;  //3/4 night
    case 4:   $shift=2;   $value=2;    break;  //night
}
    $query_string .=
        "date = \"" . strtotime($date) . "\", " .
        "checkin = \"" . timeToSeconds($checkinTime) . "\", " .
        "areaID = \"$area\", " .
        "shift = \"$shift\", " .
        "teamLead = \"$teamLead\", " .
        "value = \"$value\", " .  //from shiftsel
        "multiplier = \"$multiplier\", " .
        "patroller_id = \"$ID\"";
//        ", sweep_ids = \"\"";
    if($addBtn) {
        $query_string .= ", sweep_ids = \"\"";
        $query_string .= ", name = \"$name\"";
    } else {
        $query_string .= " WHERE history_id=\"$history_id\"";
	}
//echo "$query_string<br>";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");
    @mysql_close($connect_string);
    @mysql_free_result($result);
    header("Location: history.php?admin=1&ID=" . $ID); /* Redirect browser */
    exit;
}
if($add) {
    $arrDate = getdate();
    $sec=$arrDate[seconds];
    $min=$arrDate[minutes];
    $hr =$arrDate[hours];
    $today=mktime(0, 0, 0, $arrDate[mon], $arrDate[mday], $arrDate[year]);
    $seconds = ($hr*3600) + ($min * 60) + $sec;
    $strToday = date("m/d/Y", $today);


    $date=$strToday;
    $time=secondsToTime($seconds);
    $areaID = 0;
    $area = $getArea[$areaID];
    $shift = 0; //day
    $teamLead=0; //not a team leader
    $timeValue = $shiftValue[$shift];

    if($class == "SR" || $class == "SRA") {
	    if($canEarnCredits == 0)
	        $mult = 3;	//0.333334
		else
	        $mult = 2; 	//1.333334
    } else {
	    if($canEarnCredits == 0)
	        $mult = 0;
		else
	        $mult = 1;
	}
} else {
    $query_string = "SELECT * FROM skihistory WHERE history_id=\"" . $edit . "\"";
//echo $query_string . "<br>";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");
    if ($row = @mysql_fetch_array($result)) {
        $date    = $row[ date ];
        $date = date("m/d/Y", $date);
        $tmp = $row[checkin];
        $time = secondsToTime($tmp);
        $areaID = $row[areaID];
        $area = $getArea[$areaID];
        $shift = $row[shift];
        $teamLead = $row[teamLead];
        $timeValue = $row[value];
        if($shift == 2 && $timeValue == 3)      $shift = 3;         // 3/4 night
        else if($shift == 2 && $timeValue == 2) $shift = 4; // 1/2 night
        $timeValue /= 2;
        $mult = $row[multiplier]; //0=0.0, 1=1.0, 2=1.3333, 3=0.3333
    } else
        echo "Error, History not found.";
}
    @mysql_close($connect_string);
    @mysql_free_result($result);
//echo "history_id=$edit<br>";
//echo "add=$add<br>";
}
else
    echo "Error, ID not set<br>\n";
?>

<html>

<head>
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
<meta HTTP-EQUIV="Expires" CONTENT="-1"/>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252"/>
<title>Date</title>
<script language="JavaScript">
<!--
function updateTotal(which) {
    var shiftVal = document.myForm.shiftSel.selectedIndex;
    if (shiftVal <= 2)    shiftVal = 1.0;
    else if(shiftVal == 3)shiftVal = 0.75;
    else                shiftVal = 0.5;
	shiftVal *= 2;
    var mulVal = document.myForm.multiplier.selectedIndex;
	//if mulVal == 0 or 1, it doesn't need to be modified
    if(mulVal == 2)     mulVal = 4/3;  	//(1.333334)  //0=0.0, 1=1.0, 2=1.3333, 3=0.3333
	else if(mulVal==3)	mulval = 1/3;	//(0.333334)
    var final = shiftVal * mulVal;
//alert("shift value=="+shiftVal+", mult value="+mulVal+", final="+final);
    document.myForm.creditValue.value = shiftVal;
    document.myForm.totalCreditValue.value = final ;
}
function verifyDelete(user_id,history_id) {
    if(confirm("Are you sure you want to delete this Ski Assignment?")) {
//      alert("you answered yes");
       window.location.href="edit_history.php?ID="+user_id+"&delete="+history_id;
    }
}
//-->
</script>
</head>

<body background="images/ncmnthbk.jpg">
<h2>
<?php
    if($add)
        echo "Add NEW Individual Ski History Entry for $name";
    else
        echo "Edit Individual Ski History Entry for $name";
?>
</h2>
<form name="myForm" method="POST" action="edit_history.php">
<table border="0" cellpadding="2" style="border-collapse: collapse" bordercolor="#111111" width="380" id="AutoNumber2">
    <tr>
      <td align="right" width="158">Date</td>
      <td width="222">
      <input type="text" name="date" size="11" value="<?php echo $date; ?> "></td>
    </tr>
    <tr>
      <td align="right" width="158">Check-in Time</td>
      <td width="222"><input type="text" name="checkinTime" size="11" value="<?php echo $time; ?>"></td>
    </tr>
    <tr>
      <td align="right" width="158">Area Assignment</td>
      <td width="222">
      <select size="1" name="area">
<?php
        for($i = -1; $i < $areaCount; $i++ ) {
          $sel = ($i == $areaID) ? " SELECTED " : "";
          echo "<option $sel value=\"$i\">" . $getArea[$i] . "</option>\n";
        }
?>
      </select></td>
    </tr>
    <tr>
      <td align="right" width="158">Shift</td>
      <td width="222">
      <select size="1" name="shiftSel" onchange="updateTotal()">
<?php
    for($i = 0; $i < $shiftCount; $i++) {
      $sel = ($i == $shift) ? "SELECTED" : "";
      echo "<option value=\"$i\" $sel>" . $getShifts[$i] . "</option>\n";
    }
?>
      </select></td>
    </tr>
    <tr>
      <td align="right" width="158">Leadership</td>
      <td width="222">
      <select size="1" name="teamLead">
<?php
    for($i = 0; $i < 4; $i++) {
      $sel = ($i == $teamLead) ? "SELECTED" : "";
      echo "<option value=\"$i\" $sel>" . $getTeamLead[$i] . "</option>\n";
    }
?>
      </select> (Debug Only)</td>
    </tr>
    <tr>
      <td align="right" width="158">Value</td>
<?php    echo "<td width=\"222\"><input type=\"text\" name=\"creditValue\" readonly size=\"4\" value=\"$timeValue\"> (Computed)</td>\n"; ?>
    </tr>
    <tr>
      <td align="right" width="158">Multiplier</td>
      <td width="222">
      <select size="1" name="multiplier"  onchange="updateTotal(2)">
<?php
//$mult  0,1,2 = 0,1,1.333

      echo "<option value=\"0\" " . (($mult == 0) ? "SELECTED" : "" ). ">0.0</option>\n";
      echo "<option value=\"1\" " . (($mult == 1) ? "SELECTED" : "" ). ">1.0</option>\n";
      echo "<option value=\"2\" " . (($mult == 2) ? "SELECTED" : "" ). ">1.333</option>\n";
      echo "<option value=\"3\" " . (($mult == 3) ? "SELECTED" : "" ). ">0.333</option>\n";
?>
      </select></td>
    </tr>
    <tr>
      <td align="right" width="158">Credit Value</td>
<?php
if($mult == 0)      $val = 0;
else if($mult == 2) $val = $timeValue * 1.33334;
else if($mult == 3) $val = $timeValue * 0.33334;
else /* $mult==1 */ $val = $timeValue;
      echo "<td width=\"222\"><input type=\"text\" name=\"totalCreditValue\" readonly size=\"4\" value=\"$val\"> (Computed)</td>\n";
?>
    </tr>
  </table>
  <p>
<?php
  echo "<input type=\"HIDDEN\" name=\"ID\" VALUE=\"$ID\">";

  if($add)
    echo "<input type=\"submit\" value=\"Add Entry\" name=\"addBtn\">&nbsp;\n";
  else {
    echo "<input type=\"HIDDEN\" name=\"history_id\" VALUE=\"$edit\">";
    echo "<input type=\"submit\" value=\"Save Changes\" name=\"saveBtn\">&nbsp;\n";
  }
  echo "<input type=\"button\" value=\"Cancel\" onclick=history.back()>&nbsp;\n";
  if(!$add)
    echo "<input type=\"button\" value=\"Delete This Entry\" onclick=\"verifyDelete($ID,$edit)\">";
?>
</p>
</form>

</body>

</html>