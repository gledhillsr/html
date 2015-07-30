<!--
<img class="box" src="/images/box.png" width="5" height="5" alt="#" />
<a href="/">Home</a>
| 
<a href="mailto:adminr@online-csr.com">Contact us</a>
<?
if(!$isLogin) {
  if($isAdmin) 
    echo "| <a href=\"admin.php\">Admin</a>\n";
  echo "| <a href=\"inventory.php\">Line&nbsp;Inventory&nbsp;Manager</a>";
  echo "| <a href=\"amc.php\">Adds&nbsp;Moves&nbsp;Changes</a>";
}
echo "| <a href=\"privacy.php\">Privacy policy</a>";
if(!$isLogin)
  echo "| <a href=\"log_off.php\">Log&nbsp;Off</a>";
?>
&nbsp;|&nbsp;&nbsp; &copy; 2003 CSR-Online. All rights reserved.
-->
<!--		<td valign="top" align="right"> -->
<br>
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#000052">
<tr> <td align="right">
			<table border="0" cellpadding="0" cellspacing="10">

            	<tr>
					<td><a class="nav1" href="index.php">Home</a></td>
					<td><a class="nav1" href="mailto:adminr@online-csr.com">Contact&nbsp;us</a></td>
<?
if(!$isLogin) {
  if($isAdmin) 
    echo "<td><a class=\"nav1\" href=\"admin.php\">Admin</a></td>\n";
  echo "<td><a class=\"nav1\" href=\"inventory.php\">Line&nbsp;Inventory&nbsp;Manager</a></td>";
  echo "<td><a class=\"nav1\" href=\"amc.php\">Adds&nbsp;Moves&nbsp;Changes</a></td>";
}
echo "<td><a class=\"nav1\" href=\"privacy.php\">Privacy&nbsp;Policy</a><td>";
if(!$isLogin)
  echo "<td><a class=\"nav1\"  href=\"log_off.php\">Log&nbsp;Off</a></td>";
?>
<!-- 	font: 9pt arial,helvetica,sans-serif; -->
					<td><font class="foot1">&nbsp;&nbsp;&nbsp;&copy;&nbsp;2003&nbsp;CSR-Online. All&nbsp;rights&nbsp;reserved.</font></td>
					<td><img src="images/00-bit.gif" width="10" height="1"></td> 
            	</tr>
            </table>
</td></tr></table>
<!--		</td> -->

