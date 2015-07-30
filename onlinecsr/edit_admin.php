<?

$config_file = "config.php";  // Full path and name of the config file
$user_table    = "user";      // MySQL table name
$cur_page="edit_admin";
$page_title = "Inventory Management Service - Administrator Editor";  // Page title

require($config_file);

if ($edit_flag == "edit")
 $manager_name = "Editing User Information";
else
 $manager_name = "Adding New User";
$button_line = 
"<a class=\"nav3\" href=\"admin.php\">Admin</a>&nbsp;&nbsp;&nbsp;&nbsp;" .
"<a class=\"nav3\" href=\"inventory.php\">Line&nbsp;Inventory</a>&nbsp;&nbsp;&nbsp;&nbsp;" .
"<a class=\"nav3\" href=\"amc.php\">Adds, Moves, &amp; Changes</a>&nbsp;&nbsp;&nbsp;&nbsp;" .
"<a class=\"nav3\" href=\"log_off.php\">Log Off</a>";

if ( (!$entry AND !$add AND !$edit_add) OR ($edit_cancel) )
 {
 header("Location: admin.php");	/* Redirect browser */ 
 exit;
 }
$extra_header_str = "<style>\n"
. " .main{font-size:14pt;color:teal;}\n"
. " .small{font-size:10px;font-family : Verdana;}\n"
. " .input{background-color: #eeeeee;font-family : Verdana;}\n"
. " BODY, TD {font-family : Verdana;font-size:10pt;}\n"
. "</style>\n";

include($header_file); 
include($box_header_file); 


if ($edit_update)
 {
 update_db("edit");
 exit;
 }

if ($edit_delete)
 {
 update_db("delete");
 exit;
 }

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
global $ReqTime, $entry, $user_field, $mysql_host, $mysql_username, $mysql_password, $mysql_db, $user_table;
global $phppwd_1, $SCRIPT_NAME, $user_name, $customer_id, $customer_name, $btn_field, $user_id;
global $edit_field4, $edit_field10, $edit_field11, $edit_field12;

//MySQL query: connect and retrieve user count 
$connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");

if ($edit_flag == "edit")
 {
 $query_string = "SELECT *  FROM " . $user_table . " WHERE " . $user_field[2][1] . "=\"" . $entry  . "\" LIMIT 1";

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
  <td align="center"><? echo $user_field[1][1]; ?></td>
  <td>

<?
  if ($edit_flag == "add") {
    echo "<input type=\"text\" size=\"20\" maxlength=\"20\" class=\"input\" name=\"edit_field1\" >";
  } else {
    echo $entry; //just display the login name as a label
	echo "<input type=\"HIDDEN\" name=\"edit_field1\" VALUE=\"<?echo $entry;?>\">";
  }
?>
  </td>
 </tr>

 <tr>
  <td align="center"><? echo $user_field[1][2]; ?><br>(4 Character Minimum)</td>
  <td>

<?
  echo "<input type=\"password\" size=\"20\" maxlength=\"20\" class=\"input\" name=\"edit_field2\" >";
  if ($edit_flag != "add")
    echo "<br>Password can't be viewed, it can only set.\n";
?>
  </td>
 </tr>

 <tr>
  <td align="center">Customer</td>
  <td><? echo "$customer_name"; ?></td>
 </tr>

 <tr>
  <td align="center"><? echo $user_field[1][4]; ?></td>
  <td>
<?
  if ($edit_flag != "add") {
    $tmp =  $user_field[2][4];
    $foo = "value=\"$row[$tmp]\"";
  } else
    $foo = "";
  echo "<input type=\"text\" size=\"20\" maxlength=\"20\" class=\"input\" name=\"edit_field4\" $foo >";
?>
  </td>
 </tr>

 <tr>
  <td align="center"><? echo $user_field[1][5]; ?></td>					 
  <td>
<?
  if ($edit_flag == "add")
	  $foo = "0"; //not an administrator
  else
	  $foo = $row[$user_field[2][5]];
?>
  <select size="1" name="edit_field5" >
    <option<? if($foo == "1") echo " Selected";?>>Yes</option>
    <option<? if(!$foo || $foo == "0") echo " Selected";?>>No</option>
  </select>
  </td>
 </tr>

 <tr>
  <td align="center">Notify About Logins</td>					 
  <td>
<?
  if ($edit_flag == "add")
	  $foo = "0"; //don't notify about logins
  else
	  $foo = $row[$user_field[2][6]];
?>
  <select size="1" name="edit_field6" >
    <option<? if($foo == "1") echo " Selected";?>>Yes</option>
    <option<? if(!$foo || $foo == "0") echo " Selected";?>>No</option>
  </select>
  </td>
 </tr>

 <tr>
  <td align="center">Notify About Changes</td>
  <td>
<?
  if ($edit_flag == "add")
	  $foo = "0"; //don't notify about changes
  else
	  $foo = $row[$user_field[2][7]];
?>
  <select size="1" name="edit_field7" >
    <option<? if($foo == "1") echo " Selected";?>>Yes</option>
    <option<? if(!$foo || $foo == "0") echo " Selected";?>>No</option>
  </select>
  </td>
 </tr> 

 <tr>
  <td align="center"><? echo $user_field[1][8]; ?></td>
  <td><input type="text" size="16" maxlength="16" class="input" name="<? echo "edit_field8" . "\" value=\"" . htmlentities($row[ $user_field[2][8] ]); ?>">
  </td>
 </tr> 

 <tr>
  <td align="center"><? echo $user_field[1][9]; ?></td>
  <td><input type="text" size="32" maxlength="32" class="input" name="<? echo "edit_field9" . "\" value=\"" . htmlentities($row[ $user_field[2][9] ]); ?>">
  </td>
 </tr> 

 <tr>
  <td align="center"><? echo $user_field[1][10]; ?></td>
  <td><input type="text" size="20" maxlength="20" class="input" name="<? echo "edit_field10" . "\" value=\"" . htmlentities($row[ $user_field[2][10] ]); ?>">
  </td>
 </tr> 
 <tr>
  <td align="center"><? echo $user_field[1][11]; ?></td>
  <td><input type="text" size="20" maxlength="20" class="input" name="<? echo "edit_field11" . "\" value=\"" . htmlentities($row[ $user_field[2][11] ]); ?>">
  </td>
 </tr> 

 <tr>
  <td align="center">Access&nbsp;Count</td>
  <td>
  <?
    if ($edit_flag == "add")
	  $edit_field12 = "0"; //no access count yet
    else
	  $edit_field12 = $row[$user_field[2][12]];
   echo $edit_field12; ?>
  </td>
 </tr> 

 <tr>
  <td align="center" class="small">&nbsp</td><td align="center">
<?

if ($edit_flag == "edit")
 {
//  $foo = "" + $ReqTime; 
//echo "---$ReqTime---";
 ?> <input name="edit_update" type="submit" value="Update User"> 
    <input name="edit_delete" type="submit" value="Delete User"> 
 <?
 }
else 
 {
 ?> <input name="edit_add" type="submit" value="Add User"><?
 }

?> <input name="edit_cancel" type="submit" value="Cancel">
  </td>
 </tr>	
</table></td>
</tr></table>
<p>
</form>
<script language="JavaScript">
</script>
</body>
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
global $user_field, $entry, $mysql_host, $mysql_username, $mysql_password, $mysql_db, $user_table;
global $index_url, $SCRIPT_NAME;
global $edit_field1, $edit_field2, $edit_field4, $edit_field5, $edit_field6;
global $edit_field7, $edit_field8, $edit_field9, $edit_field10, $user_name;
global $customer_id, $customer_name, $btn_field, $user_id, $edit_field11, $edit_field12;

 $auth = 1;

$edit_field3 =  $customer_id;

if($edit_field5 == "Yes") $edit_field5 = 1;
else $edit_field5 = 0;

if($edit_field6 == "Yes") $edit_field6 = 1;
else $edit_field6 = 0;

if($edit_field7 == "Yes") $edit_field7 = 1;
else $edit_field7 = 0;

if ($auth == 1)
 {
  $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");

$mod_time = time();

  if ($update_flag == "edit")
   {
   $query_string = "UPDATE " . $user_table . " SET ";
   if(strlen($edit_field2) >= 4)
	  $query_string .= $user_field[2][2] . "=\"" . $edit_field2 . "\", ";
//don't update customer ID   $query_string .=	$user_field[2][3] . "=\"" . $edit_field3 . "\", " . 
   	  $query_string .=	$user_field[2][4] . "=\"" . $edit_field4 . "\", " . 
   		$user_field[2][5] . "=\"" . $edit_field5 . "\", " . 
   		$user_field[2][6] . "=\"" . $edit_field6 . "\", " . 
   		$user_field[2][7] . "=\"" . $edit_field7 . "\", " . 
   		$user_field[2][8] . "=\"" . $edit_field8 . "\", " . 
   		$user_field[2][9] . "=\"" . $edit_field9 . "\", " . 
   		$user_field[2][10] . "=\"" . $edit_field10 . "\", " . 
   		$user_field[2][11] . "=\"" . $edit_field11 .
//don't update count   		 "\", " . $user_field[2][12] . "=\"" . $edit_field12 . 
   		"\" WHERE " . $user_field[2][1] . "=\"" . stripslashes($entry) . "\" LIMIT 1";

   }
  elseif ($update_flag == "delete")
   {
    $query_string = "DELETE FROM " . $user_table . " WHERE " . 
   	$user_field[2][1] . "=\"" . stripslashes($entry) . "\" LIMIT 1";
    if($user_id == $entry) {
       echo "<h3>Error, You can't delete yourself!</h3><br>";
	   $query_string = "";
	}		
   }
  else //add
   {
//check for existing name
 $query_string = "SELECT LoginName FROM user WHERE LoginName=\"" . $edit_field1  . "\" LIMIT 1";
//echo "$query_string<br>"; // Debug only
 $result = @mysql_db_query($mysql_db, $query_string);
 $row = @mysql_fetch_array($result);
 $existingName = $row[LoginName];
 @mysql_free_result($result);
   $edit_field12 = 0; //create with 0 as access count   
   $query_string = "INSERT INTO " . $user_table . " (" . 
   		$user_field[2][1]  . ", " . $user_field[2][2]  . ", " . $user_field[2][3]  . ", " . 
   		$user_field[2][4]  . ", " . $user_field[2][5]  . ", " . $user_field[2][6]  . ", " . 
   		$user_field[2][7]  . ", " . $user_field[2][8]  . ", " . $user_field[2][9]  . ", " . 
   		$user_field[2][10] . ", " . $user_field[2][11] . ", " . $user_field[2][12] .  
   		") VALUES (\"" . 
   		$edit_field1  . "\", \"" . $edit_field2  . "\", \"" . $edit_field3 . "\", \"" . 
   		$edit_field4  . "\", \"" . $edit_field5  . "\", \"" . $edit_field6 . "\", \"" . 
   		$edit_field7  . "\", \"" . $edit_field8  . "\", \"" . $edit_field9 . "\", \"" . 
   		$edit_field10 . "\", \"" . $edit_field11 .  "\", \"" . $edit_field12 . "\")";

   if($existingName && $user_id) {
     echo "<h3>Error, name already exists!  Please try another name.</h3><br>";
     $query_string = "";
   }
   } //end of add's building query string

//echo "<p>--- debugging stuff ---<br>";	//debug only
//echo "user_id = $user_id<br>$query_string<br>"; 				//Debug only
//echo "--- end debugging stuff ---</p>";	//Debug only
   if($user_id && $query_string) {
		$result = mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");
   }
 }
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


   if(!$user_id) {
      echo "<h3>Database can not be modified in Demo mode!</h3><br>";
   }
  elseif ($update_flag == "edit")
   {
   echo "The User has been updated!";
   }
  elseif ($update_flag == "delete")
   {
   echo "The User has been deleted!";
   }
  else
   {
   echo "The User has been added!";
   }

?>
</b>
<form method="POST" action="">
  <p>
  <input type="button" value="Return to Admin Manager" name="B3" onClick=window.location="admin.php">
  <input type="button" value="Add New User" name="B1" onClick=window.location="<? echo $SCRIPT_NAME . "?add=1"; ?>">
  </p>
</form>

</td>
</table>
</center>

<br>
</body>
</html>

<?
if (auth == 1)
 {
  @mysql_close($connect_string);
  @mysql_free_result($result);
 }

}
?>