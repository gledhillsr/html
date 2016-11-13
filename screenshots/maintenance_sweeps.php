<?php 
require("config.php");
    $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
?>
<html>

<head>
<meta http-equiv="Content-Language" content="en-us"/>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252"/>
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
<meta HTTP-EQUIV="Expires" CONTENT="-1"/>
<title>maintenance</title>
</head>

<body background="images/ncmnthbk.jpg">

<h2>Define Sweeps, with definitions and requirements.</h2>
  &nbsp; <br>

  <table width="750" border="1" style="border-collapse: collapse" bordercolor="#111111" cellpadding="0" cellspacing="0">
    <tr>
      <td bgColor="#FFFFFF" colspan="7" bordercolor="#000000" width="750">
        <p align="center">Assignments <b>(sorted by Area, Top Shack, then by Start Shift Time)</b></p>
      </td>
    </tr>
    <tr>
      <td align="middle" width="69" bgColor="#FFFFFF" bordercolor="#000000"><font size="2"><b>Area</b></font></td>
      <td align="middle" width="32" bgColor="#FFFFFF" bordercolor="#000000"><font size="2"><b>Level</b></font></td>
      <td align="middle" width="235" bgColor="#FFFFFF" bordercolor="#000000"><font size="2"><b>AM Sweep</b></font></td>
      <td align="middle" width="113" bgColor="#FFFFFF" bordercolor="#000000"><font size="2"><b>Top Shack</b></font></td>
      <td align="middle" width="94" bgColor="#FFFFFF" bordercolor="#000000"><font size="2"><b>Shift Time</b></font></td>
      <td align="middle" width="85" bgColor="#FFFFFF" bordercolor="#000000"><font size="2"><b>Late Sweep</b></font></td>
      <td align="middle" width="92" bgColor="#FFFFFF" bordercolor="#000000">
      <a href="edit_assignment.php?new=1">
      <img border="0" src="images/btnNew.jpg" width="33" height="17"></a></td>
    </tr>
<?php 
//    $query_string = "SELECT * FROM sweepdefinitions WHERE 1 ORDER BY areaID , start_time";
    $query_string = "SELECT * FROM sweepdefinitions WHERE 1 ORDER BY areaID, location , start_time";
//echo "$query_string<br>";
//    $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");
    while ($row = @mysql_fetch_array($result)) {
        $area= $row[ areaID ];
		$area = $getAreaShort[ $area ];
        $level = $row[ ski_level ];
        if($level == 0)      $level = "TL";
        else if($level == 1) $level = "ATL";
        else if($level == 2) $level = "bas";
        else                 $level = "aux";
        $description = $row[description];
        $pos = strpos($description,"\n");
        if(!$pos)
            $pos = 30;
        else
            $pos = min($pos,35);
        $description = substr($description,0,$pos);
        $top = $row[ location ];
        $time  = secondsToTime($row[ start_time ]) . " - " . secondsToTime ($row[ end_time ]);
		if($row[ start_time2 ] > 0) {
	        $time .= "<br>" . secondsToTime($row[ start_time2 ]) . " - " . secondsToTime ($row[ end_time2 ]);
       		$top  .= "<br>" . $row[ location2 ];
		}
        $closing  = $row[ closing ];
        echo "<tr>\n";
        echo "  <td align=\"middle\" width=\"69\" bgColor=\"#FFFFFF\" bordercolor=\"#000000\"><font size=\"2\">$area</font></td>\n";
        echo "  <td align=\"middle\" width=\"32\" bgColor=\"#FFFFFF\" bordercolor=\"#000000\"><font size=\"2\">$level</font></td>\n";
        echo "  <td                  width=\"235\" bgColor=\"#FFFFFF\" bordercolor=\"#000000\"><font size=\"2\">&nbsp;&nbsp;$description</font></td>\n";
        echo "  <td align=\"middle\" width=\"113\" bgColor=\"#FFFFFF\" bordercolor=\"#000000\"><font size=\"2\">$top</font></td>\n";
        echo "  <td align=\"middle\" width=\"94\" bgColor=\"#FFFFFF\" bordercolor=\"#000000\"><font size=\"2\">$time</font></td>\n";
        echo "  <td align=\"middle\" width=\"85\" bgColor=\"#FFFFFF\" bordercolor=\"#000000\"><font size=\"2\">$closing</font></td>\n";
        echo "  <td align=\"middle\" width=\"92\" bgColor=\"#FFFFFF\" bordercolor=\"#000000\"><font size=\"2\">\n";
        echo "  <a href=\"edit_assignment.php?edit=1&id=" . $row[ id ] . "\">\n";
        echo "  <img border=\"0\" src=\"images/btnEdit.jpg\" width=\"27\" height=\"14\"></a>&nbsp;&nbsp;&nbsp;";
        echo "  <a href=\"edit_assignment.php?delete=1&id=" . $row[ id ] . "\">\n";
        echo "    <img border=\"0\" src=\"images/btnDelete.jpg\" width=\"46\" height=\"14\"></a></font></td>\n";
        echo "</tr>\n";
    }
?>
  </table>
<?php 
    @mysql_close($connect_string);
    @mysql_free_result($result);
?>
<p>&nbsp;</p>
<p>&nbsp;</p>

</body>

</html>