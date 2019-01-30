<?php
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
mail( "steve@gledhills.com", "Ski Patrol volunteer from web site", $message, "From: steve@gledhills.com" );
//dedicated mailbox
mail( "BrightonNsp@gmail.com", "Ski Patrol volunteer from web site", $message, "From: steve@gledhills.com" );

//Andy Peterson
mail( "andy@nationalequipmentcorp.com", "Ski Patrol volunteer from web site", $message, "From: steve@gledhills.com" );
//Chad D'Alessandro
//mail( "chad.dalessandro@sfdc.com", "Ski Patrol volunteer from web site", $message, "From: steve@gledhills.com" );
mail( "daless67@icloud.com", "Ski Patrol volunteer from web site", $message, "From: steve@gledhills.com" );
//Roger Rains
//mail( "rogerrains@comcast.net", "Ski Patrol volunteer from web site", $message, "From: steve@gledhills.com" );
//Chad Smith
mail( "ChadHyrumSmith@gmail.com", "Ski Patrol volunteer from web site", $message, "From: steve@gledhills.com" );

header( "Location: /Brighton/VolunteerSubmit.php?firstName=$firstName&lastName=$lastName" );

?>
