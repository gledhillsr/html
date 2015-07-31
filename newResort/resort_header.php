<?php
require("config.php");
    if($ID)
        $userID="&ID=$ID";
    else
        $userID="";
?>
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<title><?php echo $resortFull; ?> Ski Patrol</title>
<base target="contents">

</head>

<body topmargin="0" leftmargin="0">
<table border="0"  cellspacing="0" cellpadding="0" width="932" >
  <tr>
    <td>
    <a href="<?php echo $resortURL; ?>"><img border="0" src="<?php echo $resortImg; ?>" width=" <?php echo $imgWidth; ?> " height="60"></a>
    </td>
    <td align="center">
      <h1><?php echo $resortFull; ?> Ski Patrol&nbsp;
      <img border="0" src="/images/cadeuc4.gif" width="32" height="33">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      </h1>
    </td>
    <td align="right">
    <a href="http://www.nsp.org/"><img border="0" src="/images/NSP_logo.gif" width="76" height="70"></a>
    </td>
  </tr>

  <tr>
    <td  colspan="3" bgcolor="#EEEEEE" >
<b><font size="2">
      </font></b>
      </td>
  </tr>

  <tr>
    <td colspan="3">

      <p align="center">

<font size="4">
<a class="button" target="main" href="/calendar-1/MonthCalendar?resort=<?php echo $resort; ?><?php echo $userID; ?>">Online Schedule</a>
<a class="button" target="main" href="/calendar-1/ListAssignments?resort=<?php echo $resort; ?><?php echo $userID; ?>">My Assignments</a>
<a class="button" target="main" href="/calendar-1/SubList?resort=<?php echo $resort; ?><?php echo $userID; ?>">Sub List</a>
<a class="button" target="main" href="/calendar-1/UpdateInfo?resort=<?php echo $resort; ?><?php echo $userID; ?>">My Info</a>
<a class="button" target="main" href="/calendar-1/MemberList?resort=<?php echo $resort; ?><?php echo $userID; ?>">Patrollers</a>
<a class="button" target="main" href="/calendar-1/Directors?resort=<?php echo $resort; ?><?php echo $userID; ?>">Directors</a>
</font></p>

    </td>
  </tr>
</table>
<h2>&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; </h2>

</body>

</html>
