<?
require("config.php");
    if($ID)
        $userID="&ID=$ID";
    else
        $userID="";

    $resortFull = "Brighton";
    $resort     = "Brighton";
    $resortURL  = "http://www.brightonresort.com/";
    $resortImg  = "images/Brighton.gif";
    $reportDetails = "&AUX=ON&BAS=ON&SR=ON&SRA=ON&TRA=ON&CAN=ON&FullTime=ON&PartTime=ON&ALL=ON&NAME=LAST&HOME=on&WORK=on&CELL=on&EMAIL=on&FirstSort=Name&FontSize=13";
?>
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<title><? echo $resortFull; ?> Ski Patrol</title>
<base target="contents">

<style type="text/css">
<!--
@import "css/menubar.css";
-->
</style>
</head>

<body topmargin="0" leftmargin="0">
<table border="0"  cellspacing="0" cellpadding="0" width="932" height=125>
  <tr>
    <td>
    <a href="<? echo $resortURL; ?>"><img border="0" src="<? echo $resortImg; ?>" width="261" height="60"></a>
    </td>
    <td align="center">
      <h1><? echo $resortFull; ?> Ski Patrol&nbsp;
      <img border="0" src="images/cadeuc4.gif" width="32" height="33">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      </h1>
    </td>
    <td align="right">
    <a href="http://www.nsp.org/"><img border="0" src="images/NSP_logo.gif" width="76" height="70"></a>
    </td>
  </tr>

  <tr>
    <td  colspan="3" bgcolor="#EEEEEE" >
<b><font size="2">
<div class="menubar" >
<a class="button" href="Volunteer.htm" target="main">Volunteer</a>&nbsp;&nbsp;
<a class="button" href="weatherFrame.html" target="main">Weather</a>&nbsp;&nbsp;
<!-- <a class="button" href="PhotoGallery.html" target="main">Photo Gallery</a>&nbsp;&nbsp; -->
<!-- <a class="button" href="2009PatrolDay.html" target="main">2009&nbsp;Patrol&nbsp;Day</a>&nbsp;&nbsp; -->
<!-- <a class="button" href="2011GolfFlyer.jpg" width="960" target="main">2011&nbsp;Golf&nbsp;Tournament</a>&nbsp;&nbsp; -->

<a class="button" href="training_frame.htm" target="main">Training/Refresher</a>&nbsp;&nbsp;
<!-- <a class="button" href="/nspCode/MonthCalendar?resort=<? echo $resort; ?>&noLogin=1<? echo $userID; ?>" target="main">Calendar</a>&nbsp;&nbsp; -->
<!-- <a class="button" href="new_policies.htm" target="main">New Policies</a>&nbsp;&nbsp; -->
<a class="button" href="pro_form_reps.htm" target="main">Pro Form Reps</a>&nbsp;&nbsp;&nbsp;
<a class="button" href="http://www.xmission.com/~brightonnsp/forum" target="main">Forum</a>
</div>
      </font></b>
      </td>
  </tr>

  <tr>
    <td bgcolor="#DDDDDD" colspan="3">
<font size="2">
<div class="menubar2" >
<font color="white"> <b>&nbsp;(Members Only):&nbsp;</font>
<a class="button" target="main" href="/nspCode/MonthCalendar?resort=<? echo "$resort$userID"; ?>">Online Schedule</a>
<a class="button" target="main" href="/nspCode/ListAssignments?resort=<? echo "$resort$userID"; ?>">My Assignments</a>
<a class="button" target="main" href="/nspCode/SubList?resort=<? echo "$resort$userID"; ?>">Sub List</a>
<a class="button" target="main" href="/nspCode/UpdateInfo?resort=<? echo "$resort$userID"; ?>">My Info</a>
<a class="button" target="main" href="/nspCode/CustomizedList2?resort=<? echo "$resort$userID$reportDetails"; ?>">Patrollers</a>

<a class="button" target="main" href="/nspCode/Directors?resort=<? echo "$resort$userID"; ?>">Directors</a>
</div>
</font></b>
    </td>
  </tr>
</table>
</body>
</html>
