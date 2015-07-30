<?
  require("config.php");
  if($ID) {
    $szID1 = "?ID=$ID";
    $szID2 = "&ID=$ID";
  } else {
    $szID1 ="";
    $szID2 ="";
  }

  if($NSPgoto)
    $firstPage=$NSPgoto;
  else
    $firstPage="MonthCalendar";

    $resort="SnowCreek";
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
<title><? echo $resort; ?> NSP</title>

</head>

<frameset rows="125,*">
<?
  echo "<frame name=\"banner\" scrolling=\"no\" target=\"contents\" src=\"resort_header.php$szID1\">";
  echo "<frame name=\"main\" src=\"/nspCode/$firstPage?resort=$resort{$szID2}&noLogin=1\" target=\"_self\" scrolling=\"auto\">";
?>
  <noframes>
  <body>

  <p>This page uses frames, but your browser doesn't support them.
  </body>
  </noframes>
  </frameset>
</html>
