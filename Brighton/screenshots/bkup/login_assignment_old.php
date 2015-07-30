<?
require("config.php");
    $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
//define("TEAM_LEAD", 1);
//define("ASST_TEAM_LEAD", 2);
//define("DAY_SHIFT", 0);
//define("SWING_SHIFT", 1);
//define("NIGHT_SHIFT", 2);
//	$isAux
//	$inTraining
//	$canBeTeamLead
//	$currID  (Crest=0, GW=1, Milli=2, Training=3, Staff=4)
//	$currShift  (DAY_SHIFT, SWING_SHIFT, NIGHT_SHIFT)
//	$isSwingOnCrest
//	EarnCredits
//  $maxBasic
//  $maxAux
//  $basicCount
//	$auxCount
//	$needs_TL
//  $needs_ATL
//
	$skiHistoryOK = true;
//echo gethostbyname('www.gledhills.com') . "<br>";
//echo gethostbyname('localhost') . "<br>";
//$hosts = gethostbynamel('localhost');
//print_r($hosts);
    if($deleteID) {
//echo "deleteID=$deleteID";
        $query_string = "DELETE FROM skihistory WHERE history_id=\"$deleteID\"";
//echo "$query_string<br>";
        $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (delete)");
//echo "delID=$deleteID";	//show skihistory ID
        header("Location: morning_login.php?delID=$deleteID"); /* Redirect browser */
        exit;
    }

    $arrDate = getdate();
    if($shiftOverride && $shiftOverride != 0) {
//hack hack
//0 => "Use actual time"  ,
//1 => "Saturday 7:45",
//2 => "Saturday 13:45",
//3 => "Sunday 7:45",
//4 => "Monday 7:45",
//5 => "Monday 2:45 pm",
//6 => "Monday 5:45 pm",
//7 => "Monday 6:45pm",
//8 => "Monday 11:45pm");
        $sec=0;
        $min=45;
        switch ($shiftOverride) {
        case 1: $currDayOfWeek = "Saturday";    $hr = 7;   break;
        case 2: $currDayOfWeek = "Saturday";    $hr = 13;  break;
        case 3: $currDayOfWeek = "Sunday";      $hr = 7;   break;
        case 4: $currDayOfWeek = "Monday";      $hr = 7;   break;
        case 5: $currDayOfWeek = "Monday";      $hr = 14;  break;
        case 6: $currDayOfWeek = "Monday";      $hr = 17;  break;
        case 7: $currDayOfWeek = "Monday";      $hr = 18;  break;
        case 8: $currDayOfWeek = "Monday";      $hr = 23;  break;
        default: $currDayOfWeek = "Saturday";   $hr = 7;   break;
        }

    } else {
        $currDayOfWeek = $arrDate[weekday];
        $sec=$arrDate[seconds];
        $min=$arrDate[minutes];
        $hr =$arrDate[hours];
    }
    $today=mktime(0, 0, 0, $arrDate[mon], $arrDate[mday], $arrDate[year]);
    $seconds = ($hr*3600) + ($min * 60) + $sec;

    $strToday = date("l F-d-Y", $today);
//-------start hack-------
    if($shiftOverride && $shiftOverride > 0 && !$saveBtn) {
        echo "HACK, Override: " . $shiftsOvr[$shiftOverride] . "<br>";
    }
//compute Day, Swing, Night  0 = day, 1 = swing, 2 = night
    if($hr <= 9) {				//before 9am is a DAY
        $currShift  = 0;
        $shiftValue = 4;
    } else if ($hr <= 14) {	 	//before 2pm is a SWING
        $currShift  = 1;
        $shiftValue = 4;
    } else {					//after 2pm is a NIGHT
        $currShift  = 2;
        if($hr <15)
            $shiftValue = 4;	//   night, before 3pm is a FULL night
        else
            $shiftValue = 2;	//   night, after 3pm is a 1/2 night
    }
      //day, swing and night before 4 pm = 4, night before 5:30 = 3, night after 5:30 = 2
$currShiftName=array(
0 => "Day Shift"  ,
1 => "Swing Shift",
2 => "Night Shift");

//-------end hack--------
if($ID) {
//
// get info from ROSTER
//
    $query_string = "SELECT *  FROM roster WHERE IDNumber=\"" . $ID . "\"";
//echo $query_string . "<br>";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result la1)");
    $inTraining = 1;
    if ($row = @mysql_fetch_array($result)) {
        $name = $row[ FirstName ] . " " . $row[ LastName ];
        $EarnCredits = $row[canEarnCredits];
        $canBeTeamLead = $row[ teamLead ];
        $ClassificationCode = $row[ ClassificationCode ]; //looginf for "SR" or "SRA"
        if($ClassificationCode == "AUX" || $ClassificationCode == "SRA" )
            $isAux = 1;
        else
            $isAux = 0;
//isSenior no longer used, now use canBeTeamLoad
//        if($ClassificationCode == "SR") //does not include "SRA"
//            $isSenior = 1;
//        else
//            $isSenior = 0;
        if($ClassificationCode == "BAS" || $ClassificationCode == "SR" ||
           $ClassificationCode == "SRA" || $ClassificationCode == "AUX" )
        {
            $inTraining = 0;
        }
//echo "name=$name";
//        setCookie("NAME", $name);
    }
}
else
    echo "Error, ID not set<br>\n";
//------------------- save button ----------------
if($saveBtn && $TeamLead >= 0) {
//Multiplier, 0=none, 1=basic, 2=Senior or Senior Auxilary
//echo "TeamLead=$TeamLead<br>";
    $mult = 0;
//echo "EarnCredits=$EarnCredits<br>";
//echo "ClassificationCode=$ClassificationCode<br>";
    if( $EarnCredits == 1 ) {
        if($ClassificationCode == "SR" || $ClassificationCode == "SRA" )
            $mult = 2;	//1.3333
        else
            $mult = 1;	//1.00
    } else {
        if($ClassificationCode == "SR" || $ClassificationCode == "SRA" )
            $mult = 3;	//0.3333
        else
            $mult = 0;	//0.00
	}

    if($history_id)
        $query_string = "UPDATE skihistory SET ";
    else
        $query_string = "INSERT INTO skihistory  SET ";

    $query_string .=
        "date = \"$today\", " .
        "checkin = \"$seconds\", " .
        "areaID = \"$areaID\", " .
        "shift = \"$shift\", " .
        "value = \"$shiftValue\", " .
        "multiplier = \"$mult\", " .
        "patroller_id = \"$ID\", " .
        "teamLead = \"$TeamLead\", " .
        "sweep_ids = \"\"";
    if($history_id)
        $query_string .= " WHERE history_id =\"$history_id\" ";
	else
        $query_string .= ", name =\"$name\" ";
//echo $query_string . "<br>";
    @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (update/insert)");
    if ($stayHere) {
        echo "<h2>Your changes were Saved.</h2>";
    } else {
        header("Location: morning_login.php?newID=$ID"); /* Redirect browser */
        exit;
    }
}
//-------------------- end save button -----------------
    //
    // get info from SKIHISTORY
    //
        $query_string = "SELECT * FROM skihistory WHERE 1";
        $result = @mysql_db_query($mysql_db, $query_string);
		if($result == null) {
		//no ski history found.
			$skiHistoryOK = false;
		} else {

	        $query_string = "SELECT * FROM skihistory WHERE patroller_id=\"" . $ID . "\" AND date=\"$today\" AND shift=\"$currShift\"";
	//echo $query_string . "<br>";
	        $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result la3)");
	        if ($row = @mysql_fetch_array($result)) {
	            $areaID = $row[ areaID ];
	            $history_id = $row[ history_id ];
	        }
		}
//    echo "areaID=$areaID<br>";

//    @mysql_close($connect_string);
//    if($result)
//        @mysql_free_result($result);
?>
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<script language="JavaScript">
<!--

var changesMade = false;
var teamLeader=0;

var TL = new Array(10); //hack, don't query max, just set it bigger that ever needed
var ATL= new Array(10);

function checkForChanges() {
    if(changesMade) {
    var pos = "login_assignment.php";
        if(window.confirm("Save your changes?")) {
            pos += "?ID=<? echo $ID; ?>"
            + "&teamLead=0"
            + "&shiftValue=<? echo $shiftValue; ?>"
            + "&shift=<? echo $currShift; ?>";
<?        if($history_id)
              echo "pos += \"&history_id=$history_id\";\n";
?>
            pos += "&saveBtn=1&stayHere=1";

 for(i=0; i < 5; ++i) {
    if(document.myForm.areaID[i].checked)
       pos += "&areaID=" + i;
}
// window.confirm(pos);
            window.location = pos;
        }
    }
}

function madeChanges() {
    changesMade = true;
    document.myForm.saveBtn.disabled = false;
}

function myButton(btn,hist_id) {
    madeChanges();
    changesMade = false;
    if(btn == 3) {	  		//CANCEL BUTTON
        //cancel
        top.location = "morning_login.php";
    }else if(btn == 2) {	//REMOVE BUTTON

        window.location = "login_assignment.php?deleteID="+hist_id;
//        alert("Oops, 'Remove Assignment' not working yet");
//      exit;
        //reset
    }else if(btn == 1) {	//SAVE BUTTON
<?    if($canBeTeamLead) { ?>
        var i;
        for(i=0; i < 3; ++i) {	//loop to see which area is checked
			//now
            if(document.myForm.areaID[i].checked) {
				value=document.myForm.areaID_available[i].value;
				//alert("value="+value);
                if(TL[i]) {
                	if( confirm("Do you want to be a Team Leader? (OK = Yes, Cancel = No)")) {
	                    document.myForm.TeamLead.value = 1;
    	                teamLeader=1;
        	            break;
					} else {
						value -= 1;
					}
				}
                if(ATL[i]) {
                	if( confirm("Do you want to be an Assistant Team Leader?  (OK = Yes, Cancel = No)")) {
	                    document.myForm.TeamLead.value = 2;
    	                teamLeader=2;
        	            break;
					} else {
						value -= 1;
					}
                }
				//alert("value="+value);
				if(value == 0) {
//???
alert("Sorry, try a different area.  Only Team Lead or Assistant Team Lead is available here.");
document.myForm.TeamLead.value = -1;	//error, try again
document.myForm.areaID[i].checked = false;
				}
            }
        }
<?    }   ?>
//        if(teamLeader == 1) alert("Team Leader of "+i);
//        if(teamLeader == 2) alert("Assistant Team Leader of "+i);
        changesMade = false;
    }
}
//-->
</script>
<title>Daily Assignments</title>
</head>

<body onunload="checkForChanges();" background="images/ncmnthbk.jpg">
<p align="center"><font size="5">Select Assignment for <b><? echo "$name</b>, on $strToday"?></font></p>

<?
    $now = time();
    $query_string = "SELECT signinLockout FROM directorsettings WHERE 1";
    $result3 = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query 9");
    $inLockout = 0;
    if ($row3 = @mysql_fetch_array($result3)) {
        $lockTime = $row3[signinLockout];
        if($lockTime < $now) {
//            echo "Lockout time has been cleared.<br><br>";
        } else{
            $inLockout = 1;
            $diff = $lockTime - $now;
            if($diff > 3600*2){ //Oops something is wrong , clear the lockout
                echo "Warning, the lockout time was more that 2 hours ahead.  It has been cleared.<br>";
                $inLockout = 0;
            }
        }
    }
    if($inLockout == 1) {
        $strToday = date("M/d/Y - H:i:s", $lockTime);
        echo "<table border=\"1\" cellpadding=\"6\" cellspacing=\"6\" style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=\"380\" id=\"AutoNumber1\" bgcolor=\"#E9E9E9\">\n";
        echo "<tr><td><br>Shift Login is <b>locked out</b> until $strToday<br><br>";
        echo "Talk to a director if you still need to login.<br><br>";
        echo "</td></tr></table>\n";
        echo "<form method=\"POST\" name=\"myForm\" action=\"morning_login.php\">\n";
        echo "<input type=\"submit\" value=\"Cancel\"></p>\n";
        echo "</td></tr></table></form>\n";

    } else {

?>

<form method="POST" name="myForm" action="login_assignment.php">
  <div align="center">
    <center>
  <table border="1" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="480" id="AutoNumber1" bgcolor="#E9E9E9">
    <tr>
      <td align="center">Select Mountain Area</td>
      <td align="center">Available</td>
      <td align="center">Days patrolled</td>
<? /*      <td align="center">Nights patrolled</td> */ ?>
    </tr>
<?
    $totalDays = 0;
    $query_string = "SELECT * FROM areadefinitions WHERE open > 0 ORDER BY areaID ";
//echo "$query_string<br>";
    $connect_string =@mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result la4)");
//
// loop for each area
//
    while ($row = @mysql_fetch_array($result)) {
		$isSwingOnCrest = false;
		if($currShift != 0) {
			//swing or night ski
            $maxBasic=0;
            $maxAux=0;
			if($row[areaID] == 0)	//is this crest (on Swing or Night)
				$isSwingOnCrest = true;
		} else if($currDayOfWeek == "Saturday") {
			//Saturday Morning
            $maxBasic=$row[ saturdaybasic ];
            $maxAux=$row[ saturdayaux ];
        } else if ($currDayOfWeek == "Sunday") {
			//Sunday Morning
            $maxBasic=$row[ sundaybasic ];
            $maxAux=$row[ sundayaux ];
        } else {
			//any other Morning
            $maxBasic=0;
            $maxAux=0;
        }

        $open = $row[ open ];
        $areaName = $row[ areaFullText ];
        $currID = $row[ areaID ];

        //
        //get personal histories of this area
        //
		if($skiHistoryOK) {
	        $query_string = "SELECT COUNT(patroller_id) AS count  FROM skihistory WHERE patroller_id=\"" . $ID . "\" AND date<\"$today\" AND areaID=\"$currID\" AND shift<2";
	        //echo $query_string . "<br>";
	   	    $count=0;
	        $result1 = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result la5)");
		    if ($row1 = @mysql_fetch_array($result1)) {
	    	  $count=$row1[count];
	          $totalDays += $count;
		    }
		} else {
			$count = $totalDays = 0;
		}
        //
        //get total histories of this area for TODAY
        //
        $basicCount = $auxCount = 0;
        $query_string = "SELECT * FROM skihistory WHERE date=\"$today\" AND areaID=\"$currID\" AND shift=$currShift";
//echo "$query_string<br>";
        $result1 = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result la6)");
		if($isSwingOnCrest) {
	        $needs_TL="false";
    	    $needs_ATL="false";
		} else {
	        $needs_TL="true";
    	    $needs_ATL="true";
		}
        while ($row1 = @mysql_fetch_array($result1)) {
            $tlead = $row1[teamLead];
            if($tlead == 1) $needs_TL="false";
            if($tlead == 2) $needs_ATL="false";
            //get Patroller ID from ski History, so we can get their classification code
            $pid = $row1[patroller_id];
            $query_string = "SELECT *                    FROM roster WHERE IDNumber=\"" . $pid . "\"";
//            $query_string = "SELECT ClassificationCode FROM roster WHERE IDNumber=\"" . $pid . "\"";
            //echo $query_string . "<br>";
            $result2 = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result la7)");
            if ($row2 = @mysql_fetch_array($result2)) {
				//classification code from this skihistory record
                $cc = $row2[ ClassificationCode ]; //looking for "SR" or "SRA"
//echo "---" . $row2[ FirstName ] . " " . $row2[ LastName ] . " classification=$cc, tlead=$tlead<br>";
                if($cc == "AUX" || $cc == "SRA" )
                    $auxCount += 1;
                else if($tlead != 1 && $tlead != 2) {
					//if this was NOT a team lead spot, increment basic count 
					// (team lead count is stored in $needs_TL & $needs_ATL
                    $basicCount += 1;
				} else {
//do nothing right now
				}
            } else {
                echo "Failed to get row data, $result2<br>";
            }
         } //end loop reading skihistory for today and a specific area
		//continue loop for each area
//echo "area=$currID TL=$needs_TL ATL=$needs_ATL<br>";
//====
echo "<script language=\"JavaScript\">\n";
echo "<!--\n";
echo "TL[$currID] = $needs_TL;\n";
echo "ATL[$currID] = $needs_ATL;\n";
echo "-->\n";
echo "</script>\n";
//====

        //real amount available
        $available = $maxBasic + $maxAux - $basicCount - $auxCount;   //extra 2 if for TL & ATL
//echo "currID=$currID, available=$available<br>";
//        if($currShift && ($currDayOfWeek == "Saturday" || $currDayOfWeek == "Sunday")) {
		//add team lead and Asst Team Lead (2 patrollers)
		// if Morning shift, and on a weekend
        if($currShift==0 && ($currDayOfWeek == "Saturday" || $currDayOfWeek == "Sunday")) {
			//if I can be a team lead and positions are available, then show as available
			if($canBeTeamLead) {
	            if($needs_TL == "true") {
	            	$available++;
				}
	            if($needs_ATL == "true") {
 	            	$available++;
				}
			}
			//but if TL and/or ATL already filled, they must be accounted for
        }

		if($available < 0)	//can overbook basicCount and auxCount
			$available = 0;
		//don't allow Auxilaries, if no aulixary positions available
        if($isAux) {
            $available = min(($maxAux - $auxCount),$available);
        }
//echo "***auxCount=$auxCount, basicCount=$basicCount, maxBasic=$maxBasic, maxAux=$maxAux || available=$available<br>";
        echo "<tr>\n";
		//decide what area's are displayed as enabled/disabled
        if($inTraining == 1 && $currID <= 2) {  //disable crest/gw/milli if in training
            $dis = "DISABLED";
            $available = 0;
        } else if($available > 0 || 	//patroller count is positive -or-
        			$currID > 2 || 		//Training or Staff, always available -or-
        			$areaID==$currID ||	//already assigned to this area
        			$isSwingOnCrest) {	//swing or night AND Crest
			//enabled if slots available, or Training/Staff, or already assigned to this area
            $dis = "";
        } else
            $dis = "DISABLED";
		
        echo "<td align=\"left\"><input type=radio name=areaID $dis " . ($areaID==$currID ? "checked" : "") . " value=\"$currID\" onclick=\"madeChanges()\">&nbsp;$areaName\n";
        echo "<input type=hidden name=areaID_available value=\"$available\"></td>\n";
        echo "<td align=\"center\">";
        if($open == 2 || $isSwingOnCrest)
            echo "&nbsp;";
        else
            echo $available;
        echo "</td>\n";
        echo "<td align=\"center\">$count</td>\n";
        //        echo "<td align=\"center\">$nightCount</td>\n";
        echo "</tr>\n";
    } //end loop reading areas

    echo "<tr>\n";
    echo "  <td align=\"left\" colspan=\"2\" bgcolor=\"#FFFF00\">\n";
    echo "  <p align=\"right\">(Totals don't include today) <b>Season Totals</b></td>\n";
    echo "  <td align=\"center\" bgcolor=\"#FFFF00\">$totalDays</td>\n";
//    echo "  <td align=\"center\" bgcolor=\"#FFFF00\">$totalNights</td>\n";
    echo "</tr>\n";
?>
  </table>
    </center>
  </div>
  <p align="center">
<?
      if($history_id)
          echo "<input type=\"HIDDEN\" name=\"history_id\" VALUE=\"$history_id\">\n";
      echo "<input type=\"HIDDEN\" name=\"shift\" VALUE=\"$currShift\">\n";
      echo "<input type=\"HIDDEN\" name=\"shiftValue\" VALUE=\"$shiftValue\">\n";
      echo "<input type=\"HIDDEN\" name=\"ID\" VALUE=\"$ID\">\n";
      echo "<input type=\"HIDDEN\" name=TeamLead VALUE=0>\n";
      echo "<input type=\"submit\" value=\"Save\" disabled name=\"saveBtn\" onclick=\"myButton(1,0)\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
      if($history_id)
            echo "<input type=\"button\" value=\"Remove Assignment\" name=\"resetBtn\" onclick=\"myButton(2,$history_id)\">&nbsp;&nbsp;&nbsp;&nbsp;\n";
      echo "<input type=\"button\" value=\"Cancel\" name=\"abortBtn\" onclick=\"myButton(3,0)\"></p>\n";
      echo "</form>\n";
      echo "<font size=\"4\"><b>Note:</b>&nbsp; If no Mountain Area's display as available, sign in under &quot;Staff&quot;.</font><br>\n";
		if(!$saveBtn) {
		//  echo " FYI, $currDayOfWeek $hr:$min is a ".$currShiftName[$currShift]." worth ".($shiftValue/2)." credit(s)<br>";
		  echo "Note: you are currently signing up for a ".$currShiftName[$currShift]." (worth ".($shiftValue/2)." credits).<br>";
		}
    } //end of NOT being locked out

    @mysql_close($connect_string);
    if($result)
        @mysql_free_result($result);
    if($result3)
        @mysql_free_result($result3);
?>


</body>

</html>