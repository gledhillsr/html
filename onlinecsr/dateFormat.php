<?

$config_file = "config.php";  // Full path and name of the config file
$cur_page="dateFormat";

require($config_file);

$page_title = "Inventory Management Service - Date Format";  // Page title
//$manager_name = "Line Adds, Moves, & Changes";
$isForm = "true";
include($header_file);
?>
 <table border="1" align="center" bgcolor="#C0C0C0" cellspacing="2" cellpadding="8">
 <tr>
  <td align="center"><b>Example Date formats.</b></td>
 </tr>
 <tr>
<td>
<b>
10 September 2003<br>
9/10/03<br>
+3 days<br>
+1 week<br>
+1 week 2 days<br>
+3 months<br>
next Thursday<br>
Friday<br>
</b>
</td>
</tr>
</table>
<br>
<a href="xyz" align="center" onclick="window.close(); return false;">Close window</a>

<div class="foot">
<img class="box" src="/images/box.png" width="5" height="5" alt="#" />
&nbsp;&nbsp; &copy; 2003 CSR-Online. All rights reserved.
</div>

</body>
</html>
