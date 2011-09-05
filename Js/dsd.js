/*
 * dsd.js
 * -----------
 * This is part of the DSD Project
 */

if (!DSD) {
    var DSD = {
        addInput: function(id, code) {
            var n = Math.random();
            span = document.createElement('span');
            span.id = n;
            span.innerHTML = code + '<a href="javascript:DSD.removeInput(\''+n+'\');">Enlever</a><br />';
            document.getElementById(id).appendChild(span);
        },
        removeInput : function(id) {
            document.getElementById(id).innerHTML = '';
        }
    }
}

