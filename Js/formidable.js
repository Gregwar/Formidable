/*
 * formidable.js
 * -----------
 * This is part of the Formidable Project
 */
if (typeof(Formidable) == 'undefined') {
    var Formidable = {
        addInput: function(id, code) {
            var n = 'multiple-element-'+Math.random();
            div = document.createElement('div');
            div.className = 'multiple-element';
            div.id = n;
            div.innerHTML = code;
            var number = Formidable.multiple[id]++;
            code = code.replace(/{number}/g, number);
            div.innerHTML = code + '<a href="javascript:Formidable.removeInput(\''+n+'\');">{remove}</a><br />';
            document.getElementById(id).appendChild(div);
        },
        removeInput : function(id) {
            document.getElementById(id).innerHTML = '';
        },
        multiple: {}
    }
}
