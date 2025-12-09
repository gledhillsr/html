<?php
require("config.php");
    $connect_string = @mysqli_connect($mysqli_host, $mysqli_username, $mysqli_password) or die ("Could not connect to the database.");
    mysqli_select_db($connect_string, $mysqli_db);

    $areaName = $getAreaShort[ $areaID ];
    $arrDate = getdate();
    $today=mktime(0, 0, 0, $arrDate[mon], $arrDate[mday], $arrDate[year]);

    if($AutoFill) {
        require("AutoFill.php");    //this uses
    }
    require("AreaDefinition.php");    //this uses
    // read area definitions
    $areaDef = new AreaDefinition();
    $query_string = "SELECT * FROM areadefinitions WHERE areaID=$areaID";
    $result = @mysqli_query($connect_string, $query_string) or die ("AreaDefinition: Invalid query: $query_string");
    if ($row = @mysqli_fetch_array($result)) {
        $areaDef->init_from_row_query($row);
    } else {
        die ("AreaDefinition: failed to read row from query");
    }
    $BasicMin = $areaDef->saturdaybasic;
    $AuxMin   = $areaDef->saturdayaux;
//      $sundayBasic   = $row[sundaybasic];
//      $sundayAux     = $row[sundayaux];


//==============================================================================
//================================ clear button ===============================
//==============================================================================
if($clear) {
    $query_string = "SELECT sweep_ids, checkin FROM skihistory WHERE shift=0 AND date=$today AND patroller_id=$patrollerID";
//echo "query_string=$query_string<br/>";
    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query 1.5");
    $newSweep="";
    if ($row = @mysqli_fetch_array($result)) {
        $oldSweep = $row[sweep_ids];
//echo "oldSweep=($oldSweep)<br/>";
        $checkin = $row[checkin];
        $tok = strtok($oldSweep, " ");
        while ($tok) {
           if($clear != $tok) {
             $newSweep .= " " . $tok . " ";
//echo "clear=$clear, tok=$tok, newSweep=($newSweep)<br/>";
           }
           $tok = strtok(" ");
        }
    $query_string = "UPDATE skihistory SET sweep_ids=\"$newSweep\" WHERE shift=0 AND date=$today AND patroller_id=$patrollerID AND checkin=$checkin";
//echo "$query_string<br/>";
    @mysqli_query($connect_string, $query_string) or die ("Invalid query 1.6");
    }
}

function timeOverlaps($newT1,$newT1e,$t1,$t1e) {
//echo "test ($newT1 - $newT1e) overlap ($t1, $t1e) ";
    $conflict = ($newT1 >= $t1 && $newT1 < $t1e) || ($newT1e > $t1 && $newT1e <= $t1e);
//if($conflict) echo " CONFLICTS<br/>";
//else echo " no conflict<br/>";
    return $conflict;
}
//==============================================================================
//================================ insert button ===============================
//==============================================================================
if($insert) {   //$insert is the ID of the new sweep
    $timeConflict = false;
//echo "sweepID=$insert, patrollerID=$patrollerID<br/>";
//    alert("Error, time conflict: 13:00-14:00 / 10:00-11:00 conflicts with 13:30-15:00");

    //read all sweeps that is patroller has assigned
    $query_string = "SELECT sweep_ids FROM skihistory WHERE areaID=$areaID AND date=$today AND shift=0 AND patroller_id=$patrollerID";
    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query 2");
    $oldSweep="";
    if ($row = @mysqli_fetch_array($result)) {
        $oldSweep = $row[sweep_ids];
    }
    $newID = " " . $insert . " ";   //I want the ID with spaces so '2' wong be found in '21'.  Stored with space delimiter.
//check for time conflicts
       $query_string = "SELECT * FROM sweepdefinitions WHERE id=$insert";
//echo "$query_string<br/>";
   $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query 11");
    if ($result && $row = @mysqli_fetch_array($result)) {
        $newT1  = $row[ start_time ];   //time in seconds
        $newT1e = $row[ end_time ];
        $newT2  = $row[ start_time2 ];
        $newT2e = $row[ end_time2 ];
//      $desc   = $row[ description ];
        $locNew = $row[location];
    }
//    $pos = strpos($desc,"\n");
//    if($pos === false) $pos = 20;
//    $descNew = substr($desc,0,$pos-1 );
//echo "new sweep ($descNew) t1=$newT1 - t1e=$newT1e t2=$newT2, t2e=$newT2e<br/>";

//tokenize each sweep
    $tok = strtok($oldSweep, " ");
    while ($tok) {
       $sweepID = $tok;
       $query_string = "SELECT * FROM sweepdefinitions WHERE id=$sweepID";
//echo "$query_string<br/>";
       $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query 11");
        if ($result && $row = @mysqli_fetch_array($result)) {
            $t1  = $row[ start_time ]; //time in seconds
            $t1e = $row[ end_time ];
            $t2  = $row[ start_time2 ];
            $t2e = $row[ end_time2 ];
//          $desc= $row[ description ];
            $loc = $row[location];
        }
//        $pos = strpos($desc,"\n");
//        if($pos === false) $pos = 20;
//        $desc = substr($desc,0,$pos-1);
//echo "&nbsp;&nbsp;&nbsp;old sweep ($loc) ($t1 - $t1e)";
//if($t2 > 0) echo " and ($t2 - $t2e)";
//echo "<br/>";
        if($insert == $tok) {
            //skip all this nonsence, they just did a screen refresh
        }else if(timeOverlaps($newT1,$newT1e,$t1,$t1e))   //check 1st time entries
            $timeConflict = true;
        else if($newT2 > 0 && timeOverlaps($newT2,$newT2e,$t1,$t1e))   //check new 2nd entry against old 1st
            $timeConflict = true;
        else if($t2 > 0 && timeOverlaps($newT1,$newT1e,$t2,$t2e))   //check new 1st entry entry against old 2nd
            $timeConflict = true;
        else if($newT2 > 0 && $t2 > 0 && timeOverlaps($newT2,$newT2e,$t2,$t2e))   //check New 2nd entry entry against old 2nd
            $timeConflict = true;

        if($timeConflict) {
echo "*** Conflict ***<br/>";
        $msg1 = "$locNew (" . secondsToTime($newT1) . "  - " . secondsToTime($newT1e) . ")";  //id is $insert
        if($newT2 > 1 && $newT2e > 1 ) $msg1 .= " and (" . secondsToTime($newT2) . " - " . secondsToTime($newT2e) . ")";
echo "$msg1 <br/>";
        $msg2 = "$loc (" . secondsToTime($t1) . " - " . secondsToTime($t1e) . ")";    //id is $tok
        if($t2 > 1 && $t2e > 1 ) $msg2 .= " and (" . secondsToTime($t2) . " - " . secondsToTime($t2e) . ")";
echo "$msg2 <br/>";
echo "*****************<br/>";
            $szTimeConflict = "$msg1\n$msg2";
            $szTimeConflict = "Error: Time conflict:\\n\\n$msg1 \\n$msg2\\n\\nInsert NOT accepted";
            break;
        }
       $tok = strtok(" ");
    }

    //test if this sweep is already assigned to this patroller
    $pos = strpos($oldSweep,$newID);
//echo "pos=$pos<br/>";

    if($timeConflict == false && $pos === false) { //should be false, unless a screen refresh was done
        $oldSweep .= $newID; //keep leading & trailing space, make's it easy to find later
//echo "new sweep info=(" . $oldSweep . ")<br/>";
        $query_string = "UPDATE skihistory SET sweep_ids=\"$oldSweep\" WHERE areaID=$areaID AND date=$today AND shift=0 AND patroller_id=$patrollerID";
//echo "$query_string<br/>";
        @mysqli_query($connect_string, $query_string) or die ("Invalid query 3");
    } else {
//echo "sweep ID already exists, not adding it again<br/>";
    }
}

//======================================================================
//=============================== addRow ===============================
//======================================================================
function addRow($class,$ID,$name,$areaID,$sweeps){
    global $connect_string,$unassignedCnt,$rowNum;

	$rowNum++;
	if(($rowNum & 1) == 1)
		$rowColor = "\"#F7F7EF\""; //F7F7EF
	else
		$rowColor = "\"#e0e0E8\"";	//F0F0E8
    if($name == "") {
        $name = "&nbsp;";
        $unassignedCnt++;
    }
    if(!$sweeps) $sweeps="";
//tokenize each sweep
    $cnt = 0;
    $tok = strtok($sweeps, " ");
    while ($cnt == 0 || $tok) {
       $cnt++;
       if($tok)
           $sweepID = $tok;
       else
           $sweepID = "";

        if(strlen($sweepID) > 0) {
    //-- sweep found --
            $query_string = "SELECT * FROM sweepdefinitions WHERE id=$sweepID";
    //echo "$query_string<br/>";
            $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query 4");
        }
        if ($result && $row = @mysqli_fetch_array($result)) {
            $sweep = $row[ closing ];
            $topShack =  $row[ location ];
    //      if($area != $areaID && $area != 5) //must be THIS area, or "Any Area"
    //          continue;
            $t1 = $row[ start_time ];
            $t2 = $row[ end_time ];

            $t3 = $row[ start_time2 ];
            $t4 = $row[ end_time2 ];

            $time = secondsToTime($t1) . " - " . secondsToTime($t2);
            if($t3 > 0) {
                $time .= "<br/>" . secondsToTime($t3) . " - " . secondsToTime($t4);
                $topShack .=  "<br/>" . $row[ location2 ];
            }
            $desc = $row[ description ];
            if(trim($desc) == "")
                  $desc = "&nbsp;";

            $pos = strpos($desc,"\n");
            if(!$pos)
                $pos = 30;
            else
                $pos = min($pos,30);
            $desc = substr($desc,0,$pos);
        } else {
          $sweepID="0"; //flag for insert button
          $desc = "&nbsp;";
          $topShack = "&nbsp;";
          $time = "&nbsp;";
          $sweep = "&nbsp;";
        }
    //--- end sweep found or NOT

        if(!$ID)
            $ID = 0; //flag for insert button
        if($cnt > 1) {
            $class = "&nbsp;";
            $name  = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;''";
        }
        echo "<tr>\n";
        echo "  <td width=\"30\"  bgcolor=$rowColor align=center><font size=2>$class</font></td>\n"; //ski level
        echo "  <td width=\"130\" bgcolor=$rowColor><font size=2>$name</font></td>\n";        //patroller
        echo "  <td width=\"207\" bgcolor=$rowColor><font size=2>$desc</font></td>\n";        //description
        echo "  <td width=\"100\" bgcolor=$rowColor><font size=2>$topShack</font></td>\n";    //top
        echo "  <td width=\"90\"  bgcolor=$rowColor><font size=2>$time</font></td>\n";        //time
        echo "  <td width=\"70\"  bgcolor=$rowColor><font size=2>$sweep</font></td>\n";       //sweep
        echo "  <td width=\"130\" bgcolor=$rowColor>\n";
        echo "<button style=\"border:0; background-color: transparent\" onClick=\"insertBtn($ID,$areaID)\">\n";
        echo "<img src=\"images/btnInsert.jpg\" width=\"46\" height=\"15\" border=\"0\" alt=\"insert\">\n</button>\n";
      if($sweepID != "0") {
            echo "<button style=\"border:0; background-color: transparent\" onClick=\"clearBtn($ID,$areaID,$sweepID)\" >\n";
            echo "<img src=\"images/btnClear.jpg\" width=\"46\" height=\"15\" border=\"0\" alt=\"clear\">\n</button>\n";
      }
        echo "</td></tr>\n";
       $tok = strtok(" ");
    } //end loop for each assignment
}
//========================== output top of HTML page =================================
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>

<title>Area Assignments</title>

<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<script type="text/javascript">
function showTimeConflict() {
    alert("<?php echo $szTimeConflict; ?>");
}

function insertBtn(patrollerID, areaID) {
    var assID = document.myForm.assignments.selectedIndex;
    if (assID < 0) {
        alert("Error, you must first select a sweep.");
    } else if (!patrollerID) {
        alert("Oops, no patroller ID is defined");
    } else {
        var sel = document.myForm.assignments;
        var sweepID = sel.options[assID].value;
        document.myForm.insert.value=sweepID;
        document.myForm.patrollerID.value=patrollerID;
        window.location = "hill_assignments.php?insert=" + sweepID + "&patrollerID=" + patrollerID + "&areaID=" + areaID;
    }
}
function clearBtn(patrollerID, areaID, sweepID) {
    if (!patrollerID) {
        alert("Oops, no patroller ID is defined");
    } else if (sweepID == 0) {
        alert("Oops, no sweep is assigned");
    } else {
        document.myForm.clear.value=sweepID;
        document.myForm.patrollerID.value=patrollerID;
        window.location = "hill_assignments.php?clear=" + sweepID + "&patrollerID=" + patrollerID + "&areaID=" + areaID;
    }
}
</script>
</head>

<body background="images/ncmnthbk.jpg" <?php if ($timeConflict) echo "onload=\"showTimeConflict()\""; ?> >
<form method="POST" name="myForm" action="hill_assignments.php">
<INPUT TYPE="hidden" NAME="clear">
<INPUT TYPE="hidden" NAME="insert">
<INPUT TYPE="hidden" NAME="patrollerID">
<div style="height:auto;width:822">
  <table border="1" width="820" cellspacing="0" cellpadding="0">
    <tr>
      <td width="175" bgcolor="#E1E1E1" rowspan="2">
        <p align="center">&nbsp;<b><?php echo $areaName; ?></b><br>Patroller&nbsp;Assignments</p></td>
      <td width="643" bgcolor="#EBEBEB" colspan="5">
        <p align="center"><b><?php echo $areaName; ?></b> Work Assignments</p></td>
    </tr>
    <tr>
      <td width="200" bgcolor="#EBEBEB" align="center"><font size="2"><b>Early Sweep</b></font></td>
      <td width="95" bgcolor="#EBEBEB" align="center"><font size="2"><b>Shift Duties</b></font></td>
      <td width="90" bgcolor="#EBEBEB" align="center"><font size="2"><b>Shift Time</b></font></td>
      <td width="68" bgcolor="#EBEBEB" align="center"><font size="2"><b>Closing Sweep</b></font></td>
      <td width="125" bgcolor="#EBEBEB" align="center"><font size="2"><b>Make Changes</b></font></td>
    </tr>
  </table>
</div>
<?php
$query_string = "SELECT sweep_ids, patroller_id FROM skihistory WHERE date=$today AND shift=0";
$sweepsAssigned=array();
    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query 5");
	$rowNum = 0;
//	$rowColor = "\"#F7F7EF\"";
//	$rowColor = "\"#F0F0E8\"";
    while ($row = @mysqli_fetch_array($result)) {
        $oldSweep = $row[sweep_ids];
        //loop through all sweep tokens in the list
        $tok = strtok($oldSweep, " ");
        while ($tok) {
            $sweepsAssigned[$tok] = $row[patroller_id];
//echo "sweepsAssigned[" . $tok . "] = $row[patroller_id]<br/>";
           $tok = strtok(" ");
        }
    }

    $query_string = "SELECT * FROM skihistory WHERE areaID=$areaID AND date=$today AND shift=0";
//echo "$query_string<br/>";
$unassignedCnt = 0;
$basicID=array();
$basicName=array();
$basicSweeps=array();
$auxID=array();
$auxName=array();
$auxSweeps=array();
$xtraID=array();
$xtraName=array();
$xtraSweeps=array();
    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query 5");
    while ($row = @mysqli_fetch_array($result)) {
		//
		// loop through all assignments for 1 specified area, on this day
		//
        $patrollerID = $row[ patroller_id ];
        $teamLead = $row[teamLead];
        $sweepIDs = $row[sweep_ids];
        //real ALL id's
        if($sweepIDs) {
            $sweepIDs = trim($sweepIDs);
//if(strlen($sweepIDs) > 0)
// echo "patrollerID=$patrollerID, sweepIDs=($sweepIDs)<br/>";
        } else
            $sweepIDs = ""; //no sweeps defined

        if($teamLead == 1) {
            $TL_ID = $patrollerID;
            $TL_Sweeps = $sweepIDs;
        } else if($teamLead == 2) {
            $ATL_ID = $patrollerID;
            $ATL_Sweeps = $sweepIDs;
        }
        $query_string = "SELECT * FROM roster WHERE IDNumber=$patrollerID";
//echo "$query_string<br/>";
        $result1 = @mysqli_query($connect_string, $query_string) or die ("Invalid query 6");
        while ($row1 = @mysqli_fetch_array($result1)) {
            $name = $row1[ FirstName ] . " " . $row1[ LastName];
            $class = $row1[ClassificationCode];
            if($TL_ID == $patrollerID) {
                $TL_Name = $name;
            } else if($ATL_ID == $patrollerID) {
                $ATL_Name = $name;
            } else if($teamLead == 3) {
                $xtraName[] = $name;
                $xtraID[] = $patrollerID;
                $xtraSweeps[]=$sweepIDs;
            } else if($class == "BAS" || $class == "SR") {
                $basicName[] = $name;
                $basicID[] = $patrollerID;
                $basicSweeps[]=$sweepIDs;
            } else {
                $auxName[] = $name;
                $auxID[] = $patrollerID;
                $auxSweeps[]=$sweepIDs;
            }
        }
    }
?>
<div style="overflow:auto;height:275px;width:852px">
  <table border="1" width="817" cellspacing="0" cellpadding="0">
<?php
    addRow("TL",$TL_ID,$TL_Name,$areaID,$TL_Sweeps);
    addRow("ATL",$ATL_ID,$ATL_Name,$areaID,$ATL_Sweeps);
    $rowCnt = max(count($basicID),$BasicMin);
    for($i=0; $i < $rowCnt; $i++)
        addRow("bas",$basicID[$i],$basicName[$i],$areaID,$basicSweeps[$i]);
    //remember to account for Extra basics that used up Auxilary spots
    $rowCnt = max(count($auxID),$AuxMin) - max(0,count($basicID) - $BasicMin);
    //loop for each auxilary spot
    for($i=0; $i < $rowCnt; $i++)
        addRow("aux",$auxID[$i],$auxName[$i],$areaID,$auxSweeps[$i]);
    for($i=0; $i < count($xtraName); $i++)
        addRow("xtra",$xtraID[$i],$xtraName[$i],$areaID,$xtraSweeps[$i]);
?>
  </table>
</div>
  <table border="1" width="820" cellspacing="0" cellpadding="0">
    <tr>
      <td width="265" bgcolor="#C4FBF8">
        <p align="center"><?php echo $unassignedCnt; ?> patroller slot(s) unassigned</p></td>
      <td width="635" bgcolor="#C4FBF8">
        <p align="center">
<?php
    echo "<input type=\"HIDDEN\" name=\"areaID\" VALUE=\"$areaID\">";
?>
<input type="submit" value="Auto Fill Assignments" name="AutoFill">
      </p></td>
    </tr>
  </table>
<?php
//echo "areaID=" . $areaID . "<br/>";
//echo "insert=" . $insert . "<br/>";
//echo "clear=" . $clear . "<br/>";
//echo "AutoFill=" . $AutoFill . "<br/>";
//echo "patrollerID=" . $patrollerID . "<br/>";
?>
&nbsp;&nbsp;
<?php /*-------LIST ALL AVAILABLE ASSIGNMENTS--------------*/ ?>
<table border="1" width="540"  bgcolor="#EBEBEB" >
  <tr>
    <td>
  <p align="center">
  Unassigned Top Shack/Aid Room</p>
    </td>
  </tr>
  <tr>
    <td>
      <select size="5" name="assignments" style="width:540">
<?php
    $sel="SELECTED";
    $query_string = "SELECT * FROM sweepdefinitions WHERE 1 ORDER BY location, start_time";
//echo "$query_string<br/>";
    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query 7");
    while ($row = @mysqli_fetch_array($result)) {
        $area = $row[ areaID ];
        $loc =  $row[ location ];
		if($loc =="Aid Room 1") {
		}
        else if($area != $areaID && $area != 5) //must be THIS area, or "Any Area"
            continue;
        $time = $row[ start_time ];
        $time = secondsToTime($time);
        $t2 = $row[ end_time ];
        $t2 = secondsToTime($t2);
 $secondTime = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$t3 = $row[ start_time2 ];
		$t4 = $row[ end_time2 ];
		if($t3 > 0)
			$secondTime = "&nbsp;(" . secondsToTime($t3) . "" . secondsToTime($t4) . ")";
        $id   = $row[ id ];
        if($sweepsAssigned[$id])
            continue;
        $desc = $row[ description ];
        $pos = strpos($desc,"\n");
        if(!$pos)
            $pos = 30;
        else
            $pos = min($pos,30);
        $desc = substr($desc,0,$pos);

//echo "loc=$loc, getTopShack[0]=" . $getTopShack[0] . "<br/>\n";
        $space = "&nbsp;&nbsp;";
        $area = $getAreaShort[ $area ];
        if($loc == $getTopShack[0]) $space = "&nbsp;&nbsp;&nbsp;&nbsp;"; //crest
        else if($loc == $getTopShack[1]) $space = "&nbsp;&nbsp;";    //snake
        else if($loc == $getTopShack[2]) $space = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";    //majestic
        else if($loc == $getTopShack[3]) $space = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";    //western
        else if($loc == $getTopShack[4]) $space = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";  //milli
        else if($loc == $getTopShack[5]) $space = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";  //aid room 1
        else if($loc == $getTopShack[6]) $space = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";  //aid room 2

        echo "<option value=$id $sel>$loc, $space($time - $t2) $secondTime &nbsp;&nbsp;&nbsp; $desc</option>\n";
        $sel="";
    }
?>
  </select>
    </td>
  </tr>
</table>

</form>
<?php
    @mysqli_close($connect_string);
    if($result)
        @mysqli_free_result($result);
?>

</body>
