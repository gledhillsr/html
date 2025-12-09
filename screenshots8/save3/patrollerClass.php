<?php
//
//  patrollerClass
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

        //foreach ($row as $key => $value){
        //    $this->$key = $value;
        //}
        $this->IDNumber = $row[\IDNUMBER];
        $this->ClassificationCode = $row[\CLASSIFICATIONCODE];
        $this->LastName = $row[\LASTNAME];
        $this->FirstName = $row[\FIRSTNAME];
        $this->Spouse = $row[\SPOUSE];
        $this->Address = $row[\ADDRESS];
        $this->City = $row[\CITY];
        $this->State = $row[\STATE];
        $this->ZipCode = $row[\ZIPCODE];
        $this->HomePhone = $row[\HOMEPHONE];
        $this->WorkPhone = $row[\WORKPHONE];
        $this->CellPhone = $row[\CELLPHONE];
        $this->Pager = $row[\PAGER];
        $this->email = $row[\EMAIL];
        $this->EmergencyCallUp = $row[\EMERGENCYCALLUP];
        $this->Password = $row[\PASSWORD];
        $this->NightSubsitute = $row[\NIGHTSUBSITUTE];
        $this->Commitment = $row[\COMMITMENT];
        $this->Instructor = $row[\INSTRUCTOR];
        $this->Director = $row[\DIRECTOR];
        $this->lastUpdated = $row[\LASTUPDATED];
        $this->carryOverCredits = $row[\CARRYOVERCREDITS];
        $this->lastCreditUpdate = $row[\LASTCREDITUPDATE];
        $this->canEarnCredits = $row[\CANEARNCREDITS];
        $this->creditsEarned = $row[\CREDITSEARNED];
        $this->creditsUsed = $row[\CREDITSUSED];
        $this->teamLead = $row[\TEAMLEAD];
        $this->mentoring = $row[\MENTORING];
        $this->comments = $row[\COMMENTS];
     }

//-------------------
// getSQLRosterInsert
//-------------------
     function getSQLRosterInsert($table) {
        return "INSERT INTO `" . $table . "` VALUES ("
            . "'". $this->IDNumber
            . "', '" . $this->ClassificationCode
            . "', '" . $this->LastName
            . "', '" . $this->FirstName
            . "', '" . $this->Spouse
            . "', '" . $this->Address
            . "', '" . $this->City
            . "', '" . $this->State
            . "', '" . $this->ZipCode
            . "', '" . $this->HomePhone
            . "', '" . $this->WorkPhone
            . "', '" . $this->CellPhone
            . "', '" . $this->Pager
            . "', '" . $this->email
            . "', '" . $this->EmergencyCallUp
            . "', '" . $this->Password
            . "', '" . $this->NightSubsitute
            . "', '" . $this->Commitment
            . "', '" . $this->Instructor
            . "', '" . $this->Director
            . "', '" . $this->lastUpdated
            . "', '" . $this->carryOverCredits
            . "', '" . $this->lastCreditUpdate
            . "', '" . $this->canEarnCredits
            . "', '" . $this->creditsEarned
            . "', '" . $this->creditsUsed
            . "', '" . $this->teamLead
            . "', '" . $this->mentoring
            . "', '" . $this->comment
            . "');";
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
 	" `comments` text NOT NULL default ''," .
 	" PRIMARY KEY  (`IDNumber`)" .
 	" ) TYPE=MyISAM;";
 }

//-------------------
// display
//-------------------
     function display() {
 //	echo $this->FirstName . " " . $this->LastName . ": " ;
 //	echo $this->FirstName . " " . $this->LastName . ": ". $this->IDNumber . "<br>";
 	echo serialize($this) . "<br>";
     }

 } //end class Patroller
?>