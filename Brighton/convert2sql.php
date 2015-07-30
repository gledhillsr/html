<?
$cur_page="convert2sql";
$config_file = "config.php";  // Full path and name of the config file
require($config_file);
$page_title = "convert 2 sql";  // Page title
include($header_file);
require("excelparser.php");
//require("rates.php");

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


?>
<STYLE>
<!--
body, table, tr, td {font-size: 12px; font-family: Verdana, MS sans serif, Arial, Helvetica, sans-serif}
td.index {font-size: 10px; color: #000000; font-weight: bold}
td.empty {font-size: 10px; color: #000000; font-weight: bold}
td.dt_string {font-size: 10px; color: #000090; font-weight: bold}
td.dt_int {font-size: 10px; color: #909000; font-weight: bold}
td.dt_float {font-size: 10px; color: #007000; font-weight: bold}
td.dt_unknown {font-size: 10px; background-color: #f0d0d0; font-weight: bold}
td.empty {font-size: 10px; background-color: #f0f0f0; font-weight: bold}
-->
</STYLE>
	<title>convert2sql</title>
	<link rel="STYLESHEET" type="text/css" href="800save/includes/styles.css">
</head>

<body bgcolor="#ffffff" marginwidth="0" marginheight="0" topmargin="0" leftmargin="0">
<? 

//echo "showExisting=($showExisting)";
if ($showExisting == "" && $handle = opendir('./')) {
	$excel = new ExcelFileParser;
//must find at least 1 valid file for each purge to happen	
	$purgeNPANXX = false;		//if new NPANXX found, del ALL old napnxx data
	$purgeRatePlans = true;	//if new Rate Plans, delete ALL old Rate Plans (will fail if any exist)

	echo "processing files in directory:&nbsp; " . getcwd() . " <br>";
    echo "Files:---------<br>";
//loop for all files
    while (false !== ($file = readdir($handle))) { 
//test if file
		if (is_file($file)) {
			$found = stristr($file,".xls");
//test is .xls file
			if($found != "") { 
		        echo "<h2><b>$file</b>  size=" . filesize($file) . "</h2><br>\n";
				$error_code = $excel->ParseFromFile($file);
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
//loop for each worksheet
				for ($i = 0; $i < $total_worksheets; $i++) 
				{
					$maxRow = $excel->worksheet['data'][$i]['max_row'];
					$maxCol = $excel->worksheet['data'][$i]['max_col'];
	echo "<br>WorkSheet Name: <b>" . $excel->worksheet['name'][$i] . "</b>($maxRow, $maxCol)<br>\n";
					// Obtain worksheet data
					$ws = $excel->worksheet['data'][$i];

					if ($i==0 && $maxCol == 4) {
					//test is npa-nxx file
						$data = $ws['cell'];
						echo "<table>\n";
							echo "<tr>".
							"<td width=100 bgcolor=\"#cccccc\">" . get( $excel, $data[0][0]) . "</td>".
							"<td width=100 bgcolor=\"#dddddd\">" . get( $excel, $data[0][1]) . "</td>".
							"<td width=100 bgcolor=\"#cccccc\">" . get( $excel, $data[0][2]) . "</td>".
							"<td width=100 bgcolor=\"#dddddd\">" . get( $excel, $data[0][3]) . "</td>".
							"<td width=300 bgcolor=\"#cccccc\">" . get( $excel, $data[0][4]) . "</td>".
							"</tr>\n" ;
						for($j = 1; $j <= $maxRow; ++$j) {
							$aa = get( $excel, $data[$j][0] );
							$bb = get( $excel, $data[$j][1] );
							$cc = get( $excel, $data[$j][2] );
							$dd = get( $excel, $data[$j][3] );
							$ee = get( $excel, $data[$j][4] );
							if($bb != "" && $dd < 0.0)
								$color = " bgcolor=\"#dddddd\"";
							else
								$color = " bgcolor=\"#eeeeee\"";
							echo "<tr $color ><td>$aa</td><td>$bb</td><td align=\"right\">$cc</td><td align=\"right\">$dd</td><td>$ee</td></tr>\n" ;
						//	copyNXXToSQL($excel,$data,$j, $mysql_db);
						}
						echo "</table>\n";
					} else if($i == 0 && $maxCol == 8) {
						$data = $ws['cell'];
						echo "<table>\n";
							echo "<tr>".
							"<td width=100 bgcolor=\"#cccccc\">" . get( $excel, $data[0][0]) . "</td>".
							"<td width=100 bgcolor=\"#dddddd\">" . get( $excel, $data[0][1]) . "</td>".
							"<td width=100 bgcolor=\"#cccccc\">" . get( $excel, $data[0][2]) . "</td>".
							"<td width=100 bgcolor=\"#dddddd\">" . get( $excel, $data[0][3]) . "</td>".
							"<td width=100 bgcolor=\"#cccccc\">" . get( $excel, $data[0][4]) . "</td>".
							"<td width=100 bgcolor=\"#dddddd\">" . get( $excel, $data[0][5]) . "</td>".
							"<td width=100 bgcolor=\"#cccccc\">" . get( $excel, $data[0][6]) . "</td>".
							"<td width=100 bgcolor=\"#dddddd\">" . get( $excel, $data[0][7]) . "</td>".
							"<td width=100 bgcolor=\"#cccccc\">" . get( $excel, $data[0][8]) . "</td>".
							"</tr>\n" ;
						for($j = 1; $j <= $maxRow; ++$j) {
							$aa = get( $excel, $data[$j][0] );
							$bb = get( $excel, $data[$j][1] );
							$cc = get( $excel, $data[$j][2] );
							$dd = get( $excel, $data[$j][3] );
							$ee = get( $excel, $data[$j][4] );
							$ff = get( $excel, $data[$j][5] );
							$gg = get( $excel, $data[$j][6] );
							$hh = get( $excel, $data[$j][7] );
							$ii = get( $excel, $data[$j][8] );
						//	if($bb != "" && $dd < 0.0)
							echo "<tr><td>$aa</td><td>$bb</td><td>$cc</td><td>$dd</td><td>$ee</td><td>$ff</td><td>$gg</td><td>$hh</td><td>$ii</td></tr>\n" ;
						//	copyNXXToSQL($excel,$data,$j, $mysql_db);
						} //END FOR LOOP
						echo "</table>\n";
					}
					if($i == 0 && $maxCol == 14) {
						$data = $ws['cell'];
						echo "<table>\n";
							echo "<tr>".
							"<td width=100 bgcolor=\"#cccccc\">" . get( $excel, $data[0][0]) . "</td>".
							"<td width=100 bgcolor=\"#dddddd\">" . get( $excel, $data[0][1]) . "</td>".
							"<td width=100 bgcolor=\"#cccccc\">" . get( $excel, $data[0][2]) . "</td>".
							"</tr>\n" ;
						for($j = 1; $j <= $maxRow; ++$j) {
							$aa = get( $excel, $data[$j][0] );
							$bb = get( $excel, $data[$j][1] );
							$cc = get( $excel, $data[$j][2] );
						//	if($bb != "" && $dd < 0.0)
							echo "<tr><td>$aa</td><td>$bb</td><td>$cc</td></tr>\n" ;
						//	copyNXXToSQL($excel,$data,$j, $mysql_db);
						}
						echo "</table>\n";
						//header info
						if( get( $excel, $data[0][0] ) == "Place" &&
							get( $excel, $data[0][1] ) == "State" &&
							get( $excel, $data[0][2] ) == "NPANXX" &&
							get( $excel, $data[0][3] ) == "Tier") {
							echo "<b>Processing $maxRow lines of data</b><br>";
							$connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
							//new NPANXX data found, should we purge old data
							if($purgeNPANXX) {
								$purgeNPANXX = false;
//only purge if records exist, otherwise a fatel error occures within MySQL
								$query_string = "SELECT count(NPANXX) as totalCount FROM `npanxx` WHERE 1";
								$result = mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");
								$row = @mysql_fetch_array($result);
								if($row) {
									$cnt = $row[totalCount];
									echo "Prior to Truncating 'npanxx', there existed $cnt records<br>\n";
									if($cnt > 0) {
									   	$query_string = "TRUNCATE TABLE `npanxx` ";
										echo "Purging Rate Data at ". date("H:i:s") . "<br>";
										mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");
										echo "Finished Purging Rate Data at ". date("H:i:s") . "<br>";
									}
								}
							}
							//Loop for each row
							mydebug( "Processing Rate Data at ". date("H:i:s"));
							for($j = 1; $j <= $maxRow; ++$j) {
								copyNXXToSQL($excel,$data,$j, $mysql_db);
							}
							echo "Finished Processing Rate Data at ". date("H:i:s") . "<br>";
							@mysql_close($connect_string);
						} 
//						else
//							echo "<b>Oops, Invalid data on WorkSheet</b><br>\n";
					} else {
						echo "<b>Error, Invalid data on WorkSheet</b><br>\n";
					}
				} //end loop of worksheets
			} //end test if .xls file
		} //end test if file
    }  //end loop for all files
    echo "---------<br>\n";
    closedir($handle); 
} else {
//now do the showExisting
	$rs = copyFromSQL($mysql_host, $mysql_username, $mysql_password, $mysql_db,$stateNames, $showExisting);
//	displaySheet($rs,$stateNames);	//debug??
}
if($showExisting!="" && $showExisting!=1){
  echo "<p align=\"center\"><input type=\"button\" value=\"Home\" name=\"B1\" onClick=window.location=\"welcome.php\"></p>";
  require("footer.php");
}
?>
</body>
</html>
