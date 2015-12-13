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


mail( "steve@gledhills.com", "Ski Patrol volunteer from web site", $message, "From: $email" );

mail( "BrightonNsp@gmail.com", "Ski Patrol volunteer from web site", $message, "From: $email" );

mail( "andy@nationalequipmentcorp.com", "Ski Patrol volunteer from web site", $message, "From: $email" );

mail( "mrblackburn13@hotmail.com", "Ski Patrol volunteer from web site", $message, "From: $email" );

mail( "rogerrains@comcast.net", "Ski Patrol volunteer from web site", $message, "From: $email" );

mail( "ChadHyrumSmith@gmail.com", "Ski Patrol volunteer from web site", $message, "From: $email" );
//
//mail( "jennfernelius@aol.com", "Ski Patrol volunteer from web site", $message, "From: $email" );
//
header( "Location: VolunteerSubmit.php?firstName=$firstName&lastName=$lastName" );


?>
