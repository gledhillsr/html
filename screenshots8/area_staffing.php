<?php 
//area_staffing.php
require("config.php");
    $connect_string = getDBConnection() or die ("Could not connect to the database.");
    $arrDate = getdate();
//$showWeb=0; //hack
//echo "shiftOverride=$shiftOverride<br>";
if (isset($_POST['shiftOverride'])) {
    $shiftOverride = (int)$_POST['shiftOverride'];
} elseif (isset($_GET['shiftOverride'])) {
    $shiftOverride = (int)$_GET['shiftOverride'];
} elseif (isset($_COOKIE['shiftOverride'])) {
    $shiftOverride = (int)$_COOKIE['shiftOverride'];
} else {
    // Check if initRequestVars() set it (fallback)
    $shiftOverride = isset($shiftOverride) ? (int)$shiftOverride : 0;
}

// Validate and handle cookie based on the determined value
if ($shiftOverride > 0 && $shiftOverride <= 8) {
    // Valid override value (1-8) - ensure cookie is set
    if (!isset($_COOKIE['shiftOverride']) || (int)$_COOKIE['shiftOverride'] != $shiftOverride) {
        setcookie("shiftOverride", (string)$shiftOverride, time() + (365 * 24 * 60 * 60), "/");
    }
} else {
    // Invalid value - reset to 0 and clear cookie
    $shiftOverride = 0;
    if (isset($_COOKIE['shiftOverride'])) {
        setcookie("shiftOverride", "", time() - 3600, "/");
        unset($_COOKIE['shiftOverride']);
    }
}

    if($shiftOverride && $shiftOverride != 0) {
//hack hack
//0 => "Use actual time"  ,
//1 => "Saturday 7:45",
//2 => "Saturday 13:45",
//3 => "Sunday 7:45",
//4 => "Monday 7:45",
//5 => "Monday 2:45 pm",
//6 => "Monday 5:45 pm",
//7 => "Monday 6:45pm",
//8 => "Monday 11:45pm");
        $sec=0;
        $min=45;
        switch ($shiftOverride) {
        case 1: $currDayOfWeek = "Saturday";    $hr = 7;   break;
        case 2: $currDayOfWeek = "Saturday";    $hr = 13;  break;
        case 3: $currDayOfWeek = "Sunday";      $hr = 7;   break;
        case 4: $currDayOfWeek = "Monday";      $hr = 7;   break;
        case 5: $currDayOfWeek = "Monday";      $hr = 14;  break;
        case 6: $currDayOfWeek = "Monday";      $hr = 17;  break;
        case 7: $currDayOfWeek = "Monday";      $hr = 18;  break;
        case 8: $currDayOfWeek = "Monday";      $hr = 23;  break;
        default: $currDayOfWeek = "Saturday";   $hr = 7;   break;
        }

    } else {
        $currDayOfWeek = $arrDate['weekday'];
        $sec=$arrDate['seconds'];
        $min=$arrDate['minutes'];
        $hr =$arrDate['hours'];
    }
    $today = getTodayTimestamp();
    $getLBName = [-1 => "Unassigned", 0 => "Crest", 1 => "Snake", 2 => "Western",   3 => "Millicent",  4 => "Training", 5 => "Staff"];
    $strToday = date("F-d-Y", $today);
    if($saveBtn) {
        if($area0)  updateHistory($area0,-1);  //unassigned
        if($area1)  updateHistory($area1,0);  //Crest
        if($area2)  updateHistory($area2,1);  //Snake
        if($area3)  updateHistory($area3,2);  //Western
        if($area4)  updateHistory($area4,3);  //Milli
        if($area5)  updateHistory($area5,4);  //Training
        if($area6)  updateHistory($area6,5);  //Staff
    }
//echo "shiftOverride=" . $shiftOverride . "<br/>";
//only while a testing time override is set - "Use actual time" shows nothing
if ($shiftOverride > 0) {
    echo " currDayOfWeek = " . $currDayOfWeek . " hr=" . $hr . " min=" . $min . "<br/>";
}

    $totalPatrollers = 0;
//============================================================
// --------------- updateHistory  -----------------------------
//============================================================
function updateHistory($str,$area) {
global $connect_string,$today;

//echo "area$area = $str<br>";

    $tok = strtok($str, ",");
    $newSweep="";
    while ($tok) {
//echo "tok=$tok<br>";
        $id = substr($tok,0,6);
		//fixup if patroller has only a 5 diget ID
		if($id[5] < '0' || $id[5] > '9') $id = substr($tok,0,5);
//echo "id=$id<br>";
        $newLeadership = 0;
        if($area >= 0 && $area <= 3 ) {   // 
            $where = substr($tok,7,2);
            if($where == "TL")      $newLeadership = 1;
            else if($where == "AT") $newLeadership = 2;
            else if($where == "Xt") $newLeadership = 3;
            else                    $newLeadership = 0;
        }
        $query_string = "SELECT * FROM skihistory WHERE shift=0 AND date=$today AND patroller_id=$id";

//echo "$query_string<br>";
//        $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query ");
        $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result 1)");
//echo "result=$result<br>";
        if ($row = @mysqli_fetch_array($result)) {
//echo "row=$row<br>";
            $oldLeadership = $row['teamLead'];
            $oldArea = $row['areaID'];
            $oldSweep = $row['sweep_ids'];
            $history_id =  $row['history_id'];
//echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;leadership=($oldLeadership, $newLeadership)  area($oldArea, $area)<br>";
            if($oldArea != $area)
                $newSweep = "";
            else
                $newSweep = $oldSweep;
            if($oldLeadership != $newLeadership || $oldArea != $area) {
                $query_string = "UPDATE skihistory SET sweep_ids=\"$newSweep\", areaID=$area, teamLead=$newLeadership  WHERE history_id=$history_id";
//echo "$query_string (id=$id)<br>";
                @mysqli_query($connect_string, $query_string) or die ("Invalid query (result 2)");
            }
        }
       $tok = strtok(",");
    }
//echo "leaving<br>";
}
//============================================================
//----------------- webColumn --------------------------------
// The extra right-hand column behind "Display Staffing from Web!".
//
// Lists today's sign-ups from the production web site next to the locker-room
// board, marking who has actually shown up.  Read-only: the rows are not
// draggable and the live poller leaves this column alone.
//
// This used to open a MySQL connection straight to gledhills.com, which cannot
// work - port 3306 there is firewalled - and which would have handed this
// machine raw SQL access to production.  It now calls a JSON endpoint
// (LockerRoomApiController in the patrolCalendar Java app) over HTTPS, which
// needs no database credentials here at all.
//
// $local stays open throughout; the old version closed it here, which broke
// everything rendered after this point.
//============================================================
function webColumn($local, $today, $getLBName) {
    global $lockerApiUrl, $lockerApiKey, $lockerResort;

    //who is signed in at the locker room today
    $lockerIDList = [];
    $result = @mysqli_query($local, "SELECT * FROM skihistory WHERE date=\"$today\" AND shift=0");
    if ($result) {
        while ($row = @mysqli_fetch_array($result)) {
            $lockerIDList[$row['patroller_id']] = $getLBName[$row['areaID']] . " " . $row['name'];
        }
        @mysqli_free_result($result);
    }

    $out  = "  <td valign=\"top\">\n";
    $out .= "  <div class=\"lb webcol\" id=\"WebLB\">\n";

    if (empty($lockerApiUrl) || empty($lockerApiKey)) {
        $out .= webRow("Web lookup is not configured", "err");
        $out .= webRow("set \$lockerApiUrl and \$lockerApiKey in dbconfig.php", "err");
        return $out . "  </div>\n</td>\n";
    }

    $url = rtrim($lockerApiUrl, "/") . "/assignments?resort=" . rawurlencode($lockerResort) .
           "&date=" . rawurlencode(date("Y-m-d", $today));
    $answer = webFetch($url, $lockerApiKey);

    if ($answer['error'] !== "") {
        $out .= webRow("Cannot reach the web site", "err");
        $out .= webRow($answer['error'], "err");
        return $out . "  </div>\n</td>\n";
    }
    $data = json_decode($answer['body'], true);
    if (!is_array($data) || !isset($data['slots'])) {
        $msg = (is_array($data) && isset($data['error'])) ? $data['error'] : "unreadable answer from the web site";
        $out .= webRow("HTTP " . $answer['status'] . ": " . $msg, "err");
        return $out . "  </div>\n</td>\n";
    }

    $webIDList = [];
    foreach ($data['slots'] as $slot) {
        $pid  = isset($slot['patrollerId']) ? $slot['patrollerId'] : "0";
        $time = isset($slot['startTime']) ? $slot['startTime'] : "";
        if ($pid === "0" || $pid === "" || $pid === null) {
            $out .= webRow($time . " -- EMPTY", "empty");
            continue;
        }
        $webIDList[$pid] = $pid;
        //"-" means they are already signed in downstairs, "??" means not yet
        $status = array_key_exists($pid, $lockerIDList) ? "-" : "??";
        $who = (isset($slot['name']) && $slot['name'] !== null) ? $slot['name'] : "patroller $pid not found";
        $out .= webRow($time . " " . $status . " " . $who, "");
    }

    //anyone in the locker room who is not on the web schedule at all
    foreach ($lockerIDList as $k => $v) {
        if (!array_key_exists($k, $webIDList)) {
            $out .= webRow("xx " . $v, "extra");
        }
    }
    if (count($data['slots']) == 0) {
        $out .= webRow("No web assignments for " . date("Y-m-d", $today), "empty");
    }

    return $out . "  </div>\n</td>\n";
}

//A web column row.  No data-id and no draggable attribute, so wireColumns()
//and the drag handlers never pick these up - this column is display only.
function webRow($text, $kind) {
    $cls = "lbrow webrow" . ($kind !== "" ? " web$kind" : "");
    return "    <div class=\"$cls\">" . htmlspecialchars($text) . "</div>\n";
}

//============================================================
//----------------- lbRow ------------------------------------
// One row of a column.  Occupied rows are draggable; empty ones are drop
// targets.  data-label keeps the slot title ("Bas..", "aux..", "TL..") apart
// from the patroller name so neither has to be recovered by substr() later.
//============================================================
function lbRow($label, $name, $id) {
    $cls  = ($id === "" || $id === null) ? "lbrow empty" : "lbrow";
    $drag = ($id === "" || $id === null) ? "" : " draggable=\"true\"";
    return "<div class=\"$cls\"$drag data-id=\"" . htmlspecialchars((string)$id, ENT_QUOTES) .
           "\" data-label=\"" . htmlspecialchars($label, ENT_QUOTES) .
           "\" data-name=\"" . htmlspecialchars($name, ENT_QUOTES) . "\">" .
           htmlspecialchars($label . $name) . "</div>";
}
//============================================================
//----------------- insertRow --------------------------------
//============================================================
function insertRow($tl,$minRows,$skiLevel){ //skiLevel, 1=basic/sr, 2=aux/SrA
global $connect_string,$areaID,$getLBName,$today,$totalPatrollers,$currDayOfWeek;
    if($areaID == -1)
        $query_string = "SELECT * FROM skihistory WHERE date=\"$today\" AND shift=0 AND areaID=\"-1\" ORDER BY checkin";
    else
        $query_string = "SELECT * FROM skihistory WHERE date=\"$today\" AND shift=0 AND areaID=\"$areaID\" and teamLead=$tl ORDER BY checkin";
//echo "\n------\ninsertRow($tl,$minRows,$skiLevel)\n$query_string" . "<br>\n";
    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result 3)");
    $cnt = 0;
//Loop in skihistory for today, shift 0, AND this area
    while ($row = @mysqli_fetch_array($result)) {
        $patroller_id  = $row['patroller_id'];
        $patroller_name = "Name not found.";
        //get Roster information
        $row2 = getPatrollerInfo($connect_string, $patroller_id);
        if ($row2) {
            $class = $row2['ClassificationCode'];
//echo "totalPatroller = $totalPatroller, class=$class<br>";
            $prefix = getClassificationPrefix($class);
            $patroller_name = $prefix . $row2['FirstName'] . " " . $row2['LastName'];
            //if I am filling out basic, and I get an Aux (or visa-versa) skip this person
            $bas = ($class == "BAS" || $class == "SR");
            if($bas && $skiLevel == 2) continue;
            if(!$bas && $skiLevel == 1) continue;
        }
//echo $patroller_name . "<br>";
        $totalPatrollers++;
        $teamLead = $row['teamLead'];
        $cnt++;
        if($areaID == -1) {
            echo "    " . lbRow("", $patroller_name, $patroller_id) . "\n";
        } else {
            if($areaID > 3)        $pos = ".....";
            else if($teamLead == 1) $pos="TL...";
            else if($teamLead == 2) $pos="ATl..";
            else if($teamLead == 3) $pos="Xtra.";
            else if($skiLevel == 1 && $minRows >= $cnt) $pos="Bas..";   //bas.. aux.. xtra.
            else $areaID . $pos="aux..";   //bas.. aux.. xtra.
//            else if($class == "SR" || $class == "BAS") $pos="bas..";   //bas.. aux.. xtra.
//            else $pos="aux..";   //bas.. aux.. xtra.
            echo "  " . lbRow($pos, $patroller_name, $patroller_id) . "\n";
        }
    } //end loop for each patroller
   	if($currDayOfWeek != "Saturday" && $currDayOfWeek != "Sunday") 
		return 0;


	//display blanks to fill up to $minRows
    for($i = $cnt; $i < $minRows; $i++) {
        if($tl == 1)            $pos = "TL..";
        else if($tl == 2)       $pos = "ATl..";
        else if($tl == 3)       $pos = "Xtra.";
        else if($skiLevel == 1) $pos = "bas.."; //bas or sr
        else if($skiLevel == 2) $pos = "aux.."; //aux
        else                    $pos = "....."; //extra
        echo "  " . lbRow($pos, "", "") . "\n";
    }
	if ($cnt > $minRows) 
		return ($cnt - $minRows);
	return 0;
}
//this kills the display
//<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
//<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
//<!DOCTYPE HTML>
?>
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache">
<meta HTTP-EQUIV="Expires" CONTENT="-1">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Unassigned</title>

<?php require("patrol_dialog.php"); ?>
<script type="text/JavaScript">
var changesMade = false;
var param;
function madeChanges() {
    changesMade = true;
    //      document.myForm.saveBtn.disabled = false;
}

// bldParam - build Param string that ONLY consists of valid patroller ID's
function bldParam(col, area) {
    var rows = rowsOf(col);
    var id, txt, i;
    var first = true;

    for (i = 0; i < rows.length; i++) {
        id = parseFloat(rowId(rows[i]));
        if (!(id > 0)) {
            continue;                       //empty slot
        }
        //the first 2 characters of the slot title are what updateHistory()
        //reads back as leadership: TL=lead, AT=assistant, Xt=extra
        txt = (area == 0) ? "Un" : rowLabel(rows[i]).substr(0, 2);
        if (first) {
            param += "&area" + area + "=" + id + "-" + txt;
            first = false;
        } else {
            param += "," + id + "-" + txt;
        }
    }
}

//Build the save URL from what is on screen.  Area numbers stay as they were
//(1=Crest .. 6=Staff) so updateHistory() keeps mapping them the same way.
function collectParam(extra) {
    param = "area_staffing.php?saveBtn=1" + (extra ? extra : "");
    bldParam(colOf("Crest"), 1);
    bldParam(colOf("Snake"), 2);
    bldParam(colOf("Western"), 3);
    bldParam(colOf("Millicent"), 4);
    bldParam(colOf("Training"), 5);
    bldParam(colOf("Staff"), 6);
    return param;
}

function setSaveState(msg, bad) {
    var el = document.getElementById("saveState");
    if (!el) {
        return;
    }
    el.innerHTML = "";
    el.appendChild(document.createTextNode(msg));
    el.className = bad ? "savebad" : "saveok";
}

function clockNow() {
    var d = new Date(), m = d.getMinutes(), s2 = d.getSeconds();
    return d.getHours() + ":" + (m < 10 ? "0" : "") + m + ":" + (s2 < 10 ? "0" : "") + s2;
}

//Saved after every move.  The board is not reloaded - the request goes out in
//the background so the columns stay exactly where they are.
function autoSave() {
    var url = collectParam("");
    setSaveState("Saving...");
    saveInFlight++;
    var xhr = new XMLHttpRequest();
    xhr.open("GET", url, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState != 4) {
            return;
        }
        saveInFlight--;
        if (xhr.status >= 200 && xhr.status < 300) {
            changesMade = false;
            setSaveState("Saved " + clockNow());
        } else {
            if (!pageLeaving) {
                setSaveState("SAVE FAILED (" + xhr.status + ") - reload the page and try again", true);
            }
        }
    };
    xhr.send(null);
}

function webStaffing() {
    changesMade = false;
    window.location.href = collectParam("&showWeb=1");
}

function xtraCol(pos) {
    return colOf(["Crest", "Snake", "Western", "Millicent", "Training", "Staff"][pos]);
}

function newRow(col, label) {
    var row = document.createElement("div");
    row.className = "lbrow empty";
    row.setAttribute("data-id", "");
    row.setAttribute("data-label", label);
    row.setAttribute("data-name", "");
    row.appendChild(document.createTextNode(label));
    col.appendChild(row);
    wireRow(col, row);                      //new rows must accept drops too
    return row;
}

function do_add(pos) {
    //the 'add' only button is only available for column 4 (training) and 5 (staff)
    newRow(xtraCol(pos), ".....");
    clearSel();
}

function del_xtra(pos) {
    var col = xtraCol(pos);
    var rows = rowsOf(col);
    for (var i = rows.length - 1; i >= 0; i--) {
        if (rowLabel(rows[i]) == "Xtra." && rowIsEmpty(rows[i])) {
            col.removeChild(rows[i]);
            return;
        }
    }
    patrolAlert("Oops, no EMPTY 'Xtra.' positions found");
}

function add_xtra(pos) {
    newRow(xtraCol(pos), "Xtra.");
    clearSel();
}

//============================================================
// Moving people between areas
//------------------------------------------------------------
// Two ways to move somebody, and they work together:
//
//   Drag  - drag a name straight onto an open slot in any column.  Every slot
//           that will accept them lights up green while you drag.
//   Click - click a name to pick it up in place, then click the open slot you
//           want it in.  Clicking it again puts it back down.
//
// Either way the move is saved immediately in the background - there is no
// Save button and no Unassigned column any more.
//
// Each column is a div with class "lb" and id "<area>LB", holding one child
// div of class "lbrow" per slot.  A row carries data-id (the patroller id,
// empty when the slot is free), data-label (the slot title) and data-name.
//============================================================
var AREA_KEYS = ["Crest", "Snake", "Western", "Millicent", "Training", "Staff"];

function colOf(key)  { return document.getElementById(key + "LB"); }
function areaOfCol(col) { return col.getAttribute("data-area"); }

function rowsOf(col) {
    var out = [], kids = col.childNodes, i;
    for (i = 0; i < kids.length; i++) {
        if (kids[i].nodeType == 1 && kids[i].className.indexOf("lbrow") === 0) {
            out.push(kids[i]);
        }
    }
    return out;
}

function rowId(row)    { return row.getAttribute("data-id") || ""; }
function rowLabel(row) { return row.getAttribute("data-label") || ""; }
function rowName(row)  { return row.getAttribute("data-name") || ""; }
function rowIsEmpty(row) { return !(parseFloat(rowId(row)) > 0); }

function setRow(row, id, name) {
    row.setAttribute("data-id", id);
    row.setAttribute("data-name", name);
    row.innerHTML = "";
    row.appendChild(document.createTextNode(rowLabel(row) + name));
    if (id === "") {
        row.className = "lbrow empty";
        row.removeAttribute("draggable");
    } else {
        row.className = "lbrow";
        row.setAttribute("draggable", "true");
    }
}

function slotKind(label) {
    var l = label.toLowerCase();
    if (l.indexOf("atl") === 0)  return "ATL";
    if (l.indexOf("tl.") === 0)  return "TL";
    if (l.indexOf("bas") === 0)  return "BAS";
    if (l.indexOf("aux") === 0)  return "AUX";
    if (l.indexOf("xtra") === 0) return "XTRA";
    return "OTHER";                     //"....." - Training and Staff rows
}

//names are prefixed by getClassificationPrefix(): 1=SR, 2=BAS, 3=AUX/SRA, 4=other
function levelOf(name) {
    var n = parseInt(name.substr(0, 1), 10);
    return isNaN(n) ? 4 : n;
}

//The original rule, but it now also covers the empty "TL.." row, which used to
//slip past a test that looked for a 5-character "TL...".
function canOccupy(level, kind) {
    if (level >= 3) {
        return (kind == "AUX" || kind == "XTRA" || kind == "OTHER");
    }
    return true;
}

function canDropOn(level, row) {
    return rowIsEmpty(row) && canOccupy(level, slotKind(rowLabel(row)));
}

//------------------------------------------------------------ selection (click)
var selRow = null;                       //the row currently highlighted

function clearSel() {
    if (selRow) {
        selRow.className = selRow.className.replace(/ sel\b/, "");
        selRow = null;
    }
}

function selectRow(row) {
    clearSel();
    selRow = row;
    row.className += " sel";
}

//------------------------------------------------------------ the move itself
function placeInto(row, id, name, fromRow) {
    setRow(fromRow, "", "");             //the slot they came from stays, empty
    setRow(row, id, name);
    changesMade = true;
    autoSave();
}

//------------------------------------------------------------ click handling
// With the Unassigned column gone there is nowhere to park someone, so a click
// picks a patroller up in place (highlighted) and the next click on an open
// slot moves them there.  Clicking them again puts them back down.
function rowClick(row) {
    if (!rowIsEmpty(row)) {
        if (selRow == row) {
            clearSel();
        } else {
            selectRow(row);
        }
        return;
    }
    if (!selRow || rowIsEmpty(selRow)) {
        return;                          //nothing picked up yet
    }
    if (!canOccupy(levelOf(rowName(selRow)), slotKind(rowLabel(row)))) {
        patrolAlert("Error, invalid assignment.  Must have SR or BAS ski level");
        clearSel();
        return;
    }
    var from = selRow;
    clearSel();
    placeInto(row, rowId(from), rowName(from), from);
}

//------------------------------------------------------------ drag and drop
var dragRow = null;

function markTargets(on, level) {
    for (var a = 0; a < AREA_KEYS.length; a++) {
        var col = colOf(AREA_KEYS[a]);
        if (!col) {
            continue;
        }
        var rows = rowsOf(col);
        for (var i = 0; i < rows.length; i++) {
            var r = rows[i];
            r.className = r.className.replace(/ (ok|no)\b/g, "");
            if (on && rowIsEmpty(r)) {
                r.className += canDropOn(level, r) ? " ok" : " no";
            }
        }
    }
}

function onDragStart(e, row) {
    dragRow = row;
    clearSel();
    row.className += " dragging";
    if (e.dataTransfer) {
        e.dataTransfer.effectAllowed = "move";
        e.dataTransfer.setData("text/plain", rowId(row));   //Firefox needs a payload
    }
    markTargets(true, levelOf(rowName(row)));
}

function onDragEnd(row) {
    markTargets(false, 0);
    row.className = row.className.replace(/ dragging\b/, "");
    dragRow = null;
}

//returning false from dragover is what tells the browser a drop is allowed here
function onDragOver(e, row, col) {
    if (!dragRow) {
        return true;
    }
    var ok = (row && canDropOn(levelOf(rowName(dragRow)), row));
    if (!ok) {
        return true;
    }
    if (e.preventDefault) {
        e.preventDefault();
    }
    if (e.dataTransfer) {
        e.dataTransfer.dropEffect = "move";
    }
    return false;
}

function onDrop(e, row, col) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    if (!dragRow) {
        return false;
    }
    if (row && canDropOn(levelOf(rowName(dragRow)), row)) {
        placeInto(row, rowId(dragRow), rowName(dragRow), dragRow);
    }
    return false;
}

//------------------------------------------------------------ wiring
function wireRow(col, r) {
    r.ondragover = function (e) { return onDragOver(e || window.event, r, col); };
    r.ondrop     = function (e) { return onDrop(e || window.event, r, col); };
}

function wireColumns() {
    for (var a = 0; a < AREA_KEYS.length; a++) {
        var col = colOf(AREA_KEYS[a]);
        if (!col) {
            continue;
        }
        (function (col) {
            col.ondragover = function (e) { return onDragOver(e || window.event, null, col); };
            col.ondrop     = function (e) { return onDrop(e || window.event, null, col); };
            col.onclick = function (e) {
                e = e || window.event;
                var t = e.target || e.srcElement;
                while (t && t != col && t.className.indexOf("lbrow") !== 0) {
                    t = t.parentNode;
                }
                if (t && t != col) {
                    rowClick(t);
                }
            };
            col.ondragstart = function (e) {
                e = e || window.event;
                var t = e.target || e.srcElement;
                if (t && t.className.indexOf("lbrow") === 0 && !rowIsEmpty(t)) {
                    onDragStart(e, t);
                    return true;
                }
                return false;
            };
            col.ondragend = function () { if (dragRow) { onDragEnd(dragRow); } };
            var rows = rowsOf(col), i;
            for (i = 0; i < rows.length; i++) {
                wireRow(col, rows[i]);
            }
        })(col);
    }
}


function congrats (data) {
    patrolAlert("cool");
}
//============================================================
// Live board
//------------------------------------------------------------
// The morning login screen in the other room writes straight to skihistory,
// so this page re-reads the board every few seconds and swaps in any column
// that changed.  New logins appear on their own; there is no refresh button.
//
// It polls rather than holding a stream open on purpose: this runs under
// "php -S", which serves one request at a time, so an open EventSource or a
// long-poll would block the login screen from being served at all.
//============================================================
var POLL_MS = 4000;
var pollBusy = false;
var saveInFlight = 0;
var deferredPolls = 0;
var pollFailures = 0;
var liveWarned = false;
var pageLeaving = false;

//Navigating away aborts any in-flight XHR, which surfaces as readyState 4 with
//status 0 - indistinguishable from a dead server unless we notice we are leaving.
//Clicking "Display Staffing from Web!" navigates, so without this the board
//flashed "server unreachable (0)" on the way out.
window.onbeforeunload = function () {
    pageLeaving = true;
};

function setLiveState(msg, bad) {
    var el = document.getElementById("liveState");
    if (!el) {
        return;
    }
    el.innerHTML = "";
    el.appendChild(document.createTextNode(msg));
    el.className = bad ? "savebad" : "";
}

//Parse the fetched page with the browser's own HTML parser and pull the
//columns out by id.  This used to be done with a regex over the raw text,
//which was a mistake: script and comment text in the page can contain markup
//that looks exactly like a column, and the regex happily matched it and
//pasted JavaScript source into the board.  A real parse cannot do that -
//text inside <script> is not markup.  Nothing in the parsed document runs.
function parseBoard(html) {
    if (typeof DOMParser == "undefined") {
        return null;
    }
    return new DOMParser().parseFromString(html, "text/html");
}

//A cheap fingerprint of who is sitting where, so an unchanged column is left
//alone instead of being rebuilt (which would throw away its scroll position).
function sigFromDom(col) {
    var rows = rowsOf(col), out = [], i;
    for (i = 0; i < rows.length; i++) {
        out.push(rowId(rows[i]) + ":" + rowLabel(rows[i]));
    }
    return out.join("|");
}

function applyBoard(html) {
    var doc = parseBoard(html);
    if (!doc) {
        return;
    }
    var changed = 0;
    for (var a = 0; a < AREA_KEYS.length; a++) {
        var key = AREA_KEYS[a];
        var col = colOf(key);
        var fresh = doc.getElementById(key + "LB");
        if (!col || !fresh) {
            continue;
        }
        //only trust a node that really is a column of rows
        if (fresh.className != "lb" || sigFromDom(fresh) == sigFromDom(col)) {
            continue;                       //nobody moved in this column
        }
        var top = col.scrollTop;
        col.innerHTML = fresh.innerHTML;
        col.scrollTop = top;
        var rows = rowsOf(col), i;
        for (i = 0; i < rows.length; i++) {
            wireRow(col, rows[i]);
        }
        changed++;
    }
    if (changed > 0) {
        setLiveState("Updated " + clockNow());
    }
}

function pollBoard() {
    if (pollBusy) {
        return;
    }
    //never redraw out from under someone who is in the middle of a move
    if (dragRow || saveInFlight > 0) {
        return;
    }
    if (selRow) {
        deferredPolls++;
        if (deferredPolls < 4) {
            return;                         //give them a few seconds to finish
        }
        clearSel();                         //left selected - stop holding updates
    }
    deferredPolls = 0;
    pollBusy = true;
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "area_staffing.php?_=" + (new Date()).getTime(), true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState != 4) {
            return;
        }
        pollBusy = false;
        if (xhr.status < 200 || xhr.status >= 300) {
            if (pageLeaving) {
                return;                     //aborted because we are navigating, not a failure
            }
            //one missed poll is not worth alarming over - a reload, a blip, a
            //momentarily busy single-threaded server.  Say something once it persists.
            pollFailures++;
            if (pollFailures >= 3) {
                setLiveState("Not updating - server unreachable (" + xhr.status + ")", true);
                liveWarned = true;
            }
            return;
        }
        pollFailures = 0;
        if (liveWarned) {
            //applyBoard() only writes the status line when a column actually changed, so
            //without this the red warning would sit there for good once the server came back
            setLiveState(LIVE_READY);
            liveWarned = false;
        }
        if (dragRow || saveInFlight > 0) {
            return;                         //answer came back mid-move; try next tick
        }
        applyBoard(xhr.responseText);
    };
    xhr.send(null);
}

var LIVE_READY = "Live - new logins appear automatically";

function startit() {
    document.write('<span id="liveState">Live &#8211; new logins appear automatically</span>');
}

function startLive() {
    setInterval(pollBoard, POLL_MS);
}

if (document.all || document.getElementById) {
    startit();
} else {
    window.onload = function () {
        startit();
    };
}
</script>
</head>

<body onunload="checkForChanges();" background="images/ncmnthbk.jpg">
<form name="form1" method="POST" id="form1" action="area_staffing.php">

<style type="text/css">
/* the seven columns - these used to be <select size=25> listboxes */
.lb {
    width: 133px;
    height: 340px;
    overflow-y: auto;
    overflow-x: hidden;
    border: 1px solid #7F9DB9;
    background: #FFFFFF;
    font-size: 8pt;
    font-family: Arial, Helvetica, sans-serif;
    text-align: left;
}
.lbrow {
    padding: 1px 2px;
    white-space: nowrap;
    overflow: hidden;
    line-height: 1.25;
    cursor: default;
}
.lbrow:not(.empty)  { cursor: move; }      /* an occupied row can be dragged */
.lbrow.empty        { color: #808080; }
.lbrow.sel          { background: #316AC5; color: #FFFFFF; }
.lbrow.dragging     { opacity: 0.4; }
/* while dragging: green = this slot will take them, grey = it will not */
.lbrow.ok           { background: #CCF0CC; outline: 1px dashed #2E7D32; color: #000; }
.lbrow.no           { background: #EFEFEF; color: #B0B0B0; }
/* the read-only web column - no drag, no drop, tinted so it reads as separate */
.lb.webcol          { background: #F7F7F0; }
.lbrow.webrow       { cursor: default; }
.lbrow.webempty     { color: #909090; font-style: italic; }
.lbrow.webextra     { color: #14507A; }
.lbrow.weberr       { color: #C00000; white-space: normal; }
#saveState          { font-family: Arial, Helvetica, sans-serif; font-size: 13px; }
.saveok             { color: #2E7D32; }
.savebad            { color: #C00000; font-weight: bold; }
</style>

<br>
<br>
<?php /*===================== table ================================*/ ?>
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" width="800" id="AutoNumber1">
  <tr>
    <td align="center" width="133">Crest</td>
    <td align="center" width="133">Snake Creek</td>
    <td align="center" width="133">Great Western</td>
    <td align="center" width="133">Millicent</td>
    <td align="center" width="133">Training</td>
    <td align="center" width="133">Staff</td>
<?php
	if($showWeb)
		echo "    <td align=center width=133>Web Assignments</td>\n";
?>
  </tr>
  <tr>
<?php
  for($areaID = 0; $areaID <= 5; $areaID++) {
//Display "TL..." or "ATl..." UNLESS training, or Staff
    echo "<td>\n";
    if($areaID > 3) {
      echo "  <div class=\"lb\" id=\"{$getLBName[$areaID]}LB\" data-area=\"{$getLBName[$areaID]}\">\n";
      insertRow(0,3,0); //(tl)  1st arg - tl=1, 2=atl, 0=other
    } else {

//------
    $query_string = "SELECT * FROM areadefinitions WHERE areaID=$areaID";
    $result = @mysqli_query($connect_string, $query_string) or die ("Invalid query (result 5)");
    $todayBasic = 0;
    $todayAux = 0;
    if ($row = @mysqli_fetch_array($result)) {
//echo "$currDayOfWeek<br>\n";
        if($currDayOfWeek == "Saturday") {
            $todayBasic=$row['saturdaybasic'];
//echo "basic=$todayBasic<br>\n";
            $todayAux=$row['saturdayaux'];
        } else if ($currDayOfWeek == "Sunday") {
            $todayBasic=$row['sundaybasic'];
            $todayAux=$row['sundayaux'];
        } else {
            $todayBasic=0;
            $todayAux=0;
        }
//      $SunBas = $row[sundaybasic];
//      $SunAux = $row[sundayaux];
//      if($row[open] == 1) {
//    }
    }
//------
//echo "basic=$todayBasic<br>\n";

      echo "  <div class=\"lb\" id=\"{$getLBName[$areaID]}LB\" data-area=\"{$getLBName[$areaID]}\">\n";
      insertRow(1,1,0); //(tl)  1st arg - tl=1, 2=atl, 0=other, 3=Extra
      insertRow(2,1,0); //(atl) 2nd arg - Minimum number of rows to display
//echo "insertRow, basic=$todayBasic<br>\n";
      $extras = insertRow(0,$todayBasic,1); //(bas) 3rd arg - ski level, 1=basic or Senior, 2=Auxilary, 3=Extra
//echo "basic=$todayBasic<br>\n";
	  $todayAux -= $extras;
//echo "insertRow, aux=$todayBasic<br>\n";
	  if($todayAux > 0)
	      insertRow(0,$todayAux,2); //(aux)
      insertRow(3,0,0); //extra
    }
    echo "  </div>\n</td>\n";
  } //end loop for areas

//========= show web column ========
	if($showWeb) {
		echo webColumn($connect_string, $today, $getLBName);
	}  //end showWeb


?>
  </tr>
  <tr>
    <td align="center" width="133"><input type="button" value="Xtra" name="B15" onclick="add_xtra(0)"><input type="button" value="Del" name="B17" onclick="del_xtra(0)"></td>
    <td align="center" width="133"><input type="button" value="Xtra" name="B19" onclick="add_xtra(1)"><input type="button" value="Del" name="B20" onclick="del_xtra(1)"></td>
    <td align="center" width="133"><input type="button" value="Xtra" name="B22" onclick="add_xtra(2)"><input type="button" value="Del" name="B23" onclick="del_xtra(2)"></td>
    <td align="center" width="133"><input type="button" value="Xtra" name="B22" onclick="add_xtra(3)"><input type="button" value="Del" name="B233" onclick="del_xtra(3)"></td>
    <td align="center" width="133"><input type="button" value="Add" name="B11" onclick="do_add(4)"></td>
    <td align="center" width="133"><input type="button" value="Add" name="B16" onclick="do_add(5)"></td>
  </tr>
</table>

  <?php echo "<b>$totalPatrollers"; ?> Total Patrollers</b><br>
 
  <span id="saveState">Every move is saved automatically.</span>
  &nbsp;&nbsp;&nbsp;&nbsp;
  <input type="button" value="Display Staffing from Web!" onclick="webStaffing()">

</form>
<script type="text/JavaScript">
    wireColumns();
    startLive();
</script>
<HR>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=3>Ski Level: 1=SR, 2=BAS, 3=AUX, 4=Other&nbsp;&nbsp;&nbsp;&nbsp;Today is: <?php echo "$currDayOfWeek $strToday"; ?></font>
<?php
    @mysqli_close($connect_string);
    @mysqli_free_result($result);
?>
</body>

</html>
