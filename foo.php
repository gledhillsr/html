<?
$cur_page="Import CSV files into DB";
$config_file = "config.php";  // Full path and name of the config file
require($config_file);
?>

<HEAD>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
</HEAD>

<body>
<?
echo "starting";
	$verbose = true;	//dump 

	$delete = false;
	$insert = true;

$mysql_username = "root";           // MySQL user name
$mysql_password = "XXXXXXX";
$mysql_host     = "localhost";      // MySQL server host name

	$mysql_db  = "MountKato"; //MAKE SURE THIS ONE IS CORRECT, OR ELSE......
	$file = "MountKatoPatrollers.csv";
//=============== set this to 'true' to preform the db operation, set to false for a view only mode
	$modifyDB = false;
//===================
	$idToSkip = "167552"; //usually directors ID

	echo "<h1>DATABASE: $mysql_db</h1>";

	$connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");

$id		= 0;
$class 	= 1;
$last 	= 2;
$first 	= 3;
$spouse = 4;
$addr 	= 5;
$city 	= 6;
$state 	= 7;
$zip 	= 8;
$home 	= 9;
$email 	= 14;
echo "open $file<br>";
	$handle = fopen($file, "r");
	if($handle) {
	  echo "opened file OK<br>";
  	  $cnt = 0;
	  while(($inf = fgetcsv($handle,800)) != null) {
		$cnt++;
		if($cnt <= 1) { //skip header & tom
echo "IDNumber = $inf[$id]<br>";
echo "ClassificationCode = $inf[$class]<br>";
echo "LastName = $inf[$last]<br>";
echo "FirstName = $inf[$first]<br>";
echo "Spouse = $inf[$spouse]<br>";
echo "Address = $inf[$addr]<br>";
echo "City = $inf[$city]<br>";
echo "State = $inf[$state]<br>";
echo "ZipCode = $inf[$zip]<br>";
echo "HomePhone = $inf[$home]<br>";
echo "email = $inf[$email]<br>";
			continue;
		}
		if($idToSkip == $inf[$id]) {
			echo "Skipping patroller ID $idToSkip<br>";
			continue;
		}
		if($inf[$class] == "AS")      $inf[$class] = "SR";
		else if($inf[$class] == "AP") $inf[$class] = "BAS";
		else if($inf[$class] == "AA")  $inf[$class] = "AUX";
		else if($inf[$class] == "AK")  $inf[$class] = "CAN";
		else if($inf[$class] == "C")  $inf[$class] = "CAN";	//what is "C"? set it to a Candidate

		if($verbose) {
			$max = count($inf);
//echo "max=$max<br>";
			for($i=0; $i < $max; ++$i) {
				echo "($i) " . $inf[$i];
				if(($i+1) % 10 == 0) echo "<br>";
			}
			echo "<br>";
		}

		if($insert) {
			$query_string  = "INSERT INTO roster (IDNumber, ClassificationCode, LastName, FirstName, Spouse";
			$query_string .= "Address, City, State, ZipCode, HomePhone, email) ";
			$query_string .= "VALUES (\"$inf[$id]\" ";  	//ID
			$query_string .= ",\"$inf[$class]\" ";			//Class
			$query_string .= ",\"$inf[$last]\" ";
			$query_string .= ",\"$inf[$first]\" ";
			$query_string .= ",\"$inf[$spouse]\" ";
			$query_string .= ",\"$inf[$addr]\" ";
			$query_string .= ",\"$inf[$city]\" ";
			$query_string .= ",\"$inf[$state]\" ";
			$query_string .= ",\"$inf[$zip]\" ";
			$query_string .= ",\"$inf[$home]\" ";
			$query_string .= ",\"$inf[$email]\")";
echo "$query_string<br>";
			if($modifyDB) {
				@mysql_db_query($mysql_db, $query_string) or die ("Invalid INSERT query");
			} else {
				echo "Modifications NOT done, view string only<br>";
			}
		} else if ($delete) {
		}

	  }
	  fclose($handle);
    }else {
	  echo "Open failed<br>";
	}

	if($connect_string)
		@mysql_close($connect_string);
	if($result)
		@mysql_free_result($result);
?>
</body>
</html>
