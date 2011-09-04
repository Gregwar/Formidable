<?php

namespace Gregwar\DSD;

/**
 * Inclusion de Fields/*Field.php
 */
$fields_dir = __DIR__.'/Fields';

$dir = opendir($fields_dir);
while ($file = readdir($dir)) {
	if (substr($file, -9) == 'Field.php') {
		require_once($fields_dir.'/'.$file);
	}
}
closedir($dir);

/**
 * Inclusion des types
 */
require_once(__DIR__.'/Fields/Textarea.php');
require_once(__DIR__.'/Fields/Select.php');
require_once(__DIR__.'/Fields/Option.php');
require_once(__DIR__.'/Fields/Options.php');
require_once(__DIR__.'/Fields/Custom.php');

/**
 * Dispatch une balise pour créer les champs enfants
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Dispatcher
{
	public static function dispatch(&$name, &$data) {
		$field = NULL;
		switch (strtolower($name)) {
			case "input":
				$find = explode(" ",substr($data, strpos($data, "type=")+5));
				$type = strtolower(str_replace("\"","",str_replace("'","",$find[0])));
				if (!$type) {
					Form::fatal("Untyped input");
				} else {
					if ($type=="submit") {
						return "<$name $data>";
                    }

                    $classname = sprintf('Gregwar\DSD\Fields\%sField', ucfirst(strtolower($type)));

					try {
						$class = new \ReflectionClass($classname);
						if ($class->isInstantiable()) {
							$field = $class->newInstance();
						} else {
							Form::fatal("Class $classname is not instanciable");
						}
					} catch (\ReflectionException $e) {
						Form::fatal("Type ".htmlspecialchars($classname)." doesn't exists");
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
