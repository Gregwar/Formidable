<?php
/*
 * DSDDispatcher class
 *
 */

$dir = $DSDRoot."/Fields";
if (!is_dir($dir)) {
	DSDField::error("Fields directory doesn't exists");
}
$fields_dir = opendir($dir);
while ($f = readdir($fields_dir)) {
	if (substr($f,0,9)=="DSDField_" && substr($f,-4)==".php") {
		require_once($DSDRoot."/Fields/".$f);
	}
}
closedir($fields_dir);

require_once($DSDRoot."/Fields/DSDTextarea.class.php");
require_once($DSDRoot."/Fields/DSDSelect.class.php");
require_once($DSDRoot."/Fields/DSDOption.class.php");
require_once($DSDRoot."/Fields/DSDOptions.class.php");
require_once($DSDRoot."/Fields/DSDCustom.class.php");

class DSDDispatcher {
	public static function dispatch(&$name, &$data) {
		$field = NULL;
		switch (strtolower($name)) {
			case "input":
				$find = explode(" ",substr($data, strpos($data, "type=")+5));
				$type = strtolower(str_replace("\"","",str_replace("'","",$find[0])));
				if (!$type) {
					DSDForm::error("Untyped input");
				} else {
					if ($type=="submit") {
						return "<$name $data>";
					}
					$classname = "DSDField_$type";
					try {
						$class = new ReflectionClass($classname);
						if ($class->isInstantiable()) {
							$field = $class->newInstance();
						} else {
							DSDForm::error("Class $classname is not instanciable");
						}
					} catch (ReflectionException $e) {
						DSDForm::error("Type ".htmlspecialchars($classname)." doesn't exists");
					}
				}
			break;
			case "textarea":
				$field=(new DSDTextarea($data));
			break;
			case "select":
				$field=(new DSDSelect($data));
			break;
			case "option":
				$field=(new DSDOption($data));
			break;
			case "options":
				$field=(new DSDOptions($data));
			break;
			case "custom":
				$field = (new DSDCustom($data));
			break;
			case "/textarea":
				return "</textarea>";
			break;
			case "/select": 
				return "</select>";
			break;
			case "/option":
				return "</option>";
			break;
			case "/form":
				return "</form>";
			break;
			default:
				if (!$data)
				return "<$name>";
				else
				return "<$name $data>";
			break;
		}
		if ($field != NULL) {
			$buffer="";
			$guillemet="";
			for ($i=0; $i<strlen($data); $i++) {
				$c = $data[$i];
				if ($c == " ") {
					if (!$guillemet) {
						$tmp = split("=", $buffer);
						if (isset($tmp[1]) && $tmp[1][strlen($tmp[1])-1]=="/")
							$tmp[1]=substr($tmp[1],0,-1);
						if (isset($tmp[1])) {
							if ($tmp[1][0]=="\"" || $tmp[1][0]=="'")
								$tmp[1] = substr($tmp[1], 1, strlen($tmp[1])-2);	
						} else {
							$tmp[1]="";
						}
						$field->push(strtolower($tmp[0]), $tmp[1]);
						$buffer="";
					} else {
						$buffer.=" ";
					}
				} else {
					if (!$guillemet) {
						if ($c=="'" || $c=="\"")
							$guillemet = $c;
					} else {
						if ($c == $guillemet)
							$guillemet = "";
					}
					$buffer .= $c;
				}
			}
			if ($buffer!="") {
				$tmp = split("=", $buffer);
				if (isset($tmp[1]) && $tmp[1][strlen($tmp[1])-1]=="/")
					$tmp[1]=substr($tmp[1],0,-1);
				if (!isset($tmp[1])) $tmp[1]="";
				else
				if ($tmp[1][0]=="\"" || $tmp[1][0]=="'")
					$tmp[1] = substr($tmp[1], 1, strlen($tmp[1])-2);
				$field->push(strtolower($tmp[0]), $tmp[1]);
			}
			return $field;
		} else {
			return "";
		}
	}
}
?>
