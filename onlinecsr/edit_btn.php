<?

$config_file = "config.php";  // Full path and name of the config file
$btn_table    = "btn";      // MySQL table name
$cur_page="edit_btn";
$page_title = "Inventory Management Service - Line Inventory Editor";  // Page title

require($config_file);


if ($edit_flag == "edit")
 $manager_name = "Editing Line Inventory";
else
 $manager_name = "Adding New Line Inventory";
$button_line = "";
if($isAdmin)
  $button_line .= "<a class=\"nav3\" href=\"admin.php\">Admin</a>&nbsp;&nbsp;&nbsp;&nbsp;";
$button_line .= "<a class=\"nav3\" href=\"inventory.php\">Line&nbsp;Inventory</a>&nbsp;&nbsp;&nbsp;&nbsp;" .
"<a class=\"nav3\" href=\"amc.php\">Adds, Moves, &amp; Changes</a>&nbsp;&nbsp;&nbsp;&nbsp;" .
"<a class=\"nav3\" href=\"archive.php\">Archive</a>&nbsp;&nbsp;&nbsp;&nbsp;" .
"<a class=\"nav3\" href=\"log_off.php\">Log Off</a>";

if ( (!$entry AND !$add AND !$edit_add) OR ($edit_cancel) )
 {
 header("Location: inventory.php");	/* Redirect browser */ 
 exit;
 }

//echo "($user_id)...($edit_update)";
if(!$edit_update || ($edit_update && !$user_id)) {
  $extra_header_str = "<style>\n"
    . " .main{font-size:14pt;color:teal;}\n"
    . " .small{font-size:10px;font-family : Verdana;}\n"
    . " .input{background-color: #eeeeee;font-family : Verdana;}\n"
    . " BODY, TD {font-family : Verdana;font-size:10pt;}\n"
    . "</style>\n";

   include($header_file); 
   include($box_header_file); 
}

if ($edit_update)
 {
 update_db("edit");
 exit;
 }

//if ($edit_update)
// {
// update_db("edit");
// exit;
// }

if ($edit_add)
 {
 update_db("add");
 exit;
 }

if ($add)
 {
 edit_db("add");
 exit;
 }

edit_db("edit");
exit;

/*********************/
/*      edit_db      */
/*********************/

function edit_db($edit_flag)
{
global $ReqTime, $entry, $btn_field, $mysql_host, $mysql_username, $mysql_password, $mysql_db, $btn_table;
global $phppwd_1, $SCRIPT_NAME, $user_name, $customer_id, $customer_name, $user_id;
global $edit_field4, $edit_field10, $edit_field12;
global $edit_field13,$edit_field14,$edit_field15,$edit_field16,$edit_field17;

//MySQL query: connect and retrieve AMC count 
$connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");

if ($edit_flag == "edit")
 {
 $query_string = "SELECT *  FROM " . $btn_table . " WHERE " . $btn_field[2][1] . "=\"" . $entry  . "\" LIMIT 1";

//echo $query_string; // Debug only

 $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");
 $row = @mysql_fetch_array($result);
 @mysql_free_result($result);
 }

?>

<form method="POST" action="<? echo $SCRIPT_NAME . "?entry=" . urlencode($entry); ?>" name="edit_form">

<p>
<table align="center" bgcolor="#000000">
<tr>
 <td><table border="1" align="center" bgcolor="#C0C0C0" cellspacing="1" cellpadding="4">
 <tr>
  <td align="center"><? echo $btn_field[1][1]; ?></td>
  <td><B><? echo "$entry"; ?></B></td>
 </tr>

 <tr>
  <td align="center">Last Updated</td>
  <td>
  <?
   $edit_field2 = $row[ $btn_field[2][2] ];
   $edit_field2 = substr($edit_field2,0,10);  //window's numbers include millis (last 3 digets)
   echo date("l, F d, Y - h:i A",$edit_field2);
   ?>
  </td>
 </tr>

 <tr>
  <td align="center"><? echo $btn_field[1][3]; ?></td>
  <td><input type="text" size="40" maxlength="40" class="input" name="<? echo "edit_field3" . "\" value=\"" . htmlentities($row[ $btn_field[2][3] ]); ?>">
 </tr>

 <tr>
  <td align="center"><? echo $btn_field[1][4]; ?></td>
  <td><input type="text" size="20" maxlength="20" class="input" name="<? echo "edit_field4" . "\" value=\"" . htmlentities($row[ $btn_field[2][4] ]); ?>">
 </tr>

 <tr>
  <td align="center"><? echo $btn_field[1][5]; ?></td>
  <td><input type="text" size="20" maxlength="20" class="input" name="<? echo "edit_field5" . "\" value=\"" . htmlentities($row[ $btn_field[2][5] ]); ?>">
 </tr>

 <tr>
  <td align="center"><? echo $btn_field[1][7]; ?></td>
  <td><input type="text" size="40" maxlength="40" class="input" name="<? echo "edit_field7" . "\" value=\"" . htmlentities($row[ $btn_field[2][7] ]); ?>">
  </td>
 </tr> 

 <tr>
  <td align="center"><? echo $btn_field[1][8]; ?></td>
  <td>
<?
   $seconds = $row[ $btn_field[2][8] ];
   if(strlen($seconds) == 10) {
      $dateArray = getdate($seconds);
      $day = $dateArray["mday"];
	  $mon = $dateArray["mon"];
	  $yr  = $dateArray["year"];
   }
   else {
      $day="";
	  $mon="";
	  $yr="";
   }
?>
  <select size="1" name="exp_month">
    <option>-Unknown-</option>
    <option<? if($mon == "1") echo " Selected"; ?>>January</option>
    <option<? if($mon == "2") echo " Selected"; ?>>February</option>
    <option<? if($mon == "3") echo " Selected"; ?>>March</option>
    <option<? if($mon == "4") echo " Selected"; ?>>April</option>
    <option<? if($mon == "5") echo " Selected"; ?>>May</option>
    <option<? if($mon == "6") echo " Selected"; ?>>June</option>
    <option<? if($mon == "7") echo " Selected"; ?>>July</option>
    <option<? if($mon == "8") echo " Selected"; ?>>August</option>
    <option<? if($mon == "9") echo " Selected"; ?>>September</option>
    <option<? if($mon == "10") echo " Selected"; ?>>October</option>
    <option<? if($mon == "11") echo " Selected"; ?>>November</option>
    <option<? if($mon == "12") echo " Selected"; ?>>December</option>
  </select>
  <select size="1" name="exp_date">
    <option></option>
    <option<? if($day == "1") echo " Selected"; ?>>01</option>
    <option<? if($day == "2") echo " Selected"; ?>>02</option>
    <option<? if($day == "3") echo " Selected"; ?>>03</option>
    <option<? if($day == "4") echo " Selected"; ?>>04</option>
    <option<? if($day == "5") echo " Selected"; ?>>05</option>
    <option<? if($day == "6") echo " Selected"; ?>>06</option>
    <option<? if($day == "7") echo " Selected"; ?>>07</option>
    <option<? if($day == "8") echo " Selected"; ?>>08</option>
    <option<? if($day == "9") echo " Selected"; ?>>09</option>
    <option<? if($day == "10") echo " Selected"; ?>>10</option>
    <option<? if($day == "11") echo " Selected"; ?>>11</option>
    <option<? if($day == "12") echo " Selected"; ?>>12</option>
    <option<? if($day == "13") echo " Selected"; ?>>13</option>
    <option<? if($day == "14") echo " Selected"; ?>>14</option>
    <option<? if($day == "15") echo " Selected"; ?>>15</option>
    <option<? if($day == "16") echo " Selected"; ?>>16</option>
    <option<? if($day == "17") echo " Selected"; ?>>17</option>
    <option<? if($day == "18") echo " Selected"; ?>>18</option>
    <option<? if($day == "19") echo " Selected"; ?>>19</option>
    <option<? if($day == "20") echo " Selected"; ?>>20</option>
    <option<? if($day == "21") echo " Selected"; ?>>21</option>
    <option<? if($day == "22") echo " Selected"; ?>>22</option>
    <option<? if($day == "23") echo " Selected"; ?>>23</option>
    <option<? if($day == "24") echo " Selected"; ?>>24</option>
    <option<? if($day == "25") echo " Selected"; ?>>25</option>
    <option<? if($day == "26") echo " Selected"; ?>>26</option>
    <option<? if($day == "27") echo " Selected"; ?>>27</option>
    <option<? if($day == "28") echo " Selected"; ?>>28</option>
    <option<? if($day == "29") echo " Selected"; ?>>29</option>
    <option<? if($day == "30") echo " Selected"; ?>>30</option>
    <option<? if($day == "31") echo " Selected"; ?>>31</option>
  </select>
  <select size="1" name="exp_year">
    <option></option>
    <option<? if($yr == "2002") echo " Selected"; ?>>2002</option>
    <option<? if($yr == "2003") echo " Selected"; ?>>2003</option>
    <option<? if($yr == "2004") echo " Selected"; ?>>2004</option>
    <option<? if($yr == "2005") echo " Selected"; ?>>2005</option>
    <option<? if($yr == "2006") echo " Selected"; ?>>2006</option>
    <option<? if($yr == "2007") echo " Selected"; ?>>2007</option>
    <option<? if($yr == "2008") echo " Selected"; ?>>2008</option>
    <option<? if($yr == "2009") echo " Selected"; ?>>2009</option>
    <option<? if($yr == "2010") echo " Selected"; ?>>2010</option>
  </select>
<? /****** add this clear date button later ****
  &nbsp;&nbsp;&nbsp;-or-&nbsp;&nbsp;&nbsp;
<input type="button" value="Clear Date" name="B3">
****/ ?>
 </td>
 </tr> 

 <tr>
  <td align="center"><? echo $btn_field[1][9]; ?></td>
  <td>
  $<? echo $row[ $btn_field[2][9] ]; ?>
  </td>
 </tr> 

 <tr>
  <td align="center"><? echo $btn_field[1][10]; ?></td>
  <td>
  <select size="1" name=edit_field10 >
  <? $foo = $row[ $btn_field[2][10] ]; ?>
    <option<? if($foo == "")  echo " Selected";?>>Not Yet Reviewed</option>
    <option<? if($foo == "n") echo " Selected";?>>Review OK</option>
    <option<? if($foo == "t") echo " Selected";?>>Technology Update</option>
    <option<? if($foo == "y") echo " Selected";?>>Needs Review</option>
  </select>
  </td>
 </tr> 

 <tr>
  <td align="center"><? echo $btn_field[1][12]; ?></td>
  <td><TEXTAREA cols="40" rows="4" class="input" name="edit_field12"><? echo $row[ $btn_field[2][12] ];?></TEXTAREA>
  </td>
 </tr> 

 <tr>
  <td align="center"><? echo $btn_field[1][13]; ?></td>
  <td>
  $<? echo $row[ $btn_field[2][13] ];  //prevCost
  ?>
  </td>
 </tr> 

 <tr>
  <td align="center"><? echo $btn_field[1][14]; ?></td>
  <td>
  <?
   $edit_field14 = $row[ $btn_field[2][14] ];	//prev update
   if($edit_field14 > 0) {
	   $edit_field14 = substr($edit_field14,0,10);  //window's numbers include millis (last 3 digets)
	   echo date("l, F d, Y - h:i A",$edit_field14);
	} else {
	   echo "&nbsp;";
	}
   ?>
  </td>
 </tr>

 <tr>
  <td align="center"><? echo $btn_field[1][15]; ?></td>
  <td>
  <?
   $edit_field15 = $row[ $btn_field[2][15] ];	//MonthlySavings
   echo "$";
   echo "<input type=\"text\" size=\"20\" maxlength=\"10\" class=\"input\" name=\"edit_field15\" value=\"" . htmlentities($row[ $btn_field[2][15] ]) . "\">";
   ?>
  </td>
 </tr>

 <tr>
  <td align="center"><? echo $btn_field[1][16]; ?></td>
  <td>
  <?
   $edit_field16 = $row[ $btn_field[2][16] ];	//product
  $ck0 = ($edit_field16 == 0) ? "checked" : "";
  $ck1 = ($edit_field16 == 1) ? "checked" : "";
  $ck2 = ($edit_field16 == 2) ? "checked" : "";
  $ck3 = ($edit_field16 == 3) ? "checked" : "";
  $ck4 = ($edit_field16 == 4) ? "checked" : "";
  $ck5 = ($edit_field16 == 5) ? "checked" : "";
  echo "<input type=\"radio\" name=\"edit_field16\" value=\"0\" $ck0>none<br>\n";
  echo "<input type=\"radio\" name=\"edit_field16\" value=\"1\" $ck1>BLP<br>\n";
  echo "<input type=\"radio\" name=\"edit_field16\" value=\"2\" $ck2>CTX21<br>\n";
  echo "<input type=\"radio\" name=\"edit_field16\" value=\"3\" $ck3>PRI<br>\n";
  echo "<input type=\"radio\" name=\"edit_field16\" value=\"4\" $ck4>DSS<br>\n";
  echo "<input type=\"radio\" name=\"edit_field16\" value=\"5\" $ck5>Other MRC $";
  $edit_field17 = $row[ $btn_field[2][17] ];  //MRC
  echo "<input type=\"text\" size=\"20\" maxlength=\"10\" class=\"input\" name=\"edit_field17\" value=\"" . htmlentities($row[ $btn_field[2][17] ]) . "\">";
   ?>
  </td>
 </tr>

 <tr>
  <td align="center" class="small">&nbsp</td><td align="center">
<?

if ($edit_flag == "edit")
 {
 ?> <input name="edit_update" type="submit" value="Update Item"> 
 <?
 }
else 
 {
 ?> <input name="edit_add" type="submit" value="Add Item"><?
 }

?> <input name="edit_cancel" type="submit" value="Cancel">
  </td>
 </tr>	
</table></td>
</tr></table>
<p>
</form>
<script language="JavaScript">
<!--
var dc = document.cookie;
var SavedPassword = getCookie("phppwd_1");

function getCookie(name)
{
 var cname = name + "=";               
 var dc = document.cookie;

 if (dc.length > 0)
   {              
    begin = dc.indexOf(cname);
    if (begin != -1)
     {           
      begin += cname.length;
      end = dc.indexOf(";", begin);
      if (end == -1) end = dc.length;
      return unescape(dc.substring(begin, end));
     }
   }

 return null;
}
-->
</script>
</body>
<? /* force page NOT to be cached */ ?>
<HEAD>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
</HEAD>
</html>
<?

//if ($edit_flag == "edit")
// {
 @mysql_close($connect_string);
 @mysql_free_result($result);
// }

}

/*********************/
/*    update_db      */
/*********************/

function update_db($update_flag)
{
global $ReqTime, $btn_field, $entry, $mysql_host, $mysql_username, $mysql_password, $mysql_db, $btn_table;
global $index_url, $SCRIPT_NAME, $edit_password, $form_password;
global $edit_field1, $edit_field2, $edit_field3, $edit_field4, $edit_field5, $edit_field6;
global $edit_field7, $edit_field8, $edit_field9, $edit_field10, $user_name, $edit_field12;
global $edit_field13,$edit_field14,$edit_field15,$edit_field16,$edit_field17;
global $customer_id, $customer_name, $user_id;
global $exp_date, $exp_month, $exp_year;

$edit_field11=  $customer_id;
$mod_time = time();

  $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");

	if($edit_field10 == "Needs Review")  $edit_field10 = "y"; 
	elseif($edit_field10 == "Technology Update") $edit_field10 = "t"; 
	elseif($edit_field10 == "Review OK") $edit_field10 = "n"; 
	else 								 $edit_field10 = "";
// compute expiration date in seconds and store in $edit_field8
   if(strlen($exp_date) && strlen($exp_month) && strlen($exp_year)) {
      $edit_field8 = strtotime ("$exp_date $exp_month $exp_year");
   } else {
      $edit_field8 = 0;
   }
//echo "$exp_date/$exp_month/$exp_year = $edit_field8"; //debug date

   if($user_id) {

	  if ($update_flag == "edit")
	   {
	   $query_string = "UPDATE " . $btn_table . " SET " . 
	//   $btn_field[2][2] . "=\"" . $mod_time . "\", " .  //original file date, not modify date
	   $btn_field[2][3] . "=\""  . $edit_field3  . "\", " . 
	   $btn_field[2][4] . "=\""  . $edit_field4  . "\", " . 
	   $btn_field[2][5] . "=\""  . $edit_field5  . "\", " . 
	   $btn_field[2][7] . "=\""  . $edit_field7  . "\", " . 
	   $btn_field[2][8] . "=\""  . $edit_field8  . "\", " . 
	   $btn_field[2][10] . "=\"" . $edit_field10 . "\", " . 
//field11 (customer) does not change
	   $btn_field[2][12] . "=\"" . $edit_field12 . "\", " .	//review Notes
//field13 (prevCost) does not change
//field14 (prevUpdated) does not change
	   $btn_field[2][15] . "=\"" . $edit_field15 . "\", " . //MonthlySavings
	   $btn_field[2][16] . "=\"" . $edit_field16 . "\", " .	//Product
	   $btn_field[2][17] . "=\"" . $edit_field17 .			//MRC

	   "\" WHERE " . $btn_field[2][1] . "=\"" . stripslashes($entry) . "\" LIMIT 1";
	   }
	  else
	   {
	//on add, insert current time
$edit_field6 = "";
	   $query_string = "INSERT INTO " . $btn_table . " (" . 
	   		$btn_field[2][1]  . ", " . $btn_field[2][2]  . ", " . $btn_field[2][3] . ", " . 
	   		$btn_field[2][4]  . ", " . $btn_field[2][5]  . ", " . $btn_field[2][6] . ", " . 
	   		$btn_field[2][7]  . ", " . $btn_field[2][8]  . ", " . $btn_field[2][9] . ", " . 
	   		$btn_field[2][10] . ", " . $btn_field[2][11] . ", " . $btn_field[2][12] . ", " . 
//13 & 14 use default
	   		$btn_field[2][15] . ", " . $btn_field[2][16] . ", " . $btn_field[2][17] .
	   		") VALUES (\"" . 
	   		$entry  . "\", \"" . $edit_field2  . "\", \"" . $edit_field3 . "\", \"" . 
	   		$edit_field4  . "\", \"" . $edit_field5  . "\", \"" . $edit_field6 . "\", \"" . 
	   		$edit_field7  . "\", \"" . $edit_field8  . "\", \"" . $edit_field9 . "\", \"" . 
	   		$edit_field10  . "\", \"" . $edit_field11  . "\", \"" . $edit_field12 . "\", \"" . 
//13 & 14 use default
	   		$edit_field15 . "\", \"" . $edit_field16 . "\", \"" . $edit_field17 . "\")";
	   }

//echo "<p>--- debugging stuff ---<br>";	//debug only
//echo "$query_string<br>"; 				//Debug only
//echo "--- end debugging stuff ---</p>";	//Debug only

	   $result = mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");

	   @mysql_close($connect_string);
	   @mysql_free_result($result);
      header("Location: inventory.php#$entry");	/* Redirect browser */ 
      exit;
   }
//to get here, yhou must be in demo mode (no "user_id")
?>


<html>
<head></head>
<body bgcolor="#FFFFFF" link="#000000" vlink="#000000" alink="red">
<p><br>
<center>

<table border="1" bgcolor="#F0F0F0" width="99%" align="center" cellspacing="2" cellpadding="8">
<tr><td align="center">
<br><b>
<?


//   if(!$user_id) {
      echo "<h3>Database can not be modified in Demo mode!</h3><br>";
//   }
//  elseif ($update_flag == "edit")
//   {
//   echo "The Request has been updated!";
//   }
//  else
//   {
//   echo "The Request has been added!";
//   }

?>
</b>
<form method="POST" action="">
  <p>
  <input type="button" value="Return to Line Inventory List" name="B3" onClick=window.location="inventory.php">
  </p>
</form>

</td>
</table>
</center>

<br>
</body>
</html>

<?
  @mysql_close($connect_string);
  @mysql_free_result($result);

}
?>