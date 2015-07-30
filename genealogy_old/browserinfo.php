<?
// minimal browser detection (Netscape and Internet Explorer)

$br = strtolower(getenv("HTTP_USER_AGENT"));

if (
 (ereg("netscape6",  $br)) ||
 (ereg("netscape 6", $br)) ) 
 $browser = "NS6";

elseif (
 (ereg("netscape7",  $br)) ||
 (ereg("netscape/7",  $br)) ||
 (ereg("netscape 7", $br)) )
 $browser = "NS7";

elseif  (ereg("msie", $br)) {
if(ereg("msie 4.0", $br)) $browser = "IE 4";
elseif(ereg("msie 5.0", $br)) $browser = "IE5";
elseif(ereg("msie 5.1", $br)) $browser = "IE5.1";
elseif(ereg("msie 5.2", $br)) $browser = "IE5.2";
elseif(ereg("msie 5.5", $br)) $browser = "IE5.5";
elseif(ereg("msie 6.0", $br)) $browser = "IE6"; }

elseif(
 (ereg("nav", $br)) ||
 (ereg("netscape", $br)) ||
 (ereg("/4.", $br)) )
 $browser = "NS4";

else $browser = "NA";
?>