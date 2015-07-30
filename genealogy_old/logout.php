<?php
include("begin.php");
session_start();
session_unset();
session_destroy();
header( "Location: " . $homepage );
?>
