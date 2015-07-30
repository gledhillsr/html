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
	$mysql_db  = "brighton";
echo "<h1>RESORT: $mysql_db</h1>";
	$connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");

$handle = fopen("/whitepine.csv", "r");
if($handle) {
	echo "opened file OK<br>";
	$cnt = 0;
	while(($inf = fgetcsv($handle,600)) != null) {
		$cnt++;
		if($cnt <= 1) //skip header & tom
			continue;
//NSP_ID, FIRST, LAST, FULL_NAME, EMAIL, HOME_PHONE, FAX, ADDRESS_1, CITY, STATE_PROVINCE, ZIP, 
//BIRTH_DATE, GENDER, CATEGORY, JOIN_DATE, PAID_THRU, TITLE, PRO, NATL_NUM, YEAR_NATL, LCA_NUM, 
//BASIC_A_YR, BASIC_M_YR, ADV_A_YR, ADV_M_YR, OEC, OEC_REFA, OEC_REFB, OEC_REFC, PES, PHASE_I, 
//ALPTOB_IN_EXP, AVAL_IN_EXP, ADVAV_IN_EXP, MTN_IN_EXP, NST_IN_EXP, OEC_IN_EXP, PE_IN_EXP, 
//PHI_IN_EXP, ALPTOB_IT, AVAL_IT, MTN_IT, NST_IT, OEC_IT, PES_IT, PHASEI_IT, ATII, AVALII, 
//GLOBALII, MTNII, NSTII, OECII, SEC_PAT, SEC_VOL, SEC_YEAR, SEC_CATEGORY, SES, TES, PAT
	$max = count($inf);
		for($i=0; $i < $max; ++$i) {
			if($i == 13) {
				if($inf[13] == "AS")      $inf[13] = "SR";
				else if($inf[13] == "AP") $inf[13] = "BAS";
				else if($inf[13] == "C")  $inf[13] = "CAN";	//???
			}
			echo "($i) " . $inf[$i];
			if(($i+1) % 10 == 0) echo "<br>";
		}
		echo "<br>";
/*
$query_string  = "INSERT INTO roster (IDNumber, ClassificationCode, LastName, FirstName, ";
$query_string .= "Address, City, State, ZipCode, HomePhone, email) ";

$query_string .= "VALUES (\"$inf[0]\" ";
$query_string .= ",\"$inf[13]\" ";
$query_string .= ",\"$inf[2]\" ";
$query_string .= ",\"$inf[1]\" ";
$query_string .= ",\"$inf[7]\" ";
$query_string .= ",\"$inf[8]\" ";
$query_string .= ",\"$inf[9]\" ";
$query_string .= ",\"$inf[10]\" ";
$query_string .= ",\"$inf[5]\" ";
$query_string .= ",\"$inf[4]\")";
*/
$query_string = "DELETE FROM roster WHERE \"IDNumber\"=\"$inf[0]\""; 

echo "$query_string<br>";
@mysql_db_query($mysql_db, $query_string) or die ("Invalid INSERT query");
	}
	fclose($handle);
}else
	echo "Open failed<br>";
/*
	$query_string = "SELECT  IDNumber , lastUpdated  FROM roster WHERE 1";
	echo "<h2>Database: $mysql_db</h2><br>";
	$result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid READ query");
	while ($row = @mysql_fetch_array($result)) {
		$old = $row[lastUpdated];
		$id   = $row[IDNumber];
		$timeStamp = strtotime($old);
$y=substr($old,7);
$d=substr($old,4,2);
$m=substr($old,0,3);
$m = $ar[$m];
echo "ID= $id,  old=$old, ";
$new="$y-$m-$d";
echo "new= $new<br>";
//		$new = gmdate("Y-m-d",$timeStamp);
		$query_string = "UPDATE roster SET lastUpdated=\"$new\" WHERE IDNumber=\"$id\"";
//		@mysql_db_query($mysql_db, $query_string) or die ("Invalid SET query");
	}
	echo "Done<br>";
*/
	@mysql_close($connect_string);
	@mysql_free_result($result);
?>
</body>
</html>
