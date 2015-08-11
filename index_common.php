<?php
//require("config.php");
?>
<html>

<head>
  <script Language="JavaScript">
    <!--
    if (self != top)
      top.location = self.location;
    //-->
  </script>
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
  <title><?php echo $resortFull; ?> Ski Patrol</title>
  <base target="contents">
  <script>
    function changeMainWindow(destination) {
      $("#main").load("/calendar-1/" + destination + "?resort=" + "<?php echo $resort; ?>");
//      evt.preventDefault();
    }
  </script>
  <script>
    $("#memberLogin").submit(function (event) {
      alert("Handler for memberLogin.submit() called.");
      changeMainWindow("MonthCalendar");  //todo hack title
//      event.preventDefault();
    });
  </script>
  <style>
    #banner {
      padding: 0;
      /*line-height:3px;*/
      /*background-color:#F8F8F8;*/
      height: 110px;
      /*color:red;*/
      /*border-bottom: thick;*/
    }

    #main {
      padding: 0;
      background-color: #fffe97;
      height: 100%;
    }
  </style>
  <!--suppress JSUnresolvedLibraryURL -->
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script>
    $(function () {
//      $("#banner").load("resort_header.php");
      $("#main").load("/calendar-1/MonthCalendar?resort=" + "<?php echo $resort; ?>");
    });
  </script>
</head>
<body>
<div id="banner" name="banner">
  <table border="0" cellspacing="0" cellpadding="0" width="932">
    <tr>
      <td>
        <a href="<?php echo $resortURL; ?>"><img border="0" src="<?php echo $resortImg; ?>" width="<?php echo $ImgWidth; ?>" height="60"></a>
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
</div>
<div id="main" name="main"></div>
</body>
</html>
