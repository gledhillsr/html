<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "login";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
include($cms[tngpath] . "log.php" );

if( $returnpage ) {
	session_register('destinationpage');
	$destinationpage = $returnpage;
}
tng_header( $text[login], "" );
?>

<p class="header"><? echo $text[login]; ?></p>

<?
	if( $message ) {
?>
	<font color="#FF0000"><span class="normal"><em><? echo urldecode($message); ?></em>
	</span></font>
<?
	}

$formstr = getFORM( "processlogin", "POST", "form1", "" );
echo $formstr;
?>
<table>
<tr>
	<td><span class="normal"><? echo $text[username]; ?>:</span></td>
	<td><input type="text" name="username" size="20"></td>
</tr>
<tr>
	<td><span class="normal"><? echo $text[password]; ?>:</span></td>
	<td><input type="password" name="password" size="20"></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type="submit" value="<? echo $text[login]; ?>"></td>
</tr>
</table>
</form>
<br>
<script language="JavaScript">
	document.form1.username.focus();
</script>
<?
tng_footer( "" );
?>
