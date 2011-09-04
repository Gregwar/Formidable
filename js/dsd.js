/*
 * dsd.js
 * -----------
 * This is part of the DSD Project
 * see www.libdsd.fr
 */

var DSDjs=true;
var DSDFields=1;
var DSD = {
	version : "1.5.3",
	/* Functions used to create or destroy fields of "multiple" type */
	addInput : function(id,code) {
		var elt = document.getElementById(id);
		var elts = elt.getElementsByTagName("span");
		var n=Math.random();
		var tmp=new Array();
		for (var i=0; i<elts.length; i++) {
			var input = elts[i].getElementsByTagName("input")[0];
			if (input) tmp[elts[i].id] = input.value;
		}
		code = "<span id="+n+"> " + code + " <a href=\"javascript:DSD.removeInput('"+id+"','"+n+"')\">Enlever</a><br /></span>";
		elt.innerHTML += code;
		for (var i=0; i<elts.length; i++) {
			var input = elts[i].getElementsByTagName("input")[0];
			if (input && tmp[elts[i].id]) input.value = tmp[elts[i].id];
		}
		var added=document.getElementById(n).getElementsByTagName("input")[0];
		added.name=added.name.substr(0,added.name.length-3)+"["+DSDFields+"]";
		DSDFields++;
	},
	removeInput : function(id,subId) {
		var elt = document.getElementById(id);
		var elts = elt.getElementsByTagName("span");
		for (var i=0; i<elts.length; i++) {
			if (elts[i].id == subId)
				elts[i].innerHTML="";
		}
	}
}
