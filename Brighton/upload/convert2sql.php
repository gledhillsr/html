<?
$cur_page="convert2sql";
$config_file = "config.php";
// Full path and name of the config file
require($config_file);
$page_title = "convert 2 sql";
// Page title
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
      $flot = (float) $data['data'];
      if ($flot > -0.000001 && $flot < 0.000001)  //rounding error?
        $flot = 0.0;
        //yup, reset it to 0
      return $flot;
    case 3:
      return gmdate("m-d-Y",$exc->xls2tstamp($data[data]));

    default:
      return '';
  }
}

//end function get


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
<h2>Brighton Ski Patrol Voucher update</h2><br>

<body bgcolor="#ffffff" marginwidth="0" marginheight="0" topmargin="0" leftmargin="0">
<?

//echo "showExisting=($showExisting)";
if ($showExisting == "" && $handle = opendir('./')) {
  $excel = new ExcelFileParser;
  $processedPatrollers = 0;
  $processedTotals = 0;
    //must find at least 1 valid file for each purge to happen
  if (($fp = fopen($filename,'r')) != false) {
    $fileTime = filemtime($filename);
    if($postingDate && $postingDate != "") {
      $y = substr($postingDate,0,4);
      $m = substr($postingDate,5,2);
      $d = substr($postingDate,8,2);
      echo("year=$y, month=$m, date=$d<br>");
      if($y < 2000 || $y > 2020 || $m < 1 || $m > 12 || $d < 1 || $d > 31) {
        echo ("ERROR. Invalid date, no update done.<br>");
        exit;
      }
        //echo("real fileTime= $fileTime<br>");
      $fileTime = mktime(0,0,0,$m,$d,$y);
        //echo("my fileTime= $fileTime<br>");
      $postingDate = date("l, F j Y  G:i:s",$fileTime);
    }
    else {
      //			$postingDate = date("Y-m-d",$fileTime);
      $postingDate = date("l, F j Y  G:i:s",$fileTime);
    }

    echo "<h4>processing $filename<br> File date = " . $postingDate . "</h4>";
      //$mysql_host="64.32.145.130";	//update gledhills.com
    echo "mysql_host=$mysql_host<br>";
    echo "mysql_username=$mysql_username<br>";
    echo "mysql_password=$mysql_password<br>";
    $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
    echo "connect_string=$connect_string<br>";
    $query_string = "SELECT * FROM roster WHERE 1 ORDER BY LastName";
    $query_string = "SELECT * FROM `roster` WHERE 1";
    echo "query_string=$query_string<br>";
    echo "mysql_db=$mysql_db<br>";
    $result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query ($query_string)");
    echo "result=$result<br>";
      //exit;
    $hashTable = array();
    $patrollerTotals = array();
      //
      //get array of all patrollers and their ID# (put in hastTable)
      //
    while ($row = @mysql_fetch_array($result)) {
      //			$name =	$row[ FirstName ] . " " . $row[ LastName ];
      $name =  $row[ LastName ] . ", " . $row[ FirstName ];
      $name = strtolower($name);
        //echo "name=$name<br>";
      $id   = $row[ IDNumber ];
      $hashTable[$name] = $id;
 echo "hashTable[$name] = $id == [" . $hashTable[$name] . "] <br>";
    }

    $error_code = $excel->ParseFromFile($filename);
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
        //echo "<br>WorkSheet Name: <b>" . $excel->worksheet['name'][$i] . "</b>($maxRow, $maxCol)<br>\n";
        // Obtain worksheet data
      $ws = $excel->worksheet['data'][$i];
        //-----------------------
        //$nameColumn = 1;			//column # where the name is
        //$countColumn = 7;			//column # where the count is (sum_extensions)

        //-----------------------
      echo "i=$i, maxCol=$maxCol <br>\n";
      if ($i==0) {
        //worksheet 0
        $data = $ws['cell'];
        $errorFound = 0;

        $lastNameColumn = -1;
        $firstNameColumn = -1;
        $countColumn = -1;
        for($k = 0; $k <= $maxCol; ++$k) {
          $colHeader = get( $excel, $data[0][$k]);
          $colHeader = strtoupper($colHeader);
          if(!(strpos($colHeader, "LAST") === false)) {
            $lastNameColumn = $k;
          } else if(!(strpos($colHeader, "FIRST") === false)) {
            $firstNameColumn = $k;
          } else if(!(strpos($colHeader, "BALFWD") === false)) {
            $countColumn = $k;
          } else {
            //						echo "<td width=100 bgcolor=\"#cccccc\">" . $colHeader . "</td>\n";
          }
        }


        if($lastNameColumn == -1) {
          echo "error, either \"last_name\" or \"LAST NAME\" column header not found!<br>";
          exit;
        }
        if($firstNameColumn == -1) {
          echo "error, either \"first_name\" or \"FIRST NAME\" column header not found!<br>";
          exit;
        }
        if($countColumn == -1) {
          echo "error, either \"BALFWD ...\" column header not found!<br>";
          exit;
        }

          //
          // loop for each entry
          //
        for($j = 1; $j <= $maxRow; ++$j) {
          $last = get( $excel, $data[$j][$lastNameColumn] );
            //full name
          $first = get( $excel, $data[$j][$firstNameColumn] );
            //full name
          $name = $last . ", " . $first;
          $name = strtolower($name);
          $sum_extensions = get( $excel, $data[$j][$countColumn] );
            // ******** if sign is backward ************
          $sum_extensions = -$sum_extensions;
            //sign is backward
            // *****************************************

          $sum_extensions *= 100;
          echo "hashTable[$name] == [" . $hashTable[$name] . "] <br>";
          echo "name=[$name] &nbsp;&nbsp;&nbsp;&nbsp;credits=[$sum_extensions] &nbsp;&nbsp;&nbsp;&nbsp;id=" . $hashTable[$name] . "<br>";
          $id = 0;
            //if name ends with a "-f", remove it
          if (array_key_exists($name, $hashTable)) {
            $id = $hashTable[$name];
            echo "found $name = $id<br>";
          }
          else {
            echo "NOT found $name<br>";
            //name was not found, try to subsitute spellings
            //old spelling => new spelling
//            $fixes = array(
//              //first names begin with a space
//              ", clay"      => ", clayton",
//              ", curt"     => ", curtis",
//              ", dave"      => ", david",
//              ", doug"      => ", douglas",
//              //							", ed"    		=> ", edward",
//              ", elliot"     => ", elliott",
//              ", jennifer"   => ", jenny",
//              ", greg"     => ", gregory",
//              ", mark"     => ", marc",
//              ", matt"     => ", matthew",
//              ", michael"     => ", mike",
//              ", rob"       => ", robert",
//              ", tim"       => ", timothy",
//              ", jeffrey dee"     => ", jeff",
//              ", walter"     => ", walt");
//            $id = "($name)";
//            foreach ($fixes as $k => $v) {
//              $newName = str_replace($k, $v, $name);
//                //echo "-$name- === -$newName- ($k) ($v)<br>";
//              if (array_key_exists($newName, $hashTable)) {
//                $id = $hashTable[$newName];
//                $name = $newName;
//                break;
//              }
//            }
          }
          $patrollerTotals[$name] += $sum_extensions;
          echo "[$name], id=[$id], count=[$sum_extensions], total=[" . $patrollerTotals[$name] . "] hash[" . $hashTable[$name] . "]<br>";
          $dat = time ();
          if($id[0] == 0) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-------Error, $id not found.---------<br>";
            $errorFound = 1;
          }
          else {
            $query = "UPDATE roster SET creditsEarned=\"" . $patrollerTotals[$name] . "\", lastCreditUpdate=\"{$fileTime}000\" where IDNumber=\"$id\"";
            echo "$query<br>";
              // =0
            $processedPatrollers += 1;
            $processedTotals += $sum_extensions;
              //---------------------------------------------------------------------------------------------------------------
              //echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;query = $query<br>";
              $result = @mysql_db_query($mysql_db, $query) or die ("Invalid query (result)");
            //---------------------------------------------------------------------------------------------------------------
          }
          //	copyNXXToSQL($excel,$data,$j, $mysql_db);
        }
          //end loop for each row
        echo "</table>\n";
      } else if($i == 0 && $maxCol == 8) {
      }
    }
      //end loop of worksheets
    @mysql_close($connect_string);
    @mysql_free_result($result);
  } else {
    //end fopen
    echo "ERROR: file open failed.<br>";
  }
  if ($fp) {
    fclose($fp);
  }
}
//opendir
echo "<b>$processedPatrollers patrollers updated with a total of $processedTotals vouchers outstanding.</b><br>";

require("footer.php");
if($errorFound == 1) {
  echo "<br><br>Listing All patrollers, because the patroller(s) name above was not found.<br>
	Processing of all <b>other</b> patrollers were complete.<br><br>";
  foreach ($hashTable as $k => $v)
  echo "$k<br>";
}
//}
?>
</body>
</html>
