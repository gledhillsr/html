<?

$config_file = "config.php";  // Full path and name of the config file
$cur_page="filter_inventory";

require($config_file);

$page_title = "Inventory Management Service - Inventory Filter";  // Page title
$mysql_table    = "btn";               		// MySQL table name
$edit_url       = "edit_btn.php";           // URL of the edit.php file


//if (!$max_results) $max_results = $max_results_default;
//setCookie("max_results_default",$max_results);
$manager_name = "Line Inventory Display Filter";
$button_line = "<a class=\"nav3\" href=\"amc.php\">Adds, Moves &amp; Changes</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
if($isAdmin)
  $button_line .= "<a class=\"nav3\" href=\"admin.php\">Admin</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$button_line  .= "<a class=\"nav3\" href=\"archive.php\">Archive</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
"<a href=\"log_off.php\">Log Off</a>";

$connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
// $filterDept, $filterLoc, $filterDivs, $filterReview
if ($filterReview1)
{
if(!$filterDept1 || $filterDept1 == "All")
  setCookie("filterDept");
else
  setCookie("filterDept",$filterDept1);

if(!$filterLoc1 || $filterLoc1 == "All")
  setCookie("filterLoc");
else
  setCookie("filterLoc",$filterLoc1);

if(!$filterDivs1 || $filterDivs1 == "All")
  setCookie("filterDivs");
else
  setCookie("filterDivs",$filterDivs1);

if(!$filterReview1 || $filterReview1 == "All")
  setCookie("filterReview");
else
  setCookie("filterReview",$filterReview1);


 header("Location: inventory.php");	/* Redirect browser */ 
 exit;
} else {
 $query_string = "SELECT Division from btn where CustomerID='$customer_id' ORDER BY Division" ;
//echo $query_string; // Debug only
 $result_division = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");

 $query_string = "SELECT Department from btn where CustomerID='$customer_id' ORDER BY Department" ;
//echo $query_string; // Debug only
 $result_dept = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");

 $query_string = "SELECT Location from btn where CustomerID='$customer_id' ORDER BY Location" ;
//echo $query_string; // Debug only
 $result_loc = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");

 }

include($header_file); 
include($box_header_file); 

//echo "($filterDept, $filterLoc, $filterDivs, $filterReview)"; //debug

?>


<br><br>
<form method="POST" action="<? echo $SCRIPT_NAME . "?entry=" . urlencode($entry); ?>" name="edit_form">
<table width="100%" bgcolor="<? echo $header_color; ?>" border="0" cellspacing="0" cellpadding="4">
  <tr>
  <td align="center">
   <h3>Select any combination of subjects to limit the display within the &quot;Line&nbsp;Inventory&nbsp;Manager&quot;</h3>
  </td>
  </tr>
  <tr>
  <td>
  <table width="100%" bgcolor="<? echo $header_color; ?>" border="1" cellspacing="0" cellpadding="4">
    <tr>
      <td align="center"><b>Location Name:</b></td>
      <td align="center"><b>Division:</b></td>
      <td align="center"><b>Department:</b></td>
      <td align="center"><b>Review Status:</b></td>
    </tr>
    <tr>
      <td align="center"><select size="1" name="filterLoc1">
<?
/********* Location **********/
	if(!$filterLoc)
	  echo "<option selected>All</option>\n";
	else
	  echo "<option>All</option>\n";
	$cur = "";
 while ($divs_row = @mysql_fetch_array($result_loc)) {
	if($cur != $divs_row[0]) {
		$cur = $divs_row[0];
		if($filterLoc == $cur)
		  $sel = "selected";
		else
		  $sel = "";
        echo "\n<option $sel>$cur</option>";
	} // end if
 } // end while
?>
        &nbsp;
        </select></td>

      <td align="center"><select size="1" name="filterDivs1">
<?
/******** division *******/
	if(!$filterDivs)
	  echo "<option selected>All</option>\n";
	else
	  echo "<option>All</option>\n";
	$cur = "";
 while ($divs_row = @mysql_fetch_array($result_division)) {
	if($cur != $divs_row[0]) {
		$cur = $divs_row[0];
		if($filterDivs == $cur)
		  $sel = "selected";
		else
		  $sel = "";
        echo "\n<option $sel>$cur</option>";
	} // end if
 } // end while
?>
        &nbsp;
        </select></td>
      <td align="center"><select size="1" name="filterDept1">
<?
/******** department *******/
	if(!$filterDept)
	  echo "<option selected>All</option>\n";
	else
	  echo "<option>All</option>\n";
	$cur = "";
 while ($divs_row = @mysql_fetch_array($result_dept)) {
	if($cur != $divs_row[0]) {
		$cur = $divs_row[0];
		if($filterDept == $cur)
		  $sel = "selected";
		else
		  $sel = "";
        echo "\n<option $sel>$cur</option>";
	} // end if
 } // end while
?>
        &nbsp;
        </select></td>
      <td align="center"><select size="1" name="filterReview1">
          <option <? if(!$filterReview) echo "selected"; ?> >All</option>
          <option <? if($filterReview == $review_blank) echo "selected"; echo ">$review_blank";?></option>
          <option <? if($filterReview == $review_n)     echo "selected"; echo ">$review_n";?></option>
          <option <? if($filterReview == $review_y)     echo "selected"; echo ">$review_y";?></option>
        </select></td>
    </tr>
  </table>
  </td>
  </tr>
  <tr>
  <td align = "center"> 
    <br>
    <input type="submit" value="Save and Return" name="B1">&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="button" value="Cancel &amp; Return" name="B2" onClick=history.back()>
	<br>&nbsp;
  </td>
  </tr>
</table>
</form>
<p>&nbsp;</p>

<?
 include($footer_file); 
 if($result_division)
   @mysql_free_result($result_division);
 if($result_dept)
   @mysql_free_result($result_dept);
 if($result_loc)
   @mysql_free_result($result_loc);

?>
</body>

</html>
