<?

$config_file = "config.php";  // Full path and name of the config file
$amc_table    = "amc";               		// MySQL table name
$edit_url       = "edit_amc.php";           // URL of the edit.php file
$cur_page="amc";

require($config_file);

$page_title = "Inventory Management Service - Adds, Moves and Changes Database";  // Page title

//if (!$max_results) $max_results = $max_results_default;
if ($max_results) 
  setCookie("max_results_cookie",$max_results);

if (!$max_results) {
	if($max_results_cookie)
		$max_results = $max_results_cookie;
	else
		$max_results = $max_results_default;
}
setCookie("max_results_cookie",$max_results);


if ($edit == 1) $edit_string = "&edit=1";
if (!$table_bgcolor) $table_bgcolor = "#FFFFFF";

$manager_name = "Line Adds, Moves, & Changes";
$button_line = 
"<a class=\"nav3\" href=\"edit_amc.php?add=1\">New Request</a>&nbsp;&nbsp;&nbsp;";
if($isAdmin)
  $button_line .= "<a class=\"nav3\" href=\"admin.php\">Admin</a>&nbsp;&nbsp;&nbsp;";
$button_line .= "<a class=\"nav3\" href=\"inventory.php\">Line&nbsp;Inventory</a>&nbsp;&nbsp;&nbsp;" .
"<a class=\"nav3\" href=\"archive.php\">Archive</a>&nbsp;&nbsp;&nbsp;" .
"<a class=\"nav3\" href=\"help_amc.htm\">Help</a>&nbsp;&nbsp;&nbsp;" .
"<a class=\"nav3\" href=\"log_off.php\">Log Off</a>";

include($header_file);
include($box_header_file); 

?>
<form method="POST" action="<? echo $REQUEST_URI; ?>">
<?
/*******************************/
/* table for requests per page */
/*******************************
//<table bgcolor="<? echo $max_results_background_color ?>" border="0" width="100%" cellpadding="10" cellspacing="0">
//<tr><td valign="middle" align="center">
//Results per page:
//<select name="max_results" size="1">
// <option <? if ($max_results == 4) {echo "selected";}  ?> value="4">4</option>
// <option <? if ($max_results == 50) {echo "selected";}  ?> value="50">50</option>
// <option <? if ($max_results == 100) {echo "selected";} ?> value="100">100</option>
// <option <? if ($max_results == 200) {echo "selected";} ?> value="200">200</option>
// <option <? if (strtolower($max_results) == "all") {echo "selected";} ?> value="all">All</option>
//</select>
//<input type="image" value="Go" src="<? echo $go_button; ?>" border="0" name="Go">..(only visible is more than 50 btn's)
//</font></td></tr>
//</table>
/****  end table for requests per page ****/

$ctr1 = 1; $color_ctr = 1;

switch ( $sort ) {
//Ascending	(2nd sort key of BTN)
   case  "2a":  $sort_string = $amc_field[2][2] . ", " . $amc_field[2][1]; break;
   case  "3a":  $sort_string = $amc_field[2][3] . ", " . $amc_field[2][1]; break;
   case  "4a":  $sort_string = $amc_field[2][4] . ", " . $amc_field[2][1]; break;
   case  "5a":  $sort_string = $amc_field[2][5] . ", " . $amc_field[2][1]; break;
   case  "6a":  $sort_string = $amc_field[2][6] . ", " . $amc_field[2][1]; break;
   case  "7a":  $sort_string = $amc_field[2][7] . ", " . $amc_field[2][1]; break;
   case  "8a":  $sort_string = $amc_field[2][8] . ", " . $amc_field[2][1]; break;
   case  "9a":  $sort_string = $amc_field[2][9] . ", " . $amc_field[2][1]; break;
   case  "10a": $sort_string = $amc_field[2][13] . ", " . $amc_field[2][1]; break;
//Descending (2nd sort key of BTN)
   case "1d":  $sort_string = $amc_field[2][1] . " DESC"; break;
   case "2d":  $sort_string = $amc_field[2][2] . " DESC, " . $amc_field[2][1]; break;
   case "3d":  $sort_string = $amc_field[2][3] . " DESC, " . $amc_field[2][1]; break;
   case "4d":  $sort_string = $amc_field[2][4] . " DESC, " . $amc_field[2][1]; break;
   case "5d":  $sort_string = $amc_field[2][5] . " DESC, " . $amc_field[2][1]; break;   
   case "6d":  $sort_string = $amc_field[2][6] . " DESC, " . $amc_field[2][1]; break;   
   case "7d":  $sort_string = $amc_field[2][7] . " DESC, " . $amc_field[2][1]; break;   
   case "8d":  $sort_string = $amc_field[2][8] . " DESC, " . $amc_field[2][1]; break;   
   case "9d":  $sort_string = $amc_field[2][9] . " DESC, " . $amc_field[2][1]; break;   
   case "10d":  $sort_string = $amc_field[2][13] . " DESC, " . $amc_field[2][1]; break;   
//Ascending on Btn key
	case "1a":
     default:  $sort_string = $amc_field[2][1];
}

if ($search != "") { $search_string = " WHERE ". $search_type . " like \"%" . $search . "%\""; }

// Max. results per page

if ( strtolower($max_results) == "all") { $search_maximum = 99999; }
 else { $search_maximum = $max_results; }

$count_string = "SELECT count(*) FROM " . $amc_table . " " . $search_string .
	" WHERE " . $amc_field[2][11] . "=\"" . $customer_id . "\"";
if($entry != "")
	$count_string .= " AND BTN=\"" . $entry . "\"";
//???
//MySQL query: connect and retrieve DVD count 
$connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or $db_error = 1;
if (!$db_error)
{
$count_result = @mysql_db_query($mysql_db, $count_string) or die ("<p align=\"center\">Error accessing MySQL table!");

$total = @mysql_fetch_array($count_result);

if ($total[0] > 0)
{
  $header_string = "$SCRIPT_NAME?search=" . $search . "&search_type=" . $search_type . "&max_results=" . $max_results . $edit_string;
  echo "\n</form>\n\n<form method=\"POST\" action=\"" . $header_string . "&sort=" . $sort . "\">\n\n";

  $pages = ceil($total[0] / $search_maximum);

// Select page 
  if ($page != "" AND $page >= 1 AND $page <= $pages)
   { $search_results =  ( ( $total[0] - ( $total[0] - ( $page * $search_maximum ) ) ) - $search_maximum );
   $current_page = $page; }
  elseif ($page != "" AND $page > $pages)
   { $search_results =  ( ( $total[0] - ( $total[0] - ( $pages * $search_maximum ) ) ) - $search_maximum );
   $current_page = $pages; }
  elseif ($page != "" AND $page < 1)
   { $search_results =  ( ( $total[0] - ( $total[0] - ( 1 * $search_maximum ) ) ) - $search_maximum );
   $current_page = 1; }
  else { $current_page = 1 + ( ( $total[0] - ($total[0] - $search_results) ) / $search_maximum ); }

if($entry != "")
	$search_results .= "&entry=" . $entry;

/*******************/
/*   Table header  */
/*******************/
  ?>
  <table width="100%" bgcolor="<? echo $table_bgcolor; ?>" border="0" cellspacing="1" cellpadding="1">
  <tr><?
  if ($edit == 1)
   { ?>
    <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
    <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color; ?>"><b>Edit</b></font></div>
    </td>
    <?
   } 

  ?>

  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>"><a href="<? 
  echo $header_string."&sort=";
  if ($sort == "1a" OR $sort == "") { echo "1d"; } else { echo "1a"; }
  echo "&search_results=".$search_results;
  ?>"><b><? echo $amc_field[1][1]; ?></b></a></font></div>
  </td>

  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>"><a href="<? 
  echo $header_string."&sort=";
  if ($sort == "2a") { echo "2d"; } else { echo "2a"; }
  echo "&search_results=".$search_results;
  ?>"><b><? echo $amc_field[1][2]; ?></b></a></font></div>
  </td>

  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>"><a href="<? 
  echo $header_string."&sort=";
  if ($sort == "3a") { echo "3d"; } else { echo "3a"; }
  echo "&search_results=".$search_results;
  ?>"><b><? echo $amc_field[1][3]; ?></b></a></font></div>
  </td>

  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>"><a href="<? 
  echo $header_string."&sort=";
  if ($sort == "4a") { echo "4d"; } else { echo "4a"; }
  echo "&search_results=".$search_results;
  ?>"><b><? echo $amc_field[1][4]; ?></b></a></font></div>
  </td>

  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>"><a href="<? 
//Instructions
  echo $header_string."&sort=";
  if ($sort == "5a") { echo "5d"; } else { echo "5a"; }
  echo "&search_results=".$search_results;
  ?>"><b><? echo $amc_field[1][5]; ?></b></a></font></div>
  </td>

  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>">
	<b>Sent to</b></font></div>
  </td>

  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>"><a href="<? 
//Response
  echo $header_string."&sort=";
  if ($sort == "6a") { echo "6d"; } else { echo "6a"; }
  echo "&search_results=".$search_results;
  ?>"><b><? echo $amc_field[1][6]; ?></b></a></font></div>
  </td>

  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>"><a href="<? 
//OrderNum
  echo $header_string."&sort=";
  if ($sort == "7a") { echo "7d"; } else { echo "7a"; }
  echo "&search_results=".$search_results;
  ?>"><b><? echo $amc_field[1][7]; ?></b></a></font></div>
  </td>

  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>"><a href="<? 
//RemindDate
  echo $header_string."&sort=";
  if ($sort == "8a") { echo "8d"; } else { echo "8a"; }
  echo "&search_results=".$search_results;
  ?>"><b><? echo $amc_field[1][8]; ?></b></a></font></div>
  </td>

  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>"><a href="<? 
//Status
  echo $header_string."&sort=";
  if ($sort == "9a") { echo "9d"; } else { echo "9a"; }
  echo "&search_results=".$search_results;
  ?>"><b><? echo $amc_field[1][9]; ?></b></a></font></div>
  </td>

  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>">
  <a href="<? 
//CustomerID
  echo $header_string."&sort=";
  if ($sort == "10a") { echo "10d"; } else { echo "10a"; }
  echo "&search_results=".$search_results;
  ?>"><b><? echo $amc_field[1][13]; ?></b></a></font></div>
  </td>

  </tr>

<?
/***********************/
/*   end table header  */
/***********************/
  // Set MySQL row offset
  if ($search_results != 0) { $limit_offset = $search_results; }
   else  { $limit_offset = "0"; }

  // MySQL query strings
  $limit_string = " LIMIT " . $limit_offset . "," . $search_maximum;
  $where_string = "WHERE CustomerID = \"" . $customer_id . "\"";
  if($entry != "")
	  $where_string .= " AND BTN=\"" . $entry . "\"";

   $query_string = "SELECT *" . 
    	" FROM " . $amc_table . " " . $where_string . " " . $search_string . " ORDER by " . $sort_string . $limit_string;

//echo "$query_string<br>$count_string<br>"; // Debug Only
  // Grab result from db
  $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");

    
  while ($row = @mysql_fetch_array($result))
   {
   $field_1 = $row[ $amc_field[2][1] ];
   $field_2 = $row[ $amc_field[2][2] ];
   $field_3 = $row[ $amc_field[2][3] ];
   $field_4 = $row[ $amc_field[2][4] ];
   $field_5 = $row[ $amc_field[2][5] ];
   $field_6 = $row[ $amc_field[2][12] ];  /* out of order */
   $field_7 = $row[ $amc_field[2][6] ];
   $field_8 = $row[ $amc_field[2][7] ];
   $field_9 = $row[ $amc_field[2][8] ];
   $field_10 = $row[ $amc_field[2][9] ];
   $field_11 = $row[ $amc_field[2][13] ];
//   $field_12 = $row[ $amc_field[2][11] ];

   //Set alternate row color
   if ($color_ctr == 1) { $row_color = $row_color_1; $color_ctr = 2; }
    else { $row_color = $row_color_2; $color_ctr = 1; }
    
   if ($edit == 1) { $entry = urlencode($field_1); }
   $link = $field_1;

   $field_1 ="<div class=\"search2\"><font color=\"$text_color\">" . $link . "</font></div>";

   echo "<tr valign=\"top\" bgcolor=\"$row_color\">\n";

   if ($edit == 1) { 
	  $foo = "" + $field_4; 
      echo "<td align=\"center\"><div class=\"search2\"><font size=\"1\" color=\"$text_color\"><small>[ <a href=\"$edit_url?entry=$entry&ReqTime=$foo\">Edit</a> ]</small></font></div></td>\n"; 
//      echo "<td align=\"center\"><div class=\"search2\"><font size=\"1\" color=\"$text_color\"><small>[ <a href=\"$edit_url?entry=$entry&ReqTime=$foo\">Delete</a> ]</small></font></div></td>\n"; 
   }
   echo "<td align=\"center\" ><font size=\"2\" color=\"$text_color\">$field_1</font></td>\n";
   echo "<td align=\"center\" ><font size=\"2\" color=\"$text_color\">$field_2</font></td>\n";
   echo "<td align=\"center\" ><font size=\"2\" color=\"$text_color\">$field_3</font></td>\n";
   echo "<td align=\"center\" ><font size=\"2\" color=\"$text_color\">" . date("m/d/y",$field_4) . "</font></td>\n";
   echo "<td align=\"center\" ><font size=\"2\" color=\"$text_color\">$field_5</font></td>\n";
   echo "<td align=\"center\" ><font size=\"2\" color=\"$text_color\">$field_6</font></td>\n"; 
   echo "<td align=\"center\" ><font size=\"2\" color=\"$text_color\">$field_7</font></td>\n";
   echo "<td align=\"center\" ><font size=\"2\" color=\"$text_color\">$field_8</font></td>\n";
   echo "<td align=\"center\" ><font size=\"2\" color=\"$text_color\">";
	if($field_9 == 0) echo "none";
	elseif($field_9 > 0) echo date("m/d/y",$field_9);
   	else echo "( " . date("m/d/y", -$field_9) . " )";
   echo "</font></td>\n";
   echo "<td align=\"center\" ><font size=\"2\" color=\"$text_color\">$field_10</font></td>\n";

   echo "<td align=\"center\" ><font size=\"2\" color=\"$text_color\">";
   if($field_11 == 0) echo "none";
   else echo date("m/d/y",$field_11);
   echo "</font></td>\n";
//   echo "<td align=\"center\" ><font size=\"2\" color=\"$text_color\">$field_12</font></td>\n";
   echo "</tr>\n\n";
   $ctr1++;
   }
  
  echo "\n</table>";

  // Footer
  if ($search_results > 0) {
    $previous_page_results = $search_results - $search_maximum;
    $previous_page = "<div class=\"search1\"><b><a href=\"$header_string&sort=". $sort ."&search_results=". $previous_page_results . "\">&lt;</a> <a href=\"$header_string&sort=". $sort ."&search_results=". $previous_page_results . "\">Previous</a></b></div>";
    }
  else {
    $previous_page = "<font face=\"Arial\" size=\"2\" color=\"$header_text_color\">< Previous</font>";  
    }
   
  if ($search_results + $search_maximum < $total[0]) {
    $next_page_results = $search_results + $search_maximum;
    $next_page = "<div class=\"search1\"><b><a href=\"$header_string&sort=". $sort ."&search_results=". $next_page_results . "\">Next</a> <a href=\"$header_string&sort=". $sort ."&search_results=". $next_page_results . "\">&gt;</a></b></div>";
    }
  else {
    $next_page = "<font face=\"Arial\" size=\"2\" color=\"$header_text_color\">Next ></font>";    
    }
/************************
  echo "\n\n\n\n<table bgcolor=\"$header_color\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"2\">\n<tr><td bgcolor=\"$header_color\" valign=\"middle\" align=\"left\">";
  echo "<font face=\"Arial\" size=\"2\">$previous_page</font>";
  
  echo "</td>\n<td bgcolor=\"$header_color\" valign=\"middle\" align=\"center\"><font face=\"Arial\" size=\"2\" color=\"$header_text_color\">";
  echo "\n<b>Page <input class=\"search1\" type=\"text\" size=\"2\" maxlength=\"3\" name=\"page\" value=\"$current_page\">";
  echo " of $pages</b></font>";
  echo "\n<input type=\"image\" value=\"Go\" src=\"$go_button\" border=\"0\" name=\"Go\">";
echo "..(only display if more than 1 page)";    
  echo "</td>\n<td bgcolor=\"$header_color\" valign=\"middle\" align=\"right\">";
  echo "<font face=\"Arial\" size=\"2\">$next_page</font>";
  echo "</td>\n</tr></table>\n";
 
  echo "\n\n</form>\n\n";
**********************/
  $ctr1 = $ctr1 - 1;
  $titles_displayed1 = $search_results + 1;
  $titles_displayed2 = $titles_displayed1 + $ctr1 - 1;
/************  
  echo "\n\n<p>\n<font color=\"$text_color\" size=\"-1\">BTN range Displayed: <b>$titles_displayed1</b> - <b>$titles_displayed2</b>. (only display if more than 1 page)";
*************/
  echo "\n<br>Total Request's: <b>$total[0]</b></font>.\n";
}
else
{
?>
  <table bgcolor="#F0F0F0" border="0" cellpadding="10" cellspacing="0" width="100%">
  <tr>
  <td valign="middle" align="center"><i>Sorry, no results were found.</i></td>
  </tr>
  </table>
  </form>
<?
}

} // end of $db_error if statement...
else 
{
 ?>
  <table bgcolor="#F0F0F0" border="0" cellpadding="10" cellspacing="0" width="100%" align="center">
  <tr>
  <td valign="middle" align="center"><i><b>Error:</b> Cannot connect to the database!</i></td>
  </tr>
  </table>
  </form> 
 <?
}

@mysql_close($connect_string);
@mysql_free_result($result);
@mysql_free_result($count_result);

 include($footer_file); 
?>
</body>
<? /* force page NOT to be cached */ ?>
<HEAD>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
</HEAD>

</html>
