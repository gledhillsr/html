<?php
require_once 'config.php';

if($shiftOverride && $shiftOverride > 0)
{
  //part of my time overide HACK
  setcookie("shiftOverride",$shiftOverride);
echo "set cookie shiftOverride=({$shiftOverride})<br>";
}
else if($shiftOverride && $shiftOverride == 0){
  setcookie("shiftOverride","");
//echo "del shiftoverride cookie";
}
?>

<html>

<head>
<meta http-equiv="Content-Language" content="en-us"/>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252"/>
<title>Brighton Locker Room Software</title>
</head>

<body>

<p>Brighton Ski Patrol Onsite Software</p>
<form method="POST" action="index.php">
<?php 
  if($shiftOverride && $shiftOverride > 0)
    echo "Testing time has been set.<br>";
?>
  <p>( Testing only - enter shift override time
  <select size="1" name="shiftOverride">
<?php 
for($i=0; $i <= 8; $i++){
  $sel = (($shiftOverride == $i) ? "selected='selected' " : "");
  echo "<option value='$i' $sel>" . $shiftsOvr[$i] . "</option>\n";

}
?>
  </select> &nbsp;&nbsp;&nbsp;&nbsp;
        <input type="submit" value="Set Testing Time"></p>
</form>
<p> <a href="morning_login.php">Onsite Login</a></p>
<p> <a href="maintenance_frame.htm">Onsite Directors Maintenance</a></p>

</body>

</html>
<?php 
#mysql_close($link);
#echo 'db closed';
?>

