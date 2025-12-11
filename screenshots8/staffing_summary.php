<?php 
require("config.php");
    $connect_string = @mysqli_connect($mysqli_host, $mysqli_username, $mysqli_password) or die ("Could not connect to the database.");
    mysqli_select_db($connect_string, $mysqli_db);

    $arrDate = getdate();
    $today=mktime(0, 0, 0, $arrDate['mon'], $arrDate['mday'], $arrDate['year']);
?>
<html>

<head>
<meta http-equiv="Content-Language" content="en-us"/>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252"/>
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
<meta HTTP-EQUIV="Expires" CONTENT="-1"/>
<title>Aid Room Summary</title>
<style type="text/css">
<!--
body  {font-size:12px; color:#000000; background-color:#ffffff; marginheight:0; margin-top:0px; margin-bottom:0px}
table.t1 {border-width:1px; border-color:#000000; border-style:solid; border-collapse:collapse; border-spacing:0}
th.t1    {font-size:14px; font-weight: bold; color:#000000; background-color:#E1E1E1; border-width:1px; border-color:#000000; border-style:solid; padding:2px}
td.t1    {font-size:12px; color:#000000; background-color:#EBEBEB; border-width:1px; border-color:#000000; border-style:solid; padding:1px}

table.bas {border-width:0; border-style:none; border-collapse:collapse; border-spacing:0}
td.bas {border-width:0; border-style:none; border-collapse:collapse; border-spacing:0; font-size:12px; color:#000000; background-color:transparent; padding:1px}
//-->
</style></head>

<body background="images/ncmnthbk.jpg">
<script>
function printWindow(){
   bV = parseInt(navigator.appVersion)
   if (bV >= 4) window.print()
}
</script>


<font size="5">&nbsp;&nbsp;Brighton Staffing Report for <?php echo date("l, F d, Y"); ?></font>
&nbsp;&nbsp;<a href="javascript:printWindow()">Print This Page</a><br>
<?php 

  echo "<table width=\"670\" >\n";
  echo "<tr>\n";
//  echo "<td class=bas>\n";
//--------------------------------
//top shack summary, boxes on left
//--------------------------------
/*    for($i=0; $i < 6; $i++) {

  $loc = $getTopShack[$i];
  $start2 = 0;
  $name2 = "";
  $status2 = "";

  echo "<table class=t1 border=\"1\" width=\"345\" cellspacing=\"1\" cellpadding=\"0\">\n";
  echo "<tr>\n";
  echo "<th  class=t1 colspan=\"4\" align=center\">$loc Summary</th>\n";
  echo "</tr>\n";
//find every sweep definition for "Crest", etc and order them by start_time
  $query_string = "SELECT * FROM sweepdefinitions WHERE location=\"$loc\" ORDER BY start_time";
//echo "$query_string<br>";
  $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query ($query_string)");
  while ($row = @mysqli_fetch_array($result)) {
        $start = secondsToTime($row['start_time']);
        $end   = secondsToTime($row['end_time']);
        $areaID  = $row['areaID'];
        $currSweepID = $row['id'];
        if($row['start_time2'] > 0) {
        //hack, only works for 1 shift with 2 times
            $start2 = secondsToTime($row['start_time2']);
            $end2   = secondsToTime($row['end_time2']);
            $currSweepID2 = $currSweepID;
        }
//-----------Find "current sweep ID" in the correct order--------------
//echo "looking for id=$currSweepID<br>";
        //loop through ski history for today, and this find Aid Room

        $query_string = "SELECT * FROM skihistory WHERE DATE=$today AND shift=0";
//echo "$query_string<br>";
        $result2 = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result)");
        $found = false;
        $tl = "";
        $patroller_id = "192443";
        $area = "-";
        $t2 = "";
        //
        // loop thru each skihistory from today and this morning and find the current sweep id
        while (!$found && $row2 = @mysqli_fetch_array($result2)) {
            if($row2['teamLead'] == 1)
                $tl = " / TL";
            else if($row2['teamLead'] == 2)
                $tl = " / ATL";
            else
                $tl = "";
//$tl .= "-" . $row2[teamLead];
            $patroller_id = $row2['patroller_id'];
            $sweep_ids = $row2['sweep_ids'];
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
        } //end while loop
//echo "found=$found, areaID=$areaID<br>";

        //get patroller ID
        if($found) {
            $areaID = $row2['areaID'];
            $area = $getAreaShort[$areaID]; //bad

            $query_string = "SELECT * FROM roster WHERE IDNumber=\"$patroller_id\"";
            $result2 = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result)");
            $class = "xx";
            $name = "xx";
            if ($row2 = @mysqli_fetch_array($result2)) {
                 // patroller query OK
                 $class = $row2['ClassificationCode'];
                 $mentoring = $row2['Mentoring'];
                 if($mentoring == "1")
                    $name = "* ";
                else
                    $name = "";
                 $name .= $row2['FirstName'] . " " . $row2['LastName'];
                 if ($start2 > 0 && $name2 == "") {
                    $name2 = $name;
                    $status2 = $class . $tl;
                 }
            }
        } else {
            //not found
            $class = "&nbsp;";
            $name = "&nbsp;";
            $area = "&nbsp;";
            $tl = "";
        }

      echo "<tr class=t1 >\n";
      echo "  <td  class=t1 width=\"70\"  align=center>$class$tl</td>\n";
      echo "  <td  class=t1 width=\"125\">$name</td>\n";
      echo "  <td  class=t1 width=\"90\" >$start - $end</td>\n";
      echo "  <td  class=t1 width=\"60\" >$area</td>\n";
      echo "</tr>\n";
    } //end while loop
    if($start2 > 0) {
      echo "<tr class=t1 >\n";
      echo "  <td  class=t1 width=\"70\"  align=center>$status2</td>\n";
      echo "  <td  class=t1 width=\"125\">$name2</td>\n";
      echo "  <td  class=t1 width=\"90\" >$start2 - $end2</td>\n";
      echo "  <td  class=t1 width=\"60\" >$area</td>\n";
      echo "</tr>\n";
    }

    echo "</table><br>\n";
    } //end fore loop
  echo "</td>";*/
  echo "<td valign=top align=left class=bas >\n";

//--------------------------------
// hill staffing,  boxes on right
//--------------------------------
  $patrollerCount = 0;
  $trainingCount = 0;
  $candidateCount = 0;
  for($areaID = 0; $areaID <= 6; $areaID++) {
//area
    if($areaID == 6)
        $areaID = -1;   // display unassigned last, not first

    echo "<table class=t1 border=0 width=\"255\">\n";
    $area=$getAreaShort[$areaID];
    echo "<tr><th class=t1  colspan=\"4\" align=Center>$area Staff</td></tr>\n";

    $query_string = "SELECT * FROM skihistory WHERE shift=0 AND date=$today AND areaID=$areaID ORDER by name";
//echo "query_string=$query_string<br>";
    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query 2");
    $tl = "";
    while ($row = @mysqli_fetch_array($result)) {
        $checkin = secondsToTime($row['checkin']);
        $id = $row['patroller_id'];
        if($row['teamLead'] == 1)
            $tl = " / TL";
        else if($row['teamLead'] == 2)
            $tl = " / ATL";
        else
            $tl = "";

        $nAssignments = 0;
        $sweep_ids = $row['sweep_ids'];
        $foo = trim($sweep_ids); //hack
        if($foo != "") {
            $tok = strtok($foo, " ");
            while ($tok) {
//              $nAssignments++;
                //OK ,now quickly look up and see if this sweep ID has 2 shifts
                $query_string = "SELECT * FROM sweepdefinitions WHERE id=\"$tok\"";
//echo "$query_string<br>";
                $result2 = @mysqli_query($connect_string, $query_string) or die ("Invalid query 22");
                if ($row2 = @mysqli_fetch_array($result2)) {
//echo "id=$tok, start_time=(" . $row2[start_time] . "), start_time2=(" . $row2[start_time2] . ")<br>";
//                  if($row2[location2] == "Majestic Top" && $row2[start_time2] > 0)
                    if($row2['start_time'] > 0)
                        $nAssignments++; //yup, a 2nd shift exists
                    if($row2['start_time2'] > 0)
                        $nAssignments++; //yup, a 2nd shift exists
                }
                $tok = strtok(" ");
            }
        }

        $query_string = "SELECT * FROM roster WHERE IDNumber=\"$id\"";
        $result2 = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result)");
        $class = "xx";
        $name = "";
        if ($row2 = @mysqli_fetch_array($result2)) {
             $class = $row2['ClassificationCode'];
             $cellNumber = $row2['CellPhone'];
             $mentoring = $row2['Mentoring'];
             if($mentoring == "1")
                $name = "* ";
            else
                $name = "";
             $name .= $row2['FirstName'] . " " . $row2['LastName'];
		  $patrollerCount++;
		  if($areaID == 3)
		  	$trainingCount++;
		  if($class == "CAN")
			$candidateCount++;

        }

        echo "<tr>";
          echo "  <td class=t1 width=\"120\"  align=center>$class$tl</td>\n";
          echo "  <td class=t1 width=\"175\">$name</td>\n";
          echo "  <td class=t1 align=center width=\"175\">$cellNumber</td>\n";
//          echo "  <td class=t1 width=\"50\" align=center>$checkin</td>\n";
        echo "</tr>";
    }
    echo "</table><br>\n";
    if($areaID == -1)   //was unassigned
      break;        // ok quit
  } //end loop for area's
//	$query_string = "select COUNT(date) AS nPatrollers from skihistory where date=\"" . $today . "\"";
//	$result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result)");
//	if ($row = @mysqli_fetch_array($result)) {
//	   $patrollerCount = $row[nPatrollers];
//	}
    @mysqli_close($connect_string);
    @mysqli_free_result($result);
echo "<b>There is a total of $patrollerCount volunteers today</b><br>";
//  $patrollerCount = 0;
//  $trainingCount = 0;
//  $candidateCount = 0;
echo "of which, $trainingCount are training ($candidateCount are candidates).<br>";
?>
<!-- 
* Indicates patroller is being Mentored.<br>
@ Indicates patroller was not scheduled.<br> 
<br>
Note: 3rd column is # of Top Shack <br>
and/or Aid Room assignments<br><br>
-->
Report as of: <?php  echo date("H:m - F d, Y"); ?>
</td></tr></table>
</body>

</html>
