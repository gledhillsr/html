<?php
require("config.php");
?>
<html>

<head>
<meta http-equiv="Content-Language" content="en-us"/>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252"/>
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
<meta HTTP-EQUIV="Expires" CONTENT="-1"/>
<title>Daily Shift Assignment&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Assignment History&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Personal Info</title>
<base target="main">
</head>

<body>
<a href="login_assignment.php?ID=<?php echo $ID; ?>">Select Assignment</a>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="history.php?ID=<?php echo $ID; ?>">Assignment History&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="ski_credits.php?ID=<?php echo $ID; ?>">Credit History&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;
<!-- <a href="http://64.32.145.130:8080/nspCode/servlet/UpdateInfo?resort=Brighton&NoCookie=<?php echo $ID; ?>">Personal Info</a>&nbsp;&nbsp;&nbsp;&nbsp; -->
<!--
<a href="login_training.htm?ID=<?php echo $ID; ?>">On-Hill-Training</a>&nbsp;&nbsp;
<font face="Arial" size="2">&nbsp; Hello <b><?php echo "$ID"; ?></b>&nbsp;
-->
<a target="_parent" href="morning_login.php">Logout</a></font></body></html>