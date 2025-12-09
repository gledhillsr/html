<?php
//
//  AreaDefinition Class
//

 class AreaDefinition {
     var	$areaID;
     var	$area;
     var	$areaFullText;
     var	$open;
     var	$saturdaybasic;
     var	$saturdayaux;
     var	$sundaybasic;
     var	$sundayaux;

//-------------------
// init_from_row_query
//-------------------
function init_from_row_query($row) {

    foreach ($row as $key => $value){
        $this->$key = $value;
    }
//        $this->areaID = $row[areaID];
//        $this->area = $row[area];
//        $this->areaFullText = $row[areaFullText];
//        $this->open = $row[open];
//        $this->saturdaybasic = $row[saturdaybasic];
//        $this->saturdayaux = $row[saturdayaux];
//        $this->sundaybasic = $row[sundaybasic];
//        $this->sundayaux = $row[sundayaux];
}

//-------------------
// getSQLInsert
//-------------------
     function getSQLInsert() {
        return "INSERT INTO `areadefinitions` VALUES ("
            . "'". $this->IDNumber
            . "', '" . $this->areaID
            . "', '" . $this->area
            . "', '" . $this->areaFullText
            . "', '" . $this->open
            . "', '" . $this->saturdaybasic
            . "', '" . $this->saturdayaux
            . "', '" . $this->sundaybasic
            . "', '" . $this->sundayaux
            . "');";
     }

 //==========================
 //  getSQLRosterCreate
 //==========================
    function getSQLCreate() {
     //#
     //# Table structure for table `areadefinitions`
     //#
        return "CREATE TABLE `areadefinitions` (" .
          " `areaID` tinyint(4) NOT NULL default '0'," .
          " `area` varchar(16) NOT NULL default ''," .
          " `areaFullText` varchar(24) NOT NULL default ''," .
          " `open` tinyint(4) NOT NULL default '0'," .
          " `saturdaybasic` tinyint(4) NOT NULL default '0'," .
          " `saturdayaux` tinyint(4) NOT NULL default '0'," .
          " `sundaybasic` tinyint(4) NOT NULL default '0'," .
          " `sundayaux` tinyint(4) NOT NULL default '0'," .
          " PRIMARY KEY  (`areaID`)" .
            " ) ;";
    }

//-------------------
// display
//-------------------
    function display() {
 	    echo serialize($this) . "<br>";
    }

 } //end class areadefinition
?>
