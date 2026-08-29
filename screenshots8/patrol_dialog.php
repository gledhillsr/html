<?php
/*
 * patrol_dialog.php - centered stand-in for the browser's native alert()/confirm().
 *
 * Chrome pins native dialogs to the top edge of the window; these are drawn in
 * the middle of the viewport instead, and let the buttons be labelled.
 *
 * Include once inside <head>:   <?php require("patrol_dialog.php"); ?>
 *
 *   patrolAlert("message")                            one OK button
 *   patrolConfirm("question", "Yes", "No", callback)  callback(true|false)
 *   patrolPrompt("question", "", callback, "text")    callback(string) or callback(null) on Cancel
 *
 * NOTE: patrolConfirm and patrolPrompt are ASYNCHRONOUS. They return immediately and call the
 * callback when the user picks a button, so it cannot be dropped into
 * "if (confirm(...)) { ... }" the way window.confirm() could - the code that
 * followed the old confirm has to move inside the callback.
 */
?>
<style type="text/css">
#patrolDlgMask {
    display: none;
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: #000;
    background: rgba(0, 0, 0, 0.45);
    z-index: 1000;
}
#patrolDlgBox {
    position: absolute;
    left: 50%;
    top: 50%;
    -webkit-transform: translate(-50%, -50%);
    -ms-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
    min-width: 260px;
    max-width: 420px;
    padding: 24px 28px 20px 28px;
    background: #fff;
    border: 2px solid #111;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.4);
    font-family: Arial, Helvetica, sans-serif;
    text-align: center;
}
#patrolDlgMsg {
    font-size: 18px;
    line-height: 1.4;
    color: #000;
    margin-bottom: 22px;
}
#patrolDlgInput {
    font-family: inherit;
    font-size: 16px;
    padding: 6px 8px;
    width: 90%;
    margin: -8px 0 20px 0;
}
.patrolDlgBtn {
    font-family: inherit;
    font-size: 16px;
    padding: 8px 10px;
    margin: 0 8px;
    min-width: 96px;
    cursor: pointer;
}
</style>
<script language="JavaScript">
<!--
var patrolDlg = (function () {
    var mask = null, box = null, msgEl = null, inputEl = null, btnWrap = null, escBtn = null;

    function build() {
        mask = document.createElement("div");
        mask.id = "patrolDlgMask";
        box = document.createElement("div");
        box.id = "patrolDlgBox";
        msgEl = document.createElement("div");
        msgEl.id = "patrolDlgMsg";
        inputEl = document.createElement("input");
        inputEl.id = "patrolDlgInput";
        inputEl.style.display = "none";
        btnWrap = document.createElement("div");
        box.appendChild(msgEl);
        box.appendChild(inputEl);
        box.appendChild(btnWrap);
        mask.appendChild(box);
        document.body.appendChild(mask);
    }

    function onKey(e) {
        e = e || window.event;
        if (e.keyCode == 27 && escBtn) {   //Escape answers with the right-hand button
            escBtn.onclick();
        }
    }

    function close() {
        escBtn = null;
        if (document.removeEventListener) {
            document.removeEventListener("keydown", onKey, false);
        }
        mask.style.display = "none";
    }

    //buttons is an array of {label: "Yes", action: function () {...}}
    //input is optional: {type: "text", value: ""} shows an entry field
    function open(message, buttons, input) {
        if (!mask) {
            build();
        }
        while (msgEl.firstChild) {
            msgEl.removeChild(msgEl.firstChild);
        }
        msgEl.appendChild(document.createTextNode(message));
        while (btnWrap.firstChild) {
            btnWrap.removeChild(btnWrap.firstChild);
        }
        var first = null, i, el;
        for (i = 0; i < buttons.length; i++) {
            el = document.createElement("button");
            el.type = "button";              //not "submit" - this sits outside the form
            el.className = "patrolDlgBtn";
            el.appendChild(document.createTextNode(buttons[i].label));
            el.onclick = (function (action) {
                return function () {
                    close();
                    action();
                };
            })(buttons[i].action);
            btnWrap.appendChild(el);
            if (first === null) {
                first = el;
            }
            escBtn = el;                     //ends up as the last button
        }
        if (input) {
            inputEl.type = input.type || "text";
            inputEl.value = input.value || "";
            inputEl.style.display = "";
        } else {
            inputEl.style.display = "none";
        }
        mask.style.display = "block";
        if (document.addEventListener) {
            document.addEventListener("keydown", onKey, false);
        }
        if (input && inputEl.focus) {
            inputEl.focus();                 //type straight into the field
        } else if (first && first.focus) {
            first.focus();                   //so Enter picks the first choice
        }
    }

    return { open: open, value: function () { return inputEl ? inputEl.value : ""; } };
})();

function patrolAlert(message, after) {
    patrolDlg.open(message, [
        { label: "OK", action: function () { if (after) { after(); } } }
    ]);
}

function patrolConfirm(message, yesLabel, noLabel, callback) {
    patrolDlg.open(message, [
        { label: yesLabel || "Yes", action: function () { callback(true); } },
        { label: noLabel || "No", action: function () { callback(false); } }
    ]);
}

//callback gets the typed string, or null if the user cancelled
function patrolPrompt(message, defaultValue, callback, inputType) {
    patrolDlg.open(message, [
        { label: "OK", action: function () { callback(patrolDlg.value()); } },
        { label: "Cancel", action: function () { callback(null); } }
    ], { type: inputType || "text", value: defaultValue || "" });
}
//-->
</script>
