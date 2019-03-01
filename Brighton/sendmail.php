<?php
require("config.php");

$firstName = $_REQUEST['firstName'] ;
$lastName = $_REQUEST['lastName'] ;
$address = $_REQUEST['address'] ;
$city = $_REQUEST['city'] ;
$state = $_REQUEST['state'] ;
$zip = $_REQUEST['zip'] ;
$homeAreaCode = $_REQUEST['homeAreaCode'] ;
$homeNumber = $_REQUEST['homeNumber'] ;
$cellAreaCode = $_REQUEST['cellAreaCode'] ;
$cellNumber = $_REQUEST['cellNumber'] ;
$email = $_REQUEST['email'] ;
$yearsSkied = $_REQUEST['yearsSkied'] ;
$skiExperience = $_REQUEST['skiExperience'] ;

$message = "Name: $firstName $lastName\n" .
    "address: $address\n" .
    "city: $city, $state $zip\n" .
    "home: ($homeAreaCode) $homeNumber\n" .
    "cell: ($cellAreaCode) $cellNumber\n" .
    "email: $email\n" .
    "Years Skied: $yearsSkied\n" .
    "Ski Experience: $skiExperience";

//Steve Gledhill
$successfullySent1 = mail( "steve@gledhills.com", "Ski Patrol volunteer from web site", $message, "From: steve@gledhills.com" );
////dedicated mailbox
//$successfullySent2 = mail( "BrightonNsp@gmail.com", "Ski Patrol volunteer from web site", $message, "From: steve@gledhills.com" );
//
////Andy Peterson
//$successfullySent3 = mail( "andy@nationalequipmentcorp.com", "Ski Patrol volunteer from web site", $message, "From: steve@gledhills.com" );
////Chad D'Alessandro
////mail( "chad.dalessandro@sfdc.com", "Ski Patrol volunteer from web site", $message, "From: steve@gledhills.com" );
//$successfullySent4 = mail( "daless67@icloud.com", "Ski Patrol volunteer from web site", $message, "From: steve@gledhills.com" );
////Roger Rains
////mail( "rogerrains@comcast.net", "Ski Patrol volunteer from web site", $message, "From: steve@gledhills.com" );
////Chad Smith
//$successfullySent5 = mail( "ChadHyrumSmith@gmail.com", "Ski Patrol volunteer from web site", $message, "From: steve@gledhills.com" );

$date = date("Y-m-d h:m:s");
$file = __FILE__;
$level = "info";

$message = "[{$date}] [{$file}] [{$level}] message= [{$message}]".PHP_EOL;
// log to our default location
error_log($message);

if (!$successfullySent1) {
    echo("ERROR, email notification failed, please send your information to 'steve@gledhills.com' (webmaster for this site)");
    return;
}

header("Location: /Brighton/VolunteerSubmit.php?firstName=$firstName&lastName=$lastName");

?>
