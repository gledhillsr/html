<?php
require("config.php");
?>
<html>

<head>
  <meta http-equiv="Content-Language" content="en-us">
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
  <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
  <META HTTP-EQUIV="Expires" CONTENT="-1">
  <title><?php echo $resortFull; ?> Ski Patrol</title>
  <base target="contents">
  <script>
    function changeMainWindow(destination) {
//      evt.preventDefault();
      $("#main").load("/calendar-1/" + destination + "?resort=" + "<?php echo $resort; ?>");
    }
  </script>
</head>

<body>
<table border="0" cellspacing="0" cellpadding="0" width="932">
  <tr>
    <td>
      <a href="<?php echo $resortURL; ?>"><img border="0" src="<?php echo $resortImg; ?>" width="261" height="60"></a>
    </td>
    <td align="center">
      <h1><?php echo $resortFull; ?> Ski Patrol&nbsp;&nbsp;&nbsp;
        <img border="0" src="/images/cadeuc4.gif" width="32" height="33">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      </h1>
    </td>
    <td align="right">
      <a href="http://www.nsp.org/"><img border="0" src="/images/NSP_logo.gif" width="76" height="70"></a>
    </td>
  </tr>

  <tr>
    <td colspan="3">
      <p align="center">
        <font size="4">
          <a href=# onclick="changeMainWindow('MonthCalendar');return false;">Online Schedule</a>
          <a href=# onclick="changeMainWindow('ListAssignments');return false;">My Assignments</a>
          <a href=# onclick="changeMainWindow('SubList');return false;">SubList</a>
          <a href=# onclick="changeMainWindow('MemberList');return false;">Patrollers</a>
          <a href=# onclick="changeMainWindow('Directors');return false;">Directors</a>
        </font></p>

    </td>
  </tr>
</table>
</body>
</html>
