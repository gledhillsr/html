function showTimeConflict() {
    alert("<? echo $szTimeConflict; ?>");
}

function insertBtn(patrollerID, areaID) {
    var assID = document.myForm.assignments.selectedIndex;
    if (assID < 0) {
        alert("Error, you must first select a sweep.");
    } else if (!patrollerID) {
        alert("Oops, no patroller ID is defined");
    } else {
        var sel = document.myForm.assignments;
        var sweepID = sel.options[assID].value;
        //      alert("insert  patrollerID="+patrollerID+" sweepID="+sweepID+ " ("+sel.options[assID].text+")");
        var foo = "hill_assignments.php?insert=" + sweepID + "&patrollerID=" + patrollerID + "&areaID=" + areaID;
        alert(foo);
        window.location = foo;
    }
}
function clearBtn(patrollerID, areaID, sweepID) {
    if (!patrollerID) {
        alert("Oops, no patroller ID is defined");
    } else if (sweepID == 0) {
        alert("Oops, no sweep is assigned");
    } else {
        //      alert("clear:  areaID=" + areaID + ", patrollerID="+patrollerID+", sweepID="+sweepID);
        //zzz
        var foo = "hill_assignments.php?clear=" + sweepID + "&patrollerID=" + patrollerID + "&areaID=" + areaID;
        alert(foo);
        window.location = foo;
    }
}

