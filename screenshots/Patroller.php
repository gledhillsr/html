<?php
//
//  patrollerClass  (really "roster" class)
//

 class Patroller {
     var	$IDNumber;
     var	$ClassificationCode;
     var	$LastName;
     var	$FirstName;
     var	$Spouse;
     var	$Address;
     var	$City;
     var	$State;
     var	$ZipCode;
     var	$HomePhone;
     var	$WorkPhone;
     var	$CellPhone;
     var	$Pager;
     var	$email;
     var	$EmergencyCallUp;
     var	$Password;
     var	$NightSubsitute;
     var	$Commitment;
     var	$Instructor;
     var	$Director;
     var	$lastUpdated;
     var	$carryOverCredits;
     var	$lastCreditUpdate;
     var	$canEarnCredits;
     var	$creditsEarned;
     var	$creditsUsed;
     var	$teamLead;
     var	$mentoring;
     var	$comments;

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