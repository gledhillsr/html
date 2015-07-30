
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Order Service Form</title>
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#999999" vlink="#990000" alink="#666666">
<?
/********************************************************************/
/* do NOT edit the .php file, because it is a COPY of the .HTM file */
/* so others can use front page as an editor.                       */
/********************************************************************/
?>
<p>
<?
  $isForm=1;
  $cur_page="Order_service";
  require("config.php");
  include($styles_file); 
?>
<p>
<?
include($header_file);  //note: don't specify "$page_title"
?>


<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr><td valign="top" width="1%"><font face="Arial, Arial, Helvetica">

<p>&nbsp;</p>

<p>
&nbsp;</p>

</font></td><td valign="top" width="24"></td><td valign="top"><font face="Arial, Arial, Helvetica">
<p><font size="5" face="Arial">Sign up for ONLINE-CSR 
Service</font></p>
<p><font size="4"><font face="Arial">In order to get your servi</font>ce set up 
you need to provide us with:</font><br>
1)&nbsp; A signed Letter of Agency (LOA) giving us permission to pull your 
records.<br>
2)&nbsp; Your contact Information<br>
3)&nbsp; Your Account numbers from the telephone Company<br>
Once we have your information your account should be set up in 5 days.<br>
<br>
<a href="custom_loa.php">Download LOA</a> (.pdf file)</p>
<p>Review <a href="terms_and_conditions.php">Terms and Conditions</a></p>
<p><font size="4">Contact Information</font></p>
<form method="POST" action="mail.php?frm=Order_service&owner=<? echo $owner; ?>.php">
  </font>
<TABLE>
<TR>
<TD ALIGN="right"><font face="Arial, Arial, Helvetica">
<font face="Arial">
<EM>Organization</EM></font></font></TD>
<TD><font face="Arial, Arial, Helvetica">
<font face="Arial">&nbsp;<INPUT NAME="Contact_Organization" SIZE=35></font></font></TD>
</TR>
<TR>
<TD ALIGN="right"><font face="Arial, Arial, Helvetica">
<i>Contact Name</i></font></TD>
<TD><font face="Arial, Arial, Helvetica">
<font face="Arial">&nbsp;<INPUT TYPE=TEXT NAME="Contact_FullName1" SIZE=35></font></font></TD>
</TR>
<TR>
<TD ALIGN="right"><font face="Arial, Arial, Helvetica">
<font face="Arial">
<EM>Street Address</EM></font></font></TD>
<TD><font face="Arial, Arial, Helvetica">
<font face="Arial">
<INPUT TYPE=TEXT NAME="Contact_StreetAddress" SIZE=35> </font>
</font></TD>
</TR>
<TR>
<TD ALIGN="right"><font face="Arial, Arial, Helvetica">
<font face="Arial">
<EM>Address (cont.)</EM></font></font></TD>
<TD><font face="Arial, Arial, Helvetica">
<font face="Arial">
<INPUT TYPE=TEXT NAME="Contact_Address2" SIZE=35> </font>
</font></TD>
</TR>
<TR>
<TD ALIGN="right"><font face="Arial, Arial, Helvetica">
<font face="Arial">
<EM>City</EM></font></font></TD>
<TD><font face="Arial, Arial, Helvetica">
<font face="Arial">
<INPUT TYPE=TEXT NAME="Contact_City" SIZE=35> </font>
</font></TD>
</TR>
<TR>
<TD ALIGN="right"><font face="Arial, Arial, Helvetica">
<font face="Arial">
<EM>State</EM></font></font></TD>
<TD><font face="Arial, Arial, Helvetica">
<font face="Arial">
<INPUT TYPE=TEXT NAME="Contact_State" SIZE=35> </font>
</font></TD>
</TR>
<TR>
<TD ALIGN="right"><font face="Arial, Arial, Helvetica">
<font face="Arial">
<EM>Zip Code</EM></font></font></TD>
<TD><font face="Arial, Arial, Helvetica">
<font face="Arial">
<INPUT TYPE=TEXT NAME="Contact_ZipCode" SIZE=12 MAXLENGTH=12> </font>
</font></TD>
</TR>
<TR>
<TD ALIGN="right"><font face="Arial, Arial, Helvetica">
<font face="Arial">
<EM>Phone</EM></font></font></TD>
<TD><font face="Arial, Arial, Helvetica">
<font face="Arial">
<INPUT NAME="Contact_Phone" SIZE=25 MAXLENGTH=25> </font>
</font></TD>
</TR>
<TR>
<TD ALIGN="right"><font face="Arial, Arial, Helvetica">
<font face="Arial"><em>Cell Phone</em></font></font></TD>
<TD><font face="Arial, Arial, Helvetica">
<font face="Arial">
<INPUT NAME="Contact_Cell" SIZE=25 MAXLENGTH=25></font></font></TD>
</TR>
<TR>
<TD ALIGN="right"><font face="Arial, Arial, Helvetica">
<font face="Arial">
<EM>FAX</EM></font></font></TD>
<TD><font face="Arial, Arial, Helvetica">
<font face="Arial">
<INPUT NAME="Contact_FAX" SIZE=25 MAXLENGTH=25> </font>
</font></TD>
</TR>
<TR>
<TD ALIGN="right"><font face="Arial, Arial, Helvetica">
<font face="Arial">
<EM>E-mail</EM></font></font></TD>
<TD><font face="Arial, Arial, Helvetica">
<font face="Arial">
<INPUT TYPE=TEXT NAME="Contact_Email" SIZE=25> </font>
</font></TD>
</TR>
<TR>
<TD ALIGN="right"><font face="Arial, Arial, Helvetica">
<font face="Arial">
<EM>Web site</EM></font></font></TD>
<TD><font face="Arial, Arial, Helvetica">
<font face="Arial">
<INPUT TYPE=TEXT NAME="Contact_URL" SIZE=25 MAXLENGTH=25> </font>
</font></TD>
</TR>
</TABLE><font face="Arial, Arial, Helvetica">
  <p><font size="4">Who do you want notify about orders, adds, moves, changes 
  and inquiries?</font></p>
</font><TABLE height="82">
<tr>
<TD ALIGN="right" height="22"><font face="Arial, Arial, Helvetica">
<font face="Arial"><i>Who should be notified?</i></font></font></TD>
<TD height="22"><font face="Arial, Arial, Helvetica">
<font face="Arial">&nbsp;<select size="1" name="Notify_account_manager">
<option selected>Notify my Agent</option>
<option>A consultant at ONLINE-CSR</option>
<option>My account manager at the telephone Company*</option>
<option>My Agent and Account Manager*</option>
<option>ONLINE-CSR and my Account Manager*</option>
<option>Agent, Acct Manager and ONLINE-CSR*</option>
<option>Do not notify anyone at this time</option>
</select></font></font></TD>
</tr>
<TR>
<TD height="22"><font face="Arial, Arial, Helvetica">
<p align="right"><em><font face="Arial">*My Account Manager's Name </font></em>
</font></TD>
<TD height="22"><font face="Arial, Arial, Helvetica">
<font face="Arial">
<INPUT NAME="Notify_Name" SIZE=25></font></font></TD>
</TR>
<TR>
<TD ALIGN="right" height="22"><font face="Arial, Arial, Helvetica">
<font face="Arial"><em>*My Account Manager's E-mail</em></font></font></TD>
<TD height="22"><font face="Arial, Arial, Helvetica">
<font face="Arial">
<INPUT NAME="Notify_Email" SIZE=25></font></font></TD>
</TR>
<TR>
<TD ALIGN="right" height="1"><font face="Arial, Arial, Helvetica">
</font></TD>
<TD height="1"><font face="Arial, Arial, Helvetica">
</font></TD>
</TR>
</TABLE><font face="Arial, Arial, Helvetica">
  <p><font size="4">Telephone Company Account Numbers? (Billing Telephone 
  Numbers)<br>
  </font><font size="2">We need the full account number from each and every bill 
  you receive from the telephone company.<br>
  Exactly how the number appears on your bill. For example &quot;303-340-5600-425B&quot; 
  or &quot;O-801-435-9875-233M&quot;<br>
  <textarea rows="16" name="BTNs" cols="20"></textarea></font><br>
&nbsp;</p>
  <p>When we receive this information we will send you back an E-mail confirming 
  receipt.</p>
  <p>Thank You<br>
  <br>
  <input type="submit" value="Submit" name="B1">
  <input type="reset" value="Reset" name="B2">
  </p>
</form>

</font></td></tr></table>


<?
 $isLogin = 1;
 include($footer_file); 
?>

</body>

</html>