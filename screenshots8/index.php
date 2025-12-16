<?php
require_once 'config.php';

// Initialize shiftOverride - prioritize POST/GET over COOKIE
// This ensures form submission (including 0 to clear) takes precedence
if (isset($_POST['shiftOverride'])) {
    $shiftOverride = (int)$_POST['shiftOverride'];
} elseif (isset($_GET['shiftOverride'])) {
    $shiftOverride = (int)$_GET['shiftOverride'];
} elseif (isset($_COOKIE['shiftOverride'])) {
    $shiftOverride = (int)$_COOKIE['shiftOverride'];
} else {
    $shiftOverride = 0;
}

// Handle cookie setting/clearing based on shiftOverride value
if ($shiftOverride > 0) {
    // Set cookie with shift override value (expires in 1 year)
    setcookie("shiftOverride", (string)$shiftOverride, time() + (365 * 24 * 60 * 60), "/");
    echo "Cookie set: shiftOverride=({$shiftOverride}) - " . $shiftsOvr[$shiftOverride] . "<br>";
} elseif ($shiftOverride == 0) {
    // Clear the cookie by setting expiration in the past
    setcookie("shiftOverride", "", time() - 3600, "/");
    unset($_COOKIE["shiftOverride"]);
    echo "Cookie cleared: Using actual time<br>";
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
  if($shiftOverride > 0) {
    echo "Testing time override is ACTIVE: <strong>" . $shiftsOvr[$shiftOverride] . "</strong><br>";
  } else {
    echo "Using actual time (no override set)<br>";
  }
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
