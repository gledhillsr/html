<?

$config_file = "config.php";  // Full path and name of the config file
$cur_page="inventory";

require($config_file);

$page_title = "Inventory Management Service - Inventory Database";  // Page title
$mysql_table    = "btn";               		// MySQL table name
$edit_url       = "edit_btn.php";           // URL of the edit.php file

if ($max_results) 
  setCookie("max_results_cookie",$max_results);

if (!$max_results) {
	if($max_results_cookie)
		$max_results = $max_results_cookie;
	else
		$max_results = $max_results_default;
}
setCookie("max_results_cookie",$max_results);

if (!$sort) $sort = $sort_default;
setCookie("sort_default",$sort);

//xx if ($edit == 1) $edit_string = "&edit=1";
if (!$table_bgcolor) $table_bgcolor = "#FFFFFF";

$manager_name = "Line Inventory Manager";
$button_line = "<a class=\"nav3\" href=\"filter_inventory.php\">Filter</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
"<a class=\"nav3\" href=\"amc.php\">Adds, Moves &amp; Changes</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
if($isAdmin)
  $button_line .= "<a class=\"nav3\" href=\"admin.php\">Admin</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$button_line .= "<a class=\"nav3\" href=\"archive.php\">Archive</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
"<a class=\"nav3\" href=\"help_line_inventory.htm\">Help</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
"<a class=\"nav3\" href=\"log_off.php\">Log Off</a>";

include($header_file); 
//echo("user_id=$user_id");

?>
<script language="JavaScript">
<!--
function new_or_add(btn)
{
 if(confirm("BTN="+btn+"\n\nClick OK to add a -NEW- Add, Move or Change\n\nClick CANCEL to -VIEW EXISTING- Add, Move & Changes.")) {
	window.location="edit_amc.php?add=1&entry=" + btn;
 } else {
 	window.location = "amc.php?entry=" + btn;
 }
// return true;
}
-->
</script>
<?

include($box_header_file); 


//echo "($filterDept, $filterLoc, $filterDivs, $filterReview)";

// Max. results per page
if ( strtolower($max_results) == "all") { $search_maximum = 99999; }
else { $search_maximum = $max_results; }
?>

<form method="POST" action="<? echo $REQUEST_URI; ?>">

<?
 
$ctr1 = 1; $color_ctr = 1;

switch ( $sort ) {
//Ascending	(2nd sort key of BTN)
   case  "2a":  $sort_string = $btn_field[2][2] . ", " . $btn_field[2][1]; break;
   case  "3a":  $sort_string = $btn_field[2][3] . ", " . $btn_field[2][1]; break;
   case  "4a":  $sort_string = $btn_field[2][4] . ", " . $btn_field[2][1]; break;
   case  "5a":  $sort_string = $btn_field[2][5] . ", " . $btn_field[2][1]; break;
   case  "6a":  $sort_string = $btn_field[2][6] . ", " . $btn_field[2][1]; break;
   case  "7a":  $sort_string = $btn_field[2][7] . ", " . $btn_field[2][1]; break;
   case  "8a":  $sort_string = $btn_field[2][8] . ", " . $btn_field[2][1]; break;
//   case  "9a":  $sort_string = $btn_field[2][18] . ", " . $btn_field[2][1]; break;
//   case  "10a": $sort_string = $btn_field[2][19] . ", " . $btn_field[2][1]; break;
//Descending (2nd sort key of BTN)
   case "1d":  $sort_string = $btn_field[2][1] . " DESC"; break;
   case "2d":  $sort_string = $btn_field[2][2] . " DESC, " . $btn_field[2][1]; break;
   case "3d":  $sort_string = $btn_field[2][3] . " DESC, " . $btn_field[2][1]; break;
   case "4d":  $sort_string = $btn_field[2][4] . " DESC, " . $btn_field[2][1]; break;
   case "5d":  $sort_string = $btn_field[2][5] . " DESC, " . $btn_field[2][1]; break;   
   case "6d":  $sort_string = $btn_field[2][6] . " DESC, " . $btn_field[2][1]; break;   
   case "7d":  $sort_string = $btn_field[2][7] . " DESC, " . $btn_field[2][1]; break;   
   case "8d":  $sort_string = $btn_field[2][8] . " DESC, " . $btn_field[2][1]; break;   
//   case "9d":  $sort_string = $btn_field[2][18] . " DESC, " . $btn_field[2][1]; break;   
//   case "10d":  $sort_string = $btn_field[2][19] . " DESC, " . $btn_field[2][1]; break;   
//Ascending on Btn key
	case "1a":
     default:  $sort_string = $btn_field[2][1];
}

if ($search != "") { $search_string = " WHERE ". $search_type . " like \"%" . $search . "%\""; }
$search_db = $mysql_table;

// Max. results per page
//if ( strtolower($max_results) == "all") { $search_maximum = 99999; }
// else { $search_maximum = $max_results; }

//$filterDept, $filterLoc, $filterDivs, $filterReview
  $filter_string = "";
  if($filterDept)   $filter_string  = "AND Department =\"" . $filterDept . "\" ";
  if($filterLoc)    $filter_string .= "AND Location =\"" . $filterLoc . "\" ";
  if($filterDivs)   $filter_string .= "AND Division =\"" . $filterDivs . "\" ";
  $dispStr = $filter_string; 
  if($filterReview) {
    $dispStr .= "AND Review =\"" . $filterReview . "\" ";
  	$filter_string .= "AND Review =\"";
	if($filterReview == $review_n) 	$filter_string .= "n";
	if($filterReview == $review_y) 	$filter_string .= "y";

  	$filter_string .= "\" ";
  }
    	

$count_string = "SELECT count(*) FROM " . $search_db . " " . $search_string .
	" WHERE " . $user_field[2][3] . "=\"" . $customer_id . "\"" . $filter_string;
//MySQL query: connect and retrieve DVD count 
$connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or $db_error = 1;
if (!$db_error)
{
$count_result = @mysql_db_query($mysql_db, $count_string) or die ("<p align=\"center\">Error accessing MySQL table!");

$total = @mysql_fetch_array($count_result);
if ($total[0] > 0)
{
//zzz
  $header_string = "$SCRIPT_NAME?search=" . $search . "&search_type=" . $search_type . $edit_string;
  echo "\n</form>\n\n<form method=\"POST\" action=\"" . $header_string . "?sort=" . $sort . "\">\n\n";
  $header_string = "$SCRIPT_NAME?search=" . $search . "&search_type=" . $search_type . "&max_results=" . $max_results . $edit_string;

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

/******************/
/*  limit display */
/******************/
  // Set MySQL row offset
  if ($search_results != 0) { $limit_offset = $search_results; }
   else  { $limit_offset = "0"; }

  // MySQL query strings
  $limit_string = " LIMIT " . $limit_offset . "," . $search_maximum;

    $query_string = "SELECT * " . 
    	" FROM " . $search_db . " " . $search_string . "WHERE " . $user_field[2][3] . "=\"" . $customer_id . "\"" . $filter_string . " ORDER by " . $sort_string . $limit_string;

//echo "$query_string<br><br>$count_string<br><br>"; // Debug Only

  // Grab result from db
  $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");

if ($total[0] > 50 || $dispStr) //more than 50 lines or using a filter string
 {

  echo "<table bgcolor=\"$max_results_background_color\" border=\"0\" width=\"100%\" cellpadding=\"10\" cellspacing=\"0\">";
  if($total[0] > 50) 
  {
   echo "<tr><td valign=\"middle\" align=\"center\">";
   echo "Results per page:";
   echo "<select name=\"max_results\" size=\"1\">";
   if ($max_results == 50) 
     $sel = "selected"; 
   else 
     $sel = "";
   echo " <option $sel  value=\"50\">50</option>";
   if ($max_results == 100) $sel = "selected"; else $sel = "";
   echo " <option $sel value=\"100\">100</option>";
   if ($max_results == 200) $sel = "selected"; else $sel = "";
   echo " <option $sel value=\"200\">200</option>";
   if (strtolower($max_results) == "all") $sel = "selected"; else $sel = "";
   echo " <option $sel value=\"all\">All</option>";
   echo "</select>";
   echo "<input type=\"image\" value=\"Go\" src=\"$go_button\" border=\"0\" name=\"Go\">";
   echo "</font></td></tr>";
 }
 if($dispStr) {
    $foo = substr($dispStr,3);
	echo "<tr><td align=\"center\">";
	echo "<div class=\"search1\"><font face=\"Arial\" size=\"2\" color=\"red\"><b>Limit Display to:&nbsp;&nbsp; $foo</b></font></div>";
	echo "</td></tr>";
 }
   echo "</table>";
   echo "<br>";
}

/******************/
/*  Table Header  */
/******************/
  ?>
  <table width="100%" bgcolor="<? echo $table_bgcolor; ?>" border="0" cellspacing="1" cellpadding="1">
  <tr><?
//xx  if ($edit == 1)
   { ?>
    <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
    <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color; ?>"><b>Edit</b></font></div>
    </td><?
   } 

  ?>
  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>"><a href="<? 
  echo $header_string."&sort=";
  if ($sort == "1a" OR $sort == "") { echo "1d"; } else { echo "1a"; }
  echo "&search_results=".$search_results;
  ?>"><b><? echo $btn_field[1][1]; ?></b></a></font></div>
  </td>

  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>"><a href="<? 
  echo $header_string."&sort=";
  if ($sort == "2a") { echo "2d"; } else { echo "2a"; }
  echo "&search_results=".$search_results;
  ?>"><b><? echo $btn_field[1][2]; ?></b></a></font></div>
  </td>

  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>"><a href="<? 
  echo $header_string."&sort=";
  if ($sort == "3a") { echo "3d"; } else { echo "3a"; }
  echo "&search_results=".$search_results;
  ?>"><b><? echo $btn_field[1][3]; ?></b></a></font></div>
  </td>

  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>"><a href="<? 
  echo $header_string."&sort=";
  if ($sort == "4a") { echo "4d"; } else { echo "4a"; }
  echo "&search_results=".$search_results;
  ?>"><b><? echo $btn_field[1][4]; ?></b></a></font></div>
  </td>

  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>"><a href="<? 
  echo $header_string."&sort=";
  if ($sort == "5a") { echo "5d"; } else { echo "5a"; }
  echo "&search_results=".$search_results;
  ?>"><b><? echo $btn_field[1][5]; ?></b></a></font></div>
  </td>

  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>"><a href="<? 
  echo $header_string."&sort=";
  if ($sort == "6a") { echo "6d"; } else { echo "6a"; }
  echo "&search_results=".$search_results;
  ?>"><b><? echo $btn_field[1][6]; ?></b></a></font></div>
  </td>

  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>"><a href="<? 
  echo $header_string."&sort=";
  if ($sort == "7a") { echo "7d"; } else { echo "7a"; }
  echo "&search_results=".$search_results;
  ?>"><b><? echo $btn_field[1][7]; ?></b></a></font></div>
  </td>

  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>"><a href="<? 
  echo $header_string."&sort=";
  if ($sort == "8a") { echo "8d"; } else { echo "8a"; }
  echo "&search_results=".$search_results;
  ?>"><b><? echo $btn_field[1][8]; ?></b></a></font></div>
  </td>

  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>"><a href="<? 
  echo $header_string."&sort=";
  if ($sort == "9a") { echo "9d"; } else { echo "9a"; }
  echo "&search_results=".$search_results;
  ?>"><b><? echo $btn_field[1][18]; ?></b></a></font></div>
  </td>

  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color ?>"><a href="<? 
  echo $header_string."&sort=";
  if ($sort == "10a") { echo "10d"; } else { echo "10a"; }
  echo "&search_results=".$search_results;
  ?>"><b><? echo $btn_field[1][19]; ?></b></a></font></div>
  </td>
<!--
  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color; ?>"><b>CSR<br>Summary</b></font></div>
  </td>
  <td bgcolor="<? echo $header_color; ?>" valign="top" align="center">
  <div class="search1"><font face="Arial" size="2" color="<? echo $header_text_color; ?>"><b>CSR<br>Detail</b></font></div>
  </td>
-->
  </tr>

  <table bgcolor="#F0F0F0" border="0" cellpadding="10" cellspacing="0" width="100%">
  <tr>
  <td valign="middle" align="center"><i>Sorry, no results were found.</i></td>
  </tr>
  </table>
  </form>
  <table bgcolor="#F0F0F0" border="0" cellpadding="10" cellspacing="0" width="100%" align="center">
  <tr>
  <td valign="middle" align="center"><i><b>Error:</b> Cannot connect to the database!</i></td>
  </tr>
  </table>
  </form> 
<?
//-----------
  $page_total = 0.0;
    
  while ($row = @mysql_fetch_array($result))
   {
   $field_1 = $row[ $btn_field[2][1] ];
   $field_2 = $row[ $btn_field[2][2] ];
   $field_3 = $row[ $btn_field[2][3] ];
   $field_4 = $row[ $btn_field[2][4] ];
   $field_5 = $row[ $btn_field[2][5] ];
   $field_6 = $row[ $btn_field[2][6] ];
   $field_7 = $row[ $btn_field[2][7] ];
   $field_8 = $row[ $btn_field[2][8] ];
//zz   $field_9 = $row[ $btn_field[2][18] ];

   $page_total =  $field_9 + $page_total;

//zz   $field_10 = $row[ $btn_field[2][19] ];
   $field_11 = $row[ $btn_field[2][11] ];

   $prevCost 	= $row[ $btn_field[2][13] ];
   $prevUpdated = $row[ $btn_field[2][14] ];
   //Set alternate row color
   if ($color_ctr == 1) { $row_color = $row_color_1; $color_ctr = 2; }
    else { $row_color = $row_color_2; $color_ctr = 1; }
    
//xx   if ($edit == 1) 
   { 
     $entry = urlencode($field_1); 
   }
   $link = $field_1;

   $field_1 ="<div class=\"search2\"><font color=\"$text_color\">" . $link . "</font></div>";

   echo "<tr valign=\"top\" bgcolor=\"$row_color\">\n";

//xx   if ($edit == 1) 
     { echo "<td align=\"center\">"
       . "<div class=\"search2\"><font size=\"1\" color=\"$text_color\"><small>["
       . "<a name=\"$entry\" href=\"$edit_url?entry=$entry\">Edit</a> ]</small></font></div>"
       . "</td>\n"; 
   }
   echo "<td><font size=\"2\" color=\"$text_color\">$field_1</font></td>\n";
//Last Updated
//   if (strlen($field_2) == 13)
      $jump = date("m/d/y",substr($field_2,0,10));  //window's numbers include millis (last 3 digets)
//   else
//      $jump = date("m/d/y" ,$field_8);
//$jump = $field_2;
   echo "<td align=\"center\"><font size=\"2\" color=\"$text_color\">$jump</font></td>\n";
   echo "<td align=\"center\" ><font size=\"2\" color=\"$text_color\">$field_3</font></td>\n";
   echo "<td align=\"center\" ><font size=\"2\" color=\"$text_color\">$field_4</font></td>\n";
   echo "<td align=\"center\" ><font size=\"2\" color=\"$text_color\">$field_5</font></td>\n";
//add move change
   $jump = "";
   if(strlen($field_6) == 12) {
      $tmp1 = substr($field_6,0,3);
      $tmp2 = substr($field_6,3,3);
      $tmp3 = substr($field_6,6,3);
      if(strcmp($tmp1,"000"))
//         $jump = "<button src=\"$red_dot\" name=\"b12\" onclick=\"new_or_add($link)\">";
         $jump = "<button style=\"border:0\" onclick=\"new_or_add('$link')\"><IMG CLASS=navbar src=\"$red_dot\"></button>";
//         $jump = "<a href = \"amc.php?entry=$link\"><IMG CLASS=navbar src=\"$red_dot\"></a>";
      elseif(strcmp($tmp2,"000"))
         $jump = "<button style=\"border:0\" onclick=\"new_or_add('$link')\"><IMG CLASS=navbar src=\"$yellow_dot\"></button>";
//         $jump = "<a href = \"amc.php?entry=$link\"><IMG CLASS=navbar src=\"$yellow_dot\"></a>";
      elseif(strcmp($tmp3,"000"))
         $jump = "<button style=\"border:0\" onclick=\"new_or_add('$link')\"><IMG CLASS=navbar src=\"$green_dot\"></button>";
//         $jump = "<a href = \"amc.php?entry=$link\"><IMG CLASS=navbar src=\"$green_dot\"></a>";
	  if($jump != "")
	    echo "<td align=\"center\" ><font size=\"2\" color=\"$text_color\">$jump</font></td>\n";
   }
   if ($jump == "") {
     echo "<td align=\"center\">"
       . "<div class=\"search2\"><font size=\"1\" color=\"$text_color\"><small>["
       . "<a href=\"edit_amc.php?add=1&entry=$entry\">Add</a> ]</small></font></div>"
       . "</td>\n"; 
   }

   echo "<td align=\"center\" ><font size=\"2\" color=\"$text_color\">$field_7</font></td>\n";

// Contract Exp Date
   if (strlen($field_8) == 13)
      $jump = date("m/d/y" , substr($field_8,0,10));  //window's numbers include millis (last 3 digets)
   elseif(strlen($field_8) == 10)
      $jump = date("m/d/y" ,$field_8);
   else
      $jump = "";
   echo "<td align=\"center\" ><font size=\"2\" color=\"$text_color\">$jump</font></td>\n";

/**************** Cost Column  ***********/
   $jump = sprintf("%.2f", $field_9);
   if(($prevCost * 1.05) < $field_9 && $prevCost > 0)
	   $hilightColor = "bgcolor=#A6BBEC";
	else
	   $hilightColor = "";
   echo "<td align=\"right\" $hilightColor><font size=\"2\" color=\"$text_color\">\$$jump</font></td>\n";
/*** end ***/

/******** Review Column (check against a "y")  *********/
	$summary="$edit_url?entry=$entry";
   if(!strcasecmp ($field_10,"y"))
      $jump = "<a href = \"$summary\"><IMG CLASS=navbar src=\"$check_mark1\"></a>";
   elseif(!strcasecmp ($field_10,"n"))
      $jump = "<a href = \"$summary\"><IMG CLASS=navbar src=\"$check_mark2\"></a>";
   elseif(!strcasecmp ($field_10,"t"))
      $jump = "<a href = \"$summary\"><IMG CLASS=navbar src=\"$check_mark3\"></a>";
   else
      $jump = "";

   $summary="$field_11" . "/BTN/html/" . "$link" . "_totals.html";
   echo "<td align=\"center\" ><font size=\"2\" color=\"$text_color\">$jump</font></td>\n";
/*** end ***/

/********* view CSR Summary **********
   $jump = "$summary";
   echo "<td align=\"center\"><div class=\"search2\"><font size=\"1\" color=\"$text_color\"><small>[ <a href=\"$jump\">View</a> ]</small></font></div></td>\n";
*** end ***/
/******** view CSR Detail ***********
   $jump = "$field_11" . "/BTN/html/" . "$link" . "_detail.html";
   echo "<td align=\"center\"><div class=\"search2\"><font size=\"1\" color=\"$text_color\"><small>[ <a href=\"$jump\">View</a> ]</small></font></div></td>\n";
*** end ***/
   echo "</tr>\n";
   $ctr1++;
   }
//******** end loop of BTN's.  Now add a totals line************
//#F7F7EF
$foo = yellow;
   $jump = sprintf("%.2f", $page_total);
   echo "<tr valign=\"top\" bgcolor=\"$foo\">\n";
   echo "<td></td><td></td><td></td><td></td><td></td><td></td><td align=\"center\"><font size=\"2\" color=\"$text_color\">Totals</font></td><td></td><td></td>";
   echo "<td align=\"right\" ><font size=\"2\" color=\"$text_color\">\$$jump</font></td><td></td><td></td><td> </td>\n";
   echo "</tr>";
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
  $ctr1 = $ctr1 - 1;
  $titles_displayed1 = $search_results + 1;
  $titles_displayed2 = $titles_displayed1 + $ctr1 - 1;
  if ($pages > 1) 
  {

    echo "\n\n\n\n<table bgcolor=\"$header_color\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"2\">\n<tr><td bgcolor=\"$header_color\" valign=\"middle\" align=\"left\">";
    echo "<font face=\"Arial\" size=\"2\">$previous_page</font>";
  
    echo "</td>\n<td bgcolor=\"$header_color\" valign=\"middle\" align=\"center\"><font face=\"Arial\" size=\"2\" color=\"$header_text_color\">";
    echo "\n<b>Page <input class=\"search1\" type=\"text\" size=\"2\" maxlength=\"3\" name=\"page\" value=\"$current_page\">";
    echo " of $pages</b></font>";
    echo "\n<input type=\"image\" value=\"Go\" src=\"$go_button\" border=\"0\" name=\"Go\">";
    echo "</td>\n<td bgcolor=\"$header_color\" valign=\"middle\" align=\"right\">";
    echo "<font face=\"Arial\" size=\"2\">$next_page</font>";
    echo "</td>\n</tr></table>\n";
    echo "\n\n<p>\n<font color=\"$text_color\" size=\"-1\">BTN range Displayed: <b>$titles_displayed1</b> - <b>$titles_displayed2</b>.";
  }
  echo "\n<br>Total BTN's: <b>$total[0]</b></font>.\n";
  echo "\n\n</form>\n\n";
//zzzz  
}
else
{
}

?>
//----------
} //zzz
} // zzz
} // zzz
} // zzz
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
