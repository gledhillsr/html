<?php
require("config.php");
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
  <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
  <META HTTP-EQUIV="Expires" CONTENT="-1">
  <title><?php echo $resort; ?> NSP</title>

</head>
<style>
  #banner {
    padding:0;
    /*line-height:3px;*/
    background-color:#F8F8F8;
    height:110px;
    /*color:red;*/
    /*border-bottom: thick;*/
  }
  #main {
    padding:0;
    /*background-color:yellow;*/
    height:100%;
  }
</style>
<!--suppress JSUnresolvedLibraryURL -->
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script>
  $(function() {
    $("#banner").load("resort_header.php");
    $("#main").load("/calendar-1/MonthCalendar?resort=" + "<?php echo $resort; ?>");
  });
</script>
<div id="banner" name="banner"></div>
<div id="main" name="main"></div>

</html>
