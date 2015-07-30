<?
require("config.php");
    $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");

    $now = time();
    if($removeLockoutBtn) {
        $query_string = "UPDATE directorsettings SET signinLockout=\"0\" WHERE 1";
//echo "$query_string<br>";
        $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query 9A");
    }
    if($startLockoutBtn) {

        $query_string = "UPDATE directorsettings SET signinLockout=\"" . ($now + 7200) . "\" WHERE 1";
//echo "$query_string<br>";
        $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query 9A");
    }

    $arrDate = getdate();
    $today=mktime(0, 0, 0, $arrDate[mon], $arrDate[mday], $arrDate[year]);
    $query_string = "SELECT sweep_ids, patroller_id FROM skihistory WHERE date=$today AND shift=0";
    $sweepsAssigned=array();
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query 5");
    while ($row = @mysql_fetch_array($result)) {
        $oldSweep = $row[sweep_ids];
        //loop through all tokens in the list
        $tok = strtok($oldSweep, " ");
        while ($tok) {
            $sweepsAssigned[$tok] = $row[patroller_id];
//echo "sweepsAssigned[" . $tok . "] = $row[patroller_id]<br>";
           $tok = strtok(" ");
        }
    }
?>
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<script language="JavaScript">
<!--

function checkPassword() {
    if(document.myForm.Password.value == "pass")
        return true;

    alert("Oops bad password");
    return false;
}

//-->
</script>
<title>Summary</title>
</head>

<body background="images/ncmnthbk.jpg">

<h1>Brighton Ski Resort Scheduling Summary</h1>

<table border="1" cellpadding=5 width="800" bgcolor=#ffffff>
  <tr>
    <td width=329 align="center">Mountain Staffing Summary</td>
    <td width=455 align="center">Unassigned Top Shack/Aid Room</td>
  </tr>
  <tr>
    <td width="329">
      <table border="1" cellpadding="0" cellspacing="0" width="350">
        <tr>
          <td width="70">&nbsp;</td>
          <td width="40" align="center">Sr</td>
          <td width="40" align="center">SrA</td>
          <td width="40" align="center">Bas</td>
          <td width="40" align="center">Aux</td>
          <td width="40" align="center">Can</td>
          <td width="40" align="center">Total</td>
        </tr>
<?
//    $query_string = "SELECT COUNT (areaID) as cnt FROM areadefinitions WHERE 1";
//echo "$query_string<br>";
//    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query 6.5");
//    if ($row = @mysql_fetch_array($result)) {
//echo "areaCount="+$row[cnt]+"<br>";
//    }
    $areaCount = 5;
    $GrandTotal = 0;
    $SrTotal = 0;
    $SrATotal = 0;
    $BasTotal = 0;
    $AuxTotal = 0;
    $CanTotal = 0;
    for($i=-1; $i < $areaCount; $i++){
        $SrCnt =0;
        $SrACnt =0;
        $BasCnt =0;
        $AuxCnt =0;
        $CanCnt =0;
        $AreaCnt = 0;
        $query_string = "SELECT patroller_id, areaID FROM skihistory WHERE shift=0 AND date=$today AND areaID=$i";
//echo "$query_string ";
        $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query 7");
        while ($row = @mysql_fetch_array($result)) {
            $patroller_id = $row[patroller_id];
//echo "$patroller_id ";
            $query_string = "SELECT ClassificationCode FROM roster WHERE IDNumber =$patroller_id";
            $result1 = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query 7.1");
            if ($row1 = @mysql_fetch_array($result1)) {
                $class = $row1[ ClassificationCode ];
                if($class == "SR")       $SrCnt++;
                else if($class == "SRA") $SrACnt++;
                else if($class == "BAS") $BasCnt++;
                else if($class == "AUX") $AuxCnt++;
                else                     $CanCnt++;
                $AreaCnt++;
            }
        }
//echo "<br>\n";
        echo "<tr>\n";
        if($i == -1)
            echo "  <td>Unassigned</td>\n";
        else
            echo "  <td>$getAreaShort[$i]</td>\n";
        echo "  <td align=\"center\">$SrCnt</td>\n";
        echo "  <td align=\"center\">$SrACnt</td>\n";
        echo "  <td align=\"center\">$BasCnt</td>\n";
        echo "  <td align=\"center\">$AuxCnt</td>\n";
        echo "  <td align=\"center\">$CanCnt</td>\n";
        echo "  <td align=\"center\" bgcolor=\"#E9E9E9\">$AreaCnt</td>\n";
        echo "</tr>\n";
        $SrTotal  += $SrCnt;
        $SrATotal += $SrACnt;
        $BasTotal += $BasCnt;
        $AuxTotal += $AuxCnt;
        $CanTotal += $CanCnt;
        $GrandTotal += $AreaCnt;
    }
    echo "<tr>\n";
    echo "  <td >&nbsp;&nbsp; Total</td>\n";
    echo "  <td  align=\"center\" bgcolor=\"#E9E9E9\">$SrTotal</td>\n";
    echo "  <td  align=\"center\" bgcolor=\"#E9E9E9\">$SrATotal</td>\n";
    echo "  <td  align=\"center\" bgcolor=\"#E9E9E9\">$BasTotal</td>\n";
    echo "  <td  align=\"center\" bgcolor=\"#E9E9E9\">$AuxTotal</td>\n";
    echo "  <td  align=\"center\" bgcolor=\"#E9E9E9\">$CanTotal</td>\n";
    echo "  <td  align=\"center\" bgcolor=\"#C0C0C0\">$GrandTotal</td>\n";
    echo "</tr>\n";
?>
      </table>
    </td>
    <td width="455">
      <select size="10" name="D2">
<?
    $sel="SELECTED";
    $query_string = "SELECT * FROM sweepdefinitions WHERE 1 ORDER BY location, start_time";
//echo "$query_string<br>";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query 7");
    while ($row = @mysql_fetch_array($result)) {
        $area = $row[ areaID ];
        $loc =  $row[ location ];
//        if($area != $areaID && $area != 5) //must be THIS area, or "Any Area"
//            continue;
        $time = $row[ start_time ];
        $time = secondsToTime($time);
        $t2 = $row[ end_time ];
        $t2 = secondsToTime($t2);
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

//echo "loc=$loc, getTopShack[0]=" . $getTopShack[0] . "<br>\n";
        $space = "&nbsp;&nbsp;";
        $area = $getAreaShort[ $area ];
        if($loc == $getTopShack[0]) $space = "&nbsp;&nbsp;&nbsp;&nbsp;"; //crest
        else if($loc == $getTopShack[1]) $space = "&nbsp;&nbsp;";    //snake
        else if($loc == $getTopShack[2]) $space = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";    //majestic
        else if($loc == $getTopShack[3]) $space = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";    //western
        else if($loc == $getTopShack[4]) $space = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";  //milli
        else if($loc == $getTopShack[5]) $space = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";  //aid room 1
        else if($loc == $getTopShack[6]) $space = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";  //aid room 2

        echo "<option value=$id $sel>$loc, $space($time - $t2) &nbsp;&nbsp;&nbsp; $desc</option>\n";
        $sel="";
    }

?>
      </select></td>
    </td>

  </tr>
</table>
<form name="myForm" method="POST" action="summary.php" onSubmit="return checkPassword()">
<?
//$now //current seconds
    $query_string = "SELECT signinLockout FROM directorsettings WHERE 1";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query 9");
    $inLockout = 0;
    if ($row = @mysql_fetch_array($result)) {
        $lockTime = $row[signinLockout];
        if($lockTime < $now)
            echo "Lockout time has been cleared.<br><br>";
        else{
            $inLockout = 1;
            $diff = $lockTime - $now;
            if($diff > 3600*2){ //Oops something is wrong , clear the lockout
                echo "Warning, the lockout time was more that 2 hours ahead.  It has been cleared.<br>";
                $inLockout = 0;
            }
        }
    }
    if($inLockout) {
//        $hr = (int)($diff / 3600);
//        $timeString = $hr . ":";
//        $diff -= ($hr * 3600);
//        $min = (int)($diff / 60);
//        if($min < 10) $timeString .= "0";
//        $timeString .= $min . ":";
//        $sec = $diff - ($min*60);
//        if($sec < 10) $timeString .= "0";
//        $timeString .= $sec;
    $arrDate = getdate($lockTime);
//    $today=mktime($arrDate[minutes], $arrDate[minutes], $arrDate[hours], $arrDate[mon], $arrDate[mday], $arrDate[year]);
    $strToday = date("H:i:s - M/d/Y", $lockTime);

        echo "Lockout will be released at $strToday<br><br>";
        echo "<input type=\"submit\" value=\"Remove Lockout\" name=\"removeLockoutBtn\">&nbsp;\n";
        echo "Password Required to remove Lockout: <input type=\"password\" name=\"Password\" size=10> (PS the password is 'pass' for now)";
    } else {
        echo "<input type=\"submit\" value=\"Start 2 Hour Lockout\" name=\"startLockoutBtn\">&nbsp;\n";

    }
	echo "<br><br><br>Note: The \"Can\" column includes Candidates and Transfers.";
    @mysql_close($connect_string);
    @mysql_free_result($result);
?>
</form>

</body>

</html>
