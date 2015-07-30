<div class="foot">
<img class="box" src="/images/box.png" width="5" height="5" alt="#" />
<a href="csr.php">Home</a>
| <a href="mailto:adminr@online-csr.com">Contact us</a>
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
</div>
