<?
//echo "***** $frm ****<br>";
if($frm != "edit_amc") {
  $config_file = "config.php";  // Full path and name of the config file
  require($config_file);
//  $message = "undefined form (" . $frm . ") with an owner of (" . $owner . ")";
}
 
  if($frm == "edit_amc") {
   $title = "Online-CSR - Adds, Moves, Changes: Notification";
  }
  elseif($frm == "feedback") {
   $email_to = "agentmgr@online-csr.com";
   $title = "Online-CSR - Feedback";
   $message= "=============Feedback form:===========\n"
       . "$Feedback_Comments\n\n"
	   . "How did you hear about us:  $How_did_you_find_us\n"
       . "Name:         $Name \n"
       . "Company:   $Company \n"
       . "Telephone:  $Telephone \n"
       . "FAX:           $FAX \n"
       . "E-mail:        $Email \n";
  }
  elseif($frm == "Order_service" ) {
   $email_to = "admin@online-csr.com";
   $title = "Online-CSR - Order Service";
   $message= "==========Order Service form:=========\n"
       . "Contact Information:\n"
       . "  Organization:       $Contact_Organization\n"
       . "  Contact Name:    $Contact_FullName1\n"
       . "  Street Address:    $Contact_StreetAddress\n"
       . "  Address (cont):    $Contact_Address2\n"
       . "  City:                     $Contact_City\n"
       . "  State:                     $Contact_State\n"
       . "  Zip:                        $Contact_ZipCode\n"
       . "  Phone:                    $Contact_Phone\n"
       . "  Cell Phone:          $Contact_Cell\n"
       . "  Fax:                    $Contact_FAX\n"
       . "  E-mail:              $Contact_Email\n"
       . "  Web site:          $Contact_URL\n"
       . "\nNotify Information:\n"
       . "  Who should we Notify:   $Notify_account_manager\n"
       . "    Name:  $Notify_Name\n"
       . "    E-mail:  $Notify_Email\n"
	   . "BTN's:\n"
	   . $BTNs;
  }
  elseif($frm == "application" ) {
   $email_to = "agentmgr@online-csr.com";
   $title = "Online-CSR - Agent Application";
   $message= "==========Agent Application form:=========\n"
       . "Contact Information:\n"
       . "  Contact Name:    $Contact_FullName\n"
       . "  Organization:       $Contact_Organization\n"
       . "  Street Address:    $Contact_StreetAddress\n"
       . "  Address (cont):    $Contact_Address2\n"
       . "  City:                     $Contact_City\n"
       . "  State:                     $Contact_State\n"
       . "  Zip:                        $Contact_ZipCode\n"
       . "  Phone:                    $Contact_Phone\n"
       . "  Cell Phone:          $Contact_Cell\n"
       . "  Fax:                    $Contact_FAX\n"
       . "  E-mail:              $Contact_Email\n"
       . "  Web site:          $Contact_URL\n"
       . "\nWho do we notify in your organization :\n"
       . "    Name:  $Notify_Name\n"
       . "    E-mail:  $Notify_Email\n"
       . "    Who should we Notify:   $Notify_after_set_up\n"
	   . "\nOrder information:\n"
	   . "RBOC Agent to use:          $RBOC_Agent_select\n"
	   . "RBOC Agent (organization):          $RBOC_agent\n"
	   . "RBOC Agent Contact Name:          $RBOC_agent_contact\n"
	   . "RBOC Agent E-mail address:          $RBOC_agent_email\n"
	   . "RBOC Agent Phone:          $RBOC_agent_phone\n"
       . "\nClient Billing and Fees:\n"
	   . "Who will bill your clients:          $Who_bills_client\n"
	   . "customized pricing?:           $Display_pricing\n"
	   . "Set up Fee:           $Set_up_fee\n"
	   . "Basic Service           $Basic_service_fee\n"
	   . "Labor Hourly Rate          $Labor_rate\n"
	   . "Extra CSR updates          $CSR_updates\n";

  }
  elseif($frm == "login" ) {
   $title = "Online-CSR - User Login by " . $user_name;
   $today = date("D F j, Y, g:i a");
//user_id
//customer_id
//user_name
//customer_name
//open up user database, and query every email 
//  where company=$customer_id & NotifyAboutLogins=1;
	$query_string = "SELECT Email FROM user WHERE CustomerID=\"" . $customer_id . "\" AND NotifyAboutLogins=\"1\"";
//echo "$query_string<br>"; // Debug only
	$connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
	$result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query (result)");
	$first = 1;
	$email_to = "";
	while ($row = @mysql_fetch_array($result)) {
		if($first > 1)
		   $email_to .= ", ";
		$first += 1;
		$email_to .= $row[0];
	}
	@mysql_close($connect_string);
	@mysql_free_result($result);

   $message= $user_name . " logged on " . $today . "\n\n"
   . "You are recieving this message as per your request.  You can change this\n"
   . "from the Admin screen within Online-CSR.";


  }
/**** this is testing only code ****/
ini_set("SMTP","mail.carriersales.com"); // read from php.ini NOT this file!
ini_set("POP3","mail.carriersales.com"); // read from php.ini NOT this file!
ini_set("sendmail_from","webmaster@carriersales.com"); // read from php.ini NOT this file!
//<this was the original>> ini_set("SMTP","mail.onenetinc.com"); /* read from php.ini NOT this file! */

//ini_set("SMTP","mail.gledhills.com"); /* read from php.ini NOT this file! */
//ini_set("POP3","mail.gledhills.com"); // INCOMMING- read from php.ini NOT this file!
//ini_set("sendmail_from","julie@gledhills.com"); // read from php.ini NOT this file!

//$email_to ="steve@Gledhills.com";

//$email_to ="admin@oneline-csr.com";
/**** end testing code ****/
//echo "To:" . $email_to . "<br>";	//debug
//echo "Title: " . $title . "<br>";	//debug
//echo "Message" . $message . "<br>";	//debug
//echo $email_from . "<br>";			//debug

 if($frm != "edit_amc") {
   header("Location: " . $owner);	/* Redirect browser */
 }

//$agentName = "Brian Gledhill";
//$agentEmail= "Brian@carriersales.com";

$email_from = "autoUpdate@online-csr.com";

$email_headers = "From: $email_from\n";
//$email_headers = $email_from . "\n"; "From:  $agentName <$agentEmail>\n";     //maybe no HACK	(name from WS, email from prev page)	
//$email_headers .= "To:  $agentName <$email_to>\n";				 
//$email_headers .= "To:  Steve Gledhill <Steve@Gledhills.com>\n";				 
$email_headers .= "To: $email_to\n";				 
	//HACK - it'll try to send a CC even if no entry
//	$email_headers .= "Cc: $sendTo\n";
$email_headers .= "MIME-Version: 1.0\n";
//$email_headers .= "Content-Type: multipart/mixed; boundary=\"MIME_BOUNDRY\"\n";
$email_headers .= "X-Sender: $from_k <$email_from>\n";
$email_headers .= "X-Mailer: PHP4\n"; 
$email_headers .= "X-Priority: 3\n"; 
$email_headers .= "Return-Path: <$email_from>\n";
//$email_headers .= "This is a multi-part message in MIME format.\n";

//echo "----Start Debug Stuff----<br>";
//echo "--To:" . $email_to . "<br>";
//echo "--Title: " . $title . "<br>";
//echo "--Message: " . $message . "<br>";
//echo "--Headers: " . $email_headers . "<br>";
//...echo "--From: " . $email_from . "<br>";
//echo "----End Debug Stuff----<br>";


 if($noEmail != "yes" && $email_to != "") {
//old	 mail($email_to, $title, $message, $email_from);
   mail($email_to, $title, $message, $email_headers);     
 } else {
//   echo "dont send email"; //debug (must be before header(..) to actually display results
 }
 if($frm != "edit_amc") {
   exit;
 }
?>