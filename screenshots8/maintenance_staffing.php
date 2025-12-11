<?php 
require("config.php");

    $connect_string = @mysqli_connect($mysqli_host, $mysqli_username, $mysqli_password) or die ("Could not connect to the database.");
    mysqli_select_db($connect_string, $mysqli_db);

    if($saveBtn) {
//Crest
        $query_string = "UPDATE areadefinitions SET";
        $query_string .= " SaturdayBasic=$SatCrestBasic, SaturdayAux=$SatCrestAux,";
        $query_string .= " SundayBasic=$SunCrestBasic, SundayAux=$SunCrestAux";
        $query_string .= " WHERE area=\"Crest\"";
//echo "$query_string<br>";
        @mysqli_query($connect_string, $query_string) or die ("Invalid query (result)");
//Western
        $query_string = "UPDATE areadefinitions SET";
        if($Western) {
            $query_string .= " open=1,";
            $query_string .= " SaturdayBasic=$SatWesternBasic, SaturdayAux=$SatWesternAux,";
            $query_string .= " SundayBasic=$SunWesternBasic, SundayAux=$SunWesternAux";
        }
        else
            $query_string .= " open=0";
        $query_string .= " WHERE area=\"Western\"";
//echo "$query_string<br>";
        @mysqli_query($connect_string, $query_string) or die ("Invalid query (result)");
//Millicent
        $query_string = "UPDATE areadefinitions SET";
        if($Millicent) {
            $query_string .= " open=1,";
        $query_string .= " SaturdayBasic=$SatMillicentBasic, SaturdayAux=$SatMillicentAux,";
        $query_string .= " SundayBasic=$SunMillicentBasic, SundayAux=$SunMillicentAux";
        }
        else
            $query_string .= " open=0";
        $query_string .= " WHERE area=\"Millicent\"";
//echo "$query_string<br>";
        @mysqli_query($connect_string, $query_string) or die ("Invalid query (result)");
//done
        echo "<h2>Your changes have been saved.</h2>";
?>
<form method="POST" name=myForm action="maintenance_staffing.php">
<br>
<input type="submit" value="Continue">
</form>
<?php 
//        echo "SatCrestBasic = $SatCrestBasic<br>\n";
//        echo "SatCrestAux = $SatCrestAux<br>\n";
//        echo "Western = $Western<br>\n";
//        echo "SatWesternBas = $SatWesternBasic<br>\n";
//        echo "SatWesternAux = $SatWesternAux<br>\n";
//        echo "Millicent = $Millicent<br>\n";
//        echo "SatMillicentBasic = $SatMillicentBasic<br>\n";
//        echo "SatMillicentAux = $SatMillicentAux<br>\n";
        exit;
    }

    $query_string = "SELECT COUNT(area) AS areaCount FROM areadefinitions WHERE open < \"2\"";
//echo "$query_string<br>";
    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result)");
    if ($row = @mysqli_fetch_array($result)) {
        $areaCount = $row [\AREACOUNT];
    }
    $SaturdayTotal = 0;
    $SundayTotal = 0;

    function displayOptions($sel,$max) {
      for($i=0; $i <= $max; ++$i) {
          if($i == $sel)
              echo " <option selected>$i</option>\n";
          else
              echo " <option>$i</option>\n";

      }
    }

?>
<html>

<head>
<meta http-equiv="Content-Language" content="en-us"/>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252"/>
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
<meta HTTP-EQUIV="Expires" CONTENT="-1"/>
<title>maintenance</title>
<script language="JavaScript">
<!--
function updateTotal(day) {
//var id;
    var count = 0;
    //Saturday
    if((day & 1) == 1) {
        //Crest, always open
        count = 2 + document.myForm.SatCrestBasic.selectedIndex + document.myForm.SatCrestAux.selectedIndex;
        //Western, if enabled
        if(document.myForm.Western.checked)
            count += 2 + document.myForm.SatWesternBasic.selectedIndex + document.myForm.SatWesternAux.selectedIndex;
        //Millicent, if enabled
        if(document.myForm.Millicent.checked)
            count += 2 + document.myForm.SatMillicentBasic.selectedIndex + document.myForm.SatMillicentAux.selectedIndex;
        //display new total
        document.myForm.SaturdayTotals.value = count;
    }
    //Sunday
    if(day > 1) {
        //Crest, always open
        count = 2 + document.myForm.SunCrestBasic.selectedIndex + document.myForm.SunCrestAux.selectedIndex;
        //Western, if enabled
        if(document.myForm.Western.checked)
            count += 2 + document.myForm.SunWesternBasic.selectedIndex + document.myForm.SunWesternAux.selectedIndex;
        //Millicent, if enabled
        if(document.myForm.Millicent.checked)
            count += 2 + document.myForm.SunMillicentBasic.selectedIndex + document.myForm.SunMillicentAux.selectedIndex;
        //display new total
        document.myForm.SundayTotals.value = count;
    }
}

function areaStatus(area) {
    var alive = false;
    if(area == 1) { //Western
        if(!document.myForm.Western.checked)
            alive = true;
        document.myForm.SatWesternBasic.disabled = alive;
        document.myForm.SatWesternAux.disabled = alive;
        document.myForm.SunWesternBasic.disabled = alive;
        document.myForm.SunWesternAux.disabled = alive;
    } else {        //Millicent
        if(!document.myForm.Millicent.checked)
            alive = true;
        document.myForm.SatMillicentBasic.disabled = alive;
        document.myForm.SatMillicentAux.disabled = alive;
        document.myForm.SunMillicentBasic.disabled = alive;
        document.myForm.SunMillicentAux.disabled = alive;
    }
    updateTotal(3);
}
//-->
</script>
</head>

<body background="images/ncmnthbk.jpg">
<h2>Define open area's and Staffing for each area.</h2>

<form method="POST" name=myForm action="maintenance_staffing.php">
  &nbsp;
  <table border="1" cellpadding="0" cellspacing="0" width="682">
    <tr>
      <td width="103" bgcolor="#FFFFFF">
        <p align="center"><font size="2">Staffing</font></p>
      </td>
      <td width="390" align="center" colspan="3" bgcolor="#FFFFFF">
  <font size="2">  Saturday</font></td>
      <td width="452" align="center" colspan="3" bgcolor="#FFFFFF"><font size="2">Sunday</font></td>
    </tr>
<?php 
    $query_string = "SELECT * FROM areadefinitions WHERE open < \"2\"";
//echo "$query_string<br>";
//    $connect_string = @mysqli_connect($mysqli_host, $mysqli_username, $mysqli_password) or die ("Could not connect to the database.");
    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result)");
    while ($row = @mysqli_fetch_array($result)) {
        $area=$row[ \AREA ];
//        echo "$area<br>";
?>
    <tr>
      <td width="103" bgcolor="#FFFFFF"><font size="2">
<?php 
      $SatBas = $row['saturdaybasic'];
      $SatAux = $row['saturdayaux'];
      $SunBas = $row['sundaybasic'];
      $SunAux = $row['sundayaux'];
      if($row['open'] == 1) {
          $checked = "checked";
          $disabled = "";
          $SaturdayTotal += 2 + $SatBas + $SatAux;
          $SundayTotal   += 2 + $SunBas + $SunAux;
      } else {
          $checked = "";
          $disabled = "disabled";
      }


//display Area & checkbox
      if($area == "Crest")
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
      else if($area == "Western")
        echo "<input type=\"checkbox\" name=\"$area\" $checked onclick=\"areaStatus(1)\">";
      else
        echo "<input type=\"checkbox\" name=\"$area\" $checked onclick=\"areaStatus(2)\">";

      echo $area ."</font></td>\n";
//display TL/ATL
      echo "<td width=\"130\" bgcolor=\"#FFFFFF\"><font size=\"2\">TL/ATL<input type=\"text\" name=\"Sat" . $area . "TL\" size=\"1\" readonly value=\"2\"></font></td>\n";
//Saturday Basic
      echo "<td width=\"132\" bgcolor=\"#FFFFFF\"><font size=\"2\">Basic&nbsp;\n";
      echo "<select size=\"1\" $disabled name=\"Sat" . $area . "Basic\" style=\"font-size:8pt\" onchange=\"updateTotal(1)\">";
      displayOptions($SatBas,20);
      echo "</select></font></td>\n";
//Saturday Aux
      echo "<td width=\"150\" bgcolor=\"#FFFFFF\"><font size=\"2\">  Aux&nbsp; \n";
      echo "<select size=\"1\" $disabled name=\"Sat" . $area . "Aux\" style=\"font-size:8pt\" onchange=\"updateTotal(1)\">";
      displayOptions($SatAux,8);
      echo "  </select></font></td>\n";
//display TL/ATL
      echo "<td width=\"130\" bgcolor=\"#FFFFFF\"><font size=\"2\">TL/ATL<input type=\"text\" name=\"Sat" . $area . "TL\" size=\"1\" readonly value=\"2\"></font></td>\n";
//Sunday Basic
      echo "<td width=\"132\" bgcolor=\"#FFFFFF\"><font size=\"2\">Basic&nbsp;\n";
      echo "<select size=\"1\" $disabled name=\"Sun" . $area . "Basic\" style=\"font-size:8pt\" onchange=\"updateTotal(2)\">";
      displayOptions($SunBas,20);
      echo "</select></font></td>\n";
//Sunday Aux
      echo "<td width=\"150\" bgcolor=\"#FFFFFF\"><font size=\"2\">  Aux&nbsp; \n";
      echo "<select size=\"1\" $disabled name=\"Sun" . $area . "Aux\" style=\"font-size:8pt\" onchange=\"updateTotal(2)\">";
      displayOptions($SunAux,8);
      echo "  </select></font></td>\n";

    echo "</tr>\n";
    }
    @mysqli_close($connect_string);
    @mysqli_free_result($result);
?>
    <tr>
      <td width="103" bgcolor="#FFFFFF">
        <p align="center"><font size="2">Totals</font></td>
      <td width="412" align="center" colspan="3" bgcolor="#FFFFFF"><font size="2">
      <input type="text" name="SaturdayTotals" readonly size="4" value="<?php echo $SaturdayTotal; ?>"></font></td>
      <td width="457" align="center" colspan="3" bgcolor="#FFFFFF"><font size="2">
      <input type="text" name="SundayTotals" readonly size="4" value="<?php echo $SundayTotal; ?>"></font></td>
    </tr>
  </table>
  <p>
<input type="submit" value="Save Staffing Changes" name="saveBtn">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="reset" value="Reset"></p>
</form>

<p>&nbsp;</p>
<p>&nbsp;</p>

</body>

</html>