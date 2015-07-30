<?
/********************************************************************/
/* do NOT edit the .php file, because it is a COPY of the .HTM file */
/* so others can use front page as an editor.                       */
/********************************************************************/
?>
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Recovery</title>
<?
  $cur_page="recovery";
  require("config.php");
  include($styles_file); 
?>
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#999999" vlink="#990000" alink="#666666">
<?
include($header_file);  //note: don't specify "$page_title"
?>

<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td valign="top" width="1%"><font face="Arial, Arial, Helvetica">

<p>&nbsp;</p>

<p>
&nbsp;</p>

</font></td><td valign="top" width="24"></td><td valign="top"><font face="Arial, Arial, Helvetica"><p>Our staff of Telecom experts will work directly with the Regional Bell
Operating Company to assist our customers with recovery of any charges that were
mistakenly overcharged. They understand the systems and processes and can
quickly get issues resolved. This is a billable service that can be billed at an
hourly rate or a percentage of the credit recovered.</p>

</font></td></tr></table>
<?
 $isLogin = 1;
 include($footer_file); 
?>
</body>
</html>