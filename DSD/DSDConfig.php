<?
$DSDRoot = dirname(__FILE__);
function getServerRoot() {
	$DSDHttp = dirname($_SERVER["SCRIPT_NAME"]);
	$DSDHttpAbs = dirname($_SERVER["SCRIPT_FILENAME"]);
	$res = "";
	$a = explode("/", $DSDHttpAbs);
	$b = explode("/", $DSDHttp);
	for ($i=0; $i<count($b); $i++) {
		if (trim($b[$i])!="")
			unset($a[count($a)-1]);
	}
	return implode("/", $a);
}
$DSDHttpRoot = getServerRoot();

function getEndCommon($a,$b) {
	$a = explode("/", $a);
	$b = explode("/", $b);
	foreach ($a as $i=>$v) {
		if (isset($b[$i]) && $b[$i]==$a[$i]) {
			unset($a[$i]);
		} else break;
	}
	return implode("/", $a);
}
$DSDHttp = "/".getEndCommon($DSDRoot, $DSDHttpRoot)."/";
