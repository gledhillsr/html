<?

$config_file = "config.php";  // Full path and name of the config file
$user_table    = "user";           		// MySQL table name
$edit_url       = "edit_user.php";           // URL of the edit.php file
$cur_page="demo";

require($config_file);

$page_title = "Inventory Management Service - Demonstration";  // Page title
include("demo_cookies.php"); 
include($header_file);
?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
  <td valign="top" width="1%">
  <p>&nbsp;</p>
  </td>
<td valign="top" width="24">
</td>
<td valign="top">
<font face="Arial, Arial, Helvetica">

<p align="center"><b><font face="Arial" size="5" color="#666666">Online 
Demonstration</font></b><font size="4" face="Arial"><br>
</font><b><font face="Arial" size="6" color="#666666">Telecom Inventory 
Manager</font></b></p>


<p align="left"><i><font face="Arial" color="#666666">This demonstration will 
show how large and small enterprises can manage their voice and data services.</font></i></p>


<p align="left"><font face="Arial" color="#666666"><b>Facts about ABC Company:<br>
</b> 
ABC Company has 10 Physical Locations<br>
Two Divisions (Voice and Data)<br>
5 departments<br>
ABC Company currently receives 26 bills from the local telephone company for 
their service.<br>
ABC Company would like to provide access for six individuals to view their 
inventory.</font></p>


<FORM method="POST" action="<? echo $SCRIPT_NAME ?>" name="demo">
<p align="center">
	<input type="button" value="PROCEED" name="B2" onClick=window.location="login.php?demo=1" >
</p>
</FORM>


</font>
</td></tr>
</table>
<br>
<br>
<br>
<br>
<br>
<? 
$isLogin = 1;
include($footer_file); 
?>

</body>
</html>