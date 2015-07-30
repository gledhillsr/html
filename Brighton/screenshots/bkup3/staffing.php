<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Unassigned</title>
<script language="JavaScript">
function saveit() {
	alert("save button NOT working yet");
} 
  function lockout(lock) {
  	if(lock)
  		alert("Lock-out is not enabled yet");
  	else {
		var password;
		password=prompt("Enter password to Unlock Login's",' ');
		if (password=="patrick")
	  		alert("REMOVE Lock-out is not enabled yet");
	  	else
	  		alert("Sorry, wrong password");
  	}
  }
  
  function do_add(pos) {
     var listbox;
     if(pos == 4)
     	listbox = document.form1.StaffLB
     else
     	listbox = document.form1.TrainingLB
     
     var lastPos = listbox.options.length;
     var myNewOption = new Option(".....");
	  listbox.options[lastPos] = myNewOption;
	  listbox.options.selectedIndex= -1;
  }

  function add_aux(pos) {
     var listbox;
     if(pos == 1)
     	listbox = document.form1.CrestLB
     else if (pos == 2)
     	listbox = document.form1.WesternLB
     else
     	listbox = document.form1.MilliLB
     
     var lastPos = listbox.options.length;
     var myNewOption = new Option("aux..");
	  listbox.options[lastPos] = myNewOption;
	  listbox.options.selectedIndex= -1;
  }

  function add_bas(pos) {
     var listbox;
     if(pos == 1)
     	listbox = document.form1.CrestLB
     else if (pos == 2)
     	listbox = document.form1.WesternLB
     else
     	listbox = document.form1.MilliLB
     
     var lastPos = listbox.options.length;
     var myNewOption = new Option("bas..");
	  listbox.options[lastPos] = myNewOption;
	  listbox.options.selectedIndex= -1;
  }

  function move_name(mountain,listbox) {
  var currIndex = listbox.selectedIndex;
  var currID = listbox.options[currIndex].value;
  var currName = listbox.options[currIndex].text;
  var currTitle = currName.substr(0,5);
  currName = currName .substr(5);
  var messageStr
  var unAssigned = document.form1.UnassignedLB;
  var txtDisplay   = document.form1.lastCommand;
  var unIndex = unAssigned .selectedIndex
  var unID
  var unName
  if(unIndex != -1) { 
   	 unID = unAssigned.options[unIndex].value;
   	 unName = unAssigned.options[unIndex].text;
  }
  if(unIndex == -1 && (currID == null || currID == 0) ) {
     messageStr= "Oops, no one is selected as Unassigned";
  } else {
    if (currID && currID > 0) { 
     //
     // there was a patroller in this listbox, so move to "Unassigned"
     //
     messageStr = "Moved '"+currName +"' from "+mountain+" To 'Unassigned' (id=" + currID + ")"
     lastPos = unAssigned.options.length; //this is bogus, I should not subtract a 1
     //add to end of "unassigned" list
     var myNewOption = new Option(currName,currID);
	  unAssigned.options[lastPos] = myNewOption;
	  unAssigned.options.selectedIndex= lastPos;
	  //remove from current list
     listbox.options[currIndex].value = null;
     listbox.options[currIndex].text = currTitle ;
	  listbox.selectedIndex = -1;
    } else { 
        //
        //this position was empty, so move "unassigned" patroller here
        //
        messageStr= "Moved " + unName +" to " + mountain + " (id="+unID+")";	//history text
 	    listbox.options[currIndex].text = currTitle + unName;               //add to new list box
	    listbox.options[currIndex].value = unID;
        listbox.selectedIndex = -1;
		 unAssigned.options[unIndex] = null; 								//remove from Unassigned
		 if(unAssigned.options.length <= unIndex)
 		     unAssigned.selectedIndex = unIndex -1;
 		 else
	 		 unAssigned.selectedIndex = unIndex;
    }
  } 
  //display string of what changed
	txtDisplay.value = messageStr;
  }
</script>
</head>

<body>

<form name="form1" method="POST">
<!--
  <p align="left"><font size="2">Click name to move into &quot;Unassigned&quot;, Click empty
  position to move &quot;Unassigned name&quot; into that position.<br>
  Color Code: <b><font color="#0000FF">Senior</font>&nbsp; Basic&nbsp; <font color="#FF0000">Auxiliary</font><font color="#006600">&nbsp;
  Candidate</font>&nbsp;</b></font><br>
-->

<!--  -------------------------------------------------- -->
<script language="JavaScript">

//Refresh page script- By Brett Taylor (glutnix@yahoo.com.au)
//Modified by Dynamic Drive for NS4, NS6+
//Visit http://www.dynamicdrive.com for this script

//configure refresh interval (in seconds)
var countDownInterval=0;
//configure width of displayed text, in px (applicable only in NS4)
<!-- var c_reloadwidth=200 -->

</script>
<!--
//<ilayer id="c_reload" width=&{c_reloadwidth}; >
//  <layer id="c_reload2" width=&{c_reloadwidth}; left=0 top=0>
//  </layer>
//</ilayer>
-->

<script>

var countDownTime=countDownInterval-1;

function countUp(){
  countDownTime++;
//  if (countDownTime > 100){
//    countDownTime=countDownInterval;
//    clearTimeout(counter)
//    window.location.reload()
//    return
//  }
    var sec = countDownTime % 60;
    var min = (countDownTime - sec ) / 60;
    var tim = min+':';
	if(sec < 10)
		tim += '0';
	tim += sec;

  if (document.all) //if IE 4+
    document.all.countDownText.innerText = tim;
  else if (document.getElementById) //else if NS6+
    document.getElementById("countDownText").innerHTML = tim
  else if (document.layers){ //CHANGE TEXT BELOW TO YOUR OWN
    document.c_reload.document.c_reload2.document.write(
'Time since last <a href="javascript:window.location.reload()">refresh</a> is <b id="countDownText">'+tim+' </b> seconds')
    document.c_reload.document.c_reload2.document.close()
  }
  counter=setTimeout("countUp()", 1000);
}

function startit(){
  if (document.all||document.getElementById) //CHANGE TEXT BELOW TO YOUR OWN
    var sec = countDownTime % 60;
    var min = (countDownTime - sec ) / 60;
    var tim = min+':';
	if(sec < 10)
		tim += '0';
	tim += sec;
    document.write('Time since last <a href="javascript:window.location.reload()">refresh</a> is <b id="countDownText">'+tim+'</b>')
  countUp()
}

if (document.all||document.getElementById)
  startit()
else
  window.onload=startit
</script>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="Save Changes" name="saveBtn"  onclick="saveit()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<br>
<br>
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="800" id="AutoNumber1">
  <tr>
    <td align="center" width="133"><b>Unassigned</b></td>
    <td align="center" width="133">Crest</td>
    <td align="center" width="133">Great Western</td>
    <td align="center" width="133">Millicent</td>
    <td align="center" width="133">Staff</td>
    <td align="center" width="133">Training</td>
  </tr>
  <tr>
    <td width="133">
  <select size="15" name="UnassignedLB" style="font-size: 8pt; width: 133">
  <option value="123456">Rich Nickerbaucker</option>
  <option value="222222" style="color: blue">Andy Peterson</option>
  <option value="333333">Wolfgang Schwurack </option>
  <option value="444444">Bonnie Dixon</option>
  <option value="123321">David Tamowski</option>
  <option value="111222" style="color: red">Debbie Peterson</option>
  <option value="111211">George Felis</option>
  <option value="000111">Herb Lloyd</option>
  <option value="665544">Gary Ren</option>
  <option value="333222">Walt Jahries</option>
  <option value="112255">Wayne Reese</option>
    </select></td>
    <td width="133">
  <select size="15" name="CrestLB" onclick="move_name('Crest',this); return false;" style="font-size: 8pt; width: 133">
  <option value="112233" >TL...Robert Brown</option>
  <option value="554433" style="color: red">ATL..Miles Miya  </option>
  <option>bas..  </option>
  <option>bas..  </option>
  <option>bas..  </option>
  <option>aux..  </option>
  <option value="111222">aux..Sarah Malin-Craft</option>
  <option>aux..  </option>
  </select></td>
    <td width="133">
  <select size="15" name="WesternLB" onclick="move_name('Western',this);" style="font-size: 8pt; width: 133">
  <option value="11234">TL...Robert Benda</option>
  <option>ATL..  </option>
  <option>bas..  </option>
  <option>bas..  </option>
  <option value="123456">xtra.Tom Hilton</option>
  </select></td>
    <td width="133">
  <select size="15" name="MilliLB" style="font-size: 8pt; width: 133" onclick="move_name('Millicent',this);" >
  <option>TL...   </option>
  <option>ATL..  </option>
  <option value="123456">bas..Dave Okubo</option>
  <option value="654321">xtra.Ashley Mckinny</option>
  </select></td>
    <td width="133">
  <select size="15" name="StaffLB" style="font-size: 8pt; width: 133" onclick="move_name('Staff',this);" >
  <option>..... </option>
  <option>..... </option>
  <option value="123456">.....Nancy Pitstick</option>
  <option value="112233">.....Steve Gledhill</option>
  </select></td>
    <td width="133">
  <select size="15" name="TrainingLB" style="font-size: 8pt; width: 133" onclick="move_name('Training',this);" >
  <option>..... </option>
  <option>..... </option>
  <option value="113366">.....David Lund</option>
  </select></td>
  </tr>
  <tr>
    <td align="center" width="133">Add to list --&gt;</td>
    <td align="center" width="133"><input type="button" value="bas" name="B15" onclick="add_bas(1)"><input type="button" value="aux" name="B17" onclick="add_aux(1)"></td>
    <td align="center" width="133"><input type="button" value="bas" name="B19" onclick="add_bas(2)"><input type="button" value="aux" name="B20" onclick="add_aux(2)"></td>
    <td align="center" width="133"><input type="button" value="bas" name="B22" onclick="add_bas(3)"><input type="button" value="aux" name="B23" onclick="add_aux(3)"></td>
    <td align="center" width="133"><input type="button" value="Add" name="B11" onclick="do_add(4)"></td>
    <td align="center" width="133"><input type="button" value="Add" name="B16" onclick="do_add(5)"></td>
  </tr>
  <tr>
    <td width="133" align="center"><b>
  21 Total Patrollers</b></td>
    <td width="133" align="center"></td>
    <td width="133" align="center">&nbsp;</td>
    <td width="133" align="center"></td>
    <td width="133" align="center"></td>
    <td width="133" align="center"></td>
  </tr>
</table>
  Your last operation was: <input type="text" name="lastCommand" disabled size="83" value="&lt;nothing&gt;">
<br><br>
<table border="1" cellpadding="0" cellspacing="0" width="451">
  <tr>
    <td width="447">Client Login:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
      <input type="button" value="2 hour lockout" name="lockoutBtn" onclick="lockout(1)">&nbsp;&nbsp;
      <input type="button" value="Remove lockout" name="noLockoutBtn"onclick="lockout(0)">
    </td>
  </tr>
</table>
</form>

</body>

</html>