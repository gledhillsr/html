<?
/********************************************************************/
/* do NOT edit the .php file, because it is a COPY of the .HTM file */
/* so others can use front page as an editor.                       */
/********************************************************************/
?>

<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta http-equiv="Content-Language" content="en-us">
<title>Agents Feedback Page</title>
<?
  $isForm=1;
  $cur_page="feedback";
  require("config.php");
  include($styles_file); 
?>
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#999999" vlink="#990000" alink="#666666"  >
<?
include($header_file); 
?>
<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td valign="top" width="1%">
<font face="Arial, Arial, Helvetica">


<p>&nbsp;</p>


<p>
&nbsp;</p>

</font></td><td valign="top" width="24"></td><td valign="top"><font face="Arial, Arial, Helvetica">

<p> <font face="Arial"><b><font color="#333333" size="5">We Appreciate you 
Feedback</font></b><br>
Please tell us what you think about our web site, company, or services. If you provide us with your contact
information, we will be able to reach you in case we have any
questions.</font></p>
</font><TABLE>

<form method="POST" action="mail.php?frm=feedback&owner=<? echo $owner; ?>.php" name="edit_form">

<p> &nbsp;</p>

    <h3><font face="Arial">Comments</font></h3>
    <blockquote>
        <p><font face="Arial">
        <textarea name="Feedback_Comments" rows="11" cols="56"></textarea>
        </font> </p>
    </blockquote>
    <h3><font face="Arial">How did you hear about us?</font></h3>
    <blockquote>
        <p><font face="Arial"><select name="How_did_you_find_us">
            <option selected>From Another Agent</option>
            <option>From a Customer</option>


            <option>From an RBOC</option>


            <option>Other</option>


        </select> </font> </p>
    </blockquote>
    <h3><font face="Arial">Contact Information</font></h3>
    </font><table>
<tr><td align="right"><font face="Arial, Arial, Helvetica"><font face="Arial"><em>Name</em></font></font></td><td><font face="Arial, Arial, Helvetica">
  <font face="Arial"><input name="Name" value size="35"></font></font></td></tr>

<tr><td align="right"><font face="Arial, Arial, Helvetica"><font face="Arial"><em>Company</em></font></font></td><td><font face="Arial, Arial, Helvetica">
  <font face="Arial"><input type="TEXT" name="Company" value size="35"></font></font></td></tr>

<tr><td align="right"><font face="Arial, Arial, Helvetica"><font face="Arial"><em>Telephone</em></font></font></td><td><font face="Arial, Arial, Helvetica">
  <font face="Arial"><input type="TEXT" name="Telephone" value size="35"></font></font></td></tr>
<tr><td align="right"><font face="Arial, Arial, Helvetica"><font face="Arial"><em>FAX</em></font></font></td><td><font face="Arial, Arial, Helvetica">
  <font face="Arial"><input type="TEXT" name="FAX" value size="35"></font></font></td></tr>
<tr><td align="right"><font face="Arial, Arial, Helvetica"><font face="Arial"><em>E-mail</em></font></font></td><td><font face="Arial, Arial, Helvetica">
  <font face="Arial"><input type="TEXT" name="Email" value size="35"></font></font></td></tr>
</table><font face="Arial, Arial, Helvetica">
    <p><font face="Arial">
	<input type="SUBMIT" value="Submit Feedback" >
    <input type="RESET" value="Reset Form"> </font> </p>
</form>



</font></td></tr></table><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td><font face="Arial, Arial, Helvetica">

<p>&nbsp;</p>


<p>
</p>

</font></td></tr></table>

<?
 $isLogin = 1;
 include($footer_file); 
?>
</body>
</html>