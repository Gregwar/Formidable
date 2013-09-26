/*
 * formidable.js
 * -----------
 * This is part of the Formidable Project
 */

if (!Formidable) {
    var Formidable = {
        addInput: function(id, code) {
            var n = Math.random();
            span = document.createElement('span');
            span.id = n;
            span.innerHTML = code + '<a href="javascript:Formidable.removeInput(\''+n+'\');">Enlever</a><br />';
            document.getElementById(id).appendChild(span);
        },
        removeInput : function(id) {
            document.getElementById(id).innerHTML = '';
        }
    }
}

