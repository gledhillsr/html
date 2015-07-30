<?
$config_file = "config.php";  // Full path and name of the config file
$user_table     = "user";      // MySQL table name
$customer_table = "customer";  // MySQL table name
$agency_table   = "agency";    // MySQL table name
$cur_page="login";
$page_title = "Inventory Management Service - Login";  // Page title

require($config_file);

//remove all filter cookies
setCookie("filterDept");
setCookie("filterLoc");
setCookie("filterDivs");
setCookie("filterReview");

$isDemo = 1;
if($User) {
	$query_string = "SELECT *  FROM " . $user_table . " WHERE " . $user_field[2][1] . "=\"" . $User . "\" LIMIT 1";
//echo "$query_string<br>"; // Debug only
	$connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
	$result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");
	$row = @mysql_fetch_array($result);
//increment access count here


	$isAdmin = 0;
	if($row) {
		//row as read from 'user'
	 	$login =	$row[ $user_field[2][1] ];
	 	$pswrd =	$row[ $user_field[2][2] ];
	 	$custID =	$row[ $user_field[2][3] ];
	 	$usrName =	$row[ $user_field[2][4] ];
		$isAdmin =  $row[ $user_field[2][5] ];
		$loginCount=$row[ $user_field[2][12] ];
		$loginCount += 1;
		if($pswrd == $Password) {
			//increment the user access count
		   $query_string = "UPDATE " . $user_table . " SET " . 
   				$user_field[2][12] . "=\"" . $loginCount . 
		   		"\" WHERE " . $user_field[2][1] . "=\"" . stripslashes($User) ."\" LIMIT 1";
//echo $query_string; //debug only
				$result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");

			//valid user, now look up the company
			$query_string = "SELECT *  FROM " . $customer_table . " WHERE " . $cust_field[2][1] . "=\"" . $custID . "\" LIMIT 1";
//echo "$query_string<br>"; // Debug only (will break this page)
			@mysql_free_result($result);
			$result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");
			$row = @mysql_fetch_array($result);
			$company = "Company";
			$agency = "Agency";
			$agencyID = "agentID";
			if($row) {
				//row as read from 'user'
				$company = $row[ $cust_field[2][2] ];	//Name 
				$agencyID =$row[ $cust_field[2][4] ];	//AgencyID
				//now read the agency
				$query_string = "SELECT *  FROM " . $agency_table . " WHERE " . $agent_field[2][1] . "=\"" . $agencyID . "\" LIMIT 1";
//echo "$query_string<br>"; // Debug only (will break this page)
				@mysql_free_result($result);
				$result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");
				$row = @mysql_fetch_array($result);
				if($row) {
					$agency =$row[ $agent_field[2][2] ];	//AgencyID
				}
			}
			@mysql_close($connect_string);
			@mysql_free_result($result);

			//cookies need to be set before the header
			setCookie("user_id",$login);
			setCookie("customer_id", $custID);
			setCookie("user_name",$usrName);
			setCookie("customer_name", $company);
			setCookie("agency_name", $agency); 
			setCookie("agency_id", $agencyID);
			setCookie("isAdmin", $isAdmin);
			setCookie("isDemo");
			$isDemo = 0;

			$new_url = "mail.php?frm=login&owner=inventory.php";
//$new_url = "inventory.php"; //if localhost
			header("Location: " . $new_url);	/* Redirect browser */ 
 			exit;
		}
	}
	@mysql_close($connect_string);
	@mysql_free_result($result);
}

if($isDemo == 1) {
	$connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
	$query_string = "SELECT *  FROM " . $user_table . " WHERE " . $user_field[2][1] . "=\"guest\" LIMIT 1";
//echo $query_string;	//debug only
	$result = @mysql_db_query($mysql_db, $query_string);
	$row = @mysql_fetch_array($result);
	if($row) {
		$loginCount=$row[ $user_field[2][12] ];
		$loginCount += 1;
//increment the user access count
		$query_string = "UPDATE " . $user_table . " SET " . 	$user_field[2][12] . "=\"" . $loginCount . 
			"\" WHERE " . $user_field[2][1] . "=\"guest\" LIMIT 1";
//echo $query_string; //debug only
	   	$result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");
	}
	@mysql_close($connect_string);
	@mysql_free_result($result);
	//invalid password, set up for Demo mode
 include("demo_cookies.php"); 
}
?>

<?
 include($header_file);
?>
</font>
</td></tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr><td valign="top" width="1%">
<font face="Arial, Arial, Helvetica">
<p>&nbsp;</p>

<p>
&nbsp;</p>

</font></td><td valign="top" width="24"></td>
<td valign="top"><font face="Arial, Arial, Helvetica">
<p> &nbsp; </p>
<?
if ($agent) 
  echo "<p align=\"center\"><img src=\"_themes/blank/blrule.gif\" width=\"600\" height=\"10\"></p>";

//echo "User=($User) Password=($Password)";
	if($User) {
		echo "<h4>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Invalid Password.  Remember, Passwords are Case Sensitive.</h4>";
	}
?>
<!-- OUTER WHITE TABLE (2 COLUMNS WIDE) -->
</font>
<table border="0" width="100%">
  <tr>
	<td><font face="Arial, Arial, Helvetica">
<!-- COLORED TABLE (ONLY 1 CELL)-->		
	</font><TABLE WIDTH=450 BGCOLOR=#ABABAB ALIGN=left BORDER=0 CELLSPACING=1 CELLPADDING=5 height="37">
	<TR>
		<TD height="26"><font face="Arial, Arial, Helvetica">
		<P align="center"><b><font size="5">Log In</font></b></P></font>
<!-- TABLE/FORM FOR PASSWORDS ONLY-->		
		<FORM method="POST" action="<? echo $SCRIPT_NAME ?>" name="login_form">
		<TABLE WIDTH="75%" BGCOLOR=#ABABAB align=center BORDER=0 CELLSPACING=1 CELLPADDING=1>
			<TR>
				<TD><font face="Arial, Arial, Helvetica"><b><font face="verdana," size="2" color="#FFFFFF">User Name</font><font face=verdana, size =2 color=white arial>:</font></b>				</font></TD>
				<TD><font face="Arial, Arial, Helvetica"><INPUT id=User name=User size="20"
				  <? if($demo) echo " value=\"guest\"";?>></font></TD>
			</TR>
			<TR>
				<TD><font face="Arial, Arial, Helvetica"><B><font face=verdana, size =2 color=white arial>Password:</font></B></font></TD>
				<TD><font face="Arial, Arial, Helvetica"><INPUT type="password" id=Password name=Password size="20"
				<? if($demo) echo " value=\"guest\"";?>></font></TD>
			</TR>
			<TR>
				<TD colspan=2 align=middle><font face="Arial, Arial, Helvetica">
       			<p align="center">
<?
	if($demo)
		echo "<input type=\"button\" value=\"Start Demo\" name=\"B2\" onClick=window.location=\"inventory.php\" >";
	else
		echo "<input type=\"submit\" value=\"Login\" name=\"B2\" >";

?>
					</p>
        		</font></TD>
			</TR>
  		</TABLE><font face="Arial, Arial, Helvetica">
  		</FORM>
<!-- END TABLE/FORM FOR PASSWORDS -->		
		</font></TD>
  	</TABLE><font face="Arial, Arial, Helvetica">
<!-- END COLORED TABLE -->		
   	</font></td>
   	<td width="43%" align="center">
<?
	if($demo) {
	echo "<font face=\"Arial, Arial, Helvetica\">";
    echo "<p align=\"left\">When ABC Company set up their online inventory management ";
	echo "    service they requested that any time someone &#39;logs in&#39; to view their ";
	echo "    inventory, the telecom manager will be notified by email. </p>";
    echo "<p align=\"left\">For the Demo, &quot;User Name&quot; &amp;";
	echo "    &quot;Password&quot; are not required. </p>";
   	echo "</font>";
	}
?>
   	</td>
  </tr>
<!-- END OUTTER WHITE TABLE (2 COLUMNS WIDE) -->
</table><font face="Arial, Arial, Helvetica">
<p>&nbsp;</p>
</font></td></tr></table><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td><font face="Arial, Arial, Helvetica">

<p>&nbsp;</p>


<?
	if($demo) {
	    $isLogin = 1;
		include($footer_file);
	} else {
		echo "<h5>";
		echo "Send mail to <a href=\"mailto:webmaster@online-csr.com\">";
		echo "webmaster@online-csr.com</a> with";
		echo "questions or comments about this web site.<br>";
		echo "Copyright © 2003 OnLine-CSR, Inc.<br>";
		echo "</h5>";
	}
?>
</font></td></tr>
</table>
</body>
