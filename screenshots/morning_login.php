<?php

require_once 'config.php';

if($delID || $newID) {
//echo "shiftOverride -($shiftOverride)-";	//was testing override enabled?
	$id = ($newID) ? $newID : $delID;
    $query_string = "SELECT LastName, FirstName FROM roster WHERE IDNumber=\"" . $id . "\"";
//echo "$query_string<br>";
	$name = "";	//delID was passed in as skiHistory ID, not user ID
    $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");
    if ($row = @mysql_fetch_array($result)) {
        $name = $row[ FirstName ] . " " . $row[ LastName ];
    }
    @mysql_close($connect_string);
    @mysql_free_result($result);
?>
<html>
<head>
<meta http-equiv="Content-Language" content="en-us"/>
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
<meta HTTP-EQUIV="Expires" CONTENT="-1"/>
<meta http-equiv="REFRESH" content="3; URL=morning_login.php"/>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252"/>
<title>Brighton Ski Patrol</title>
</head>
<body onload="document.myForm.pname.focus();" background="images/ncmnthbk.jpg">

<?php
echo "<h1 align=center>$name</h1><br><br><font size=6><p align=center>";
if($newID) echo "Login Successful";
else	   echo "Removal Successful";

echo "</p></font>\n";
echo "<br><br><br><br>You should be redirected to Login page.  Click ";
echo "<a href=\"morning_login.php\">here</a> to go there immediately.";
echo "</body></html>\n";

//    $html = "login_frame.php?ID=$id";
//  header("Location: " . $html);    /* Redirect browser */
  exit;
}   //end new ID or del ID
?>

<html>
<head>
<meta http-equiv="Content-Language" content="en-us"/>
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
<meta HTTP-EQUIV="Expires" CONTENT="-1"/>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252"/>
<title>Brighton Ski Patrol</title>
<script language="JavaScript">
<!--
//verify that this is the top frame.  If it's not, them reload it as the top frame
if(self.location!=top.location) top.location="morning_login.php";

function validateKeyPress(evt) {
var id = 0;
//    var hack =document.myForm.shiftOverride.selectedIndex;
    var index=document.myForm.pname.selectedIndex;

//	 if(evt == null && index == 0)
	 if((evt == null || evt == 0) && index == 0)
				alert("Please Select your name.");

    if(index > 0)
       id = document.myForm.pname.options[index].value;
	    if(id > 0 && (evt == null || (evt.keyCode == 13 || evt.keyCode == 32))) {
	        window.location.href="login_frame.php?ID="+id;
    }

}
//-->
</script>
</head>

<body onload="document.myForm.pname.focus();" background="images/ncmnthbk.jpg">

<form method="POST" name="myForm">
<!--  <p>&nbsp;</p> -->
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <div align="center">
    <center>
    <table border="1" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="300" id="AutoNumber1" bgcolor="#C0C0C0">
      <tr>
        <td>
        <h2 align="center">Brighton Ski Patrol</h2>
        <p align="center">Morning Login </p>
        <p align="center">
<?php
        $query_string = "SELECT LastName, FirstName, IDNumber FROM roster ORDER BY LastName, FirstName";
        $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
        $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");
        echo "<select size=\"1\" name=\"pname\" onkeypress=\"validateKeyPress(event)\">";
		  echo "<option value=0>Please Select Your Name</option>";

        while ($row = @mysql_fetch_array($result)) {
            $name = $row[ LastName ] . ", " . $row[ FirstName ];
            echo "<option value=\"" . $row[IDNumber] . "\">$name</option>";
        }
        echo "</select></p>";
        @mysql_close($connect_string);
        @mysql_free_result($result);
?>
        <p align="center">
        <input type="button" value="Login" name="login" onclick="validateKeyPress(null)"></p>
        <p>&nbsp;</td>
      </tr>
    </table>
    </center>
  </div>
</form>
<br><br><br>

<form action="dailyRosterLogSheet.php" method="get">
  <input type="submit" value="Daily Log Sheet" />
</form>

</body>

</html>
