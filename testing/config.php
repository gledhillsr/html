<?php

$mysql_username = "root";           // MySQL user name

$mysql_username = "aftonkent451";           // MySQL user name
$zzmysql_password = "XXXXXXX";    // MySQL password (leave empty if no password is required.)
$mysql_db       = "Afton";        // MySQL database name
$mysql_host     = "localhost";      // MySQL server host name
$gledhills_host = "166.70.236.73";      // MySQL server host name

require_once 'DB.php';

//Localhost database connection
$dsn = "mysql://{$mysql_username}:{$mysql_password}@{$mysql_host}/{$mysql_db}";
$db =& DB::connect($dsn);
if (DB::isError ($db))
    die ("Could not connect to the database at {$mysql_host}.");
?>
