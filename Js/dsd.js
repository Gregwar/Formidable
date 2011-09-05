/*
 * dsd.js
 * -----------
 * This is part of the DSD Project
 */

if (!DSD) {
    var DSD = {
        addInput: function(id,code) {
            var elt = document.getElementById(id);
            var elts = elt.getElementsByTagName('span');
            var n = Math.random();
            var values = [];
            for (var i=0; i<elts.length; i++) {
                var input = elts[i].getElementsByTagName('input')[0];
                if (input) {
                    values[elts[i].id] = input.value;
                }
            }
            code = '<span id="'+n+'">' + code + '<a href="javascript:DSD.removeInput(\''+n+'\');">Enlever</a><br /></span>';
            elt.innerHTML += code;
            for (var i=0; i<elts.length; i++) {
                var input = elts[i].getElementsByTagName("input")[0];
                if (input && values[elts[i].id]) {
                    input.value = values[elts[i].id];
                }
            }
            var added = document.getElementById(n).getElementsByTagName('input')[0];
            added.name = added.name.substr(0,added.name.length-3) + '[]';
        },
        removeInput : function(id) {
            document.getElementById(id).innerHTML = '';
        }
    }
}

