<?php 
require("config.php");


function readRecord($id) {
    global $mysql_host, $mysql_username, $mysql_password, $area,$minLevel,$description,$topShack,$startTime;
    global $endTime,$topShack2,$startTime2,$endTime2,$lateSweep,$mysql_db,$getAreaShort;

    $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
    $query_string = "SELECT * FROM sweepdefinitions WHERE id=\"$id\"";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)avail");
    if ($row = @mysql_fetch_array($result)) {
        $area      = $row[ areaID ];
		$area = $getAreaShort[ $area ];
        $minLevel  = $row[ ski_level ];
        if($minLevel == 0)
            $minLevel = "Team Leader";
        else if($minLevel == 1)
            $minLevel = "Asst Team Leader";
        else if($minLevel == 2)
            $minLevel = "Basic";
        else //if($minLevel == 3)
            $minLevel = "Auxilary";
        $description = $row[ description ];
        $topShack  = $row[ location ];
        $startTime = secondsToTime($row[ start_time ]);
        $endTime   = secondsToTime($row[ end_time ]);
        $topShack2  = $row[ location2 ];
		if(!$topShack2)
			$topShack2="None";
        $startTime2 = secondsToTime($row[ start_time2 ]);
        $endTime2   = secondsToTime($row[ end_time2 ]);
        $lateSweep = $row[ closing ];
    }
    @mysql_close($connect_string);
    $title="Save Completed";
}

function displayOption($last,$txt) {
echo "old=$last, new=$txt\n";
  if(strcmp($last,$txt) == 0)
    $sel = "selected";
  else
    $sel = "";
  echo "<option $sel>$txt</option>\n";
}

if($deleteBtn) {
    $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
    $query_string = "DELETE FROM sweepdefinitions WHERE id=$id";
//echo "$query_string<br>";
    @mysql_db_query($mysql_db, $query_string) or die ("Invalid query1 (result)");
    @mysql_close($connect_string);
    header("Location: maintenance_sweeps.php"); /* Redirect browser */
exit;
} else if($saveBtn) {
    if($minLevel == "Team Leader")
        $ski_level = 0;
    else if($minLevel == "Asst Team Leader")
        $ski_level = 1;
    else if($minLevel == "Basic")
        $ski_level = 2;
    else //if($minLevel == "Auxilary")
        $ski_level = 3;
    //time crap
    $startSeconds = timeToSeconds($startTime);
    $endSeconds = timeToSeconds($endTime);

    $startSeconds2 = timeToSeconds($startTime2);
    $endSeconds2 = timeToSeconds($endTime2);

    if($id)
        $query_string = "UPDATE sweepdefinitions SET ";
    else
        $query_string = "INSERT INTO sweepdefinitions SET ";
	//get key, from value in getAreaShort
	$flip = array_flip ( $getAreaShort);
	$area = $flip[$area];

    $query_string .=  "areaID=\"" . $area . "\", " .
        "ski_level=\"" . $ski_level . "\", " .
        "description=\"" . $description . "\", " .
        "location=\"" . $topShack . "\", " .
        "start_time=\"" . $startSeconds . "\", " .
        "end_time=\"" . $endSeconds . "\", " .
        "location2=\"" . $topShack2 . "\", " .
        "start_time2=\"" . $startSeconds2 . "\", " .
        "end_time2=\"" . $endSeconds2 . "\", " .
        "closing=\"" . $lateSweep . "\"";

    if($id)
        $query_string .= " WHERE id=\"$id\"";
echo "$query_string<br>";
    $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
    @mysql_db_query($mysql_db, $query_string) or die ("Invalid query2 (result)");
    @mysql_close($connect_string);
    header("Location: maintenance_sweeps.php"); /* Redirect browser */
exit;
}
if ($fixit) {
    $title="Please fix the time.  They must be in the form h:mm or hh:mm";
} else if($saveBtn) {
    //no nothing
} else if($new) {
    $title="NEW Shift Assignment";
    $area      = $getAreaShort[0];
//echo "new area=$area<br>\n";
    $minLevel  = "Basic";
    $description="Sweep Title\n@<Location 1>\n<chore 1>\n<chore 2>\n@<Location 2>\n<chore 3>\n"
    . "<chore 4>\n\n   First line is the TITLE, then a '@' "
    . "starts a new LOCATION, and each line starts a new CHORE at that location.  You can have as many chores "
    . "at each location as you want (PLEASE DELETE THIS HELP MESSAGE)";
    $topShack  = "Crest";
	$topShack2 = "None";
    $startTime = "10:00";
    $endTime   = "11:00";
    $startTime2 = "";
    $endTime2   = "";
    $lateSweep = "Crest";
} else if($delete && $id) {
//----DELETE (confirm) -----
    $title="Delete Shift Assignment";
    readRecord($id);
} else if($edit && $id) {
    $title="Edit Shift Assignment";
    readRecord($id);
} else {
    $title="Oops, undefined parameters";
}
?>

<html>
<head>
<meta http-equiv="Content-Language" content="en-us"/>
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
<meta HTTP-EQUIV="Expires" CONTENT="-1"/>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252"/>
<title>Edit Shift Assignment</title>

<script language="JavaScript">
<!--
function updateAreas(selObject) {
    var idx = selObject.selectedIndex;
	if(idx == 0) {			//Crest
		document.myForm.minLevel.selectedIndex = 2; 
		document.myForm.topShack.selectedIndex = 0; 
		document.myForm.topShack2.selectedIndex = 7; 
		document.myForm.lateSweep.selectedIndex = 0; 
	} else if (idx == 1) {	//Snake  new
		document.myForm.minLevel.selectedIndex = 2; 
		document.myForm.topShack.selectedIndex = 1; 
		document.myForm.topShack2.selectedIndex = 7; 
		document.myForm.lateSweep.selectedIndex = 1; 
	} else if (idx == 2) {	//Western topShack was 3
		document.myForm.minLevel.selectedIndex = 2; 
		document.myForm.topShack.selectedIndex = 2; 
		document.myForm.topShack2.selectedIndex = 7; 
		document.myForm.lateSweep.selectedIndex = 2; 
	} else if (idx == 3) {	//Milli
		document.myForm.minLevel.selectedIndex = 2; 
		document.myForm.topShack.selectedIndex = 3; 
		document.myForm.topShack2.selectedIndex = 7; 
		document.myForm.lateSweep.selectedIndex = 3; 
	} else if (idx == 4) {	//Any
		document.myForm.minLevel.selectedIndex = 3; 
		document.myForm.topShack.selectedIndex = 5; 
		document.myForm.topShack2.selectedIndex = 7; 
		document.myForm.lateSweep.selectedIndex = 4; 
	}
}

function checkTime(selObject,which) {
    var idx = selObject.selectedIndex;
	if(idx == 7) {
		if(which == 1) {
			document.myForm.startTime.value="";
			document.myForm.endTime.value="";
		} else {
			document.myForm.startTime2.value="";
			document.myForm.endTime2.value="";
		}
	}
}
//-->
</script>
</head>

<body background="images/ncmnthbk.jpg">

<h2><?php echo $title; ?></h2>
<form method="POST" name=myForm action="edit_assignment.php">
<table>
    <tr>
      <td align="right">Area:&nbsp;
      </td>
      <td>&nbsp;
      <select size="1" name="area" onChange="updateAreas(this)">
<?php 
//this is bogus, but this definition comes from config.php
    displayOption($area,$getAreaShort[0]);
    displayOption($area,$getAreaShort[1]);
    displayOption($area,$getAreaShort[2]);
    displayOption($area,$getAreaShort[3]);
    displayOption($area,$getAreaShort[4]);
    displayOption($area,$getAreaShort[5]); //Any area
?>
      </select></td>
    </tr>
    <tr>
      <td align="right">Minimum Ski Level:</td>
      <td >&nbsp; <select size="1" name="minLevel">
<?php 
    displayOption($minLevel,"Team Leader");
    displayOption($minLevel,"Asst Team Leader");
    displayOption($minLevel,"Basic");
    displayOption($minLevel,"Auxilary");
?>
      </select></td>
    </tr>
    <tr>
      <td  align="right">Morning Sweep:</td>
      <td>&nbsp; <textarea rows="12" name="description" cols="80">
<?php    echo $description;  ?>
      </textarea></td>
    </tr>
    <tr>
      <td align="right">Top Shack/Aid Room:</td>
      <td>&nbsp; <select size="1" name="topShack"  onChange="checkTime(this,1)">
<?php 
    displayOption($topShack,"Crest Top Shack");
//    displayOption($topShack,"Majestic Top");
    displayOption($topShack,"Snake Creek Top");
    displayOption($topShack,"Western Top");
    displayOption($topShack,"Millicent Top");
    displayOption($topShack,"Aid Room 1");
    displayOption($topShack,"Aid Room 2");
    displayOption($topShack,"None");
?>
      </select>
      &nbsp;&nbsp; Time: &nbsp; 
      <input type="text" name="startTime" size="6" value="<?php echo $startTime; ?>">
      &nbsp;to&nbsp; 
      <input type="text" name="endTime" size="6" value="<?php echo $endTime; ?>">
      &nbsp; (must use 24 hour clock)</td>
    </tr>
    <tr>
    </tr>

      <td align="right">2nd Top Shack (<?php echo $topShack2; ?>)</td>
      <td>&nbsp; <select size="1" name="topShack2" onChange="checkTime(this,2)">
<?php 
    displayOption($topShack2,"Crest Top Shack");
    displayOption($topShack2,"Majestic Top");
    displayOption($topShack2,"Snake Creek Top");
    displayOption($topShack2,"Western Top");
    displayOption($topShack2,"Millicent Top");
    displayOption($topShack2,"Aid Room 1");
    displayOption($topShack2,"Aid Room 2");
    displayOption($topShack2,"None");
?>
      </select>
      &nbsp;&nbsp; Time: &nbsp; 
      <input type="text" name="startTime2" size="6" value="<?php echo $startTime2; ?>">
      &nbsp;to&nbsp; 
      <input type="text" name="endTime2" size="6" value="<?php echo $endTime2; ?>">
      </td>
    <tr>
      <td align="right">Evening Sweep:</td>
      <td>&nbsp; <select size="1" name="lateSweep">
<?php 
    displayOption($lateSweep,"Crest");
    displayOption($lateSweep,"Snake Creek");
    displayOption($lateSweep,"Western");
    displayOption($lateSweep,"Millicent");
    displayOption($lateSweep,"None");
?>
      </select></td>
    </tr>
  </table>
  <p>
<?php 
    if($delete) {
        echo "<input type=\"HIDDEN\" name=\"id\" VALUE=\"$id\">";
        echo "<input type=\"submit\" value=\"Delete\" name=\"deleteBtn\">&nbsp;";
        echo "<input type=\"button\" value=\"Cancel\" onclick=window.location=\"maintenance_sweeps.php\">";
    } else {
        if($edit)
            echo "<input type=\"HIDDEN\" name=\"id\" VALUE=\"$id\">";
        echo "<input type=\"submit\" value=\"Save\" name=\"saveBtn\">&nbsp;";
        echo "<input type=\"button\" value=\"Cancel\" onclick=window.location=\"maintenance_sweeps.php\">";
    }
?>
  </p>
</form>
<br>
Formatting commands for the "Morning Sweep" text entry box above:<br>
1st row is the sweep title<br>
any row that starts with the @ symbol, is the name of the run<br>
any lines following the line with the @ symbol are instructions for each run.<br><br>
Example:<br>
row1:   Sweep 6 - Pacific - Drill Run<br>
row2:   @Top Terminal<br>
row3:   Drill holes and setup signs<br>
row4:   "Do not stop here" sign<br>
row5-8: more stuff for top terminal<br>
row9:   @Pacific<br>
row10:  "Slow banners" - 3<br>
row 11: Two turn arrows at Tanta Mount<br>
etc.<br>
</body>

</html>