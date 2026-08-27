<?php
//
//  patrollerClass  (really "roster" class)
//

 class Patroller {
     public $IDNumber;
     public $ClassificationCode;
     public $LastName;
     public $FirstName;
     public $Spouse;
     public $Address;
     public $City;
     public $State;
     public $ZipCode;
     public $HomePhone;
     public $WorkPhone;
     public $CellPhone;
     public $Pager;
     public $email;
     public $EmergencyCallUp;
     public $Password;
     public $NightSubsitute;
     public $Commitment;
     public $Instructor;
     public $Director;
     public $lastUpdated;
     public $carryOverCredits;
     public $lastCreditUpdate;
     public $canEarnCredits;
     public $creditsEarned;
     public $creditsUsed;
     public $teamLead;
     public $mentoring;
     public $comments;

//-------------------
// init_from_row_query
//-------------------
     function init_from_row_query($row) {

        foreach ($row as $key => $value){
            $this->$key = $value;
        }
//        $this->IDNumber = $row[IDNumber];
//        $this->ClassificationCode = $row[ClassificationCode];
//        $this->LastName = $row[LastName];
//        $this->FirstName = $row[FirstName];
//        $this->Spouse = $row[Spouse];
//        $this->Address = $row[Address];
//        $this->City = $row[City];
//        $this->State = $row[State];
//        $this->ZipCode = $row[ZipCode];
//        $this->HomePhone = $row[HomePhone];
//        $this->WorkPhone = $row[WorkPhone];
//        $this->CellPhone = $row[CellPhone];
//        $this->Pager = $row[Pager];
//        $this->email = $row[email];
//        $this->EmergencyCallUp = $row[EmergencyCallUp];
//        $this->Password = $row[Password];
//        $this->NightSubsitute = $row[NightSubsitute];
//        $this->Commitment = $row[Commitment];
//        $this->Instructor = $row[Instructor];
//        $this->Director = $row[Director];
//        $this->lastUpdated = $row[lastUpdated];
//        $this->carryOverCredits = $row[carryOverCredits];
//        $this->lastCreditUpdate = $row[lastCreditUpdate];
//        $this->canEarnCredits = $row[canEarnCredits];
//        $this->creditsEarned = $row[creditsEarned];
//        $this->creditsUsed = $row[creditsUsed];
//        $this->teamLead = $row[teamLead];
//        $this->mentoring = $row[mentoring];
//        $this->comments = $row[comments];
     }

//-------------------
// getSQLRosterInsert
//-------------------
     // $connect_string is the mysqli link the INSERT will run on; it is used to
     // escape the values. Names like O'Brien used to abort the whole sync.
     function getSQLRosterInsert($table, $connect_string = null) {
        $columns = array(
            "IDNumber", "ClassificationCode", "LastName", "FirstName", "Spouse",
            "Address", "City", "State", "ZipCode", "HomePhone", "WorkPhone",
            "CellPhone", "Pager", "email", "EmergencyCallUp", "Password",
            "NightSubsitute", "Commitment", "Instructor", "Director",
            "lastUpdated", "carryOverCredits", "lastCreditUpdate",
            "canEarnCredits", "creditsEarned", "creditsUsed", "teamLead",
            "mentoring", "comments");

        $names = array();
        $values = array();
        foreach ($columns as $column) {
            $value = isset($this->$column) ? $this->$column : "";
            if ($connect_string) {
                $value = mysqli_real_escape_string($connect_string, $value);
            } else {
                $value = addslashes($value);
            }
            $names[]  = "`" . $column . "`";
            $values[] = "'" . $value . "'";
        }

        return "INSERT INTO `" . $table . "` ("
            . implode(", ", $names) . ") VALUES ("
            . implode(", ", $values) . ");";
     }

 //==========================
 //  getSQLRosterCreate
 //==========================
 function getSQLRosterCreate($table) {
     //#
     //# Table structure for table `roster`
     //#
     return "CREATE TABLE `" . $table . "` (" .
 	" `IDNumber` varchar(6) NOT NULL default ''," .
 	" `ClassificationCode` varchar(4) default NULL," .
 	" `LastName` varchar(24) NOT NULL default ''," .
 	" `FirstName` varchar(24) NOT NULL default ''," .
 	" `Spouse` varchar(24) default NULL," .
 	" `Address` varchar(48) default NULL," .
 	" `City` varchar(32) default NULL," .
 	" `State` varchar(16) default NULL," .
 	" `ZipCode` varchar(10) default NULL," .
 	" `HomePhone` varchar(26) default NULL," .
 	" `WorkPhone` varchar(26) default NULL," .
 	" `CellPhone` varchar(26) default NULL," .
 	" `Pager` varchar(26) default NULL," .
 	" `email` varchar(48) default NULL," .
 	" `EmergencyCallUp` varchar(8) default NULL," .
 	" `Password` varchar(16) NOT NULL default ''," .
 	" `NightSubsitute` varchar(4) default NULL," .
 	" `Commitment` tinyint(4) NOT NULL default '2'," .
 	" `Instructor` tinyint(4) NOT NULL default '0'," .
 	" `Director` varchar(10) default NULL," .
 	" `lastUpdated` date NOT NULL default '2003-01-01'," .
 	" `carryOverCredits` smallint(6) NOT NULL default '0'," .
 	" `lastCreditUpdate` bigint(11) NOT NULL default '0'," .
 	" `canEarnCredits` tinyint(4) NOT NULL default '0'," .
 	" `creditsEarned` smallint(6) NOT NULL default '0'," .
 	" `creditsUsed` smallint(6) NOT NULL default '0'," .
 	" `teamLead` tinyint(4) NOT NULL default '0'," .
 	" `mentoring` tinyint(4) NOT NULL default '0'," .
 	" `comments` text NOT NULL," .   // MySQL 8: TEXT columns cannot have a DEFAULT
 	" PRIMARY KEY  (`IDNumber`)" .
 	" );";
 }

//-------------------
// display
//-------------------
 function display() {
  	echo serialize($this) . "<br>";
  }

 } //end class Patroller
?>