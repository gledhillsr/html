<?php
/*
category.php - Administer Categories
Copyright (C) 2002, 2003, 2004  Stephen Lawrence

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

// check for valid session 
session_start();
if (!isset($_SESSION['uid']))
{
	header('Location:index.php?redirection=' . urlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']));
	exit;
}
// includes
include('config.php');
$secureurl = new phpsecureurl;
$user_obj = new User($_SESSION['uid'], $GLOBALS['connection'], $GLOBALS['database']);
if(!$user_obj->isAdmin())        
{
    header('Location:' . $secureurl->encode('error.php?ec=4'));
    exit;
}

if(isset($_REQUEST['submit']) and $_REQUEST['submit'] != 'Cancel')
{
    draw_menu($_SESSION['uid']);
}

if(isset($_GET['submit']) && $_GET['submit'] == 'add')
{
    if (!isset($_REQUEST['last_message']))
    {
        $_REQUEST['last_message']='';
    }
    draw_header('Add New Category');
    draw_status_bar('Add New Category', $_REQUEST['last_message']);
    // Check to see if user is admin
    ?>
<center>
<form action="commitchange.php?last_message=<?php $_REQUEST['last_message']; ?>" method="GET" enctype="multipart/form-data">
<table border="0" cellspacing="5" cellpadding="5">
	<tr>
		<td><b>Category</b></td>
		<td colspan="3"><input name="category" type="text"></td>
	</tr>
	<input type="hidden" name="submit" value="Add Category">
	<tr>
		
</table>
<input type="Submit" name="submit" value="Add Category">
</form>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="Submit" name="submit" value="Cancel">
</form>
</center>
<?php
	draw_footer();
}
elseif(isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'delete')
{
        // If demo mode, don't allow them to update the demo account
        if (@$GLOBALS['CONFIG']['demo'] == 'true')
        {
                @draw_status_bar('Delete Category ' ,$_POST['last_message']);
                echo 'Sorry, demo mode only, you can\'t do that';
                draw_footer();
                exit;
        }
	$delete='';
        if (!isset($_REQUEST['last_message']))
        {       
                $_REQUEST['last_message']='';
        }
	draw_header('Category Deletion');
	draw_status_bar('Delete Item', $_REQUEST['last_message']);
	// query to show item
	echo '<center>'; 
	echo '<table border=0>';
	$query = 'SELECT id, name FROM category where id="' . $_REQUEST['item'] . '"';
	$result = mysql_query($query, $GLOBALS['connection']) or die ("Error in query: $query. " . mysql_error());
	while(list($lid, $lname) = mysql_fetch_row($result))
	{
		echo '<tr><td>Id # :</td><td>' . $lid . '</td></tr>';
		echo '<tr><td>Name :</td><td>' . $lname . '</td></tr>';
	}
?>
	<TABLE name="delete_table">
	<form action="commitchange.php" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="id" value="<?php echo $_REQUEST['item']; ?>">
		<tr>
			<td valign="top">Are you sure you want to delete this?</td>
			<td colspan="4" align="center"><input type="Submit" name="deletecategory" value="Yes"></td>
	</form>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?last_message=<?php echo $_REQUEST['last_message']; ?>" method="POST" enctype="multipart/form-data">
		<td colspan="4" align="center"><input type="Submit" name="" value="No, Cancel"></td>
	</form>
		</tr>
	</TABLE>
<?php
	draw_footer();
}
elseif(isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'deletepick')
{
        if (!isset($_REQUEST['last_message']))
        {
                $_REQUEST['last_message']='';
        }
	$deletepick='';
	draw_header('Category Selection');
	draw_status_bar('Choose Item to Delete', $_REQUEST['last_message']);
?>
	<center>
	<table border="0" cellspacing="5" cellpadding="5">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="state" value="<?php echo ($_REQUEST['state']+1); ?>">
			<tr>
				<td><b>Category</b></td>
				<td colspan=3><select name="item">
<?php
	$query = 'SELECT id, name FROM category ORDER BY name';
	$result = mysql_query($query, $GLOBALS['connection']) or die ("Error in query: $query. " . mysql_error());
	while(list($lid, $lname) = mysql_fetch_row($result))
	{
		$str = '<option value="' . $lid . '"';
		$str .= '>' . $lname . '</option>';
		echo $str;
	}
	mysql_free_result ($result);
	$deletepick='';
?>
	</select></td>
	<tr>
		<td></td>
		<td colspan="2" align="center">
		<input type="Submit" name="submit" value="delete">
		<input type="Submit" name="submit" value="Cancel">
		</td>
	</tr>
			</form>
		</table>
		</center>
<?php
	draw_footer();
}
elseif(isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'Show Category')
{
	// query to show item
	draw_header('Category Information');
        if (!isset($_REQUEST['last_message']))
        {
                $_REQUEST['last_message'] = '';
        }
	draw_status_bar('Display Item Information', $_REQUEST['last_message']);
	echo '<center>';
	// Select name
	$query = "SELECT category.name FROM category where category.id='$_REQUEST[item]'";
	$result = mysql_query($query, $GLOBALS['connection']) or die ("Error in query: $query. " . mysql_error());
	echo('<table name="main" cellspacing="15" border="0">');
	list($lcategory) = mysql_fetch_row($result);
	echo '<th>Name</th><th>ID</th>';
	echo '<tr>';
	echo '<td>' . $lcategory . '</td>';	
	echo '<td>' . $_REQUEST['item'] . '</td>';
	echo '</tr>';	
?>
	<form action="admin.php?last_message=<?php echo $_REQUEST['last_message']; ?>" method="POST" enctype="multipart/form-data">
		<tr>
			<td colspan="4" align="center"><input type="Submit" name="" value="Back"></td>
		</tr>
	</form>
	</table>
<?php
	
	draw_footer();
}
elseif(isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'showpick')
{
                if (!isset($_REQUEST['last_message']))
                {
                        $_REQUEST['last_message']='';
                }       
		draw_header('Category Selection');
		draw_status_bar('Choose item to view', $_REQUEST['last_message']);
		$showpick='';
?>
			<center>
			<table border="0" cellspacing="5" cellpadding="5">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>?last_message=<?php echo $_REQUEST['last_message']; ?>" method="POST" enctype="multipart/form-data">
			<input type="hidden" name="state" value="<?php echo ($_REQUEST['state']+1); ?>">
			<tr>
			<td><b>Category</b></td>
			<td colspan="3"><select name="item">
<?php
			$query = 'SELECT id, name FROM category ORDER BY name';
			$result = mysql_query($query, $GLOBALS['connection']) or die ("Error in query: $query. " . mysql_error());
		while(list($lid, $lname) = mysql_fetch_row($result))
		{
				echo '<option value="' . $lid . '">' . $lname . '</option>';
		}
		mysql_free_result ($result);
?>
		</select></td>
		<tr>
		<td></td>
		<td colspan="3" align="center">
		<input type="Submit" name="submit" value="Show Category">
		<input type="Submit" name="submit" value="Cancel">
		</td>
		</tr>
		</form>
		</table>
		</center>
	</body>
</html>
<?php
	draw_footer();
}
elseif(isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'Update')
{
        if (!isset($_REQUEST['last_message']))
        {
                $_REQUEST['last_message']='';
        }
	draw_header('Update Category');
	draw_status_bar('Update Item', $_REQUEST['last_message']);
?>
	<center>
		<table border="0" cellspacing="5" cellpadding="5">
			<tr>
		<form action="commitchange.php?last_message=<?php echo $_REQUEST['last_message']; ?>" method="POST" enctype="multipart/form-data">
<?php
	// query to get a list of users
	$query = "SELECT id, name FROM category where id='$_REQUEST[item]'";
	$result = mysql_query($query, $GLOBALS['connection']) or die ("Error in query: $query. " . mysql_error());
	while(list($lid, $lname) = mysql_fetch_row($result))
	{
		echo '<tr>';
		echo '<td><input type="textbox" name="name" value="' . $lname . '"></td>';
		echo '<td><input type="hidden" name="id" value="' . $lid . '"></td>';
		echo '</tr>';
	}
	mysql_free_result ($result);
?>
		<td>
			<input type="Submit" name="updatecategory" value="Modify Category">
	</form>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?last_message=<?php echo $_REQUEST['last_message']; ?>">
		<input type="Submit" name="submit" value="Cancel">
	</form>
			</td>
		</tr>
	</table>
</center>
<?php
draw_footer();
}
elseif(isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'updatepick')
{
        if (!isset($_REQUEST['last_message']))
        {
                $_REQUEST['last_message']='';
        }
	draw_header('Category Selection');
	draw_status_bar('Modify Item',$_REQUEST['last_message']);
?>
	<center>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="state" value="<?php echo ($_REQUEST['state']+1); ?>">
			<table border="0">
				<tr>
				<td><b>Category to modify:</b></td>
				<td colspan="3"><select name="item">
<?php
	// query to get a list of users
	$query = "SELECT id, name FROM category ORDER BY name";
	$result = mysql_query($query, $GLOBALS['connection']) or die ("Error in query: $query. " . mysql_error());
	while(list($lid, $lname) = mysql_fetch_row($result))
	{
		echo '<option value="' . $lid . '">' . $lname . '</option>';
	}
	mysql_free_result ($result);
?>
		</td>
	</tr>
	<tr>
        <td></td>
        <td colspan="3" align="center">
        <input type="Submit" name="submit" value="Update">
        <input type="Submit" name="submit" value="Cancel">
        </td>
        </tr>
	</form></TD>
	</tr>
	</table>
	</center>
<?php
	draw_footer();
}
elseif (isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'Cancel')
{
		$_REQUEST['last_message']=urlencode('Action canceled');
		header ('Location: admin.php?last_message=' . $_REQUEST['last_message']);
}
?>
