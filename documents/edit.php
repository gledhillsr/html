<?php
/*
edit.php - edit file properties
Copyright (C) 2002-2007  Stephen Lawrence, Khoa Nguyen, Jon Miner

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


// check session and $id
//$_SESSION['uid']=102;
//$id=67;
//$submit=true;

session_start();
include('config.php');
include('udf_functions.php');
if(strchr($_REQUEST['id'], '_') )
{
	    header('Location:error.php?ec=20');
}
if (!session_is_registered('uid'))
{
  header('Location:index.php?redirection=' . urlencode( $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] ) );
  exit;
}

if (!isset($_REQUEST['id']) || $_REQUEST['id'] == '')
{
	header('Location:error.php?ec=2');
  	exit;
}

$filedata = new FileData($_REQUEST['id'], $GLOBALS['connection'], $GLOBALS['database']);
if( $filedata->isArchived() ) header('Location:error.php?ec=21');
if (!isset($_REQUEST['last_message']))
{	$_REQUEST['last_message'] = '';	}
if (!isset($_REQUEST['submit']))
// form not yet submitted, display initial form
{
	draw_header('File Properties Modification');
	draw_menu($_SESSION['uid']);
	draw_status_bar('Edit Document Properties', $_REQUEST['last_message']);
	$user_perm_obj = new User_Perms($_SESSION['uid'], $GLOBALS['connection'], $GLOBALS['database']);
	checkUserPermission($_REQUEST['id'], $user_perm_obj->ADMIN_RIGHT);
	$data_id = $_REQUEST['id'];
	// includes
	$query ="SELECT user.department from user where user.id=$_SESSION[uid]";
	//echo($GLOBALS['database']); echo($query); echo($connection);
	$result = mysql_query($query, $GLOBALS['connection']) or die ("Error in query: $query. " . mysql_error());
	if(mysql_num_rows($result) != 1)
	{
	  header('Location:error.php?ec=14');
	  exit; //non-unique error
	}
	list($current_user_dept) = mysql_fetch_row($result);
	$query = "SELECT default_rights from data where data.id = $data_id";
	$result = mysql_query($query, $GLOBALS['connection']) or die ("Error in query: $query. " . mysql_error());
	if(mysql_num_rows($result) != 1)
	{
		header('Location: error.php?ec=14&message=Error locating file id '. $filedata->getId());
		exit;
	}
	list($default_rights) = mysql_fetch_row($result);
?>
	<Script Language="JavaScript">
	 //define a class like structure to hold multiple data
    		function Department(name, id, rights)
    		{
       			this.name = name;
        		this.id = id;
        		this.rights = rights;
        		this.isset_flag = false;
        		if (typeof(_department_prototype_called) == "undefined")
        		{
             			_department_prototype_called = true;
            		 	Department.prototype.getName = getName;
            			Department.prototype.getId = getId;
            			Department.prototype.getRights = getRights;
             			Department.prototype.setName = setName;
            	 		Department.prototype.setId = setId;
             			Department.prototype.setRights = setRights;
             			Department.prototype.issetFlag = issetFlag;
             			Department.prototype.setFlag = setFlag;

        		}
	    		function setFlag(set_boolean)
	    		{	this.isset_flag = set_boolean;	}

       			function getName()
        		{       return this.name;		}

       			function getId()
        		{       return this.id;	                }
			
				function getRights()
				{	return parseInt(this.rights);		}

				function setRights(rights)
        		{       this.rights = parseInt(rights); }

       	 		function setName(name)
        		{       this.name = name;               }

				function setId(id)
            	{       this.id = id;         }

				function issetFlag()
            	{       return this.isset_flag;         }
    		} //end class

	var default_Setting_pos = 0;
	var all_Setting_pos = 1;
	var departments = new Array();
	var default_Setting = new Department("Default Setting for Unset Department", 0, <?php echo $default_rights; ?>);
	var all_Setting = new Department("All", 0, 0);
	departments[all_Setting_pos] = all_Setting;
	departments[default_Setting_pos] = default_Setting;
<?php
	$query = "SELECT name, dept_id, rights FROM department, dept_perms  WHERE department.id = dept_perms.dept_id and dept_perms.fid = $data_id ORDER by name";
	$result = mysql_query ($query, $GLOBALS['connection']) or die ("Error in query: $query. " . mysql_error());
	$dept_data = $result;
	$index = 0;
  	while( list($dept_name, $dept_id, $rights) = mysql_fetch_row($result) )
  	{    
    	echo "\t" . 'departments[' . ($index+2) . '] = new Department("' . $dept_name . '", "' . $dept_id . '", "' . $rights . "\");\n";
  	    $index++;
  	}
  //These are abstractive departments.  There are no discrete info in the database
  echo '</Script>' . "\n";

// open a connection

	
	// query to obtain current properties and rights 
//	$query = "SELECT category, realname, description, comment FROM data WHERE id = '$id' AND status = '0' AND owner = '$_SESSION[uid]'";
//	$result = mysql_query($query, $connection) or die ("Error in query: $query. " . mysql_error());
	$filedata = new FileData($data_id, $GLOBALS['connection'], $GLOBALS['database']);
	// error check
	if( !$filedata->exists() ) 
	{
		header('Location:error.php?ec=2');
		exit;
	}
	else
	{
		// obtain data from resultset
		//list($category, $realname, $description, $comment) = mysql_fetch_row($result);
		//mysql_free_result($result);
		$category = $filedata->getCategory();
		$realname = $filedata->getName();
		$description = $filedata->getDescription();
		$comment = $filedata->getComment();
		$owner_id = $filedata->getOwner();
		// display the form
?>
		<p>
		<center>
		<table border="0" cellspacing="5" cellpadding="5">
		<form name=main action="<?php  echo $_SERVER['PHP_SELF']; ?>" method="POST">
		<input type="hidden" name="id" value="<?php  echo $_REQUEST['id']; ?>">
	
		<tr>
		<td valign="top">Name</td>
		<td colspan="3"><b><?php  echo $realname; ?></b></td>
		</tr>
		<tr>
		<td valign="top">Owner</td>
		<td colspan="3"><b>
		<select name="users">
			<?php  
			$lusers = getAllUsers();
			for($i = 0; $i < sizeof($lusers); $i++)
			{
				if($lusers[$i][0] == $owner_id)
				{	
					echo '<option value="' . $lusers[$i][0] . '" selected>' . $lusers[$i][1] . ' - ' . $lusers[$i][2] . '</option>' . "\n";
				}
				else
				{
					echo '<option value="' . $lusers[$i][0] . '">' . $lusers[$i][1] . ' - ' . $lusers[$i][2] . '</option>' . "\n";
				}
			}
			?>
		</select>
		</b></td>
		</tr>
		<tr>
		<td valign="top">Category</td>
		<td colspan="3"><select name="category">
<?php
		// query for category list
		$query = "SELECT id, name FROM category ORDER BY name";
		$result = mysql_query($query, $GLOBALS['connection']) or die ("Error in query: $query. " . mysql_error());
		while(list($ID, $CATEGORY) = mysql_fetch_row($result))
		{
			$str = '<option value="' . $ID . '"';
			// pre-select current category
			if ($category == $ID) 
			{ 
				$str .= ' selected'; 
			}
			$str .= '>' . $CATEGORY . '</option>';
			echo $str;
		}
		mysql_free_result($result);
?>
		</select></td>
		</tr>
<?php
		udf_edit_file_form();
?>
		<!-- Select Department to own file -->
        <TR>
	    <TD><B>Department</B></TD>
     	<TD COLSPAN="3"><SELECT NAME="dept_drop_box" onChange ="loadDeptData(this.selectedIndex, this.name)">
		<option value="0"> Select a Department</option>
		<option value="1"> Default Setting for Unset Department</option>
		<option value="2"> All Departments</option>
<?php
		// query to get a list of department 
		$query = "SELECT id, name FROM department ORDER BY name";
		$result = mysql_query($query, $GLOBALS['connection']) or die ("Error in query: $query. " . mysql_error());
        //since we want value to corepodant to group id, 2 must be added to compesate for the first two none group related options.
        while(list($dept_id, $name) = mysql_fetch_row($result))
        {
		  //$id+=2;
		  echo '	<option value="' . $dept_id . '" name="' . $name . '">' . $name . '</option> ' . "\n";  
        }
		mysql_free_result ($result);
?>
        </TD></SELECT>
		</TR>
    	<TR>
		<!-- Loading Authority radio_button group -->
		<TD>Authority: </TD> <TD>  	
<?php
      	$query = "SELECT RightId, Description FROM rights order by RightId";
      	$result = mysql_query($query, $GLOBALS['connection']) or die("Error in querry: $query. " . mysql_error());
      	while(list($RightId, $Description) = mysql_fetch_row($result))
      	{
      		echo $Description . ' <input type="radio" name="' . $Description . '" value="' . $RightId . '" onClick="setData(this.name)"> | ' . "\n";
      	}
     
	$query = "SELECT department.name, dept_perms.dept_id, dept_perms.rights FROM dept_perms, department where dept_perms.dept_id = department.id and fid = ".$filedata->getId()." ORDER BY name";
	$result = mysql_query($query, $GLOBALS['connection']) or die ("Error in query: $query. " . mysql_error());
	while( list($dept_name, $dept_id, $rights) = mysql_fetch_row($result) )
	{
	      echo "\n\t" . '<input type="hidden" name="' . space_to_underscore($dept_name) . '" value=' . $rights . '>';
	}
	echo "\n\t" . '<input type="hidden" name="default_Setting" value=' . $default_rights . '>';
?>
	</td>
	</tr>
	<tr>
	<td valign="top">Description</td>
	<td colspan="3"><input type="Text" name="description" size="50" value="<?php  echo str_replace('"', '&quot;', $description); ?>"></td>
	</tr>
	<tr>
	<td valign="top">Comment</td>
	<td colspan="3"><textarea name="comment" rows="4"><?php  echo $comment; ?></textarea></td>
	</tr>
	</table>
	<table border="1" cellspacing="0" cellpadding="3">
	<tr>
	<td valign="top"><b><i>Forbidden</i> rights</b></td>
	<td valign="top"><b><i>View</i> rights</b></td>
	<td valign="top"><b><i>Read</i> rights</b></td>
	<td valign="top"><b><i>Modify</i> rights</b></td>
	<td valign="top"><b><i>Admin</i> rights</b></td>
	</TR>
	<!--/////////////////////////////////////////////////////FORBIDDEN////////////////////////////////////////////-->
	<TR>
<?php 
	$id = $data_id;
	// GET ALL USERS
	$query = "SELECT id from user order by username";
	$result = mysql_query($query, $GLOBALS['connection']) or die ( "Error in query(forbidden): " .$query . mysql_error() );
	$all_users = array();
	for($i = 0; $i<mysql_num_rows($result); $i++)
	{
		list($my_uid) = mysql_fetch_row($result);
		$all_users[$i] = new User($my_uid, $GLOBALS['connection'], $GLOBALS['database']);
	}
	//  LIST ALL FORBIDDEN USERS FOR THIS FILE
	$lquery = "SELECT user_perms.uid FROM user_perms WHERE user_perms.fid = $id AND user_perms.rights=" . $filedata->FORBIDDEN_RIGHT;
	$lresult = mysql_query($lquery) or die('Error in querying:' . $lquery . "\n<BR>" . mysql_error());

	for($i = 0; $i < mysql_num_rows($lresult); $i++ )
	{
		list($user_forbidden_array[$i]) = mysql_fetch_row($lresult);
	}

	$found = false;
	echo '<td><select name="forbidden[]" multiple size=10 onchange="changeForbiddenList(this, this.form);">' . "\n\t";
	for($a = 0; $a<sizeof($all_users); $a++)
	{
		$found = false;
		if(isset($user_forbidden_array))
		{
				for($u = 0; $u<sizeof($user_forbidden_array); $u++)
				{
						if($all_users[$a]->getId() == $user_forbidden_array[$u])
						{
								echo '<option value="' . $all_users[$a]->getId() . '" selected> ' . $all_users[$a]->getName() . '</option>';
								$found = true;
								$u = sizeof($user_forbidden_array);
						}
				}
		}
		if(!$found)
		{
			echo '<option VALUE="' . $all_users[$a]->getId() . '">' . $all_users[$a]->getName() . '</option>';
		}
	}
?>
	</select></td>
	<!--/////////////////////////////////////////////////////VIEW[]////////////////////////////////////////////-->
	<td><select name="view[]" multiple size = 10 onchange="changeList(this, this.form);">
<?php
	$lquery = "SELECT user_perms.uid FROM user_perms WHERE user_perms.fid = $id AND user_perms.rights>=" . $filedata->VIEW_RIGHT;
	$lresult = mysql_query($lquery) or die('Error in querying:' . $lquery . "\n<BR>" . mysql_error());
	for($i = 0; $i < mysql_num_rows($lresult); $i++ )
		list($user_view_array[$i]) = mysql_fetch_row($lresult);
	for($a = 0; $a<sizeof($all_users); $a++)
	{
		$found = false;
		for($u = 0; $u<sizeof($user_view_array); $u++)
		{
			if($all_users[$a]->getId() == $user_view_array[$u])
			{
				echo '<option value="' . $all_users[$a]->getId() . '" selected> ' . $all_users[$a]->getName() . '</option>';
				$found = true;
				$u = sizeof($user_view_array);
			}
		}
		if(!$found)
		{
			echo '<option VALUE="' . $all_users[$a]->getId() . '">' . $all_users[$a]->getName() . '</option>';
		}
	}
?>
	</select></td>

	<!--/////////////////////////////////////////////////////READ[]////////////////////////////////////////////-->
	<td><select name="read[]" multiple size="10" onchange="changeList(this, this.form);">
	<?php 
	$lquery = "SELECT user_perms.uid FROM user_perms WHERE user_perms.fid = $id AND user_perms.rights>=" . $filedata->READ_RIGHT;
	$lresult = mysql_query($lquery) or die('Error in querying:' . $lquery . "\n<BR>" . mysql_error());
	for($i = 0; $i < mysql_num_rows($lresult); $i++ )
		list($user_read_array[$i]) = mysql_fetch_row($lresult);
	for($a = 0; $a<sizeof($all_users); $a++)
	{
		$found = false;
		for($u = 0; $u<sizeof($user_read_array); $u++)
		{
			if($all_users[$a]->getId() == $user_read_array[$u])
			{
				echo '<option value="' . $all_users[$a]->getId() . '" selected> ' . $all_users[$a]->getName() . '</option>';
				$found = true;
				$u = sizeof($user_read_array);
			}
		}
		if(!$found)
		{
			echo '<option VALUE="' . $all_users[$a]->getId() . '">' . $all_users[$a]->getName() . '</option>';
		}
	}
?>
	</select></td>

	<!--/////////////////////////////////////////////////////MODIFY[]////////////////////////////////////////////-->
	<td><select name="modify[]" multiple size = 10 onchange="changeList(this, this.form);">
	<?php 
	$lquery = "SELECT user_perms.uid FROM user_perms WHERE user_perms.fid = $id AND user_perms.rights>=" . $filedata->WRITE_RIGHT;
	$lresult = mysql_query($lquery) or die('Error in querying:' . $lquery . "\n<BR>" . mysql_error());
	for($i = 0; $i < mysql_num_rows($lresult); $i++ )
		list($user_write_array[$i]) = mysql_fetch_row($lresult);
	for($a = 0; $a<sizeof($all_users); $a++)
	{
		$found = false;
		for($u = 0; $u<sizeof($user_write_array); $u++)
		{
			if($all_users[$a]->getId() == $user_write_array[$u])
			{
				echo '<option value="' . $all_users[$a]->getId() . '" selected> ' . $all_users[$a]->getName() . '</option>';
				$found = true;
				$u = sizeof($user_write_array);
			}
		}
		if(!$found)
		{
			echo '<option VALUE="' . $all_users[$a]->getId() . '">' . $all_users[$a]->getName() . '</option>';
		}
	}
	?>
	</select></td>

	<!--/////////////////////////////////////////////////Admin/////////////////////////////////////////////////////-->
	<td><select name="admin[]" multiple size = 10 onchange="changeList(this, this.form);">
	<?php 
	$lquery = "SELECT user_perms.uid FROM user_perms WHERE user_perms.fid = $id AND user_perms.rights>=" . $filedata->ADMIN_RIGHT;
	$lresult = mysql_query($lquery) or die('Error in querying:' . $lquery . "\n<BR>" . mysql_error());
	for($i = 0; $i < mysql_num_rows($lresult); $i++ )
		list($user_admin_array[$i]) = mysql_fetch_row($lresult);
	for($a = 0; $a<sizeof($all_users); $a++)
	{
		$found = false;
		for($u = 0; $u<sizeof($user_admin_array); $u++)
		{
			if($all_users[$a]->getId() == $user_admin_array[$u])
			{
				echo '<option value="' . $all_users[$a]->getId() . '" selected> ' . $all_users[$a]->getName() . '</option>';
				$found = true;
				$u = sizeof($user_admin_array);
			}
		}
		if(!$found)
		{
			echo '<option VALUE="' . $all_users[$a]->getId() . '">' . $all_users[$a]->getName() . '</option>';
		}
	}

?>
	</select></td>
	</tr>
	</table>
	<table>
	<tr>
	
	<td colspan="4" align="center"><input type="Submit" name="submit" value="Update Document Properties"></td>
	<td colspan="4" align="center"><input type="Reset" name="reset" value="Reset" onclick="reload()"></td>
	</tr>
	<table>
	</form>
	</table>
	</center>
	</body>
	</html>
<?php 
	}//end else
}
else
{
	// form submitted, process data
	$filedata = new FileData($_REQUEST['id'], $GLOBALS['connection'], $GLOBALS['database']);
	$filedata->setId($_REQUEST['id']);
	// check submitted data
	// at least one user must have "view" and "modify" rights
	if ( !isset($_REQUEST['view']) or !isset($_REQUEST['modify']) or !isset($_REQUEST['read']) or !isset ($_REQUEST['admin'])) { header("Location:error.php?ec=12"); exit; }
	
	// query to verify
	$query = "SELECT status FROM data WHERE id = '$_REQUEST[id]' and status = '0'";
	$result = mysql_query($query, $GLOBALS['connection']) or die ("Error in query: $query. " . mysql_error());
	if(mysql_num_rows($result) <= 0)
	{
		header('Location:error.php?ec=2'); 
		exit; 
	}
	// update db with new information	
	mysql_escape_string($query = "UPDATE data SET category='" . addslashes($_REQUEST['category']) . "', description='" . addslashes($_REQUEST['description'])."', comment='" . addslashes($_REQUEST['comment'])."', default_rights='" . addslashes($_REQUEST['default_Setting']) . "'  WHERE id = '$_REQUEST[id]'");
	$result = mysql_query($query, $GLOBALS['connection']) or die ("Error in query: $query. " . mysql_error());
	if(isset($_REQUEST['users']))
		mysql_query('UPDATE data set owner="' . $_REQUEST['users'] . '" WHERE id = ' . $_REQUEST['id']) or die(mysql_error());

	udf_edit_file_update();
	
	// clean out old permissions
	$query = "DELETE FROM user_perms WHERE fid = '$_REQUEST[id]'";
	$result = mysql_query($query, $GLOBALS['connection']) or die ("Error in query: $query. " . mysql_error());
	$result_array = array();// init;
	if( isset( $_REQUEST['admin'] ) && isset ($_REQUEST['modify']) )
	{	$result_array = advanceCombineArrays($_REQUEST['admin'], $filedata->ADMIN_RIGHT, $_REQUEST['modify'], $filedata->WRITE_RIGHT);	}
	if( isset( $_REQUEST['read'] ) )
	{	$result_array = advanceCombineArrays($result_array, 'NULL', $_REQUEST['read'], $filedata->READ_RIGHT);	}
	if( isset( $_REQUEST['view'] ) )
	{	$result_array = advanceCombineArrays($result_array, 'NULL', $_REQUEST['view'], $filedata->VIEW_RIGHT);	}
	if( isset( $_REQUEST['forbidden'] ) )
	{	$result_array = advanceCombineArrays($result_array, 'NULL', $_REQUEST['forbidden'], $filedata->FORBIDDEN_RIGHT);	}
	//display_array2D($result_array);
	for($i = 0; $i<sizeof($result_array); $i++)
	{
		$query = "INSERT INTO user_perms (fid, uid, rights) VALUES($_REQUEST[id], '".$result_array[$i][0]."','". $result_array[$i][1]."')";
		//echo $query."<br>";
		$result = mysql_query($query, $GLOBALS['connection']) or die("Error in query: $query" .mysql_error());;
	}
	//UPDATE Department Rights into dept_perms
	$query = "SELECT name, id FROM department ORDER BY name";
	$result = mysql_query($query, $GLOBALS['connection']) or die("Error in query: $query. " . mysql_error() );
	while( list($dept_name, $id) = mysql_fetch_row($result) )
	{
		$string=addslashes(space_to_underscore($dept_name));
		$query = "UPDATE dept_perms SET rights =\"".$_REQUEST[$string]."\" where fid=".$filedata->getId()." and dept_perms.dept_id =$id";
		$result2 = mysql_query($query, $GLOBALS['connection']) or die("Error in query: $query. " . mysql_error() );
	}
	// clean up
	mysql_freeresult($result);
	$message = urlencode('Document successfully updated');
	header('Location: out.php?last_message=' . $message);
}
?>
<SCRIPT LANGUAGE="JavaScript">
	var index = 0;
    var index2 = 0;
	var begin_Authority;
    var end_Authority;
    var frm_main = document.main;
    var dept_drop_box = frm_main.dept_drop_box;
    //Find init position of Authority
    while(frm_main.elements[index].name != "forbidden")
    {       index++;        }
	index2 = index;         //continue the search from index to avoid unnessary iteration
	// Now index contains the position of the view radio button
        //Next search for the position of the admin radio button
    while(frm_main.elements[index2].name != "admin")
    {       index2++;       }
    //Now index2 contains the position of the admin radio button
    //Set the size of the array
    begin_Authority = index;
    end_Authority = index2;

/////////////////////Defining event-handling functions///////////////////////////////////////////////////////

	
	//loadData(_selectedIndex) load department data array
	//loadData(_selectedIndes) will only load data at index=_selectedIndex-1 of the array since
	//since _selectedIndex=0 is the "Please choose a department" option
	//when _selectedIndex=0, all radio button will be cleared.  No department[] will be set
	function loadDeptData(_selectedIndex, dropbox_name)
    {
    	if(_selectedIndex > 0)  //does not load data for option 0
    	{
    		switch(departments[(_selectedIndex-1)].getRights())
			{
            	case -1:
            		frm_main.forbidden.checked = true;
					deselectOthers("forbidden");
					break;
				case 0:
					frm_main.none.checked = true;
					deselectOthers("none");
					break;
				case 1:
                    frm_main.view.checked = true;
					deselectOthers("view");
                    break;
                case 2:
					frm_main.read.checked = true;
					deselectOthers("read");
                    break;
                case 3:
					frm_main.write.checked = true;
                    deselectOthers("write");
                    break;
                case 4:
					frm_main.admin.checked = true;
					deselectOthers("admin");
                break;				
				default: break;
             }
                }
		else
        {
			index = begin_Authority;
            while(index <= end_Authority)
            {
				frm_main.elements[index++].checked = false;
            }
        }
    }
	//return weather or not a department name is a department
	function isDepartment(department_name)
	{
		index = 0;
		while(index < departments.length)
		{
			if(departments[index++].getName() == department_name)
				return true;
		}
		return false;
	}
	function isFormElements(department_name)
	{
		index = 0;
		while(index < frm_main.elements.length)
		{
			index2 = 0;
			while(index2<documents.length)
			{
				if(frm_main.elements[index]==documents[index2++].getName())
					return true;
			}
			index++
		}
		return false;
	}
	//Deselect other button except the button with the name stored in selected_rb_name
	//Design to control the rights radio buttons
	function deselectOthers(selected_rb_name)
    {
		var index = begin_Authority;
    	while(index <= end_Authority)
        {
			if(frm_main.elements[index].name != selected_rb_name)
            {
            	frm_main.elements[index].checked = false;
            }
			index++;
    	}
    }

    function spTo_(string)
    {
        // Joe Jeskiewicz fix
        var pattern = / /g;
        return string.replace(pattern, "_");
    //  return string.replace(" ", "_");
    }

	function setData(selected_rb_name)
	{
		var index = 0;
		var current_selected_dept =  dept_drop_box.selectedIndex - 1;
		var current_dept = departments[current_selected_dept];
		deselectOthers(selected_rb_name);
		//set right into departments
		departments[current_selected_dept].setRights(frm_main.elements[selected_rb_name].value); 
		//Since the All and Defualt department are abstractive departments, hidden fields do not exists for them.
		if(current_selected_dept-2 >= 0) // -1 from above and -2 now will set the first real field being 0
		{
			//set department data into hidden field
			frm_main.elements[spTo_( current_dept.getName() )].value = current_dept.getRights();		
		}
		departments[current_selected_dept].setFlag("true");
		if(  current_selected_dept == default_Setting_pos )  //for default user option
        {
			frm_main.elements['default_Setting'].value = frm_main.elements[selected_rb_name].value;
        	while (index< dept_drop_box.length)
        	{
            	//do not need to set "All Department" and "Default Department"  they are only abstracts
				if(departments[index].issetFlag() == false && index != all_Setting_pos && index != default_Setting_pos)
                {
                	//set right radio buton's value into all Department that is available on the database
					departments[index].setRights(frm_main.elements[selected_rb_name].value); 
					//set right onto hidden valid hidden fields to communicate with php
					frm_main.elements[spTo_(departments[index].getName())].value = frm_main.elements[selected_rb_name].value;
				}
                index++;
            }
			index = 0;
    	}
		if( current_selected_dept == all_Setting_pos) //for all user option. linked with predefine value above.
		{
			index = 0;
			while(index < dept_drop_box.length)
			{
				if(index != default_Setting_pos && index != all_Setting_pos) //Don't set default and All
				{
					//All setting acts like the user actually setting the right for all the department. -->setFlag=true
					departments[index].setFlag(true);
					//Set rights into department array
					departments[index].setRights(frm_main.elements[selected_rb_name].value );
					//Set rights into hidden fields for php
					frm_main.elements[spTo_(departments[index].getName())].value = frm_main.elements[selected_rb_name].value;
				}
				index++;
			}
		} 
				
	}
	function changeList(select_list, current_form)
	{
		var select_list_array = new Array();
		select_list_array[0] = current_form['view[]']; 
		select_list_array[1] = current_form['read[]']; 
		select_list_array[2] = current_form['modify[]'];
		select_list_array[3] = current_form['admin[]'];
		for( var i=0; i < select_list_array.length; i++)
		{
			if(select_list_array[i] == select_list)
			{
				for(var j=0; j< select_list.options.length; j++)
				{
					if(select_list.options[j].selected)
					{
						for(var k=0; k < i; k++)
						{
							select_list_array[k].options[j].selected=true;	
						}//end for
						current_form['forbidden[]'].options[j].selected=false;
					}//end if
					else
					{
						for(var k=i+1; k < select_list_array.length; k++)
						{
							select_list_array[k].options[j].selected=false;
						}
					}//end else
				}//end for	
			}//end if
		}//end for
	}
	function changeForbiddenList(select_list, current_form)
	{
		var select_list_array = new Array();
		select_list_array[0] = current_form['view[]']; 
		select_list_array[1] = current_form['read[]']; 
		select_list_array[2] = current_form['modify[]'];
		select_list_array[3] = current_form['admin[]'];
		for(var i=0; i < select_list.options.length; i++)
		{
			if(select_list.options[i].selected==true)
			{
				for( var j=0; j < select_list_array.length; j++)
				{
					select_list_array[j].options[i].selected=false;	
				}//end for
			}
		} //end for
	}
</SCRIPT>
