<?php 
 require("config.php");
 $connect_string = @mysql_connect($mysql_host, $mysql_username, $mysql_password) or die ("Could not connect to the database.");
 $strToday = "xyz";   
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Brighton Ski Patrol Training</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script></head>

<body background="img/background.jpg" bottommargin="0" leftmargin="0" marginheight="0" marginwidth="0" rightmargin="0" topmargin="0">

	
<center><table width="765" height="100%" cellpadding="0" cellspacing="0" border="0" background="img/mainbackground.jpg"><tr valign="top"><td>

<table width="764" height="97" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
	<td width="248"><!-- logo image --><img src="Brighton.gif" width="248" height="97" border="0" alt=""><!-- end logo image --></td>
	<td width="100%" background="img/toplogobg.jpg">
	<font size="6" color="black"><center>Brighton Ski Patrol Training 2020/2021</center></font></td>
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
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a onclick="showIntro()">2020 Introduction</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a onclick="showPandemicResponse()">Pandemic Response</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a onclick="showWIP()">-PPE</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a onclick="showIDCProcedures()">IDC Prodecures</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a onclick="showEarlyTransport()">Early Transport</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a onclick="showWIP()">-Screen Area Guide</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a onclick="showSymptomsC19()">Symptoms of Covid-19</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a onclick="showNSAA()">NSAA Webinar</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a onclick="showCPR()">--CPR</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a onclick="showWIP()">-C-19 Response plan</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a onclick="showStress()">Signs of Stress</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a onclick="showWIP()">-Snow Safety</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a onclick="showNaloxone()">Naloxone</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a onclick="showWIP()">-BSP Documentation</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a onclick="showTriage()">Triage</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a onclick="showLiftEvac()">Lift Evac</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a onclick="showAssessment()">Patient Assessment</a><br>
<img src="img/menudivider.jpg" width="150" height="6" border="0" alt=""><br>&nbsp; <a onclick="showEvaluations()">Evaluations</a><br>
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
<td width="744"><iframe id="panel" width="100%" height="600" style="border:none;" src="Overview.html"></iframe>  <script>
	function showIntro() {
		document.getElementById("panel").src="Overview.html";
//		document.getElementById("panel").src="Overview.html";
	}
	function showPandemicResponse() {
		document.getElementById("panel").src="2-BSP-Pandemic_Response_FINAL-07_09-jc.pdf";
	}
	function showIDCProcedures() {
		document.getElementById("panel").src="IDC.html";
	}
	function showEarlyTransport() {
		document.getElementById("panel").src="9-Early_Transport_Decisions-jc.pdf";
	}
	function showNSAA() {
		document.getElementById("panel").src="12-NSAA_Webinar-Ski_Patrol_Challenges_in_the_Age_of_COVID-jc.pdf";
	}
	function showStress() {
		document.getElementById("panel").src="15-Signs-and-Symptoms-of-Stress-jc.pdf";
	}
	
	function showScreeningArea() {
		document.getElementById("panel").src="UnderConstruction.html";
	}
	function showSymptomsC19() {
		document.getElementById("panel").src="11-COVID19-symptoms.pdf";
	}
	function showSnowSafety() {
		document.getElementById("panel").src="UnderConstruction.html";
	}
	function showBSPDocumentation() {
		document.getElementById("panel").src="UnderConstruction.html";
	}
	function showTriage() {
		document.getElementById("panel").src="Triage.html";
	}
	function showAssessment() {
		document.getElementById("panel").src="Assessment.html";
	}
	function showEvaluations() {
		document.getElementById("panel").src="Evaluations.html";
	}
		
	function showNaloxone() {
		document.getElementById("panel").src="19-Naloxone.pdf";
	}

	
	
	
	function showPPE() {
		document.getElementById("panel").src="PPE.html";
	}
	function showLiftEvac() {
		document.getElementById("panel").src="LiftEvac.html";
	}
	function showCPR() {
		document.getElementById("panel").src="CPR.html";
	}
	function showKnots() {
		document.getElementById("panel").src="KnotsAndTerms.pdf";
	}
	function showWIP() {
		document.getElementById("panel").src="UnderConstruction.html";
	}
	function showASL() {
		document.getElementById("panel").src="brighton_asl.pdf";
	}		
    </script>

<br>
<center>Web site and all contents (C) Copyright Brighton Ski Patrol, All rights reserved.</center>
<center><a href="http://www.steves-templates.com">Free website templates</a></center>
</td>
<td width="10">&nbsp;</td>
	</tr>
</table>
</td></tr></table></center>
</body>
</html>
