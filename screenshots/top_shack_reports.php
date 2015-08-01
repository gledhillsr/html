<?php
require("config.php");
    $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
    $arrDate = getdate();
    $today=mktime(0, 0, 0, $arrDate[mon], $arrDate[mday], $arrDate[year]);
    $todayStr = date("l  F d, Y");
/****************/
/* displaySweep */
/****************/
function displaySweep($name,$sweepID,$i,$topLoc) {
global $mysql_db,$pageLen,$pageNum,$todayStr, $getArea;
if(!$name)
    $name="&lt;Unassigned&gt;";
else {
//given an ID, look up the real name
    $query_string = "SELECT LastName, FirstName FROM roster WHERE IDNumber=$name";
//echo $query_string;
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query 1");
    if ($row = @mysql_fetch_array($result)) {
        $name = $row[FirstName] . " " . $row[LastName];
    }
}
$top1Name = " ";
$top1Time = "-";
$top2Name = "None";
$top2Time = "-";
$eveningSweep = "*** Report to Snack Creek Top ****";
    $query_string = "SELECT * FROM sweepdefinitions WHERE id=$sweepID ORDER BY location, start_time";
//    $query_string = "SELECT * FROM sweepdefinitions WHERE location=\"$topLoc\" ORDER BY start_time";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query 2");
    if ($row = @mysql_fetch_array($result)) {
        $top1Name = $row[location];
        $top1Time = secondsToTime($row[start_time]) . " - " . secondsToTime($row[end_time]);
        $top2Name = $row[location2];
        $top2Time = secondsToTime($row[start_time2]) . " - " . secondsToTime($row[end_time2]);
//..        $eveningSweep = "<br>*** Report to " . $row[closing]. " ****";
        $eveningSweep = "*** Report to " . $row[closing]. " ****";
        $description= trim($row[description]);
		if(strlen($description) < 2)
			$description = "";
    }

	$sizeOfSweep = 27; //size of page header (name, top shack, evening sweep
	$lineCnt = 0;
	//compute size of sweep table
    $tok = strtok($description, "\n");
    while ($tok) {
       if($tok[0] != "@") {    //title
		   $lineCnt++;
    	   $sizeOfSweep += 4;
	   }
       $tok = strtok("\n");
    }
//echo "(start size= $pageLen, upcomming size=$sizeOfSweep + 5. ($lineCnt lines in box).  Finishes on " . ($pageLen + $sizeOfSweep + 5). ")<br>";
//$sizeOfSweep += 5; //hack for error message
//zzzzzzzzzzzzzzzz adjust this number for sweep sheets zzzzzzzzzzz
	if($pageLen + $sizeOfSweep > 240 ) {
		$pageNum++;
		echo "<h5><b>&nbsp;</h5>";	//start a new page
//	    echo "<font size=\"4\"><b>{$getArea[$i]}</b> Individual Assignments for $todayStr</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;page $pageNum<br>\n";
	    echo "<font size=\"3\"><b>$topLoc</b> Individual Assignments for $todayStr</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;page $pageNum<br>\n";

		$pageLen = 15; //size of page header, just printed
	}
	$pageLen += $sizeOfSweep; //size of page header (name, top shack, evening sweep


echo "<div> <br> \n";
//echo "<h4 align=center>$name</h4>\n";
//echo "<table  border=0 cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" width=\"100%\">\n";
echo "<table cellpadding=\"0\" cellspacing=\"0\" style=\"border-width:1px; border-color:#ffffff; border-style:solid; border-collapse:collapse\" width=\"100%\">\n";
echo "  <tr style=\"border-width:1px; border-color:#ffffff; border-style:solid; border-collapse:collapse\">\n";
echo "    <td ALIGN=center style=\"background-color:none; border-width:1px; border-color:#ffffff; border-style:solid; border-collapse:collapse\" ><b><font size=4>$name</font></B></td>\n"; 
echo "  </tr>\n";
echo "  </table>\n";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=\"100%\">\n";
echo "  <tr style=\"border-width:1px; border-color:#ffffff; border-style:solid; border-collapse:collapse\">\n";
echo "    <td width=\"20%\" style=\"border-width:1px; border-color:#ffffff; border-style:solid; border-collapse:collapse\"><b>Top Shack(s)</B></td>\n";
echo "    <td width=\"20%\" style=\"border-width:1px; border-color:#ffffff; border-style:solid; border-collapse:collapse\"><b>$top1Name</B></td>\n";
echo "    <td width=\"60%\" style=\"border-width:1px; border-color:#ffffff; border-style:solid; border-collapse:collapse\"><b>$top1Time</B></td>\n";
echo "  </tr>\n";
if($top2Name != "None") {
    echo "  <tr>\n";
    echo "    <td width=\"20%\" style=\"border-width:1px; border-color:#ffffff; border-style:solid; border-collapse:collapse\"><b>&nbsp;</B></td>\n";
    echo "    <td width=\"20%\" style=\"border-width:1px; border-color:#ffffff; border-style:solid; border-collapse:collapse\"><b>$top2Name</B></td>\n";
    echo "    <td width=\"60%\" style=\"border-width:1px; border-color:#ffffff; border-style:solid; border-collapse:collapse\"><b>$top2Time</B></td>\n";
    echo "  </tr>\n";
}
echo "  <tr>\n";
//..echo "    <td width=\"20%\"><br><b>Evening Sweep</B></td>\n";
echo "    <td width=\"20%\" style=\"border-width:1px; border-color:#ffffff; border-style:solid; border-collapse:collapse\"><b>Evening Sweep</B></td>\n";
echo "    <td width=\"80%\" colspan=\"2\" style=\"border-width:1px; border-color:#ffffff; border-style:solid; border-collapse:collapse\"><b>$eveningSweep</B></td>\n";
echo "  </tr>\n";
echo "</table>\n";
//
//parse description here
//
$pos = strpos($description,"\n");
if($pos === false)
    $sweepTitle = $description;
else {
    $sweepTitle = substr($description,0,$pos);
    $description = substr($description,$pos);
}

echo "<table style=\"font-size=12\" border=\"1\" cellpadding=\"2\" style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=\"100%\">\n";
echo "  <tr>\n";
echo "    <td width=\"100%\" colspan=\"2\">$sweepTitle</td>\n";
echo "  </tr>\n";

if($pos === false) {
    echo "</table>\n";
    return;
}

//
// loop for each location in sweep
//
echo "  <tr>\n";
$line = 0;
$chore = "";
        $tok = strtok($description, "\n");
        while ($tok) {
            if($tok[0] == "@") {    //title
                if($line > 0) {
                    echo "    <td width=\"88%\">$chore</td>\n";
                    echo "  </tr>\n";
                }
                echo "    <td width=\"12%\">" . substr($tok,1) . "</td>\n";
                $line = 0;
                $chore = "";
            } else {
                $chore .= $tok;
                if($line >= 1) $chore .= "<br>";
            }
            $line++;
//echo "($tok)<br>";
           $tok = strtok("\n");
        }
echo "    <td width=\"88%\">$chore</td>\n";
echo "  </tr>\n";
echo "</table>\n";
echo "</div>\n";
//echo "----------------------------------------------------------------------------------------------------------\n";
// end of function Display Sweep
}
/* end function displaySweep */
?>
<html>

<head>
<meta http-equiv="Content-Language" content="en-us"/>
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
<meta HTTP-EQUIV="Expires" CONTENT="-1"/>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252"/>
<title>Top Shack Reports</title>
<style type="text/css"> 

<style type="text/css">

table {border-width:1px; border-color:#000000; border-style:solid; border-collapse:collapse; border-spacing:0}
th    {font-size:14; font-weight: bold; color:#000000; background-color:#E1E1E1; border-width:1px; border-color:#000000; border-style:solid; padding:2px}
td    {font-size:11; color:#000000; background-color:#EBEBEB; border-width:1px; border-color:#000000; border-style:solid; padding:1px}


h5 { page-break-before: always } 
div { page-break-before: auto }
</style> 
</head>

<body background="images/ncmnthbk.jpg">
<script>
function printWindow(){
   bV = parseInt(navigator.appVersion)
   if (bV >= 4) window.print()
}
</script>

<!--
Top Shack Reports&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:printWindow()">Print This Page</a><br>
&nbsp;<br>
-->
<?php
//
// loop for each area
//
for($areaId=0; $areaId < 4; $areaId++) {
// Display new Page, and Title information
	$pageLen = 15; //size of page header
	$pageNum = 1;
	if($areaId > 0) {
		echo "<h5>&nbsp;</h5>";	//start a new page
	}
	$topLoc = $getTopShackDBName[$areaId];
    	echo "<font size=\"4\"><b>$topLoc</b> Individual Assignments for $todayStr</font>";
    	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=1><a href=\"javascript:printWindow()\">Print This Page</a></font>";
    	echo "<br>\n";

// Build array of assigned sweeps, ordered by name
    $sweepsAssigned=array();
    $query_string = "SELECT sweep_ids, patroller_id FROM skihistory WHERE date=$today AND shift=0 AND areaID=$areaId ORDER BY name";
//echo "$query_string<br>";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query 3");
    while ($row = @mysql_fetch_array($result)) {
        $oldSweep = $row[sweep_ids];
        //loop through all tokens in the list
        $tok = strtok($oldSweep, " ");
        while ($tok) {
            $sweepsAssigned[$tok] = $row[patroller_id];
//echo "sweepsAssigned[" . $tok . "] = $row[patroller_id]<br>";
           $tok = strtok(" ");
        }
    } //end while

//
// loop for each assignment
//
	$query_string = "SELECT * FROM sweepdefinitions WHERE areaID=$areaId ORDER BY description";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query 4");
    while ($row = @mysql_fetch_array($result)) {
        $id = $row[id];
	$location = $row[location];

	if( //$areaId == 0 &&
		($location == $getTopShackDBName[5] ||		//skip aid room1
		 $location == $getTopShackDBName[6]) ) {	//skip aid room2
		continue;
	}

        displaySweep($sweepsAssigned[$id], $id,$areaId,$topLoc);   //# 13 has a double top shack
    }
	if($areaId > 0) 
		echo "</p>";
}
?>
</body>
<?php
    @mysql_close($connect_string);
    if($result)
        @mysql_free_result($result);
?>

</html>