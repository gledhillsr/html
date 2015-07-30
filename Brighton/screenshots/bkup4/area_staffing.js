var changesMade = false;
var param;
//configure refresh interval (in seconds)
var countDownInterval = 0;

function refreshDocument() {
    if (changesMade) {
        if (window.confirm("Save your changes?")) {
            saveChanges();
        }
    }
    else
        window.location.reload();
}
function cancelChanges() {
    changesMade = false;
    window.location.reload();
}

function checkForChanges() {
    if (changesMade) {
        if (window.confirm("Save your changes?")) {
            saveChanges();
        }
    }
}

function madeChanges() {
    changesMade = true;
    //      document.myForm.saveBtn.disabled = false;
}

// bldParam - build Param string that ONLY consists of valid List box ID's
function bldParam(listbox, area) {
    var count = listbox.length;
    var id,txt;
    var first = true;

    for (var i = 0; i < count; i++) {
        id = listbox.options[i].value;
        if (area == 0)
            txt = "Un";
        else
            txt = listbox.options[i].text.substr(0, 2);
        //alert("parseFloat(id)="+parseFloat(id));

        //alert("i="+i+", id=("+id+"), txt="+txt);
        id = parseFloat(id);
        if (id != NaN && id > 0) {
            if (first) {
                param += "&area" + area + "=" + id + "-" + txt;
                first = false;
            } else
                param += "," + id + "-" + txt;
        }
        //	param += "&_" + area + "_";
    }
}

function saveChanges() {
    changesMade = false;

    param = "area_staffing.php?saveBtn=1";
    bldParam(document.form1.UnassignedLB, 0);
    bldParam(document.form1.CrestLB, 1);
    bldParam(document.form1.SnakeLB, 2);
    bldParam(document.form1.WesternLB, 3);
    bldParam(document.form1.MillicentLB, 4);
    bldParam(document.form1.TrainingLB, 5);
    bldParam(document.form1.StaffLB, 6);
    //alert(param);
    //alert("data=" + param);
    window.location.href = param;
    //alert("now don't you wish SAVE worked :-)");
}

function webStaffing() {

    changesMade = false;

    param = "area_staffing.php?saveBtn=1&showWeb=1";
    bldParam(document.form1.UnassignedLB, 0);
    bldParam(document.form1.CrestLB, 1);
    bldParam(document.form1.SnakeLB, 2);
    bldParam(document.form1.WesternLB, 3);
    bldParam(document.form1.MillicentLB, 4);
    bldParam(document.form1.TrainingLB, 5);
    bldParam(document.form1.StaffLB, 6);
    //alert(param.substr(0,20));
    window.location.href = param;
    //    alert("now don't you wish SAVE worked :-)");
}

function do_add(pos) {
    var listbox;
    //the 'add' only button is only available for column 4 (training) and 5 (staff)
    if (pos == 4)
        listbox = document.form1.TrainingLB
    else
        listbox = document.form1.StaffLB

    var lastPos = listbox.options.length;
    var myNewOption = new Option(".....");
    listbox.options[lastPos] = myNewOption;
    listbox.options.selectedIndex = -1;
}

function del_xtra(pos) {
    var listbox;
    if (pos == 0)
        listbox = document.form1.CrestLB
    else if (pos == 1)
        listbox = document.form1.SnakeLB
    else if (pos == 2)
            listbox = document.form1.WesternLB
        else //3
            listbox = document.form1.MillicentLB

    var lastPos = listbox.options.length;
    //loop through all entries and if a "Xtra." is found, delete it
    var found = false;
    for (var $i = 2; $i < lastPos; $i++) {
        var txt = listbox.options[$i].text;
        if (txt == "Xtra.") {
            found = true;
            listbox.options[$i] = null;
            break;
        }
    }
    if (found == false)
        alert("Oops, no EMPTY 'Xtra.' positions found");
}

function add_xtra(pos) {
    var listbox;
    if (pos == 0)
        listbox = document.form1.CrestLB
    else if (pos == 1)
        listbox = document.form1.SnakeLB
    else if (pos == 2)
            listbox = document.form1.WesternLB
        else // 3
            listbox = document.form1.MillicentLB

    var lastPos = listbox.options.length;
    var myNewOption = new Option("Xtra.");
    listbox.options[lastPos] = myNewOption;
    listbox.options.selectedIndex = -1;
}

function move_name(mountain, listbox) {
    changesMade = true;
    var currIndex = listbox.selectedIndex;
    var currID = listbox.options[currIndex].value;
    var currName = listbox.options[currIndex].text;
    var currTitle = currName.substr(0, 5);
    currName = currName.substr(5);
    var messageStr
    var unAssigned = document.form1.UnassignedLB;
    //  var txtDisplay   = document.form1.lastCommand;
    var unIndex = unAssigned.selectedIndex
    var unID
    var unName
    if (unIndex != -1) {
        unID = unAssigned.options[unIndex].value;
        unName = unAssigned.options[unIndex].text;

        //currTitle ->"bas..", "TL...", "ATL..", etc
        var newLevel = unName.substr(0, 1);	//1 (sr),2(bas),3(aux),4(can...)
        if (newLevel >= 3 && (currTitle == "bas.." || currTitle == "TL..." || currTitle == "ATl..")) {
            alert("Error, invalid assignment.  Must have SR or BAS ski level");
            unAssigned.selectedIndex = -1;	//unselect everything
            listbox.selectedIndex = -1;
            return;
        }
    }

    //  alert("currindex=("+currIndex+")\n unindex=("+unIndex+")\n currID=("+currID+")\n currTitle=("+currTitle+")");
    //alert("(" +currTitle+", " + newLevel + ")");
    if (unIndex == -1 && (currID == null || currID == 0)) {
        messageStr = "Oops, no one is selected as Unassigned";
        listbox.selectedIndex = -1;
    } else {
        if (currID && currID > 0) {
            //
            // there was a patroller in this listbox, so move to "Unassigned"
            //

            messageStr = "Moved '" + currName + "' from " + mountain + " To 'Unassigned' (id=" + currID + ")"
            lastPos = unAssigned.options.length; //this is bogus, I should not subtract a 1
            //add to end of "unassigned" list
            var myNewOption = new Option(currName, currID);
            unAssigned.options[lastPos] = myNewOption;
            unAssigned.options.selectedIndex = lastPos;
            //remove from current list
            listbox.options[currIndex].value = null;
            listbox.options[currIndex].text = currTitle;
            listbox.selectedIndex = -1;
        } else {
            //
            //this position was empty, so move "unassigned" patroller here
            //
            messageStr = "Moved " + unName + " to " + mountain + " (id=" + unID + ")";    //history text
            listbox.options[currIndex].text = currTitle + unName;               //add to new list box
            listbox.options[currIndex].value = unID;
            listbox.selectedIndex = -1;
            unAssigned.options[unIndex] = null;                                //remove from Unassigned
            if (unAssigned.options.length <= unIndex)
                unAssigned.selectedIndex = unIndex - 1;
            else
                unAssigned.selectedIndex = unIndex;
        }
    }
    //display string of what changed
    //    txtDisplay.value = messageStr;
}

function congrats (data) {
    alert("cool");
}
var countDownTime = countDownInterval - 1;

function countUp() {
    countDownTime++;
    //  if (countDownTime > 100){
    //    countDownTime=countDownInterval;
    //    clearTimeout(counter)
    //    window.location.reload()
    //    return
    //  }
    var sec = countDownTime % 60;
    var min = (countDownTime - sec ) / 60;
    var tim = min + ':';
    if (sec < 10)
        tim += '0';
    tim += sec;

    if (document.all) //if IE 4+
        document.all.countDownText.innerText = tim;
    else if (document.getElementById) //else if NS6+
        document.getElementById("countDownText").innerHTML = tim
    else if (document.layers) { //CHANGE TEXT BELOW TO YOUR OWN
            document.c_reload.document.c_reload2.document.write(
                    'Time since last <a href="javascript:refreshDocument()">refresh</a> is <b id="countDownText">' + tim + ' </b> seconds')
            document.c_reload.document.c_reload2.document.close()
        }
    counter = setTimeout("countUp()", 1000);
}

function startit() {
    if (document.all || document.getElementById) //CHANGE TEXT BELOW TO YOUR OWN
        var sec = countDownTime % 60;
    var min = (countDownTime - sec ) / 60;
    var tim = min + ':';
    if (sec < 10)
        tim += '0';
    tim += sec;
    //    document.write('Time since last <a href="javascript:window.location.reload()">refresh</a> is <b id="countDownText">'+tim+'</b>')
    document.write('Time since last <a href="javascript:refreshDocument()">refresh</a> is <b id="countDownText">' + tim + '</b>')
    countUp()
}

if (document.all || document.getElementById) {
    startit();
} else {
    window.onload = function(){
        startit();
    }
}