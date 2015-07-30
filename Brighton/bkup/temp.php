<?
$cur_page="temp";
$config_file = "config.php";  // Full path and name of the config file
require($config_file);
?>

<HEAD>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
</HEAD>

<body bgcolor="#ffffff" marginwidth="0" marginheight="0" topmargin="0" leftmargin="0">
<?
	$mysql_db  = "brighton";

	$connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
	$query_string = "SELECT  IDNumber , lastUpdated  FROM roster WHERE 1";
	//echo $query_string . "<br>";
	$result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query");
	while ($row = @mysql_fetch_array($result)) {
		$old = $row[lastUpdated];
		$id   = $row[IDNumber];
		$new = $old;
		echo "$id: $old $new<br>"; 
//		$query_string = "UPDATE roster SET lastUpdated=\"$new\" WHERE IDNumber=\"$id\"";
	}
	echo "Done<br>";
	@mysql_close($connect_string);
	@mysql_free_result($result);
?>
</body>
</html>
