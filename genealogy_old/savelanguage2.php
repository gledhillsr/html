<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
session_register('session_language');
eval( "\$newlanguage = \$newlanguage$instance;" );
$session_language = $newlanguage;
$textpart = "language";
include($cms[tngpath] . "$session_language/text.php");
include($cms[tngpath] . "checklogin.php");

header( "Location: " . "$returnpage" );
?>
