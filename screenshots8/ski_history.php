<?php
require("config.php");

//================
// init
//
//read input settings from the screen
//StartTime (contents of dialog) - 
//	strBeginning (formatted string that I did)
//  startingTicks
//EndTime (contents of dialog) - 
//  strEnding (formatted string that I did)
//  endingTicks
//sortBy (either first/last/shifts)
//showDay, showSwing, showNight
//showDetails, showCommitment

//---local variables
//--$strToday
//--$srtLastPrinted (at last printing of this report
//================
// init
//================
function init() {
global $chg,$srtLastPrinted,$strToday, $sortBy, $connect_string, $lastPrintedTicks, $showZero;
global $showDetails,$showCommitment,$startingTicks,$endingTicks,$mysqli_db, $dataFrom;
global $showDay, $showSwing, $showNight, $strBeginning, $strEnding, $showWeekday, $showDouble;
global $StartTime,$EndTime;
//$mysqli_username = "root";           // MySQL user name
//$mysqli_password = "Gandalf2";    // MySQL password (leave empty if no password is required.)
//$mysqli_db       = "Brighton";        // MySQL database name
//$mysqli_host     = "localhost";      // MySQL server host name
//echo "connect_string=$mysqli_host<br>";
//echo "connect_string=$mysqli_username<br>";
//echo "connect_string=$mysqli_password<br>";
    $connect_string = @mysqli_connect($mysqli_host, $mysqli_username, $mysqli_password) or die ("Could not connect to the database.");
	mysqli_select_db($connect_string, $mysqli_db);

//echo "connect_string=$connect_string<br>";
//        $query_string = "SELECT * FROM `roster` WHERE 1";
//echo "query_string=$query_string<br>";
//        $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result)");
//echo "result=$result<br>";

	$chg = true;


	//set defaults
	if(!$sortBy) {
		$sortBy = "last";
		$showZero = "ON";
	}
	if(!$showDay && !$showSwing && !$showNight) {
		$showDay = "ON";
		$showSwing = "ON";
		$showDetails = "ON";
		$showCommitment = "ON";
	}
	if(!$dataFrom)
		$dataFrom = "locker";

    if($EndTime) {
        $endingTicks = strtotime((string) $EndTime);
        $startingTicks = strtotime((string) $StartTime);
    } else {
        $startingTicks = strtotime("Oct 23, 2004");
        $endingTicks = time();
    }
//echo "startingTicks=$startingTicks<br>\n";
//echo "endingTicks=$endingTicks<br>\n";
    if($showDetails)
        $dateFormat = "l, F d,Y  H:i:s";
    else
        $dateFormat = "l, F d,Y";

    $strBeginning = date("l, F d,Y  H:i:s", $startingTicks);
    $strEnding = date("l, F d,Y  H:i:s", $endingTicks);
    $strToday = date("l, F d,Y  H:i:s", time());
    $srtLastPrinted = date("l, F d,Y  H:i:s", $lastPrintedTicks);
} //-- end function init

//
// Display table row
//
//=======================
// DisplayLoginHistory
//=======================
function DisplayLoginHistory($name,$commitment,$firstName,$lastName) {
global $showDetails,$showCommitment,$startingTicks,$endingTicks,$mysqli_db, $dataFrom;
global $showDay, $showSwing, $showNight, $showWeekday, $showDouble, $loginDetail;
global $showZero, $classification, $isMentoring;

//global $ID, $firstName, $lastName;
//dataFrom
//"locker" 
//"web"    
//"sum"    
//"diff"    
//"merge"  
	$outLine = "  <tr>\n";
//    $outLine .= "    <td>$name</td>\n";
    $outLine .= "    <td>$lastName, $firstName</td>\n";
	if($showCommitment) {
		if($commitment == 2) {
			if($classification == "OTH") $tmp = "Other";
			else if($classification == "TRA") $tmp = "Transfer";
			else if($classification == "PRO") $tmp = "PRO";
			else if($classification == "CAN") $tmp = "Canidate";
			else if($isMentoring)             $tmp = "Mentoring";
			else	                            $tmp = "&nbsp;";    //normal case

		} else if($commitment == 1) 	$tmp = "Part Time";
		else 						$tmp = "Inactive";
	    $outLine .= "    <td>$tmp</td>\n";
	}

    //setup for loop of printing (all records so we can get carry over amounts
//    $query_string = "SELECT * FROM skihistory WHERE name=\"$name\" AND date > \"$startingTicks\" AND date < \"$endingTicks\"";
    $query_string = "SELECT * FROM skihistory WHERE name=\"$name\" AND (date + checkin) > \"$startingTicks\" AND (date + checkin) < \"$endingTicks\"";
//echo "$query_string<br>";
    $result1 = @mysqli_query($connect_string, $query_string) or die ("Invalid query ($query_string)");
	$count = 0;
	$loginDetail = "";
    while ($row1 = @mysqli_fetch_array($result1)) {
//echo $row1[checkin] . "<br>\n";
		$shift = $row1[\SHIFT];
		if(	($showDay   && $shift == 0) ||
			($showSwing && $shift == 1) ||
			($showNight && $shift == 2) ) 
		{
			$count += 1;
			if($showDetails) {
				$time1 = secondsToTime($row1[\CHECKIN]);
				$b1 = $b2 = "";
				$dow = date("l", $row1[\DATE]);	//display date as only day of week, ie" "Saturday"
				$isWeekend = ($dow == "Sunday" || $dow == "Saturday");
				if($showWeekday && !$isWeekend) {
					$b1 = "<b>";
					$b2 = "</b>";
				}
				if($showDouble && $isWeekend) {
					$dow = date("n/j", $row1[\DATE]);	//display date mm/dd
					if($dow == "12/24" || $dow == "12/25" || $dow == "12/31" || $dow == "1/1") {
						$b1 = "<b>";
						$b2 = "</b>";
					}
				}

				$loginDetail .= $b1 . date("m/d/Y-", $row1[\DATE]). $time1 . "&nbsp; " . $b2;
			}
		}
	}

    $outLine .= "    <td><p align=center>$count</p></td>\n";
	if($showDetails) {
	    $outLine .= "    <td>$loginDetail</td>\n";
	}
    $outLine .= "  </tr>\n";

	if ($dataFrom != "web") {
		if($showZero || $count > 0)
			echo $outLine;
	}
}

//=======================
// printPageTop
//=======================
function printPageTop() {
global $chg,$srtLastPrinted,$strToday;
?>
<html>
<head>
<meta http-equiv="Content-Language" content="en-us"/>
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
<meta HTTP-EQUIV="Expires" CONTENT="-1"/>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252"/>
<title>Brighton Ski History Report</title>

<script language="JavaScript">
var changesMade = <?php echo $chg; ?>;

//--------------------------
// resetDate (in javascript)
//--------------------------
  function resetDate(start) {
    if(start == 1)
        document.myForm.StartTime.value="<?php echo $srtLastPrinted; ?>";
    else
        document.myForm.EndTime.value="<?php echo $strToday; ?>";
  }

</script>

<style type="text/css">
<!--
body  {font-size:12; color:#000000; background-color:#ffffff}

table {border-width:1px; border-color:#000000; border-style:solid; border-collapse:collapse; border-spacing:0}
th    {font-size:14; font-weight: bold; color:#000000; background-color:#E1E1E1; border-width:1px; border-color:#000000; border-style:solid; padding:2px}
td    {font-size:12; color:#000000; background-color:#EBEBEB; border-width:1px; border-color:#000000; border-style:solid; padding:1px}
td.t2    {font-size:12; color:#000000; background-color:#ff0000; border-width:1px; border-color:#000000; border-style:solid; padding:1px}

input    {font-size:12; color:#000000; background-color:#EBEBEB;}
-->
</style>
</head>

<body background="images/ncmnthbk.jpg">

<script>
//------------------------------
// printWindow() (in javascript)
//------------------------------
function printWindow(){
//   changesMade = false;
   bV = parseInt(navigator.appVersion)
   if (bV >= 4) window.print()
}
</script>
<?php }	//---- end of printPageTop


//====================
// printInputFieldArea
//====================
function printInputFieldArea() {
global $chg,$srtLastPrinted,$strToday, $sortBy, $dataFrom, $showZero;
global $showDetails,$showCommitment,$startingTicks,$endingTicks,$mysqli_db;
global $showDay, $showSwing, $showNight, $strBeginning, $strEnding, $showWeekday, $showDouble;
//print input field area
?>
<form method="POST" name=myForm action="ski_history.php">
<Font size=6><b>Brighton Ski History Report<b></font>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:printWindow()">Print This Page</a><br><br>

<table  style="{background-color:#FFFFFF; border-width:1px; border-color:#FFFFFF; border-collapse:collapse; border-spacing:0" border="0" cellpadding="2"  width=660 >
  <tr>
    <td style="{background-color:#FFFFFF; border-width:1px; border-color:#FFFFFF; border-collapse:collapse; border-spacing:0" width="80">
        <font size="2">Beginning: </font>
    </td>
    <td style="{background-color:#FFFFFF; border-width:1px; border-color:#FFFFFF; border-collapse:collapse; border-spacing:0" width="250">
    <input type=text size=45 name=StartTime value="<?php echo $strBeginning; ?>"></td>
    <td style="{background-color:#FFFFFF; border-width:1px; border-color:#FFFFFF; border-collapse:collapse; border-spacing:0" width="250">
        <input style="font-size: 8pt" type="button" value="Reset"  onclick="resetDate(1)"name="reset1">
        &nbsp;&nbsp;(Start of Season)
    </td>
  </tr>
  <tr>
    <td style="{background-color:#FFFFFF; border-width:1px; border-color:#FFFFFF; border-collapse:collapse; border-spacing:0" ><font size="2">Ending:</font></td>
    <td style="{background-color:#FFFFFF; border-width:1px; border-color:#FFFFFF; border-collapse:collapse; border-spacing:0" ><font size="2">
    <input type=text size=45 name=EndTime value="<?php echo $strEnding; ?>"></font></td>
    <td style="{background-color:#FFFFFF; border-width:1px; border-color:#FFFFFF; border-collapse:collapse; border-spacing:0" >
        <input style="font-size: 8pt" type="button" value="Reset" onclick="resetDate(0)" name="reset2">
        &nbsp;&nbsp;(now)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input style="font-size: 8pt" type="submit" onclick="changesMade=false;" value="Refresh" name="refresh">

    </td>
  </tr>
</table>
<table  style="{background-color:#FFFFFF; border-width:1px; border-color:#FFFFFF; border-collapse:collapse; border-spacing:0" border="0" cellpadding="2"  width=660 >
  <tr>
    <td style="{background-color:#FFFFFF; border-width:1px; border-color:#FFFFFF; border-collapse:collapse; border-spacing:0">
      &nbsp;&nbsp; <!--	  <font size="2" color="#FF0000">(Make sure reports looks OK, before doing this)</font> -->
    </td>
    <td  style="{background-color:#FFFFFF; border-width:1px; border-color:#FFFFFF; border-collapse:collapse; border-spacing:0">
        Display:&nbsp; <input type="checkbox" name="showDay" value="ON" <?php if($showDay) echo "checked"; ?> >Day Shifts&nbsp;&nbsp; 
        			   <input type="checkbox" name="showSwing" value="ON" <?php if($showSwing) echo "checked"; ?> >Swing Shifts&nbsp; 
        			   <input type="checkbox" name="showNight" value="OFF" <?php if($showNight) echo "checked"; ?> >Night Shifts<br>
        Display:&nbsp; <input type="checkbox" name="showDetails" value="ON" <?php if($showDetails) echo "checked"; ?> >Detail of Shifts
        			   <input type="checkbox" name="showWeekday" value="OFF" <?php if($showWeekday) echo "checked"; ?> >Show Weekdays in Bold&nbsp; 
        			   <input type="checkbox" name="showDouble" value="OFF" <?php if($showDouble) echo "checked"; ?> >Show Double Days in bold (Christmas and New Years weekends)&nbsp; 
        				<br>
        Display:&nbsp; <input type="checkbox" name="showCommitment" value="ON" <?php if($showCommitment) echo "checked"; ?> >Commitment (Part Time/Candidate/Inactive/etc.)<br>
        <br>
        Sort By:&nbsp; 
<?php 
/* 
          <input type="radio" value="first"  name="sortBy" <?php if($sortBy == "first") echo "checked"; ?> >First Name&nbsp;&nbsp;&nbsp; 
*/
?>

        			   <input type="radio" value="last"   name="sortBy" <?php if($sortBy == "last")  echo "checked"; ?> >Last Name&nbsp;&nbsp;&nbsp; 
        			   <input type="radio" value="shifts" name="sortBy" <?php if($sortBy == "shifts")echo "checked"; ?> >Shifts <b>Skied</b> (
						<input type="checkbox" name="showZero" value="ON" <?php if($showZero) echo "checked"; ?> >
						Include patrollers with 0 days)
        <br>
  Use Data From:&nbsp; <input type="radio" value="locker" name="dataFrom" <?php if($dataFrom == "locker") echo "checked"; ?> >Locker&nbsp;&nbsp;&nbsp; 
        			   <input type="radio" value="web"    name="dataFrom" <?php if($dataFrom == "web")    echo "checked"; ?> >Web&nbsp;&nbsp;&nbsp; 
        			   <input type="radio" value="sum"    name="dataFrom" <?php if($dataFrom == "sum")    echo "checked"; ?> >Both&nbsp;&nbsp;&nbsp; 
        			   <input type="radio" value="diff"   name="dataFrom" <?php if($dataFrom == "diff")   echo "checked"; ?> >Display Merged&nbsp;&nbsp;&nbsp; 
        			   <input type="radio" value="merge"  disabled name="dataFrom" <?php if($dataFrom == "merge")  echo "checked"; ?> >Merge Locker to Web
    </td>
  </tr>
</table>
<br>
<?php 
} //-- end of printInputFieldArea

//====================
// start of printEnding
//====================
function printEnding() {
//display table footer, and document footer
global $strToday;
?>
</table><br>
<font size="2">This report was printed on <b><?php echo $strToday; ?></b></font><br>
<br>
</form>
<p>&nbsp;</p>
</body>
</html>
<?php 
}

//====================
// start of buildQueryString1
//====================
function buildQueryString1() {
global $chg,$srtLastPrinted,$strToday, $sortBy, $query_string1, $query_string1;
global $showDetails,$showCommitment,$startingTicks,$endingTicks,$mysqli_db;
global $showDay, $showSwing, $showNight, $strBeginning, $strEnding, $siz;

    $siz = 140 + 50;	//name + shift count
	if($showCommitment)
		$siz += 70;
    if($showDetails)
        $siz = "100%"; //was 100%

	if($sortBy == "shifts" ) {
	    $query_string1 = "SELECT * FROM skihistory WHERE (date + checkin) > \"$startingTicks\" AND (date + checkin) < \"$endingTicks\" ORDER BY name";

	    $query_string1 = "SELECT  name, COUNT(name) AS cnt FROM skihistory WHERE ";
		$query_string1 .="(date + checkin) > \"$startingTicks\" AND (date + checkin) < \"$endingTicks\" AND ( ";
		if($showDay) {				 	 $query_string1 .= "shift = 0";
			if($showSwing || $showNight) $query_string1 .= " OR ";
		}
		if($showSwing){				 	 $query_string1 .= "shift = 1";
			if($showNight) 				 $query_string1 .= " OR ";
		}
		if($showNight)			 	 	 $query_string1 .= "shift = 2";

		$query_string1 .= " ) GROUP BY name ORDER BY cnt DESC";
//zzz
//echo "query_string1=$query_string1<br>";
	}
}

//====================
// start of DisplayWebHistory
//====================
function DisplayWebHistory($name,$commitment,$ID){
global $query_string2,$connect_string, $loginDetail, $dataFrom, $showCommitment, $showDetails;
global $classification, $isMentoring;
//dataFrom
//"locker" 
//"web"    
//"sum"    
//"diff"    
//"merge"  
//zzzz
if($dataFrom == "locker") return;
	buildQueryString2($ID);
//echo "query_string2=$query_string2<br>";
  	$result = @mysqli_query($connect_string, $query_string2) or die ("Invalid query2 ($query_string2)");
	$cnt = 0;
	$date1 = "";
  	while ($row = @mysqli_fetch_array($result)) {
		$date2 = $row[\DATE];
		$date2 = substr((string) $date2,5,2) . "/" . substr((string) $date2,8,2) . "/" . substr((string) $date2,0,4) ;
		if($dataFrom == "diff") {
//echo " $loginDetail ($date2) [" . substr_count($loginDetail,$date2) . "]<br>";
//why does this not work????????????????
		    if(substr_count((string) $loginDetail,$date2) == 0) {
//echo " not found<br>";
				$date1 .= $date2 . " "; //"&nbsp;";
				++$cnt;
			}
//else echo " FOUND!<br>";
		} else {
			$date1 .= $date2 . " "; //"&nbsp;";
			++$cnt;
		}
//		echo "date1=$date1<br>";
	}
	if($cnt == 0 && $dataFrom == "diff")
		return;		//do nothing if they are the same

	if($cnt == 0 && !$showZero)
		return;		//do nothing if no days

	echo "  <tr>\n";
	if($dataFrom == "web") {
		echo "    <td>$name</td>\n";
		if($showCommitment) {
			if($commitment == 2) 		$tmp = "Full Time";
			else if($commitment == 1) 	$tmp = "Part Time";
			else 						$tmp = "Inactive";
				echo "    <td>$tmp</td>\n";
		}

	} else if($dataFrom == "diff" || $dataFrom == "sum") {
//		echo "    <td></td><td>(web only)</td>\n";
		if($showCommitment)
			echo "    <td colspan=2 align=\"right\">(web only)</td>\n";
		else
			echo "    <td align=\"right\">(web only)</td>\n";
	} else {
		//name and commitment already displayed
		echo "    <td></td><td></td>\n";
	}
	echo "    <td><p align=center>$cnt</p></td>\n";
	if($showDetails)
	echo "    <td>$date1</td>\n";
	echo "  </tr>\n";

}

//====================
// start of buildQueryString2
//====================
function buildQueryString2($ID) {
global $chg,$srtLastPrinted,$strToday, $sortBy, $query_string1, $query_string1;
global $showDetails,$showCommitment,$startingTicks,$endingTicks,$mysqli_db;
global $showDay, $showSwing, $showNight, $strBeginning, $strEnding, $siz, $query_string2;
//Assignments table
//Date : (char 14) 2006-04-19_1
//ShiftType (int)
//Count	(int)
//p0-p9 (char 7)
//            date = new GregorianCalendar(ns.getYear(),ns.getMonth(),ns.getDay());
//SELECT * FROM assignments WHERE date >= "2006-01-10_0" AND date <= "2006-01-10_Z" ORDER BY date
//zzzz
    $strBeginning = date("Y", $startingTicks);
    $strEnding = date("l, F d,Y  H:i:s", $endingTicks);

$y1 = date("Y", $startingTicks);	//4 diget year
$m1 = date("m", $startingTicks);	//2 diget month (01-12)
$d1 = date("d", $startingTicks);	//2 diget Day of the month (01-31)
$y2 = date("Y", $endingTicks);
$m2 = date("m", $endingTicks);
$d2 = date("d", $endingTicks);
//echo "query_string2=$query_string2<br>";
  $query_string2 = "SELECT * FROM assignments WHERE date >= \"$y1-$m1-{$d1}_0\" AND date <= \"$y2-$m2-{$d2}_Z\" AND (";
//  ShiftType
  if($showDay) {
	$query_string2 .= " ShiftType = 0";
	if($showSwing || $showNight) $query_string2 .= " OR";
  }
  if($showSwing) {
	$query_string2 .= " ShiftType = 1";
	if($showNight) $query_string2 .= " OR";
  }
  if($showNight) {
	$query_string2 .= " ShiftType = 2";
  }
  
  
  for($i=0; $i <= 9; ++$i) {
	if($i > 0) $query_string2 .= " OR";
	else 	   $query_string2 .= ") AND (";
	$query_string2 .= " P$i = $ID";
  }
  $query_string2 .= " ) ORDER BY Date";
//echo "query_string2=$query_string2<br>";
//SELECT * FROM assignments 
//WHERE 
//date >= "1969-12-31_0" AND date <= "2006-04-02_Z" AND
//(P0 = "192443" OR P1 = "192443" OR P2 = "192443" OR P3 = "192443")
//ORDER BY date
//

}

//==================================
// start of displaySortedData
//==================================
function displaySortedData(){
global $chg,$srtLastPrinted,$strToday, $sortBy, $query_string1, $showDouble;
global $showDetails,$showCommitment,$startingTicks,$endingTicks,$mysqli_db, $dataFrom;
global $showDay, $showSwing, $showNight, $strBeginning, $strEnding, $siz, $showWeekday,$showZero;
global $classification, $isMentoring;
//global $ID, $firstName, $lastName;

//display title
	$what="List of <b>";
	if($showDay) {				 	 $what .= "DAY";
		if($showSwing || $showNight) $what .= " / ";
	}
	if($showSwing){				 	 $what .= "SWING";
		if($showNight) 				 $what .= " / ";
	}
	if($showNight)			 	 	 $what .= "NIGHT";
	$what .="</b> shifts skied ";
//dataFrom
//"locker" 
//"web"    
//"sum"    
//"diff"    
//"merge"  
	if($dataFrom == "locker") $what .= "(From Locker Room)";
	else if($dataFrom == "web") $what .= "(From Web Site)";
	else if($dataFrom == "sum") $what .= "(From Both Locker Room and Web Site)";
	else if($dataFrom == "diff") $what .= "(Differences between Locker Room and Web)";
	else  $what .= "(unknown)";
	echo "<p align=left><font size=4>$what</font></p>\n";
//display table header
    echo "<table border=1 cellpadding=0 cellspacing=0 width=\"$siz\">\n";
  	echo "<tr>";
    echo "<td width=140><b>Name</b></td>\n";
	if($showCommitment) 
	    echo "<td width=70><b>Commitment</b></td>\n";
    echo "<td width=50><p align=center><b>Shifts</b></td>\n";
	if($showDetails)
	    echo "<td><b>Shift Details</b></td>\n";
  	echo "</tr>\n";

	
//display table contents
	//loop through ski history ordered by name, date, checkin time
	if($sortBy == "last") {
		 $query_string = "SELECT * FROM `roster` WHERE 1 ORDER BY LastName, FirstName";
//echo "query_string = $query_string<br>";
//echo "mysqli_db=$mysqli_db<br>";
	    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query3 ($query_string)");
	    while ($row = @mysqli_fetch_array($result)) {
	        $ID 	   = $row[\IDNUMBER];
			$firstName = $row[\FIRSTNAME];
			$lastName  = $row[\LASTNAME];
			$name = $firstName . " " . $lastName;
//echo "name = $name<br>";
			$classification = $row[\CLASSIFICATIONCODE];
			$isMentoring = $row[\MENTORING];
			$commitment= $row[\COMMITMENT];
			//allow to view only One patroller
			DisplayLoginHistory($name,$commitment,$firstName,$lastName);
			DisplayWebHistory($name,$commitment,$ID);
//echo "done<br>";
	    }
	} else { //($sortBy == "shifts" ) 
	    // query_string1 looks at the "skiHistory" table
	    $result1 = @mysqli_query($connect_string, $query_string1) or die ("Invalid query4 ($query_string1)");
        $patrollers=[];
	    while ($row1 = @mysqli_fetch_array($result1)) {
	        $name 	   = $row1[\NAME];
			$pos = strpos((string) $name," ");
//$ID	   = $row[ patroller_id ];
//$patrollers[$ID] = true;
//echo "--$ID, $patrollers[$ID]";
			$firstName = substr((string) $name,0,$pos);
			$lastName = substr((string) $name,$pos+1);
		    $query_string = "SELECT * FROM roster WHERE FirstName=\"$firstName\" AND LastName=\"$lastName\"";
//echo "$query_string<br>";

		    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query5 ($query_string)");
		    if ($row = @mysqli_fetch_array($result)) {
		        $ID 	   = $row[ \IDNUMBER ];
				$patrollers[$ID] = true;
//echo "++$ID, $patrollers[$ID]<br>";
//		        $firstName = $row[FirstName];
//		        $lastName  = $row[LastName];
//				$name = $firstName . " " . $lastName;
				$commitment= $row[\COMMITMENT];
			$classification = $row[\CLASSIFICATIONCODE];
			$isMentoring = $row[\MENTORING];
//echo "isMentoring=$isMentoring<br>";
				//allow to view only One patroller
				DisplayLoginHistory($name,$commitment,$firstName,$lastName);
				DisplayWebHistory($name,$commitment,$ID);
		    }
		} //end loop for everyone with shifts
//       $vals=explode('&',$value);
//       foreach($vals as $v)
//zzzz
//    echo "</table>\n";
//echo "showZero=$showZero<br>";
		if($showZero) {
		    $query_string = "SELECT * FROM roster WHERE 1 ORDER BY LastName";
		    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query6 ($query_string)");
	    	while ($row = @mysqli_fetch_array($result)) {
		        $ID = $row[\IDNUMBER];
//echo "ID=$ID ($patrollers[$ID])<br>";
				if(!$patrollers[$ID]) {
					$firstName = $row[\FIRSTNAME];
					$lastName  = $row[\LASTNAME];
					$name = $firstName . " " . $lastName;
					$commitment= $row[\COMMITMENT];
					$classification = $row[\CLASSIFICATIONCODE];
					$isMentoring = $row[\MENTORING];
//echo "$firstName, $lastName, $isMentoring<br>";
					DisplayLoginHistory($name,$commitment,$firstName,$lastName);
					DisplayWebHistory($name,$commitment,$ID);
				}
			}
		}
	}
//    echo "</table>\n";

//foreach ($patrollers as $k => $v) {
//   echo "..$k, $patrollers[$k] => $v<br>";
//}

}

//====================
// start of main
//====================
init();					//read passed in parameters
printPageTop();			//display header, scripts, CSS, start of container table
printInputFieldArea();	//populate and display all input fields
buildQueryString1();   	//build queryString1
displaySortedData();	//display results
printEnding();			//end of container table, end of document
?>
