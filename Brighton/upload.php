<?
$cur_page="upload_order";
$page_title = "Upload spreadsheet";  // Page title
require_once("config.php");
require("excelparser.php");
require("fileupload-class.php");

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
			return (float) $data['data'];
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

	$path = "\\tmp\\";

// The name of the file field in your form.

	$upload_file_name = "userfile";

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
	#$acceptable_file_types = "";

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

$query_string = "SELECT fileName FROM `order` WHERE `index`=\"$orderIndex\"";
//echo "$query_string<br>"; // Debug only
		
//		$connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
//	$result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result: type)");
//    if ($row = @mysql_fetch_array($result)) {
//		$fileName 		= $row[fileName];
//echo $fileName."<br>";
//    }
		$fileName = "joanne.xls";		


		/* 
			A simpler way of handling the submitted upload form
			might look like this:
			
			$my_uploader = new uploader('en'); // errors in English
	
			$my_uploader->max_filesize(30000);
			$my_uploader->max_image_size(800, 800);
			$my_uploader->upload('userfile', 'image/gif', '.gif');
			$my_uploader->save_file('uploads/', 2);
			
			if ($my_uploader->error) {
				print($my_uploader->error . "<br><br>\n");
			} else {
				print("Thanks for uploading " . $my_uploader->file['name'] . "<br><br>\n");
			}
		*/

		// Create a new instance of the class
		$my_uploader = new uploader($_POST['language']); // for error messages in french, try: uploader('fr');
		
		// OPTIONAL: set the max filesize of uploadable files in bytes
		$my_uploader->max_filesize(1000000); // 1 mb
		
		// OPTIONAL: if you're uploading images, you can set the max pixel dimensions 
		$my_uploader->max_image_size(800, 800); // max_image_size($width, $height)
		
$index  = 1;
		// UPLOAD the file
		$acceptable_file_types = "application/vnd.ms-excel";
//		$default_extension = "xls";
		if ($my_uploader->upload($upload_file_name, $acceptable_file_types, $default_extension)) {
echo "upload OK<br>";
			$my_uploader->save_file($path, $dateModified . "-" . $index . "_" , $mode);
echo "error code =" . $my_uploader->error . "<br>";
		} else {
echo "upload failed<br>";
		}
				
		if ($my_uploader->error) {
			echo $my_uploader->error . "<br><br>\n";
		
		} else {
			// Successful upload!
			if($fileName)
				$fileName .= ";";
			$fileName .= $my_uploader->file['name'];
//			$query_string = "UPDATE `order` SET fileName=\"" . $fileName . "\" WHERE `index`=\"$orderIndex\"";
//echo $query_string . "<br>";
//..			$result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result: type)");
			print("xyz" . $my_uploader->file['name'] . "    was successfully uploaded!<br>"); 
			
			// <a href=\"" . $_SERVER['PHP_SELF'] . "\">Try Again</a><br>");
			
			// Print all the array details...
//			print_r($my_uploader->file);
			
			// ...or print the file
			if(stristr($my_uploader->file['type'], "image")) {
//				echo "<img src=\"" . $path . $my_uploader->file['name'] . "\" border=\"0\" alt=\"\">";
			} else {
				$fp = fopen($path . $my_uploader->file['name'], "r");
echo "dump file contents here, path=$path, file=" . $my_uploader->file['name'] .", fp=$fp<br>";
//				while(!feof($fp)) {
//					$line = fgets($fp, 255);
//					echo $line . "<br>";
//				}
//zzzzz				
				$excel = new ExcelFileParser;
		        echo "<h2><b>$file</b>  size=" . filesize($fp) . "</h2><br>\n";
				$error_code = $excel->ParseFromFile($fp);
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
				echo "sheet count = $total_worksheets <br>\n";

				if ($fp) { fclose($fp); }
			}
 		}
	@mysql_close($connect_string);
	@mysql_free_result($result);
	if($ots)
		header("Location: " . "ots.php?index=" . $orderIndex);	/* Redirect browser */ 
	else
		header("Location: " . "orderInformation.php?index=" . $orderIndex);	/* Redirect browser */ 
	exit;
 	}




#--------------------------------#
# HTML FORM
#--------------------------------#
?>
	<form enctype="multipart/form-data" action="<?= $_SERVER['PHP_SELF']; ?>" method="POST">
	<input type="HIDDEN" name="submitted" value="true">
	<input type="HIDDEN" name="orderIndex" value="<? echo $orderIndex; ?>">
	<input type="HIDDEN" name="dateModified" value="<? echo $dateModified; ?>">

		
		Upload this file:<br>
		<input size=60 name="<?= $upload_file_name; ?>" type="file">

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
		
		<input type="submit" value="Upload File">
	</form>
	<hr>

<?php
	if (isset($acceptable_file_types) && trim($acceptable_file_types)) {
		print("This form only accepts <b>" . str_replace("|", " or ", $acceptable_file_types) . "</b> files\n");
	}
?>



</body>
</html>
