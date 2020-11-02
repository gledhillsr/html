<?php 
 require("config.php");
 $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
 $strToday = "uninitialized";   
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Brighton Ski Patrol Training</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script></head>

<body background="img/background.jpg" bottommargin="0" leftmargin="0" marginheight="0" marginwidth="0" rightmargin="0" topmargin="0">
<script>
	var previousId = "intro";
	function showPanel(id, contents) {
		document.getElementById("panel").src=contents;
        document.getElementById(previousId).removeAttribute("style");
        document.getElementById(id).style.background = "yellow";
		previousId = id;
	}
</script>	

	
<center><table width="765" height="100%" cellpadding="0" cellspacing="0" border="0" background="img/mainbackground.jpg"><tr valign="top"><td>

<table width="764" height="97" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
	<td width="248"><!-- logo image --><img src="Brighton.gif" width="248" height="97" border="0" alt=""><!-- end logo image --></td>
	<td width="100%" background="img/toplogobg.jpg">
	<center>
	  <font size="6" color="black">Brighton Ski Patrol<br>
		  Refresher 2020</font>
	</center></td>
	</tr>
</table>
<table width="764" height="42" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
	<td width="169"><img src="img/left1.jpg" width="169" height="42" border="0" alt=""></td>
	<td width="100%" background="img/left1bg.jpg"><img src="img/left1bg.jpg" width="20" height="42" border="0" alt=""></td>
	</tr>
</table>
<table width="764" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">

<td width="150">
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a id="intro"  onclick="showPanel(this.id, 'Overview.html')" style='background:yellow;'>2020 Introduction</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a id="bsp"    onclick="showPanel(this.id, 'BSPResponsePlan.html')">BSP Response Plan</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a id="ppe"    onclick="showPanel(this.id, 'PPE.html')">PPE</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a id="idc"    onclick="showPanel(this.id, 'IDC.html')">IDC Prodecures</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a id="trans"  onclick="showPanel(this.id, 'Transport.html')">Transport Decisions</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a id="area"   onclick="showPanel(this.id, 'screening.html')">Screening Area Guide</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a id="c19"    onclick="showPanel(this.id, '11-COVID19-symptoms.pdf')">Symptoms of Covid-19</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a id="c19a"   onclick="showPanel(this.id, '11-SymptomsofCoronavirus.pdf')">Symptoms from CDC</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a id="nssa"   onclick="showPanel(this.id, 'NsaaWebinar.html')">NSAA Webinar</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a id="cpr"    onclick="showPanel(this.id, 'CPR.html')">CPR</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a id="nsp"    onclick="showPanel(this.id, 'NSPResponsePlan.html')">NSP Response Plan</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a id="stres"  onclick="showPanel(this.id, '15-Signs-and-Symptoms-of-Stress-jc.pdf')">Signs of Stress</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a id="snow"   onclick="showPanel(this.id, 'SnowSafety.html')">Snow Safety</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a id="nalox"  onclick="showPanel(this.id, 'Naloxone.html')">Naloxone</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a id="docs"   onclick="showPanel(this.id, 'BspDocumentation.html')">BSP Documentation</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a id="triag"  onclick="showPanel(this.id, 'Triage.html')">Triage</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a id="evac"   onclick="showPanel(this.id, 'LiftEvac.html')">Lift Evac</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a id="assmnt" onclick="showPanel(this.id, 'Assessment.html')">Patient Assessment</a><br
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp;<a id="volDocs" onclick="showPanel(this.id, 'VolunteerDocuments.html')">Volunteer Documents</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>
&nbsp;&nbsp;

<?php
	$arrDate = getdate();
	$today=mktime($arrDate['hours'], $arrDate['minutes'], 0, $arrDate['mon'], $arrDate['mday'], $arrDate['year']);
	$strToday = date("m/d/Y g:i a", $today);
    $query_string = "SELECT * FROM roster WHERE IDNumber=$id";
	$result = @mysql_db_query($mysql_db, $query_string) or die ("Invalid query member id [$id] not recognized");
	if ($row = @mysql_fetch_array($result)) {
		$firstName = $row["FirstName"];
		$lastName = $row["LastName"];
	}

	function mylog($menuItem) {
	global $firstName, $lastName, $strToday, $id;  
	error_log("zz $menuItem page ($strToday) ~ $id $firstName $lastName");
	}			

	mylog("main");
	echo "<br><br><br>&nbsp;Welcome<br><b>&nbsp;$firstName $lastName</b><br><br>&nbsp;$strToday";
?>
	
</td> 
<td width="10">&nbsp;</td>
<td width="744"><iframe id="panel" width="100%" height="750" style="border:none;" src="Overview.html"></iframe>  

<br>
<center>Web site and all contents © Copyright Brighton Ski Patrol, All rights reserved.</center>
<center><a href="http://www.steves-templates.com">Free website templates</a></center>
</td>
<td width="10">&nbsp;</td>
	</tr>
</table>
</td></tr></table></center>
</body>
</html>
