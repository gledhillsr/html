<?
$cur_page="upload_order";
$page_title = "Upload spreadsheet";  // Page title
require_once("config.php");
require("excelparser.php");
require("fileupload-class.php");

//echo "name=($userfile), path=($path)<br>";

/************************/
/* fatal                */
/************************/
	function fatal($msg = '') {
		echo '[Fatal error]';
		if( strlen($msg) > 0 )
			echo ": $msg";
		echo "<br>\nScript terminated<br>\n";
//		if( $f_opened) @fclose($fh);
		exit();
	};
	
/************************/
/* uc2html              */
/************************/
	function uc2html($str) {
		$ret = '';
		for( $i=0; $i<strlen($str)/2; $i++ ) {
			$charcode = ord($str[$i*2])+256*ord($str[$i*2+1]);
			$ret .= '&#'.$charcode;
		}
		return $ret;
	}
/************************/
/* get                  */
/************************/
	function get( $exc, $data )
	{
		switch( $data['type'] )
		{
			// string
		case 0:
			$ind = $data['data'];
			if( $exc->sst[unicode][$ind] )
				return uc2html($exc->sst['data'][$ind]);
			else
				return $exc->sst['data'][$ind];

			// integer
		case 1:
			return (integer) $data['data'];

			// float
		case 2:
			$flot = (float) $data['data'];
//echo "==$flot==<br>";
			if ($flot > -0.000001 && $flot < 0.000001)	//rounding error?
				$flot = 0.0;									//yup, reset it to 0
			return $flot;
//			return (float) $data['data'];
        case 3:
			return gmdate("m-d-Y",$exc->xls2tstamp($data[data]));

		default:
			return '';
		}
	} //end function get

#--------------------------------#
# Variables
#--------------------------------#

// The path to the directory where you want the 
// uploaded files to be saved. This MUST end with a 
// trailing slash unless you use $path = ""; to 
// upload to the current directory. Whatever directory
// you choose, please chmod 777 that directory.

//.	$path = "data/";
//.	$path = ".";
//	$path = "C:/web/brighton/upload/";
//	$path = "C:/tmp/";

// The name of the file field in your form.

//	$upload_file_name = "userfile";

// ACCEPT mode - if you only want to accept
// a certain type of file.
// possible file types that PHP recognizes includes:
//
// OPTIONS INCLUDE:
//  text/plain
//  image/gif
//  image/jpeg
//  image/png
	
	// Accept ONLY gifs's
	#$acceptable_file_types = "image/gifs";
	
	// Accept GIF and JPEG files
	#$acceptable_file_types = "image/gif|image/jpeg|image/pjpeg";
	
	// Accept ALL files
	$acceptable_file_types = "";

// If no extension is supplied, and the browser or PHP
// can not figure out what type of file it is, you can
// add a default extension - like ".jpg" or ".txt"

	$default_extension = "";

// MODE: if your are attempting to upload
// a file with the same name as another file in the
// $path directory
//
// OPTIONS:
//   1 = overwrite mode
//   2 = create new with incremental extention
//   3 = do nothing if exists, highest protection

	$mode = 1;
	
	
#--------------------------------#
# PHP
#--------------------------------#
	if (isset($_REQUEST['submitted'])) {
$page_title = "Voucher Update";  // Page title
include($header_file);

$query_string = "SELECT fileName FROM `order` WHERE `index`=\"$orderIndex\"";
//echo "$query_string<br>"; // Debug only
		
//		$connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
//	$result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result: type)");
//    if ($row = @mysql_fetch_array($result)) {
//		$fileName 		= $row[fileName];
//echo $fileName."<br>";
//    }
//		$fileName = "joanne.xls";		


		/* 
			A simpler way of handling the submitted upload form
			might look like this:
			
			$my_uploader = new uploader('en'); // errors in English
	
			$my_uploader->max_filesize(40000);
			$my_uploader->max_image_size(800, 800);
			$my_uploader->upload('userfile', 'image/gif', '.gif');
			$my_uploader->save_file('uploads/', 2);
			
			if ($my_uploader->error) {
				print($my_uploader->error . "<br><br>\n");
			} else {
				print("Thanks for uploading " . $my_uploader->file['name'] . "<br><br>\n");
			}
		*/
echo "instanceiate uploader('en')<br>\n";
		$my_uploader = new uploader('en'); // errors in English
		// Create a new instance of the class
echo "instanceiate uploader('".$_POST['language']."')<br>\n";
		$my_uploader = new uploader($_POST['language']); // for error messages in french, try: uploader('fr');
		
		// OPTIONAL: set the max filesize of uploadable files in bytes
echo "set max_filesize<br>\n";
		$my_uploader->max_filesize(500000); // 500 KB
		
		// OPTIONAL: if you're uploading images, you can set the max pixel dimensions 
//		$my_uploader->max_image_size(800, 800); // max_image_size($width, $height)
		
$index  = 1;
		// UPLOAD the file
		$acceptable_file_types = "application/vnd.ms-excel"; //if excel is loaded
		$acceptable_file_types .= ":application/octet-stream"; //if open office is loaded
		$acceptable_file_types .= ":application/msexcell"; 	 //if open office is loaded
//		$default_extension = "xls";
$path="/tmp/";
echo "upload_file_name=($userfile), path=($path)<br>";
		if ($my_uploader->upload("userfile", $acceptable_file_types, $default_extension)) {
echo "upload OK<br>";
//			$my_uploader->save_file($path, $dateModified . "-" . $index . "_" , $mode);
//echo "555, err=".$my_uploader->error."<br>\n";
			$my_uploader->save_file($path, "", 1);
//echo "666, err=".$my_uploader->error."<br>\n";
//..echo "error code =(" . $my_uploader->error . ")<br>";
		} else {
//echo "777<br>\n";
//..echo "upload failed<br>";
		}

		if (strcasecmp($password, "patrick")) {
			$my_uploader->error = "Invalid Password";
		}
//echo "888<br>\n";
		if ($my_uploader->error) {
			echo "Error:<br><br>";
			echo $my_uploader->error . "<br><br>\n";
			echo "<br> <input type=\"button\" value=\"Back\" onclick=history.back()>";
			exit;
		
		} else {
			// Successful upload!
//			if($fileName)
//				$fileName .= ";";
			$fileName .= $my_uploader->file['name'];
//			$query_string = "UPDATE `order` SET fileName=\"" . $fileName . "\" WHERE `index`=\"$orderIndex\"";
echo $query_string . "<br>";
//..			$result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result: type)");
			print("<b>" . $my_uploader->file['name'] . "</b>    was successfully uploaded!<br>"); 
			
			// <a href=\"" . $_SERVER['PHP_SELF'] . "\">Try Again</a><br>");
			
			// Print all the array details...
//			print_r($my_uploader->file);
			
			// ...or print the file
			if(stristr($my_uploader->file['type'], "image")) {
//				echo "<img src=\"" . $path . $my_uploader->file['name'] . "\" border=\"0\" alt=\"\">";
			} else {
//				$path="data\\";
//				$path = "C:/web/brighton/upload/";
//				$path = "./";
//				$path = "c:/tmp/";
				$fullFileName = $path . $my_uploader->file['name'];
//				$fullFileName = "VoucherHistory.xls";
echo "fullFileName=$fullFileName<br>";
				$fp = fopen($fullFileName, "r");

				$excel = new ExcelFileParser;
//		        echo "<h2><b>$file</b>  size=" . filesize($fullFileName) . "</h2><br>\n";
				$error_code = $excel->ParseFromFile($fullFileName);
				switch ($error_code) {
					case 0: break;
					case 1: fatal("Can't open file");
					case 2: fatal("File too small to be an Excel file");
					case 3: fatal("Error reading file header");
					case 4: fatal("Error reading file");
					case 5: fatal("This is not an Excel file or file stored in Excel < 5.0");
					case 6: fatal("File corrupted");
					case 7: fatal("No Excel data found in file");
					case 8: fatal("Unsupported file version");

					default:
						fatal("Unknown error");
				}
				$total_worksheets = count($excel->worksheet['name']);
//				echo "sheet count = $total_worksheets <br>\n";
//---------------------
				$patrollerTotals = array();
				$lastNameColumn = -1;
				$firstNameColumn = -1;
				$countColumn = -1;
				for ($i = 0; $i < $total_worksheets; $i++) 
				{
					$subTotal=0;
					$grandTotal = 0;
					$maxRow = $excel->worksheet['data'][$i]['max_row'];
					$maxCol = $excel->worksheet['data'][$i]['max_col'];
echo "<br>WorkSheet Name: <b>" . $excel->worksheet['name'][$i] . "</b>($maxRow, $maxCol)<br>\n";
					// Obtain worksheet data
					$ws = $excel->worksheet['data'][$i];
					$data = $ws['cell'];
					echo "<table>\n";
					echo "<tr>";
					//======display header===========
					for($k = 0; $k <= $maxCol; ++$k) {
						$colHeader = get( $excel, $data[0][$k]);
						$colHeader = strtoupper($colHeader);
						if(!(strpos($colHeader, "LAST") === false)) {
							echo "<td width=100 bgcolor=\"#ccFFcc\">" . $colHeader . "</td>\n";
							$lastNameColumn = $k;
						} else if(!(strpos($colHeader, "FIRST") === false)) {
							echo "<td width=100 bgcolor=\"#ccFFcc\">" . $colHeader . "</td>\n";
							$firstNameColumn = $k;
						} else if(!(strpos($colHeader, "BALFWD") === false)) {
							echo "<td width=100 bgcolor=\"#ccFFcc\">" . $colHeader . "</td>\n";
							$countColumn = $k;
						} else {
							echo "<td width=100 bgcolor=\"#cccccc\">" . $colHeader . "</td>\n";
						}
					}
					echo "</tr>\n" ;
					if($lastNameColumn == -1) {
						echo "<tr>";
						echo "<td colspan=".$maxCol." bgcolor=\"#88ffff\"> Error: did not find column named \"last_name\" or \"last name\"</td>\n";
						echo "</tr>\n" ;
					}
					if($firstNameColumn == -1) {
						echo "<tr>";
						echo "<td colspan=".$maxCol." bgcolor=\"#88ffff\"> Error: did not find column named \"first_name\" or \"first name\"</td>\n";
						echo "</tr>\n" ;
					}
					if($countColumn == -1) {
						echo "<tr>";
						echo "<td colspan=".$maxCol." bgcolor=\"#88ffff\"> Error: did not find column named \"BALFWD ...\"</td>\n";
						echo "</tr>\n" ;
					}
					//===========display data===============
					for($j = 1; $j <= $maxRow; ++$j) {
						$last = get( $excel, $data[$j][0] ); //***** full_name (0 based index)
						$first = get( $excel, $data[$j][1] ); //***** full_name (0 based index)
						$amt = get( $excel, $data[$j][2] );	 //***** sum_extension  (this is the one)
						$patrollerTotals[$last . ", " . $first] += $amt;
						echo "<tr>";
						for($k = 0; $k <= $maxCol; ++$k) {
							echo "<td>" . get( $excel, $data[$j][$k] ) . "</td>\n";
						}
						echo "</tr>\n" ;
					}
					echo "</table>\n";
				}
ksort($patrollerTotals);
reset($patrollerTotals);
while (list($key, $val) = each($patrollerTotals)) {
    echo "$key totals: $val<br>\n";
}
//---------------------
				if ($fp) { fclose($fp); }
			}
 		}
	@mysql_close($connect_string);
	@mysql_free_result($result);
//	if($ots)
//		header("Location: " . "ots.php?index=" . $orderIndex);	/* Redirect browser */ 
//	else
//		header("Location: " . "orderInformation.php?index=" . $orderIndex);	/* Redirect browser */ 
echo "fullFileName=($fullFileName)<br>";
echo "postingDate=($postingDate)<br>";
	echo "<form method=\"POST\" action=\"convert2sql.php\">";
	echo "<input type=\"HIDDEN\" name=\"filename\" value=\"$fullFileName\">";
	echo "<input type=\"HIDDEN\" name=\"postingDate\" value=\"$postingDate\">";
	echo "<br><br><input type=\"submit\" value=\"Update Web Database\">&nbsp;&nbsp;&nbsp;&nbsp;";
    echo "<input type=\"button\" value=\"go to BrightonNSP.org\" onclick=window.location=\"http://www.BrightonNSP.org\">";
	echo "</form>";
	exit;
 	}


									  

#--------------------------------#
# HTML FORM
#--------------------------------#
$page_title = "Voucher Update";  // Page title
include($header_file);
?>
	<form enctype="multipart/form-data" action="<?= $_SERVER['PHP_SELF']; ?>" method="POST">
	<input type="HIDDEN" name="submitted" value="true">
	<input type="HIDDEN" name="orderIndex" value="<? echo $orderIndex; ?>">
	<input type="HIDDEN" name="dateModified" value="<? echo $dateModified; ?>">
	<br>
	Please input voucher Password: <input type=password size=12 name=password><br><br>
		
	Upload this file: <input size=50 name=userfile type="file"><br><br>

	Enter posting date (yyyy-mm-dd), or leave blank for today: <input size=14 name=postingDate>

	<br><br>
		
<!--
		Error Messages:<br>
		<select name="language">
			<option value="en">English</option>
			<option value="fr">French</option>
			<option value="de">German</option>
			<option value="nl">Dutch</option>
			<option value="it">Italian</option>
		</select>
-->
<input type="HIDDEN" name="language" value="en">
		<br><br>
		
		<input type="submit" value="Upload File">&nbsp;&nbsp;&nbsp;&nbsp;
	    <input type="button" value="go to BrightonNSP.org" onclick=window.location="http://www.BrightonNSP.org">&nbsp;&nbsp;

	</form>
	<hr>

<?php
	if (isset($acceptable_file_types) && trim($acceptable_file_types)) {
		print("This form only accepts <b>" . str_replace("|", " or ", $acceptable_file_types) . "</b> files\n");
	}
?>



</body>
</html>
