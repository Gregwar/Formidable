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
            span.innerHTML = code;
            var number = FormidableMultiple[id]++;
            code = code.replace(/{number}/g, number);
            span.innerHTML = code + '<a href="javascript:Formidable.removeInput(\''+n+'\');">Remove</a><br />';
            document.getElementById(id).appendChild(span);
        },
        removeInput : function(id) {
            document.getElementById(id).innerHTML = '';
        }
    }
}

if (!FormidableMultiple) {
    var FormidableMultiple = {};
}

