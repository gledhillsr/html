<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
$textpart = "search";
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");

$query = "SELECT gedcom, treename FROM $trees_table ORDER BY treename";
$result = mysql_query($query) or die ("$admtext[cannotexecutequery]: $query");
$numtrees = mysql_num_rows($result);

tng_header( $text[searchnames], "" );
?>

<p class="header"><? echo $text[searchnames];?></p>
<?
echo tng_menu( "search", "", 1 );
?>
<b><? echo stripslashes(urldecode($msg));?></b>
<?
$formstr = getFORM( "search", "", "", "" );
echo $formstr;
?>
<table>
<?
if( $numtrees > 1 ) {
?>
<tr>
	<td><span class="normal"><? echo $text[tree];?>:</span></td>
	<td colspan="2">
		<select name="tree">
			<option value="-x--all--x-" <? if( !$tree ) echo "selected"; ?>><? echo $text[alltrees]; ?></option>
<?
	while( $row = mysql_fetch_assoc($result) ) {
		echo "	<option value=\"$row[gedcom]\"";
		if( $tree && $row[gedcom] == $tree ) echo " selected";
		echo ">$row[treename]</option>\n";
	}
?>
		</select>
	</td>
</tr>
<?
}
?>
<tr>
	<td><span class="normal"><? echo $text[lastname];?>:</span></td>
	<td>
		<select name="lnqualify">
			<option SELECTED><? echo $text[contains];?></option>
			<option><? echo $text[equals];?></option>
			<option><? echo $text[startswith];?></option>
			<option><? echo $text[endswith];?></option>
			<option><? echo $text[soundexof];?></option>
			<option><? echo $text[metaphoneof];?></option>
		</select>
	</td>
	<td><input type="text" name="mylastname"></td>
</tr>
<tr>
	<td><span class="normal"><? echo $text[firstname];?>:</span></td>
	<td>
		<select name="fnqualify">
			<option SELECTED><? echo $text[contains];?></option>
			<option><? echo $text[equals];?></option>
			<option><? echo $text[startswith];?></option>
			<option><? echo $text[endswith];?></option>
			<option><? echo $text[soundexof];?></option>
		</select>
	</td>
	<td><input type="text" name="myfirstname"></td>
</tr>
<tr>
	<td><span class="normal"><? echo $text[nickname];?>:</span></td>
	<td>
		<select name="nnqualify">
			<option SELECTED><? echo $text[contains];?></option>
			<option><? echo $text[equals];?></option>
			<option><? echo $text[startswith];?></option>
			<option><? echo $text[endswith];?></option>
			<option><? echo $text[soundexof];?></option>
		</select>
	</td>
	<td><input type="text" name="mynickname"></td>
</tr>
<tr>
	<td><span class="normal"><? echo $text[title];?>:</span></td>
	<td>
		<select name="tqualify">
			<option SELECTED><? echo $text[contains];?></option>
			<option><? echo $text[equals];?></option>
			<option><? echo $text[startswith];?></option>
			<option><? echo $text[endswith];?></option>
			<option><? echo $text[soundexof];?></option>
		</select>
	</td>
	<td><input type="text" name="mytitle"></td>
</tr>
<tr>
	<td><span class="normal"><? echo $text[suffix];?>:</span></td>
	<td>
		<select name="sfqualify">
			<option SELECTED><? echo $text[contains];?></option>
			<option><? echo $text[equals];?></option>
			<option><? echo $text[startswith];?></option>
			<option><? echo $text[endswith];?></option>
			<option><? echo $text[soundexof];?></option>
		</select>
	</td>
	<td><input type="text" name="mysuffix"></td>
</tr>
<tr>
	<td><span class="normal"><? echo $text[birthplace];?>:</span></td>
	<td>
		<select name="bpqualify">
			<option SELECTED><? echo $text[contains];?></option>
			<option><? echo $text[equals];?></option>
			<option><? echo $text[startswith];?></option>
			<option><? echo $text[endswith];?></option>
			<option><? echo $text[soundexof];?></option>
		</select>
	</td>
	<td><input type="text" name="mybirthplace"></td>
</tr>
<tr>
	<td><span class="normal"><? echo $text[birthdatetr];?>:</span></td>
	<td>
		<select name="byqualify">
			<option SELECTED><? echo $text[equals];?></option>
			<option value="pm2"><? echo $text[plusminus2];?></option>
			<option value="pm10"><? echo $text[plusminus10];?></option>
			<option value="lt"><? echo $text[lessthan];?></option>
			<option value="gt"><? echo $text[greaterthan];?></option>
			<option value="lte"><? echo $text[lessthanequal];?></option>
			<option value="gte"><? echo $text[greaterthanequal];?></option>
		</select> 
	</td>
	<td><input type="text" name="mybirthyear"></td>
</tr>
<tr>
	<td><span class="normal"><? echo $text[deathplace];?>:</span></td>
	<td>
		<select name="dpqualify">
			<option SELECTED><? echo $text[contains];?></option>
			<option><? echo $text[equals];?></option>
			<option><? echo $text[startswith];?></option>
			<option><? echo $text[endswith];?></option>
			<option><? echo $text[soundexof];?></option>
		</select>
	</td>
	<td><input type="text" name="mydeathplace"></td>
</tr>
<tr>
	<td><span class="normal"><? echo $text[deathdatetr];?>:</span></td>
	<td>
		<select name="dyqualify">
			<option SELECTED><? echo $text[equals];?></option>
			<option value="pm2"><? echo $text[plusminus2];?></option>
			<option value="pm10"><? echo $text[plusminus10];?></option>
			<option value="lt"><? echo $text[lessthan];?></option>
			<option value="gt"><? echo $text[greaterthan];?></option>
			<option value="lte"><? echo $text[lessthanequal];?></option>
			<option value="gte"><? echo $text[greaterthanequal];?></option>
		</select> 
	</td>
	<td><input type="text" name="mydeathyear"></td>
</tr>
<tr>
	<td><span class="normal"><? echo $text[joinwith];?>:</span></td>
	<td>
		<select name="mybool">
			<option value="AND" SELECTED><? echo $text[cap_and];?></option>
			<option value="OR"><? echo $text[cap_or];?></option>
		</select>
	</td>
	<td></td>
</tr>
</table>
<span class="normal"><input type="checkbox" name="showspouse" value="yes"> <? echo $text[showspouse];?><br><br></span>
<input type="submit" value="<? echo $text[submitquery];?>">
</form>

<?
echo tng_menu( "search", "", 2 );
tng_footer( "" );
?>
