<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");

session_register('session_language');
$session_language = $newlanguage;
$textpart = "language";
include($cms[tngpath] . "$session_language/text.php");
include($cms[tngpath] . "checklogin.php");

$changelanguage_noargs_url = getURL( "changelanguage", 0 );

tng_header( $text[languagesaved], "" );
?>
<p class="header"><? echo $text[languagesaved]; ?></span></p>

<? echo $text[newlanguage]; ?>: <? echo $session_language; ?>
<br><br>
<? echo "<a href=\"$changelanguage_noargs_url\">"; ?>
<? echo $text[changelanguage]; ?></a>

<?
echo tng_menu( "", "", 2 );
tng_footer( "" );
?>
