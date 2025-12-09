<?php 
require("config.php");
    $connect_string = @mysqli_connect($mysqli_host, $mysqli_username, $mysqli_password) or die ("Could not connect to the database.");
    mysqli_select_db($connect_string, $mysqli_db);

	$totalDays=0;
	$totalNights=0;
	$basicCredits = 0;
	$srCreditsGiven = 0;
	$srCredits = 0;
	if($ID)
		$viewOnlyID = $ID;
//ending time


    //
    // get start time from time of last printing (from database)
    //
    $query_string = "SELECT lastSkiHistoryUpdate FROM directorsettings WHERE 1";
    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query");
    if ($row = @mysqli_fetch_array($result)) {
        $lastPrintedTicks = $row[lastSkiHistoryUpdate];
    }

	$chg = true;
    if($EndTime) {
        $endingTicks = strtotime($EndTime);
        $startingTicks = strtotime($StartTime);
//echo "saveTimeBtn=$saveTimeBtn<br>";
        if($saveTimeBtn) {
			$chg = false;
            $query_string = "UPDATE directorsettings SET lastSkiHistoryUpdate=\"$endingTicks\" WHERE 1";
//echo "$query_string ($EndTime)<br>";
            @mysqli_query($connect_string, $query_string) or die ("Invalid UPDATE");
            $startingTicks = $endingTicks;
            $endingTicks = mktime();
        }
    } else {
        $startingTicks = $lastPrintedTicks;
        $endingTicks = mktime();
    }
	if($viewOnlyID) {
		$startingTicks = 0;
        $endingTicks = mktime();
		$strLastUpdated = date("l, F d,Y", $millis);
		if(!isset($millis)) {
			$detailed = true;
		}
//		echo "lastupdated on $strLastUpdated<br>";
		$daysOld = (int)(($endingTicks - $millis + 1000) / (24*3600));
//		echo "about $daysOld days old<br>";
	}

//    if($detailed)
//        $dateFormat = "l, F d,Y  H:i:s";
        $dateFormat = "l, F d,Y  (H:i)";
//    else
//        $dateFormat = "l, F d,Y";

    $strBeginning = date("l, F d,Y  H:i:s", $startingTicks);
    $strEnding = date("l, F d,Y  H:i:s", $endingTicks);
    $strToday = date("l, F d,Y  H:i:s", mktime());
    $srtLastPrinted = date("l, F d,Y  H:i:s", $lastPrintedTicks);


//======================
//displayTop()
//======================
function displayTop() {
	global $chg;	//changes made
	global $srtLastPrinted;
	global $strToday;
	global $viewOnlyID;
	global $strBeginning;
	global $strEnding;
	global $millis;
	global $strLastUpdated;
	global $daysOld;
	global $detailed;
	global $memberWasDisplayed;

?>

<html>
<head>
<meta http-equiv="Content-Language" content="en-us"/>
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
<meta HTTP-EQUIV="Expires" CONTENT="-1"/>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252"/>
<title>Brighton Ski Credit Report</title>

<script language="JavaScript">
var changesMade = <?php echo $chg; ?>;

  function resetDate(start) {
    if(start == 1)
        document.myForm.StartTime.value="<?php echo $srtLastPrinted; ?>";
    else
        document.myForm.EndTime.value="<?php echo $strToday; ?>";
  }

    function checkForChanges() {
<?php if(!$viewOnlyID) { ?>
        if(changesMade) {
            if(window.confirm("Save -new- ENDING time?")) {
                saveChanges();
            }
        }
<?php } ?>
    }

  function saveChanges() {
    changesMade = false;
    param = "ski_credits.php?saveTimeBtn=1&EndTime=<?php echo $strEnding; ?>";
//alert(param);
    window.location.href=param;
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

<body onunload="checkForChanges();" background="images/ncmnthbk.jpg">

<script>
function printWindow(){
//   changesMade = false;
   bV = parseInt(navigator.appVersion)
   if (bV >= 4) window.print()
}
</script>

<h1>Brighton Ski Credit Report</h1>

<form method="POST" name=myForm action="ski_credits.php">
<b>
<?php if($viewOnlyID && isset($millis))
  echo "<font size=3>(from LOCKER ROOM COMPUTER, NOT SPORTS DESK)";
else
  echo "<font size=4>Detail of Volunteer Ski Credits earned this season.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
?>
</font></b>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:printWindow()">Print This Page</a><br><br>

<?php if(!$viewOnlyID) { ?>
<table  style="{background-color:#FFFFFF; border-width:1px; border-color:#FFFFFF; border-collapse:collapse; border-spacing:0" border="0" cellpadding="2"  width=580 >
  <tr>
    <td style="{background-color:#FFFFFF; border-width:1px; border-color:#FFFFFF; border-collapse:collapse; border-spacing:0" width="80">
        <font size="2">Beginning: </font>
    </td>
    <td style="{background-color:#FFFFFF; border-width:1px; border-color:#FFFFFF; border-collapse:collapse; border-spacing:0" width="250">
    <input type=text size=45 name=StartTime value="<?php echo $strBeginning; ?>"></td>
    <td style="{background-color:#FFFFFF; border-width:1px; border-color:#FFFFFF; border-collapse:collapse; border-spacing:0" width="250">
        <input style="font-size: 8pt" type="button" value="Reset"  onclick="resetDate(1)"name="reset1">
        &nbsp;&nbsp;(Time of last report)
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
<table  style="{background-color:#FFFFFF; border-width:1px; border-color:#FFFFFF; border-collapse:collapse; border-spacing:0" border="0" cellpadding="2"  width=580 >
  <tr>
    <td style="{background-color:#FFFFFF; border-width:1px; border-color:#FFFFFF; border-collapse:collapse; border-spacing:0">
	  <input type="submit" name="saveTimeBtn" onclick="changesMade=false;" value="Save ENDING Time as 'Last Report Time'" >&nbsp;&nbsp;
<!--	  <font size="2" color="#FF0000">(Make sure reports looks OK, before doing this)</font> -->
    </td>
    <td  style="{background-color:#FFFFFF; border-width:1px; border-color:#FFFFFF; border-collapse:collapse; border-spacing:0">
        <input type="checkbox" name="detailed" value="ON" <?php if($detailed)echo "checked"; ?>>show detailed report
    </td>
  </tr>
</table>
<?php } ?>
<br>
<?php 
    $siz = 500;
    if($detailed)
        $siz += 150;
    echo "<table border=1 cellpadding=0 cellspacing=0 width=$siz>\n";
?>
  <tr>
    <td width="100"><b>Name</b></td>
    <td width="190"><b>Date</b></td>
    <td width="60"><p align=center><b>Shift</b></td>
    <td width="50"><p align=center><b>Credit</b></p></td>
<?php 
    if($detailed) {
        echo "    <td width=75><p align=center><b>Sr Amt</b></p></td>\n";
        echo "    <td width=75><p align=center><b>Totals</b></p></td>\n";
    }
?>
  </tr>
<?php 
    $memberWasDisplayed = false;
} //end displayTop

//-----------------------
// DisplaySeniorCredits
//----------------------
function DisplaySeniorCredits($srTotal,$memberWasDisplayed,$srCarryOver,$name,$detailed) {
	global $srCreditsGiven;
	$srCdt = 0;
//echo "srCarryOver=$srCarryOver---<br>";
    if($srCarryOver >= 1.9999999 && $memberWasDisplayed) {
		//finish up display of last patroller
		$srCarryOver += 0.0000001;
        $rem = fmod($srCarryOver ,2);  // get remainder
        $srCdt = $srCarryOver - $rem;    //subtract remainder from total to get value
//echo "   srCarryOver=$srCarryOver, remainder=$rem, sr amount=$srCdt<br>";
		$srCreditsGiven += $srCdt;
        DisplayRow($name,"&nbsp;&nbsp;&nbsp;Senior Credits","&nbsp;",$srCdt,"&nbsp;","&nbsp;");
    }
//else echo "not displayed. memberWasDisplayed=$memberWasDisplayed<br>";
    if ($detailed && $memberWasDisplayed) {
        $rem = fmod($srTotal ,2);  //
//echo "   srTotal=$srTotal, rem=$rem<br>";
        if ($detailed) 
        	DisplayRow($name,"&nbsp;&nbsp;&nbsp;Remainder (not yet accounted for)","&nbsp;","&nbsp;",number_format($rem, 2, '.', ','),"&nbsp;");
    }
	return $srCdt;
}
//======================
// Display table row
//======================
function DisplayRow($name,$strDate,$shift,$credit,$srAmt,$totals) {
global $detailed;

    echo "  <tr>\n";
    echo "    <td>$name</td>\n";
    echo "    <td>$strDate</td>\n";
    echo "    <td><p align=center>$shift</p></td>\n";
    echo "    <td><p align=center>$credit</p></td>\n";
    if($detailed) {
        echo "    <td><p align=center>$srAmt</p></td>\n";
        echo "    <td><p align=center>$totals</p></td>\n";
    }
    echo "  </tr>\n";
}  //end DisplaySeniorCredits


//=============================
// displayTotals  (including end of table
//=============================
function	displayTotals() {
global $srCredits;
global $srCreditsGiven;
global $detailed;
global $totalDays;
global $totalNights;
global $basicCredits;
global $srCreditsGiven;

   echo "  <tr style=\"{background-color:yellow}\" >\n";
   echo "    <td style=\"{background-color:yellow}\" >--Totals--</td>\n";
	$foo = $srCredits-$srCreditsGiven;
	$foo = number_format($foo, 2, '.', ',');
   if($detailed)
	    echo "    <td style=\"{background-color:yellow}\" >Credits not yet accounted for: $foo</td>\n";
	else
		echo "<td style=\"{background-color:yellow}\" >&nbsp;</td>\n";
   echo "    <td style=\"{background-color:yellow}\" ><p align=center>Days: $totalDays</p></td>\n";
   echo "    <td style=\"{background-color:yellow}\" ><p align=center>Night $totalNights</p></td>\n";
   if($detailed) {
        echo "    <td style=\"{background-color:yellow}\" ><p align=center>Credits: $basicCredits</p></td>\n";
        echo "    <td style=\"{background-color:yellow}\" ><p align=center>Sr: $srCreditsGiven</p></td>\n";
   }
   echo "  </tr>\n";
}

//==================
// displayBottom
//==================
function displayBottom() {
global $viewOnlyID; 	// Display for Single user
global $srTotal;		
global $totalCredits;
global $srCdt;
global $strToday;		//time report was generated
global $millis;


	echo "</table><br>";
   if($viewOnlyID) { 
	  echo "<font size=\"2\">Totals: Regular Credits=<b>" . ($totalCredits- $srTotal) . "</b>&nbsp;&nbsp;&nbsp;Senior Credits=<b>$srCdt</b><br><br>";
   }

   echo "<font size=2>This report was printed on <b>" . $strToday ."</b></font><br>";
   echo "<br>";

//  <input type="submit" name="saveTimeBtn" value="Save ENDING Time as 'Last Report Time'" >&nbsp;&nbsp;
//  <font size="2" color="#FF0000">(Print this report and make sure it looks OK, before doing this)</font>

	if($viewOnlyID && isset($millis)) {
	    echo "<input type=\"button\" value=\"Back\" onclick=history.back()>&nbsp;\n";
	}

	echo "</form> <p>&nbsp;</p>";
	echo "</body>\n";
	echo "</html>\n";
}   //end displayBottom

//============================================================
//=================== main display stuff =====================
//============================================================
	displayTop();

//
//		    $query_string = "SELECT * FROM roster WHERE 1 ORDER BY LastName";
//	    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query");
//	    while ($row = @mysqli_fetch_array($result)) {
//	        $ID 	   = $row[IDNumber];
//			$name = $row[FirstName] . " " . $row[LastName];
	 $name0 = "";
	 $query_string0 = "SELECT * FROM roster WHERE 1 ORDER BY LastName, FirstName";
//echo "query_string0 = $query_string0<br>";
    $result0 = @mysqli_query($connect_string, $query_string0) or die ("Invalid query0 ($query_string0)");
    while ($row0 = @mysqli_fetch_array($result0)) {
      $ID 	   = $row0[IDNumber];
		$firstName = $row0[FirstName];
		$lastName  = $row0[LastName];
		$name0 = $lastName . ", " .$firstName ;
		$name = $firstName . " " . $lastName;
//		echo "name=$name<br>";

    //setup for loop of printing (all records so we can get carry over amounts
    $query_string = "SELECT * FROM skihistory WHERE name=\"$name\" ORDER BY name, date, checkin";
//echo "query_string = $query_string<br>";
    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query");

	//loop through ski history ordered by name, date, checkin time
    while ($row = @mysqli_fetch_array($result)) {
		  $normalizedName = $name0; 
        $ID = $row[patroller_id];
		//allow to view only One patroller
		if($viewOnlyID && $ID != $viewOnlyID) 
			continue;
        if($lastID != $ID) {
			//
			//start new Patroller from beginning of season
			//
			$srCdt = DisplaySeniorCredits($srTotal,$memberWasDisplayed,$srCarryOver,$namePrevious,$detailed);
			//initialize new patroller
            $srCarryOver = 0;
            $memberWasDisplayed = false;
            $calcCarryOver = true;
            $srTotal = 0.0;
            $totalCredits = 0.0;
            $lastID = $ID;
        }
		//process next ski history
		  if($namePrevious != $normalizedName) {
 		  	   $namePrevious=$normalizedName;
		  }
        $name =  $row[name];
        $date = $row[date];
        $checkin = $row[checkin];
        $shift = $row[shift];
        $value = $row[value];
        $multiplier = $row[multiplier];
        $checkinTicks = $date + $checkin;
        if( $checkinTicks >= $endingTicks) {
			//After time I am looking at
            continue;
        }
        $strDate = date($dateFormat, $checkinTicks);
		  $wasNight = false;
        switch ($shift) {
            case 0:
                $shift = "Day";
                break;
            case 1:
                $shift = "Swing";
                break;
            default: /* shift = 2 */
//echo "$name has a night ski, multiplier=$multiplier<br>";
				    $wasNight = true;
                if($value == 4)
                    $shift = "Full Night";
                else if($value == 3)
                    $shift = "3/4 Night";
                else
                    $shift = "Night";
                break;
        }
		//get credit and srCmount for this record
		//update srTotal for this patroller
        $credit  = $value / 2;  //day, swing and night before 4 pm = 4, night before 5:30=3, night=2
        $srAmt = 0;
        if($multiplier == 0) {
	        $credit  = 0;
        } else if($multiplier == 2) {
            $srAmt = $credit / 3;
            $srTotal += $srAmt;
        } else if($multiplier == 3) {
            $srAmt = $credit / 3;
            $srTotal += $srAmt;
            $credit = 0;
        }
//echo "value=$value, credit=$credit, srAmt=$srAmt  shift=$shift<br>";
//        if($credit > 0 || $srAmt >= 2)
//        {
         if( $checkinTicks < $startingTicks) {
            //BEFORE starting time
            $totalCredits += $credit + $srAmt;
            $srCarryOver += $srAmt;
         } else if( $checkinTicks < $endingTicks) {
            //WITHIN time range
				$basicCredits += $credit;
				$srCredits += $srAmt;
				if($wasNight) 				
					$totalNights += 1;
				else
					$totalDays += 1;
            if($calcCarryOver) {
                 // display Carry over amount for this patroller
//                    $foo = fmod($srCarryOver ,2);  //
					$srCarryOver = fmod($srCarryOver + 0.0000001 ,2);  //
               if ($detailed) 
               	DisplayRow($name0,"&nbsp;&nbsp;&nbsp;Carry Over Amount","&nbsp;","&nbsp;",number_format($srCarryOver, 2, '.', ','),number_format($totalCredits, 2, '.', ','));
               $memberWasDisplayed = true;
               $calcCarryOver = false;
             }
             $totalCredits += $credit + $srAmt;
             $srCarryOver += $srAmt;
			//build the string (srAmt / total sr amount for this period) !!!
             $foo = number_format($srAmt, 2, '.', ',') . " / " . number_format($srCarryOver, 2, '.', ',');
             if($multiplier < 2)
                 $foo = "&nbsp;";	//no multiplier, throw away string

             DisplayRow($name0,$strDate,$shift,$credit,$foo,number_format($totalCredits, 2, '.', ','));
         }
         //ignore any checkin times ON or AFTER $endingTicks
//        }
//=========================================
    }	 //end loop looking for each assignment for a single name

	 }	  //end loop by LastName

//echo "memberWasDisplayed=$memberWasDisplayed<br>";
	$srCdt = DisplaySeniorCredits($srTotal,$memberWasDisplayed,$srCarryOver,$namePrevious,$detailed);

	displayTotals(); //including end of table

	displayBottom();
