<?
require("config.php");
    $name="NOBODY";
    $arrDate = getdate();
    $today=mktime(0, 0, 0, $arrDate[mon], $arrDate[mday], $arrDate[year]);
    $strToday = date("l F-d-Y", $today);
	$simpleCredits = 0;
	if($audit) {
	    $strToday = date("l F-d-Y   g:i a");
	}
    $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
    if($ID) {
    //
    // get info from ROSTER
    //
        $query_string = "SELECT LastName, FirstName FROM roster WHERE IDNumber=\"" . $ID . "\"";
    //echo $query_string . "<br>";
        $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");
        if ($row = @mysql_fetch_array($result)) {
            $name = $row[ FirstName ] . " " . $row[ LastName ];
    //echo "name=$name";
        }
        $endingTicks = mktime();
        if($millis) {
            $daysOld = (int)(($endingTicks - $millis + 1000) / (24*3600));
            $strToday = date("l F-d-Y", $millis);
            $strToday .= " (about $daysOld days old)";
        }
    }
    else if (!admin) {
        echo "Error, ID not set<br>\n";
    }

?>

<html>
<head>
<meta http-equiv="Content-Language" content="en-us"/>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252"/>
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
<meta HTTP-EQUIV="Expires" CONTENT="-1"/>
<title>History</title>
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
//   changesMade = false;
   bV = parseInt(navigator.appVersion)
   if (bV >= 4) window.print()
}
</script>

<?
    if($admin) {
?>
<form method="POST" name="myForm">
Select patroller to view:&nbsp;&nbsp;&nbsp;
<?
    $query_string = "SELECT LastName, FirstName, IDNumber FROM roster ORDER BY LastName, FirstName";
//    $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");
    echo "<select size=\"1\" name=\"pname\" onkeypress=\"validateKeyPress(event)\">\n";
    while ($row = @mysql_fetch_array($result)) {
        $szName = $row[ LastName ] . ", " . $row[ FirstName ];
        $sel = ($row[IDNumber] == $ID) ? " selected" : "";
        echo "  <option value=\"" . $row[IDNumber] . "\" $sel>$szName</option>\n";
    }
    echo "</select>\n";
//    @mysql_close($connect_string);
//    @mysql_free_result($result);
?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="Show" onclick="validateKeyPress(null)"></p>

</form>
<hr>
<?
    }
if(!$audit)
	echo "<p align=center><font size=5>Individual History for </font>";
?>
<font size="5">
<?
if($audit) {
    echo "Brighton Ski Patrol <b>Audit Trail</b> as of $strToday<br></font>\n";
	echo "<font size=2> <a href=\"javascript:printWindow()\">Print This Page</a></font>\n";

} else
    echo "<b>$name</b></p></font>\n"; //, as of $strToday";
echo "<div align=center>";
echo "  <center>\n";
if($audit) {
//	$extra1 = "width=\"95%\"";
	}
  else {
    $extra1 = "";
  }
echo "<table $extra1 border=1 cellpadding=0 cellspacing=0 style=\"border-collapse: collapse\" bordercolor=\"#111111\" id=\"AutoNumber1\" bgcolor=\"#E9E9E9\">\n";
echo "  <tr>\n";
 if($audit) 
    echo "  <td width=150 align=center>Name</td>\n";
?>
  <td width="100" align="center">Date</td>
  <td width="50"  align="center">Check-in Time</td>
  <td width="100" align="center">&nbsp;&nbsp;Area&nbsp;Assignment</td>
  <td width="50"  align="center">Shift</td>
<? 
    echo "  <td width=50 align=center>Team<br>Lead</td>";

    echo "  <td width=50 align=center>Credit<br>Value</td>\n";
    echo "  <td width=50 align=center>Multiplier</td>\n";
    echo "  <td width=50 align=center>Final Credit Value</td>\n";

    if($admin) {
        echo "  <td width=\"50\" >&nbsp;</td>\n";
    }
    echo "</tr>\n";
    $totalDays = 0;
    $totalNights = 0;
    $totalCredits = 0;
    if($audit) {
        $query_string = "SELECT * FROM skihistory WHERE 1 ORDER BY name, date, checkin";
    } else {
        $query_string = "SELECT * FROM skihistory WHERE patroller_id=\"" . $ID . "\" OR name=\"" . $name . "\"ORDER BY date, checkin";
	}
//echo $query_string . "<br>";
$result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");
while ($row = @mysql_fetch_array($result)) {
    $name    = $row[ name ];
    $date    = $row[ date ];
    $date = date("M-d-Y", $date);

    $checkin = $row[ checkin ];
    $time = secondsToTime($checkin);
    $areaID  = $row[areaID]+0;
    $shift  = $row[shift];
    $value  = $row[value];  //day, swing and night before 4 pm = 4, night before 5:30=3, night=2
    if($date == "Nov-19-2005") {
      $value = 4;	//major hack, our computers were off this day.  please remove or fix hack hack hack hack
    //echo "--value=$value<br>";
    }
    switch ($shift) {
        case 0:
            $shift = "Day";
            $totalDays += 1;
            break;
        case 1:
            $shift = "Swing";
            $totalDays += 1;
            break;
        default:
            if($value == 4)
                $shift = "Full Night";
            else if($value == 3)
                $shift = "3/4 Night";
            else
                $shift = "Night";

            $totalNights += 1;
            break;
    }
    $value  = $value / 2;  //day, swing and night before 4 pm = 4, night before 5:30=3, night=2
	$simpleCredits += $value;
    $multiplier  = $row[multiplier];    //o=none, 1=basic, 2=senior
    $teamLead = $row[teamLead];
    if($teamLead == 0)  $teamLead="-";
    else if($teamLead == 1) $teamLead="TL";
    else if($teamLead == 2) $teamLead="ATL";
    else if($teamLead == 3) $teamLead="xtra";
    // multiplier 0 = no credits at all
    //            1 = credits of a Basic
    //            2 = credits of a Senior
    //            3 = credits of a Senior on the family plan
    $multiplierString = "Error";
    if($multiplier == 0) {
        $multiplierString = "Zero&nbsp;or&nbsp;Fam&nbsp;Plan";
    } else if($multiplier == 1) {
        $multiplierString = "Standard";
    } else if($multiplier == 2) {
        $multiplier = 4/3;  //1.33334;
        $multiplierString = "Senior";
	} else if($multiplier == 3) {
        $multiplier = 1/3; // 0.33334;
        $multiplierString = "Sr&nbsp;Fam&nbsp;Plan";
	}
    $history_id  = $row[history_id ];
    $credit = $value * $multiplier;
    $totalCredits += $credit;

  echo "<tr>\n";
    if($audit) {
      echo "  <td align=\"center\">$name</td>\n";     //Name (audit only)
    }
  echo "  <td align=\"center\">$date</td>\n";     //Date
  echo "  <td align=\"center\">$time</td>\n";  //Check-in time
//  echo "  <td align=\"center\">" . $areaID . "</td>\n";   //Area name
  echo "  <td align=\"center\">" . $getArea[$areaID] . "</td>\n";   //Area name
  echo "  <td align=\"center\">$shift</td>\n";    //Day,Swing/Night
//if(!$audit)
  echo "  <td align=\"center\">$teamLead</td>\n";
  echo "  <td align=\"center\">$value</td>\n";            //Value, pct of full day. 4=full, 3=3/4, 2=1/2
    echo "  <td align=\"center\">$multiplierString</td>\n";
  echo "  <td align=\"center\">" . number_format($credit, 2, '.', ',') . "</td>\n";
  if($admin) {
    //only show "edit" button if an administrator
  	echo "<td align=\"center\"><a href=\"edit_history.php?ID=$ID&edit=$history_id\">";
  	echo "  <img border=\"0\" src=\"images/btnEdit.jpg\" width=\"27\" height=\"14\"></a>";
  	echo "</td>";
  }
  echo "</tr>\n";
}
  $extraColumns = 0;
  if($audit) {
    $extraColumns = 2;
  }
  $spanCols = 8;
  echo "<tr>\n";
  echo "  <td align=\"center\" colspan=$spanCols>&nbsp;</td>\n";
  if($admin && $ID) {
    echo "<td align=\"center\"><a href=\"edit_history.php?ID=$ID&add=1\">Add</a></td>";
  }
  else if($admin) {
    echo "<td>&nbsp</td>";
  }

	echo "</tr>\n";
	echo "<tr>\n";
	echo "  <td align=right bgcolor=\"#FFFF00\">Season Totals:</td>\n";

    echo "  <td align=center colspan=2 bgcolor=\"#FFFF00\">$totalDays Days</td>\n";
  $spanCols = 5;
    echo "  <td align=center colspan=$spanCols bgcolor=\"#FFFF00\">$totalNights Nights</td>\n";
//    echo "<td align=\"center\" bgcolor=\"#FFFF00\">" . number_format($simpleCredits, 2, '.', ',') . " Credits</td>\n";
//	if($millis) 
//	    echo "<td>&nbsp</td>\n";
//	else
//	    echo "<td colspan=3 align=\"center\" bgcolor=\"#FFFF00\">" . number_format($totalCredits, 2, '.', ',') . " Credits</td>\n";
    if($admin) echo "<td bgcolor=\"#FFFF00\">&nbsp;</td>";

?>
  </tr>
</table>
    </center>
  </div>
<br>
<?
	if($millis) {
	    echo "<input type=\"button\" value=\"Back\" onclick=history.back()>&nbsp;\n";
	}
 echo "<hr>";
    echo "** New Policy.  Senior Credits are only gived at the end of a period.<br>\n";
if(!$audit) 
    echo "* Check-in time for NIGHT shifts may be modified from original, if the Top Shack check-in time is late.";
 echo "</body>";
 echo "</html>";

    @mysql_close($connect_string);
    if($result)
        @mysql_free_result($result);
?>