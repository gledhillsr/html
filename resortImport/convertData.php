<?
$cur_page="temp";
$config_file = "config.php";  // Full path and name of the config file
require($config_file);
?>

<HEAD>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
</HEAD>

<body bgcolor="#ffffff" marginwidth="0" marginheight="0" topmargin="0" leftmargin="0">
<?
$ar = array(
"Jan" => "01",
"Feb" => "02",
"Mar" => "03",
"Apr" => "04",
"May" => "05",
"Jun" => "06",
"Jul" => "07",
"Aug" => "08",
"Sep" => "09",
"Oct" => "10",
"Nov" => "11",
"Dec" => "12");
$resort = array(
	0 => "afton" ,
	1 => "brighton",
	2 => "kellycanyon",
	3 => "pebblecreek",
	4 => "sample",
	5 => "soldierhollow",
	6 => "uop");
	$totalCount = 0;
for($i=0; $i <= 6; $i++) {

//	if($i != 1) continue;   //update Brighton ONLY
	
	$mysql_db  = $resort[$i];
	$count = 0;
	$connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
//	$query_string = "SELECT  IDNumber , lastUpdated  FROM roster WHERE IDNumber=192443";
	$query_string = "SELECT * FROM roster WHERE ClassificationCode=\"SR\"";
echo "$query_string<br>";
	echo "<h2>Database: $mysql_db</h2><br>";
	$result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid READ query");
	while ($row = @mysql_fetch_array($result)) {
		$count++;
		$totalCount++;
		$old = $row[lastUpdated];
		$id   = $row[IDNumber];
		$teamLead   = $row[teamLead];
		$name = $row[FirstName] . " " . $row[LastName];
		$vouchers = $row[ carryOverCredits ];
		$credits = $vouchers*2;
echo "<br>$count) $name, teamLead was :$teamLead";
//echo "<br>$count) $name now has $credits credits:";
//		$timeStamp = strtotime($old);
//		$y=substr($old,7);
//		$d=substr($old,4,2);
//		$m=substr($old,0,3);
//		$m = $ar[$m];
//echo "ID= $id,  old=$old, ";
//		$new="$y-$m-$d";
//echo "new= $new<br>";
//		$new = gmdate("Y-m-d",$timeStamp);

		$query_string = "UPDATE roster SET teamLead =\"1\" WHERE IDNumber=\"$id\"";

		if(false) {
			echo "&nbsp;&nbsp;&nbsp;&nbsp;$query_string";
		} else {
			@mysql_db_query($mysql_db, $query_string) or die ("Invalid SET query");
			echo " Done";
		}
	}
	echo "<br><br>Finished processing $count patrollers<br>";
	echo "Finished TOTAL processing $totalCount patrollers<br>";

	@mysql_close($connect_string);
	@mysql_free_result($result);
}
?>
</body>
</html>
