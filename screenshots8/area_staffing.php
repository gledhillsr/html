<?php 
require("config.php");
    $connect_string = @mysqli_connect($mysqli_host, $mysqli_username, $mysqli_password) or die ("Could not connect to the database.");
    mysqli_select_db($connect_string, $mysqli_db);
    $arrDate = getdate();
//$showWeb=0; //hack
//echo "shiftOverride=$shiftOverride<br>";
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
        $currDayOfWeek = $arrDate[\WEEKDAY];
        $sec=$arrDate[\SECONDS];
        $min=$arrDate[\MINUTES];
        $hr =$arrDate[\HOURS];
    }
    $today=mktime(0, 0, 0, $arrDate[\MON], $arrDate[\MDAY], $arrDate[\YEAR]);
    $getLBName = [-1 => "Unassigned", 0 => "Crest", 1 => "Snake", 2 => "Western",   3 => "Millicent",  4 => "Training", 5 => "Staff"];
    $strToday = date("F-d-Y", $today);
    if($saveBtn) {
        if($area0)  updateHistory($area0,-1);  //unassigned
        if($area1)  updateHistory($area1,0);  //Crest
        if($area2)  updateHistory($area2,1);  //Snake
        if($area3)  updateHistory($area3,2);  //Western
        if($area4)  updateHistory($area4,3);  //Milli
        if($area5)  updateHistory($area5,4);  //Training
        if($area6)  updateHistory($area6,5);  //Staff
    }

    $totalPatrollers = 0;
//============================================================
// --------------- updateHistory  -----------------------------
//============================================================
function updateHistory($str,$area) {
global $connect_string,$today;

//echo "area$area = $str<br>";

    $tok = strtok($str, ",");
    $newSweep="";
    while ($tok) {
//echo "tok=$tok<br>";
        $id = substr($tok,0,6);
		//fixup if patroller has only a 5 diget ID
		if($id[5] < '0' || $id[5] > '9') $id = substr($tok,0,5);
//echo "id=$id<br>";
        $newLeadership = 0;
        if($area >= 0 && $area <= 3 ) {   // 
            $where = substr($tok,7,2);
            if($where == "TL")      $newLeadership = 1;
            else if($where == "AT") $newLeadership = 2;
            else if($where == "Xt") $newLeadership = 3;
            else                    $newLeadership = 0;
        }
        $query_string = "SELECT * FROM skihistory WHERE shift=0 AND date=$today AND patroller_id=$id";

//echo "$query_string<br>";
//        $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query ");
        $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result 1)");
//echo "result=$result<br>";
        if ($row = @mysqli_fetch_array($result)) {
//echo "row=$row<br>";
            $oldLeadership = $row[\TEAMLEAD];
            $oldArea = $row[\AREAID];
            $oldSweep = $row[\SWEEP_IDS];
            $history_id =  $row[\HISTORY_ID];
//echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;leadership=($oldLeadership, $newLeadership)  area($oldArea, $area)<br>";
            if($oldArea != $area)
                $newSweep = "";
            else
                $newSweep = $oldSweep;
            if($oldLeadership != $newLeadership || $oldArea != $area) {
                $query_string = "UPDATE skihistory SET sweep_ids=\"$newSweep\", areaID=$area, teamLead=$newLeadership  WHERE history_id=$history_id";
//echo "$query_string (id=$id)<br>";
                @mysqli_query($connect_string, $query_string) or die ("Invalid query (result 2)");
            }
        }
       $tok = strtok(",");
    }
//echo "leaving<br>";
}
//============================================================
//----------------- insertRow --------------------------------
//============================================================
function insertRow($tl,$minRows,$skiLevel){ //skiLevel, 1=basic/sr, 2=aux/SrA
global $connect_string,$areaID,$getLBName,$today,$totalPatrollers,$currDayOfWeek;
    if($areaID == -1)
        $query_string = "SELECT * FROM skihistory WHERE date=\"$today\" AND shift=0 AND areaID=\"-1\" ORDER BY checkin";
    else
        $query_string = "SELECT * FROM skihistory WHERE date=\"$today\" AND shift=0 AND areaID=\"$areaID\" and teamLead=$tl ORDER BY checkin";
//echo "\n------\ninsertRow($tl,$minRows,$skiLevel)\n$query_string" . "<br>\n";
    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result 3)");
    $cnt = 0;
//Loop in skihistory for today, shift 0, AND this area
    while ($row = @mysqli_fetch_array($result)) {
        $patroller_id  = $row[\PATROLLER_ID ];
        $patroller_name = "Name not found.";
        $query_string = "SELECT FirstName, LastName, ClassificationCode FROM roster WHERE IDNumber=\"$patroller_id\"";
//echo $query_string . "<br>\n";
//get Roster information
        $result2 = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result 4)");
        if ($row2 = @mysqli_fetch_array($result2)) {
            $class = $row2[\CLASSIFICATIONCODE];
//echo "totalPatroller = $totalPatroller, class=$class<br>";
            if($class == "SR") $prefix = "1-";
            else if($class == "BAS") $prefix = "2-";
            else if($class == "AUX" || $class == "SRA") $prefix = "3-";
            else $prefix = "4-";
            $patroller_name = $prefix . $row2[\FIRSTNAME] . " " . $row2[\LASTNAME];
            //if I am filling out basic, and I get an Aux (or visa-versa) skip this person
            $bas = ($class == "BAS" || $class == "SR");
            if($bas && $skiLevel == 2) continue;
            if(!$bas && $skiLevel == 1) continue;

        }
//echo $patroller_name . "<br>";
        $totalPatrollers++;
        $teamLead = $row[\TEAMLEAD];
        $cnt++;
        if($areaID == -1) {
            echo "    <option value=\"$patroller_id\">$patroller_name</option>\n";
        } else {
            if($areaID > 3)        $pos = ".....";
            else if($teamLead == 1) $pos="TL...";
            else if($teamLead == 2) $pos="ATl..";
            else if($teamLead == 3) $pos="Xtra.";
            else if($skiLevel == 1 && $minRows >= $cnt) $pos="Bas..";   //bas.. aux.. xtra.
            else $areaID . $pos="aux..";   //bas.. aux.. xtra.
//            else if($class == "SR" || $class == "BAS") $pos="bas..";   //bas.. aux.. xtra.
//            else $pos="aux..";   //bas.. aux.. xtra.
            echo "  <option value=\"$patroller_id\">{$pos}{$patroller_name}</option>\n";
        }
    } //end loop for each patroller
   	if($currDayOfWeek != "Saturday" && $currDayOfWeek != "Sunday") 
		return 0;


	//display blanks to fill up to $minRows
    for($i = $cnt; $i < $minRows; $i++) {
        if($tl == 1)            $pos = "TL..";
        else if($tl == 2)       $pos = "ATl..";
        else if($tl == 3)       $pos = "Xtra.";
        else if($skiLevel == 1) $pos = "bas.."; //bas or sr
        else if($skiLevel == 2) $pos = "aux.."; //aux
        else                    $pos = "....."; //extra
        echo "  <option>{$pos}</option>\n";
    }
	if ($cnt > $minRows) 
		return ($cnt - $minRows);
	return 0;
}
//this kills the display
//<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
//<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
//<!DOCTYPE HTML>
?>
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache">
<meta HTTP-EQUIV="Expires" CONTENT="-1">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Unassigned</title>

<script type="text/JavaScript">
var changesMade = false;
var param;
//configure refresh interval (in seconds)
var countDownInterval = 0;

function refreshDocument() {
    if (changesMade) {
        if (window.confirm("Save your changes?")) {
            saveChanges();
        }
    }
    else
        window.location.reload();
}
function cancelChanges() {
    changesMade = false;
    window.location.reload();
}

function checkForChanges() {
    if (changesMade) {
        if (window.confirm("Save your changes?")) {
            saveChanges();
        }
    }
}

function madeChanges() {
    changesMade = true;
    //      document.myForm.saveBtn.disabled = false;
}

// bldParam - build Param string that ONLY consists of valid List box ID's
function bldParam(listbox, area) {
    var count = listbox.length;
    var id,txt;
    var first = true;

    for (var i = 0; i < count; i++) {
        id = listbox.options[i].value;
        if (area == 0)
            txt = "Un";
        else
            txt = listbox.options[i].text.substr(0, 2);
        //alert("parseFloat(id)="+parseFloat(id));

        //alert("i="+i+", id=("+id+"), txt="+txt);
        id = parseFloat(id);
        if (id != NaN && id > 0) {
            if (first) {
                param += "&area" + area + "=" + id + "-" + txt;
                first = false;
            } else
                param += "," + id + "-" + txt;
        }
        //	param += "&_" + area + "_";
    }
}

function saveChanges() {
    changesMade = false;

    param = "area_staffing.php?saveBtn=1";
    bldParam(document.form1.UnassignedLB, 0);
    bldParam(document.form1.CrestLB, 1);
    bldParam(document.form1.SnakeLB, 2);
    bldParam(document.form1.WesternLB, 3);
    bldParam(document.form1.MillicentLB, 4);
    bldParam(document.form1.TrainingLB, 5);
    bldParam(document.form1.StaffLB, 6);
    //alert(param);
    //alert("data=" + param);
    window.location.href = param;
    //alert("now don't you wish SAVE worked :-)");
}

function webStaffing() {

    changesMade = false;

    param = "area_staffing.php?saveBtn=1&showWeb=1";
    bldParam(document.form1.UnassignedLB, 0);
    bldParam(document.form1.CrestLB, 1);
    bldParam(document.form1.SnakeLB, 2);
    bldParam(document.form1.WesternLB, 3);
    bldParam(document.form1.MillicentLB, 4);
    bldParam(document.form1.TrainingLB, 5);
    bldParam(document.form1.StaffLB, 6);
    //alert(param.substr(0,20));
    window.location.href = param;
    //    alert("now don't you wish SAVE worked :-)");
}

function do_add(pos) {
    var listbox;
    //the 'add' only button is only available for column 4 (training) and 5 (staff)
    if (pos == 4)
        listbox = document.form1.TrainingLB
    else
        listbox = document.form1.StaffLB

    var lastPos = listbox.options.length;
    var myNewOption = new Option(".....");
    listbox.options[lastPos] = myNewOption;
    listbox.options.selectedIndex = -1;
}

function del_xtra(pos) {
    var listbox;
    if (pos == 0)
        listbox = document.form1.CrestLB
    else if (pos == 1)
        listbox = document.form1.SnakeLB
    else if (pos == 2)
            listbox = document.form1.WesternLB
        else //3
            listbox = document.form1.MillicentLB

    var lastPos = listbox.options.length;
    //loop through all entries and if a "Xtra." is found, delete it
    var found = false;
    for (var $i = 2; $i < lastPos; $i++) {
        var txt = listbox.options[$i].text;
        if (txt == "Xtra.") {
            found = true;
            listbox.options[$i] = null;
            break;
        }
    }
    if (found == false)
        alert("Oops, no EMPTY 'Xtra.' positions found");
}

function add_xtra(pos) {
    var listbox;
    if (pos == 0)
        listbox = document.form1.CrestLB
    else if (pos == 1)
        listbox = document.form1.SnakeLB
    else if (pos == 2)
            listbox = document.form1.WesternLB
        else // 3
            listbox = document.form1.MillicentLB

    var lastPos = listbox.options.length;
    var myNewOption = new Option("Xtra.");
    listbox.options[lastPos] = myNewOption;
    listbox.options.selectedIndex = -1;
}

function move_name(mountain, listbox) {
    changesMade = true;
    var currIndex = listbox.selectedIndex;
    var currID = listbox.options[currIndex].value;
    var currName = listbox.options[currIndex].text;
    var currTitle = currName.substr(0, 5);
    currName = currName.substr(5);
    var messageStr
    var unAssigned = document.form1.UnassignedLB;
    //  var txtDisplay   = document.form1.lastCommand;
    var unIndex = unAssigned.selectedIndex
    var unID
    var unName
    if (unIndex != -1) {
        unID = unAssigned.options[unIndex].value;
        unName = unAssigned.options[unIndex].text;

        //currTitle ->"bas..", "TL...", "ATL..", etc
        var newLevel = unName.substr(0, 1);	//1 (sr),2(bas),3(aux),4(can...)
        if (newLevel >= 3 && (currTitle == "bas.." || currTitle == "TL..." || currTitle == "ATl..")) {
            alert("Error, invalid assignment.  Must have SR or BAS ski level");
            unAssigned.selectedIndex = -1;	//unselect everything
            listbox.selectedIndex = -1;
            return;
        }
    }

    //  alert("currindex=("+currIndex+")\n unindex=("+unIndex+")\n currID=("+currID+")\n currTitle=("+currTitle+")");
    //alert("(" +currTitle+", " + newLevel + ")");
    if (unIndex == -1 && (currID == null || currID == 0)) {
        messageStr = "Oops, no one is selected as Unassigned";
        listbox.selectedIndex = -1;
    } else {
        if (currID && currID > 0) {
            //
            // there was a patroller in this listbox, so move to "Unassigned"
            //

            messageStr = "Moved '" + currName + "' from " + mountain + " To 'Unassigned' (id=" + currID + ")"
            lastPos = unAssigned.options.length; //this is bogus, I should not subtract a 1
            //add to end of "unassigned" list
            var myNewOption = new Option(currName, currID);
            unAssigned.options[lastPos] = myNewOption;
            unAssigned.options.selectedIndex = lastPos;
            //remove from current list
            listbox.options[currIndex].value = null;
            listbox.options[currIndex].text = currTitle;
            listbox.selectedIndex = -1;
        } else {
            //
            //this position was empty, so move "unassigned" patroller here
            //
            messageStr = "Moved " + unName + " to " + mountain + " (id=" + unID + ")";    //history text
            listbox.options[currIndex].text = currTitle + unName;               //add to new list box
            listbox.options[currIndex].value = unID;
            listbox.selectedIndex = -1;
            unAssigned.options[unIndex] = null;                                //remove from Unassigned
            if (unAssigned.options.length <= unIndex)
                unAssigned.selectedIndex = unIndex - 1;
            else
                unAssigned.selectedIndex = unIndex;
        }
    }
    //display string of what changed
    //    txtDisplay.value = messageStr;
}

function congrats (data) {
    alert("cool");
}
var countDownTime = countDownInterval - 1;

function countUp() {
    countDownTime++;
    //  if (countDownTime > 100){
    //    countDownTime=countDownInterval;
    //    clearTimeout(counter)
    //    window.location.reload()
    //    return
    //  }
    var sec = countDownTime % 60;
    var min = (countDownTime - sec ) / 60;
    var tim = min + ':';
    if (sec < 10)
        tim += '0';
    tim += sec;

    if (document.all) //if IE 4+
        document.all.countDownText.innerText = tim;
    else if (document.getElementById) //else if NS6+
        document.getElementById("countDownText").innerHTML = tim
    else if (document.layers) { //CHANGE TEXT BELOW TO YOUR OWN
            document.c_reload.document.c_reload2.document.write(
                    'Time since last <a href="javascript:refreshDocument()">refresh</a> is <b id="countDownText">' + tim + ' </b> seconds')
            document.c_reload.document.c_reload2.document.close()
        }
    counter = setTimeout("countUp()", 1000);
}

function startit() {
    if (document.all || document.getElementById) //CHANGE TEXT BELOW TO YOUR OWN
        var sec = countDownTime % 60;
    var min = (countDownTime - sec ) / 60;
    var tim = min + ':';
    if (sec < 10)
        tim += '0';
    tim += sec;
    //    document.write('Time since last <a href="javascript:window.location.reload()">refresh</a> is <b id="countDownText">'+tim+'</b>')
    document.write('Time since last <a href="javascript:refreshDocument()">refresh</a> is <b id="countDownText">' + tim + '</b>')
    countUp()
}

if (document.all || document.getElementById) {
    startit();
} else {
    window.onload = function(){
        startit();
    }
}
</script>
</head>

<body onunload="checkForChanges();" background="images/ncmnthbk.jpg">
<form name="form1" method="POST" id="form1" action="area_staffing.php">

<br>
<br>
<?php /*===================== table ================================*/ ?>
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" width="800" id="AutoNumber1">
  <tr>
    <td align="center" width="133">Unassigned</td>
    <td align="center" width="133">Crest</td>
    <td align="center" width="133">Snake Creek</td>
    <td align="center" width="133">Great Western</td>
    <td align="center" width="133">Millicent</td>
    <td align="center" width="133">Training</td>
    <td align="center" width="133">Staff</td>
<?php
	if($showWeb)
		echo "    <td align=center width=133>Web Assignments</td>\n";
?>
  </tr>
  <tr>
<?php
  for($areaID = -1; $areaID <= 5; $areaID++) {
//Display "TL..." or "ATl..." UNLESS training, or Staff
    echo "<td>\n";
    if($areaID == -1) {
      echo "  <select size=25 name=\"" . $getLBName[$areaID] . "LB\" style=\"font-size: 8pt; width: 133\">\n";
      insertRow(0,0,0); //(tl)  1st arg - tl=1, 2=atl, 0=other
    } else if($areaID > 3) {
      echo "  <select size=\"25\" name=\"{$getLBName[$areaID]}LB\" onclick=\"move_name('{$getLBName[$areaID]}',this);\" style=\"font-size: 8pt; width: 133\">\n";
      insertRow(0,3,0); //(tl)  1st arg - tl=1, 2=atl, 0=other
    } else {

//------
    $query_string = "SELECT * FROM areadefinitions WHERE areaID=$areaID";
    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result 5)");
    $todayBasic = 0;
    $todayAux = 0;
    if ($row = @mysqli_fetch_array($result)) {
//echo "$currDayOfWeek<br>\n";
        if($currDayOfWeek == "Saturday") {
            $todayBasic=$row[ \SATURDAYBASIC ];
//echo "basic=$todayBasic<br>\n";
            $todayAux=$row[ \SATURDAYAUX ];
        } else if ($currDayOfWeek == "Sunday") {
            $todayBasic=$row[ \SUNDAYBASIC ];
            $todayAux=$row[ \SUNDAYAUX ];
        } else {
            $todayBasic=0;
            $todayAux=0;
        }
//      $SunBas = $row[sundaybasic];
//      $SunAux = $row[sundayaux];
//      if($row[open] == 1) {
//    }
    }
//------
//echo "basic=$todayBasic<br>\n";

      echo "  <select size=\"25\" name=\"{$getLBName[$areaID]}LB\" onclick=\"move_name('{$getLBName[$areaID]}',this);\" style=\"font-size: 8pt; width: 133\">\n";
      insertRow(1,1,0); //(tl)  1st arg - tl=1, 2=atl, 0=other, 3=Extra
      insertRow(2,1,0); //(atl) 2nd arg - Minimum number of rows to display
//echo "insertRow, basic=$todayBasic<br>\n";
      $extras = insertRow(0,$todayBasic,1); //(bas) 3rd arg - ski level, 1=basic or Senior, 2=Auxilary, 3=Extra
//echo "basic=$todayBasic<br>\n";
	  $todayAux -= $extras;
//echo "insertRow, aux=$todayBasic<br>\n";
	  if($todayAux > 0)
	      insertRow(0,$todayAux,2); //(aux)
      insertRow(3,0,0); //extra
    }
    echo "  </select>\n</td>\n";
  } //end loop for areas

//========= show web column ========
	if($showWeb) {
		//get list of patrollers who are here today
     $query_string = "SELECT * FROM skihistory WHERE date=\"$today\"";
     $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result 10)");
     $lockerIDList=[];
     while ($row = @mysqli_fetch_array($result)) 
     {
			$id = $row[ \PATROLLER_ID];
			$ar = $row[ \AREAID ];
			
         $lockerIDList[$id] = $getLBName[$ar] . " " . $row[ \NAME ] ;
	  }

	   //new column
      echo "  <td><select size=25 name=\"showWebLB\" style=\"font-size: 8pt; width: 133\">\n";
		//close local connection
    	@mysqli_close($connect_string);
    	@mysqli_free_result($result);

		//open web connection
		$mysqli_host = "gledhills.com"; //54.173.17.203";  //IP of nspOnline.org
		$connect_string = @mysqli_connect($mysqli_host, $mysqli_username, $mysqli_password) or die ("Could not connect to web database.");
        mysqli_select_db($connect_string, $mysqli_db);
		//build date string
		  $tdate  = $arrDate[\YEAR] . "-";
		  if($arrDate[\MON] < 10) $tdate .= "0";
		  $tdate .= $arrDate[\MON] . "-";
		  if($arrDate[\MDAY] < 10) $tdate .= "0";
		  $tdate .= $arrDate[\MDAY];
        $query_string = "SELECT * FROM `assignments` WHERE `Date`> '". $tdate . "_0' AND `Date` < '" . $tdate . "_z'";
        $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result 11)");
	     $webIDList=[];

        while ($row = @mysqli_fetch_array($result)) 
        {
            $startTime = $row[\STARTTIME];
				$max1 = $row[ \COUNT ];
				for ($i=0; $i < $max1; ++$i) {
				  	$idx = "P" . $i;
				  	$patroller_id = $row[ $idx ];
					$webIDList[ $patroller_id ] = $patroller_id;
					if($patroller_id != 0) {
//zzz
						$status = "??";	//not here
						if (array_key_exists($patroller_id, $lockerIDList)) {
							$status = "-";	//not here
						}
		        		$query_string = "SELECT FirstName, LastName FROM roster WHERE IDNumber=\"$patroller_id\"";
	        			$result2 = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result 7)");
			        	if ($row2 = @mysqli_fetch_array($result2)) {
							$name = $row2[ \FIRSTNAME ] . " " . $row2[ \LASTNAME ];
					    	echo "  <option>$startTime $status $name</option>\n";
						} else {
					    	echo "  <option>patroller $id not found</option>\n";
						}
					} else {
					    	echo "  <option>$startTime -- EMPTY</option>\n";
					}
				}
        } //end loop for reading web assignments

//zzz
//						$lockerIDList[$id]
//webIDList
       foreach($lockerIDList as $k => $v) {
//			    	echo "  <option>xx $k $v</option>\n";
			  if (!in_array($k, $webIDList)) {
			    	echo "  <option>xx $v</option>\n";
				}
       }
    echo "  </select>\n</td>\n";

    }  //end showWeb


?>
  </tr>
  <tr>
    <td align="center" width="133">Add to list --&gt;</td>
    <td align="center" width="133"><input type="button" value="Xtra" name="B15" onclick="add_xtra(0)"><input type="button" value="Del" name="B17" onclick="del_xtra(0)"></td>
    <td align="center" width="133"><input type="button" value="Xtra" name="B19" onclick="add_xtra(1)"><input type="button" value="Del" name="B20" onclick="del_xtra(1)"></td>
    <td align="center" width="133"><input type="button" value="Xtra" name="B22" onclick="add_xtra(2)"><input type="button" value="Del" name="B23" onclick="del_xtra(2)"></td>
    <td align="center" width="133"><input type="button" value="Xtra" name="B22" onclick="add_xtra(3)"><input type="button" value="Del" name="B233" onclick="del_xtra(3)"></td>
    <td align="center" width="133"><input type="button" value="Add" name="B11" onclick="do_add(4)"></td>
    <td align="center" width="133"><input type="button" value="Add" name="B16" onclick="do_add(5)"></td>
  </tr>
</table>

  <?php echo "<b>$totalPatrollers"; ?> Total Patrollers</b><br>
 
  &nbsp;&nbsp;&nbsp;&nbsp;
  <input type="button" id="save" value="Save Changes" onclick="saveChanges()">
  &nbsp;&nbsp;&nbsp;&nbsp;
  <input type="button" value="Cancel Changes" onclick="cancelChanges()">
  &nbsp;&nbsp;&nbsp;&nbsp;
  &nbsp;&nbsp;&nbsp;&nbsp;
  <input type="button" value="Display Staffing from Web!" onclick="webStaffing()">

</form>
<HR>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=3>Ski Level: 1=SR, 2=BAS, 3=AUX, 4=Other&nbsp;&nbsp;&nbsp;&nbsp;Today is: <?php echo "$currDayOfWeek $strToday"; ?></font>
<?php
    @mysqli_close($connect_string);
    @mysqli_free_result($result);
?>
</body>

</html>
