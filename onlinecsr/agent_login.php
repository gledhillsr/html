<?
$config_file = "config.php";  // Full path and name of the config file
$cur_page="agent_login";
$page_title = "Inventory Management Service - Agent Login";  // Page title

require($config_file);

if (!strcasecmp($Password, "money")) {
	$new_url = urlencode("agents1.php");
	header("Location: " . $new_url);	/* Redirect browser */ 
	exit;
}

include($header_file);
?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr><td valign="top" width="1%">
<font face="Arial, Arial, Helvetica">
<p>&nbsp;</p>

<p>&nbsp;</p>

</font></td><td valign="top" width="24"></td>
<td valign="top"><font face="Arial, Arial, Helvetica">
<p> &nbsp; </p>
<!-- <p align="center"><img src="_themes/blank/blrule.gif" width="600" height="10"></p> -->
<?
//echo "User=($User) Password=($Password)";
	if($User) {
		echo "<h4>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Invalid Password.  Remember, Passwords are Case Sensitive.</h4>";
	}
?>

<p><b>
<font face="Arial, Arial, Helvetica"><font size="6">
<font color="#666666">AGENTS</font> 
<font color="#999999">- Welcome Password Required<br><br>
</font></font></font></b></p>

<!-- OUTER WHITE TABLE (2 COLUMNS WIDE) -->
</font>
<table border="0" width="100%">
  <tr>
	<td><font face="Arial, Arial, Helvetica">
<!-- COLORED TABLE (ONLY 1 CELL)-->		
	</font><TABLE WIDTH=450 BGCOLOR=#ABABAB ALIGN=left BORDER=0 CELLSPACING=1 CELLPADDING=5 height="37">
	<TR>
		<TD height="26"><font face="Arial, Arial, Helvetica">
		<P align="center"><b><font size="5">Agent Log In</font></b></P></font>
		<FORM method="POST" action="<? echo $SCRIPT_NAME ?>" name="login_form">
		<TABLE WIDTH="75%" BGCOLOR=#ABABAB align=center BORDER=0 CELLSPACING=1 CELLPADDING=1>
			<TR>
				<TD><font face="Arial, Arial, Helvetica"><B><font face=verdana, size =2 color=white arial>Password:</font></B></font></TD>
				<TD><font face="Arial, Arial, Helvetica"><INPUT type="password" id=Password name=Password size="20">&nbsp;</font></TD>
			</TR>
			<TR>
				<TD colspan=2 align=middle><font face="Arial, Arial, Helvetica">
       			<p align="center">
				<input type="submit" value="Login" name="B2" >
					</p>
        		</font></TD>
			</TR>
  		</TABLE><font face="Arial, Arial, Helvetica">
  		</FORM>
		</font></TD>
  	</TABLE><font face="Arial, Arial, Helvetica">
<!-- END COLORED TABLE -->		
   	</font></td>
   	<td width="43%" align="center">
   	</td>
  </tr>
<!-- END OUTTER WHITE TABLE (2 COLUMNS WIDE) -->
</table><font face="Arial, Arial, Helvetica">
<p>&nbsp;</p>
</font></td></tr></table><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td><font face="Arial, Arial, Helvetica">

<p>&nbsp;</p>

<p><font color="#333333" size="4">If you do not have a password email us and we 
will get it to you <a href="mailto:agentmanager@online-csr.com">agentmanager@online-csr.com</a></font></p>

</font></td></tr>
</table>
<br><br>
<?
 $isLogin = 1;
 include($footer_file); 
?>
</body></html>
