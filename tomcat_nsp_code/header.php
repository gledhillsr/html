<?	/* This is the Header. You can use HTML and PHP code here. 
           It will be included at the top of the page.             */ 
function mydebug($message){
	global $showDebug;
	if($showDebug==1)
	echo $message . "<br>\n";
	}

if($page_title) {
  echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
  echo "<html>\n";
  echo "<head>\n";
  echo "<title>" . $page_title . "</title>\n";

  if (file_exists($styles_file)) {
    include($styles_file); 
  }
//  if($extra_header_str)
//    echo $extra_header_str;
//force page NOT to be cached
  echo "<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">\n";
  echo "<META HTTP-EQUIV=\"Expires\" CONTENT=\"-1\">\n";
// end of cache code

  echo "</head>\n";
  echo "<body bgcolor=\"" . $body_bgcolor  ."\" link=\"" . $body_link_color . "\" vlink=\"" . $body_vlink_color . "\" alink=\"" . $body_alink_color . "\">";
} // if no page title, assume DOCTYPE, <HEAD>, AND <BODY> ARE DONE
?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td bgcolor="#999933" align="left" width="100">
<?
if($showDebug==1) {
echo "<font size=\"1\" face=\"Arial\" color=\"#FFFFFF\">";
echo "Master Agency info:<br>$masterAgent<br>$masterAgency</font>";
}
?>
</td>
<td bgcolor="#999933" align="center">
<p><font size="6" face="Arial" color="#FFFFFF">1800SAVE.com</font></p>
</td>
<td bgcolor="#999933" align="left" width="100">

<font size="1" face="Arial" color="#FFFFFF">
<?
if($isLogin != 1)
  echo "Welcome,&nbsp;$agentFirstName<br>" . htmlspecialchars ($companyName);
?>
</font>
</td>
</tr>
</table>
