<?php	
/*
Department_class.php - Department class is an extended class of the abstractive databaseData 
class.  The only difference is that it provides it's own constructor to handle its own 
characteristics.
Copyright (C) 2002, 2003, 2004  Stephen Lawrence, Khoa Nguyen

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

if( !defined('Department_class') )
{
  define('Department_class', 'true', false);
  class Department extends databaseData
  {
	function Department($id, $connection, $database)
	{
		$this->field_name = 'name';
		$this->field_id = 'id';
		$this->result_limit = 1; //there is only 1 department with a certain department_id and department_name
		$this->tablename = $this->TABLE_DEPARTMENT;
		databaseData::databaseData($id, $connection, $database);
	}
  } 
}
?>
