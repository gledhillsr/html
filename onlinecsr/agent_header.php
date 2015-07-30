
<body>

<table border="1" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber1" height="73">
  <tr>
    <td width="100%" height="43" bgcolor="#babaef" bordercolor="#babaef">
    <p align="center"><b><i><font face="Arial" size="5" color="#000000">
    <? 
	$agents_name = stripslashes($agents_name);
    echo $agents_name; 
    ?></font></i></b><font face="Arial" color="#000000"><i><b><font size="5"> 
    Inventory Management Service</font>
<!--    <sup><font size="4">TM</font></sup> -->
    <br></b></i>
    <sup>
<b>
<?
if ($isForm == "") {
  echo "<a href=\"index.php\">Home</a>&nbsp;&nbsp;&nbsp;\n";
  echo "<a href=\"login.php\">Log In</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
  echo "<a href=\"demo.php\">View Demo</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
  echo "<a href=\"Order_service.php?owner=" . $cur_page . "\">Order Service</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
  echo "<a href=\"privacy.php\">Privacy</a>";
}
?>
</b>
    </sup></font></td>
  </tr>
</table>
<br>
