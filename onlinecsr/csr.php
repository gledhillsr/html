<?
$cur_page="index";
require("config.php");

//make sure passed in value overrides any existing cookie
reset($HTTP_GET_VARS); 
  while ( list($key, $value) = each($HTTP_GET_VARS) ) {
//	echo "$key value=$value<br>";
    if($key == "agent") {
		$agent = $value;
	}
}
if(!$agent)
	$agent = 'carrier';
if(strlen($agent) > 1) {
   //get agents_name from agent file
$mysql_table    = "agency";               		// MySQL table name
$query_string = "SELECT agencyPluralName FROM agency WHERE agencyID=\"" . $agent . "\"";
//echo "($query_string)\n"; // Debug Only
$connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or $db_error = 1;
$result = @mysql_db_query($mysql_db, $query_string);
$row = @mysql_fetch_array($result);
$agents_name = $row[ "agencyPluralName" ];
//echo "-4-($agents_name)--\n";
//echo "-5-($agent)--\n";
@mysql_close($connect_string);
@mysql_free_result($result);

   if($agent == "noemail") {
	  setCookie("noEmail","yes");
      $agents_name = stripslashes($agents_name);
      setcookie("agent",$agent);
      setcookie("agents_name",$agents_name);
   }
   elseif($agent == "noagent") {
//echo "no agent";
	  setCookie("noEmail");
      setcookie("agent");				//expire the cookie
      setcookie("agents_name");	//expire the cookie
	  $agent = "";
   }
   else {
//      $agents_name = stripslashes($agents_name);
//      $agents_name = stripslashes($agents_name);
      $agents_name = stripslashes($agents_name);
      setcookie("agent",$agent,time()+60+60*24*365);
      setcookie("agents_name",$agents_name,time()+60+60*24*365);
   }
}
?>
<html>
<head>
	<title>CSR</title>
	<link rel="STYLESHEET" type="text/css" href="includes/styles.css">
</head>

<body background="images/bg1.gif" bgcolor="#080032" marginwidth="0" marginheight="0" topmargin="0" leftmargin="0">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><img src="images/h_1.jpg" width="279" height="303"></td>
		<td align="right" valign="bottom">
		<? 
		if($agent) {
		   echo "<div style=\"width: 553; height: 49; padding: 10px\">";
		   echo "<p align=\"center\"><b><font size=\"6\" color=\"#FFFFFF\"><font face=\"Tahoma\">$agents_name ONLINE-CSR</font>";
  		   echo "</font></b></div>";
     	} else
			echo "<img src=\"images/h_title.gif\" width=\"376\" height=\"38\">";
		?>
		</td>
	</tr>
	<tr>
		<td bgcolor="#ffffff"><img src="images/00-bit.gif" width="1" height="1"></td>
		<td align="left" bgcolor="#ffffff"><img src="images/00-bit.gif" width="491" height="1"></td>
	</tr>
	<tr>
		<td bgcolor="#05001D"><img src="images/h_2.jpg" width="279" height="142"></td>
		<td width="100%" bgcolor="#000000" align="right" valign="top"><img src="images/00-bit.gif" width="1" height="40"><br>
			<table border="0" cellpadding="0" cellspacing="7">
            	<tr>
					<td colspan="9"><img src="images/00-bit.gif" width="1" height="5"></td>
				</tr>
				<tr>
					<td><a class="nav1" href="login.php"><b>Log In</b></a></td>
					<td><a class="nav1" href="demo.php"><b>View Demo</b></a></td>
<? if(!$agent) {
   echo "<td><a class=\"nav1\" href=\"services1.php\"><b>Services</b></a></td>";
} ?>
					<td><a class="nav1" href="Order_service.php?owner=<? echo $cur_page; ?>"><b>Order Service</b></a></td>
<? if(!$agent) {
   echo "<td><a class=\"nav1\" href=\"about_us.php\"><b>About Us<b/></a></td>";
} ?>
					<td><a class="nav1" href="privacy.php"><b>Privacy</b></a></td>
<? if(!$agent) {
   echo "<td><a class=\"nav1\" href=\"agent_login.php\"><b>Agents</b></a></td>";
} ?>
            	</tr>
            </table></td>
	</tr>
	<tr>
		<td><img src="images/h_3.jpg" width="279" height="154"></td>
		<td valign="top" class="homeCopy" width="650">
		  <font color="#c0c0c0"><b>ONLINE-CSR™</b>  Provides a service to business, government and education which allows them 
			to effectively manage their voice and data services.  <b>ONLINE-CSR™</b> is the first web-based application to 
			give clients complete visibility into and control over their telecommunication services.</font></td>
	</tr>
</table>
</body>
</html>
