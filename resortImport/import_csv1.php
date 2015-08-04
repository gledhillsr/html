<?
$cur_page="Import CSV files into DB";
$config_file = "config.php";
// Full path and name of the config file
require($config_file);
?>

<HEAD>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
</HEAD>

<body>
<?
echo "starting";
$verbose = true;

$delete = false;
$insert = true;

$mysql_username = "root";
// MySQL user name
$mysql_password = "XXXXXXX";
$mysql_host     = "localhost";
// MySQL server host name

$mysql_db  = "PowderRidge";
//MAKE SURE THIS ONE IS CORRECT, OR ELSE......
$file = "PowderRidge.csv";
//=============== set this to 'true' to preform the db operation, set to false for a view only mode
$modifyDB = true;
//===================
$idToSkip = "167552";
//usually directors ID

echo "<h1>DATABASE: $mysql_db</h1>";

$connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
//$id		= 0;
//$class 	= 13;
//$last 	= 2;
//$first 	= 1;
//$addr 	= 7;
//$city 	= 8;
//$state 	= 9;
//$zip 	= 10;
//$home 	= 5;
//$email 	= 4;

$id    = 0;
$first = 1;
$last  = 2;
$addr  = 3;
$class = 4; //hack
$city  = 5;
$state = 6;
$zip   = 7;
$home  = 8;
$work  = 9;
$cell  = 10;
$email = 14;

//$spouse= 4;
echo "open $file<br>";
$handle = fopen($file, "r");
if($handle) {
    echo "opened file OK<br>";
    $cnt = 0;
    while(($inf = fgetcsv($handle,800)) != null) {
        $cnt++;
        $inf[$class] = "AP"; //hack
        $inf[$spouse] = ""; //hack
        if($cnt <= 1) {
            //skip header & tom
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
            echo "WorkPhone = $inf[$work]<br>";
            echo "CellPhone = $inf[$cell]<br>";
            echo "email = $inf[$email]<br>";
            continue;
        }
        if($idToSkip == $inf[$id]) {
            echo "Skipping patroller ID $idToSkip<br>";
            continue;
        }
            //NSP_ID, FIRST, LAST, FULL_NAME, EMAIL, HOME_PHONE, FAX, ADDRESS_1, CITY, STATE_PROVINCE, ZIP,
            //BIRTH_DATE, GENDER, CATEGORY, JOIN_DATE, PAID_THRU, TITLE, PRO, NATL_NUM, YEAR_NATL, LCA_NUM,
            //BASIC_A_YR, BASIC_M_YR, ADV_A_YR, ADV_M_YR, OEC, OEC_REFA, OEC_REFB, OEC_REFC, PES, PHASE_I,
            //ALPTOB_IN_EXP, AVAL_IN_EXP, ADVAV_IN_EXP, MTN_IN_EXP, NST_IN_EXP, OEC_IN_EXP, PE_IN_EXP,
            //PHI_IN_EXP, ALPTOB_IT, AVAL_IT, MTN_IT, NST_IT, OEC_IT, PES_IT, PHASEI_IT, ATII, AVALII,
            //GLOBALII, MTNII, NSTII, OECII, SEC_PAT, SEC_VOL, SEC_YEAR, SEC_CATEGORY, SES, TES, PAT

        if($inf[$class] == "AS")      $inf[$class] = "SR";
        else if($inf[$class] == "AP") $inf[$class] = "BAS";
        else if($inf[$class] == "AA")  $inf[$class] = "AUX";
        else if($inf[$class] == "AK")  $inf[$class] = "CAN";
        else if($inf[$class] == "C")  $inf[$class] = "CAN";
            //what is "C"? set it to a Candidate

        for($i=0; $i < $max; ++$i) {
            $inf[$i] = trim($inf[$i]);
        }
        if($verbose) {
            $max = count($inf);
                //echo "max=$max<br>";
            for($i=0; $i < $max; ++$i) {
                echo "($i) (" . $inf[$i] . ")&nbsp;&nbsp;";
                if(($i+1) % 10 == 0) echo "<br>";
            }
            echo "<br>";
        }
        if($insert) {
            $query_string  = "INSERT INTO roster (IDNumber, ClassificationCode, LastName, FirstName, Spouse, ";
            $query_string .= "Address, City, State, ZipCode, HomePhone, WorkPhone, CellPhone, Pager, Director, EmergencyCallUp, NightSubsitute, email) ";
            $query_string .= "VALUES (\"$inf[$id]\" ";
                //ID
            $query_string .= ",\"$inf[$class]\" ";
                //Class
            $query_string .= ",\"$inf[$last]\" ";
            $query_string .= ",\"$inf[$first]\" ";
            $query_string .= ",\"$inf[$spouse]\" ";
            $query_string .= ",\"$inf[$addr]\" ";
            $query_string .= ",\"$inf[$city]\" ";
            $query_string .= ",\"$inf[$state]\" ";
            $query_string .= ",\"$inf[$zip]\" ";
            $query_string .= ",\"$inf[$home]\" ";
            $query_string .= ",\"$inf[$work]\" ";
            $query_string .= ",\"$inf[$cell]\" ";
            $query_string .= ",\"\" ";
                //Pager
            $query_string .= ",\"no\" ";
                //Director
            $query_string .= ",\"\" ";
                //EmergencyCallUp
            $query_string .= ",\"\" ";
                //NightSubsitute
            $query_string .= ",\"$inf[$email]\")";
            echo "$query_string<br>";
            if($modifyDB) {
                @mysql_db_query($mysql_db, $query_string) or die ("Invalid INSERT query");
                echo "Insert into database OK.";
            } else {
                echo "Modifications NOT done, view string only<br>";
            }
        } else if ($delete) {
            $query_string = "DELETE FROM `roster` WHERE `IDNumber` = '$inf[0]' LIMIT 1 ";
            echo "$query_string<br>";
            if($modifyDB)
                @mysql_db_query($mysql_db, $query_string) or die ("Invalid DELETE query");
            else
            echo "Modifications NOT done, view string only<br>";
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
