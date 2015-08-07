<?php
require("config.php");
if (isset($ID)) {
  $szID1 = "?ID=$ID";
  $szID2 = "&ID=$ID";
} else {
  $szID1 = "";
  $szID2 = "";
}

if (isset($NSPgoto)) {
  $firstPage = $NSPgoto;
} else {
  $firstPage = "MonthCalendar";
}

?>
<html>

<head>
  <SCRIPT Language="JavaScript">
    <!--
    if (self != top)
      top.location = self.location;
    //-->
  </SCRIPT>
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
  <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
  <META HTTP-EQUIV="Expires" CONTENT="-1">
  <title><?php echo $resort; ?> NSP</title>

</head>
<style>
  #header {
    background-color:black;
    color:white;
    text-align:center;
    padding:0px;
  }
  #banner {
    line-height:3px;
    background-color:#eeeeee;
    height:110px;
    color:red;
    border-bottom: thick;
  }
  #main {
    padding:0px;
    background-color:yellow;
    height:100%;
  }
  #footer {
    background-color:black;
    color:white;
    clear:both;
    text-align:center;
    padding:5px;
  }
</style>
<!--suppress JSUnresolvedLibraryURL -->
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script>
  $(function() {
    $("#banner").load("resort_header.php");
    $("#main").load("calendar.html");
  });
</script>
<div id="banner" name="banner"></div>
<div id="main" name="main"></div>

</html>
