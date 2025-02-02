<?

/*****************************************************************************
 * register cookies, POST's, & GET's                                         *
 *****************************************************************************/

if(!ini_get('register_globals')){
	$__am = array('COOKIE','POST','GET');
	while(list(,$__m) = each($__am)){
//var_dump($__m);
//echo "---" . ${"HTTP_".$__m."_VARS"} . "---<br>";
		$__ah = &${"HTTP_".$__m."_VARS"};
//var_dump($__ah);
		if(!is_array($__ah)) continue;
		while(list($__n, $__v) = each ($__ah)) {
//echo "yyy(".$__n.", ".$__v.")<br>";
			$$__n = $__v;
		}
	}
}

/*****************************************************************************
 * email stuff                                                               *
 *****************************************************************************/

$email_to ="steve@Gledhills.com";
//$email_from = "From: autoUpdate@online-csr.com";
$email_from = "autoUpdate@online-csr.com";

/*****************************************************************************
 * Edit password                                                             *
 *****************************************************************************/

$edit_password  = "";   // Password required to edit the database.
                     	//  (This is not the same as the MySQL password)

/*****************************************************************************
 * Database settings                                                         *
 *****************************************************************************/

$mysql_username = "root";           // MySQL user name
$mysql_password = "my_password";    // MySQL password (leave empty if no password is required.)
$mysql_db       = "csrdata";        // MySQL database name

$mysql_host     = "localhost";      // MySQL server host name
                                    // ("localhost" should be fine on 
                                    // most systems)

/*****************************************************************************
 * File locations                                                            *
 *****************************************************************************/

$inventory_url  = "inventory.php";          // URL of the index.php file

$header_file    = "header.php";     // Path and name of the header file
$footer_file    = "footer.php";     // Path and name of the footer file
$box_header_file= "box_header.php";	// Path and name of the footer file
$styles_file    = "styles.php";     // Path and name of the style sheet

/*****************************************************************************
 * IMDb link mode - See the README for more information.                     *
 *****************************************************************************/
$edit = 1;

$review_blank = "Not Yet Reviewed";
$review_n     = "Review OK";
$review_y     = "Needs Review";

/*****************************************************************************
 * Default maximum results per page.                                         *
 * Valid settings are "50", "100", "200" or "all"                            *
 * "all" displays all results from the database on a single page.            *
 *****************************************************************************/

$max_results_default = "50";    // Default Maximum results per page
									
/*****************************************************************************
 * Colors and misc. settings                                                 *
 *****************************************************************************/

$max_results_background_color = "#F0F0F0";   // Search box background color

$header_text_color       = "#000000";   // Header/Footer text color
$header_hover_color      = "red";       // Header/Footer hover color
$header_color            = "#C8C8C8";   // Header/Footer background color

$text_color              = "#000000";   // DVD Results text color
$text_hover_color        = "red";       // DVD Results hover color
$table_bgcolor           = "#FFFFFF";   // DVD Results table border color

$totals_color			 = "yellow";	// Row color for Totals
$row_color_1             = "#F7F7EF";   // Row 1 background color
$row_color_2             = "#F0F0E8";   // Row 2 background color

$body_bgcolor            = "#FFFFFF";   // Body background color
$body_text_color         = "#000000";   // Body text color
$body_link_color         = "#000000";   // Body link color
$body_vlink_color        = "#000000";   // Body visited link color
$body_alink_color        = "red";       // Body activated link color
$body_hover_color        = "red";       // Body hover color

$go_button               = "images/go.gif";    // URL of the "go" button used onthe page

$check_mark1              = "images/tick_red.gif";	//for Reset
$check_mark2              = "images/tick_green.gif";	//for Reset
$check_mark3              = "images/tick_blue.gif";

$green_dot               = "images/greendot.gif";	//for Adds, Moves, Changes
$yellow_dot              = "images/yellowdot.gif";
$red_dot                 = "images/reddot.gif";

/*****************************************************************************
 * The following variables define the name of each column, as it will        *
 * appear on the header                                                      *
 *****************************************************************************/
//---------- AMC --------------
$amc_field[1][1] = "BTN";         			// Column 1	Adds, Moves, Changes
$amc_field[1][2] = "Service";  				// Column 2
$amc_field[1][3] = "Requested By"; 			// Column 3
$amc_field[1][4] = "Date<br>Created";   	// Column 4
$amc_field[1][5] = "Instructions";    		// Column 5
$amc_field[1][6] = "Response";    			// Column 6
$amc_field[1][7] = "Order&nbsp;#";			// Column 7
$amc_field[1][8] = "Remind<br>Date"; 		// Column 8
$amc_field[1][9] = "Status";         		// Column 9
$amc_field[1][10] = "Last<br>Modified"; 	// Column 10
$amc_field[1][11] = "Customer ID"; 			// Column 11
$amc_field[1][12] = "Notify Users"; 		// Column 12
$amc_field[1][13] = "Due<br>Date"; 			// Column 13
//---------- BTN --------------
$btn_field[1][1] = "BTN";         			// Column 1 Line Inventory Manager
$btn_field[1][2] = "Record<br>Date";  		// Column 2
$btn_field[1][3] = "Location Name"; 		// Column 3
$btn_field[1][4] = "Division";      		// Column 4
$btn_field[1][5] = "Department";    		// Column 5
$btn_field[1][6] = "Adds<br>Moves&nbsp;<br>Changes";    // Column 6
$btn_field[1][7] = "Description<br>of Service";// Column 7
$btn_field[1][8] = "Contract<br>Exp Date"; 	// Column 8
$btn_field[1][9] = "Cost";         			// Column 9
$btn_field[1][10] = "Review";           	// Column 10
//$btn_field[1][11] = "Customer";       	// Column 11 (not used)
$btn_field[1][12] = "Review<br>Comments";  	// Column 12
$btn_field[1][13] = "Previous&nbsp;Price";  	// Column 13
$btn_field[1][14] = "Previous&nbsp;Update&nbsp;Date"; // Column 14
$btn_field[1][15] = "Monthly<br>Savings";   // Column 15
$btn_field[1][16] = "Product";     			// Column 16
$btn_field[1][17] = "MRC";     				// Column 17
//---------- user --------
$user_field[1][1] = "Login Name";        // MySQL varchar(20)
$user_field[1][2] = "Password";  		// MySQL varchar(20)
$user_field[1][3] = "Customer ID";    	// MySQL varchar(16)
$user_field[1][4] = "User Name"; 		// MySQL varchar(32)
$user_field[1][5] = "Admin"; 	// MySQL tinyint(1)
$user_field[1][6] = "Notify<br>About<br>Logins";// MySQL tinyint(1)
$user_field[1][7] = "Notify<br>About<br>Changes";// MySQL tinyint(1)
$user_field[1][8] = "Dept";   	// MySQL varchar(16)
$user_field[1][9] = "Email";        	// MySQL varchar(32)
$user_field[1][10] ="Phone";     		// MySQL varchar(20)
$user_field[1][11] ="Mobil";     		// MySQL varchar(20)
$user_field[1][12] ="Access<br>Count";     	// MySQL int(11)
/*****************************************************************************
 * The following variables define the column names to be retrieved from the  *
 * MySQL table.																 *
 * the Follwign fields MUST exactly match the Database                       *
 *****************************************************************************/
//---------- agency --------------
$agent_field[2][1] = "agencyID";    	// MySQL varchar(16)
$agent_field[2][2] = "agencyName";  	// MySQL varchar(32)
$agent_field[2][3] = "Address";   		// MySQL varchar(80)
$agent_field[2][4] = "Contact"; 		// MySQL varchar(32)
$agent_field[2][5] = "Phone"; 			// MySQL varchar(20)
$agent_field[2][6] = "Fax";  			// MySQL varchar(20)
$agent_field[2][7] = "SecondContact";   // MySQL varchar(32)

//---------- AMC --------------
$amc_field[2][1] = "BTN";         		// MySQL varchar(20)
$amc_field[2][2] = "Service";  			// MySQL varchar(8)
$amc_field[2][3] = "RequestedBy";   	// MySQL varchar(20)
$amc_field[2][4] = "RequestDate"; 		// MySQL varchar(16)
$amc_field[2][5] = "Instructions"; 		// MySQL varchar(60)
$amc_field[2][6] = "Response";  		// MySQL varchar(60)
$amc_field[2][7] = "OrderNum";   		// MySQL varchar(20)
$amc_field[2][8] = "RemindDate";       	// MySQL int(11)
$amc_field[2][9] = "Status";     		// MySQL varchar(20)
$amc_field[2][10] = "LastModified"; 	// MySQL varchar(16)
$amc_field[2][11] = "CustomerID";	 	// MySQL varchar(16)
$amc_field[2][12] = "UsersNotified";  	// MySQL varchar(60)
$amc_field[2][13] = "DueDate";		  	// MySQL int(11)

//---------- BTN --------------
$btn_field[2][1] = "BTN";         		// MySQL varchar(20)
$btn_field[2][2] = "LastUpdated";  		// MySQL varchar(16)
$btn_field[2][3] = "Location";    		// MySQL varchar(40)
$btn_field[2][4] = "Division"; 			// MySQL varchar(20)
$btn_field[2][5] = "Department"; 		// MySQL varchar(20)
$btn_field[2][6] = "AMC";    			// MySQL varchar(12)
$btn_field[2][7] = "Description";  		// MySQL varchar(40)
$btn_field[2][8] = "ContractExpDate";   // MySQL varchar(16)
$btn_field[2][9] = "Cost";        		// MySQL float
$btn_field[2][10] = "Review";     		// MySQL char(1)
$btn_field[2][11] = "CustomerID";     	// MySQL varchar(16)
$btn_field[2][12] = "ReviewNotes";     	// MySQL text
$btn_field[2][13] = "prevCost";     	// MySQL float
$btn_field[2][14] = "prevUpdated";     	// MySQL varchar(16)
$btn_field[2][15] = "MonthlySavings";   // MySQL float
$btn_field[2][16] = "Product";     		// MySQL tinyint(1)
$btn_field[2][17] = "MRC";     			// MySQL float

//---------- customer --------
$cust_field[2][1] = "CustomerID";       // MySQL varchar(16)
$cust_field[2][2] = "Name";  			// MySQL varchar(32)
$cust_field[2][3] = "Address";    		// MySQL varchar(80)
$cust_field[2][4] = "AgencyID"; 		// MySQL varchar(16)
$cust_field[2][5] = "AllowArchiveAccess";// MySQL tinyint(1)
$cust_field[2][6] = "BTNCount";			// MySQL int(11)
$cust_field[2][7] = "BTNStorage";		// MySQL int(11)
$cust_field[2][8] = "ArchiveBtnCount"; 	// MySQL int(11)
$cust_field[2][9] = "ArchiveBtnStorage";// MySQL int(11)

//---------- USER --------------
$user_field[2][1] = "LoginName";        // MySQL varchar(20)
$user_field[2][2] = "Password";  		// MySQL varchar(20)
$user_field[2][3] = "CustomerID";    	// MySQL varchar(16)
$user_field[2][4] = "UserName"; 		// MySQL varchar(32)
$user_field[2][5] = "Administrator"; 	// MySQL tinyint(1)
$user_field[2][6] = "NotifyAboutLogins";// MySQL tinyint(1)
$user_field[2][7] = "NotifyAboutChanges";// MySQL tinyint(1)
$user_field[2][8] = "Department";   	// MySQL varchar(16)
$user_field[2][9] = "Email";        	// MySQL varchar(32)
$user_field[2][10] ="Phone";     		// MySQL varchar(20)
$user_field[2][11] ="Mobil";     		// MySQL varchar(20)
$user_field[2][12] ="AccessCount";     	// MySQL int(11)

/*****************************************************************************
 * End of configuration file.                                                *
 *****************************************************************************/

?>
