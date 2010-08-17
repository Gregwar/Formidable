<?php
/*
 * DSDField class
 *
 */
			
class DSDField {
	protected $name;
	protected $HTML;
	protected $value = false;
	protected $type;
	protected $optional;
	protected $regex;
	protected $minlength;
	protected $maxlength;
	protected $sqlname;
	protected $class;
	protected $prettyname;
	protected $readonly;
	protected $valuechanged;
	protected $myform;
	protected $multiple;
	protected $multipleChange;
	protected $in;
	protected $notin;

	public function __construct() {
		$this->type = "text";
		$this->optional = false;
		$this->readonly = false;
		$this->valuechanged = false;
		$this->multiple = false;
		$this->in="";
		$this->notin="";
		$this->multipleChange="";
	}

	public function needJS() {
		return $this->multiple;
	}

	public function setForm($form) {
		$this->myform = $form;
	}

	public function push($name, $value) {
		switch ($name) {
			case "class":
				$this->class = $value;
			break;
			case "name":
				$this->name = $value;
			break;
			case "type":
				if (!$this->type)
					$this->type = $value;
			break;
			case "value":
				$this->value = $value;
			break;
			case "optional":
				$this->optional = true;
			break;
			case "regex":
				$this->regex = $value;
			break;
			case "minlength":
				$this->minlength = $value;
			break;
			case "maxlength":
				$this->maxlength = $value;
				$this->HTML .= " maxlength=\"".$value."\"";
			break;
			case "multiple":
				$this->multiple = true;
			break;
			case "multiplechange":
				$this->multipleChange = $value;
			break;
			case "sqlname":
				$this->sqlname = $value;
			break;
			case "in":
				$this->in = $value;
			break;
			case "notin":
				$this->notin = $value;
			break;
			case "prettyname":
				$this->prettyname=$value;
			break;
			case "readonly":
				$this->readonly=true;
				$this->HTML .=" readonly ";
			break;
			default:
				if (eregi("^([a-z0-9_-]+)$",$name)) {
					if ($value!==NULL) {
						$this->HTML .= " $name=\"".$value."\"";
					} else {
						$this->HTML .= " $name";
					}
				}
			}
	}

	public function printName() {
		if ($this->prettyname)
			return $this->prettyname;
		return $this->name;
	}

	public function check() {
		if ($this->valuechanged && $this->readonly) {
			return "Le champ ".$this->printName()." est en lecture seule";
		}

		if ($this->multiple && is_array($this->value)) {
			$tmp = $this->value;
			$nodata=true;
			foreach ($tmp as $val) {
				if ($val!="")
					$nodata=false;
				$this->value = $val;
				$err = $this->check();
				if ($err) {
					$this->value = $tmp;
					return $err;
				}
			}
			if (!$this->optional && $nodata)
				return "Vous devez saisir une valeur pour ".$this->printName();
			$this->value = $tmp;
			return;
		}
		if ($this->value===false || (is_string($this->value) && $this->value=="")) {
			if ($this->optional || $this->multiple)
				return;
			else {
				return "Vous devez saisir une valeur pour ".$this->printName();
			}
		} else {
			if ($this->regex) {
				if (!eregi($this->regex, $this->value))
					return "Le format du champ ".$this->printName()." est incorrect";
			}
			if ($this->minlength && strlen($this->value)<$this->minlength)
				return "Le champ ".$this->printName()." doit faire au moins ".$this->minlength." caracteres.";
			if ($this->maxlength && strlen($this->value)>$this->maxlength)
				return "Le champ ".$this->printName()." ne doit pas dépasser ".$this->maxlength." caracteres.";

			$err = $this->inNotIn();
			if ($err)
				return $err;
		}
	}

	function inNotIn() {
			if ($this->in) {
				if ($this->checkInQuery($this->in)==0)
					return "La valeur du champ ".$this->printName()." doit être présent dans la base";
			}
			if ($this->notin) {
				if ($this->checkInQuery($this->notin)!=0)
					return "La valeur du champ ".$this->printName()." doit pas déja être présent dans la base";
			}
	}

	function checkInQuery($v) {
		if (isset($this->sqlname)) $defaultField = $this->sqlname;
		else $defaultField = $this->name;
		$tmp = explode(".",$v);
		$table=$tmp[0];
		if (isset($tmp[1]))
			$field=$tmp[1];
		else $field=$defaultField;
		$q = mysql_query("SELECT COUNT(*) AS NB FROM `".$table."` WHERE `$field`=\"".mysql_real_escape_string($this->value)."\"");
		$r = mysql_fetch_assoc($q);
		return $r["NB"];
	}

	public function getName() {
		return $this->name;
	}

	public function getSQLName() {
		return $this->sqlname;
	}
	
	public function getValue() {
		return $this->value;
	}

	public function setValue($val,$default=0) {
		if ($val!=$this->value && !$default)
			$this->valuechanged = true;
		if (!($this->valuechanged && $this->readonly))
			$this->value = $val;
		if ($this->multiple && !is_array($this->value)) {
			$this->value = explode(",",$this->value);
		}
		if ($this->multiple && is_array($this->value)) {
			$valuez = array();
			foreach ($this->value as $v) {
				if ($v!="")
					$valuez[] = $v;
			}
			$this->value = $valuez;
		}
	}

	public function setClass($cl) {
		$this->class = $cl;
	}

	public function getHTMLForValue($extra, $value="", $nameb="") {
			return "<input class=\"".$this->class."\" type=\"".$this->type."\" name=\"".$this->name."$nameb\"".$this->HTML.$extra." value=\"".htmlspecialchars($value)."\" />\n";
	}

	public function getHTML($extra="") {
		if ($extra!="")
			$extra=" $extra";
			$nameb="";
			$rnd = md5(mt_rand(0,1000000).md5(time()).mt_rand(0,100000));
			$value = $this->value;
			if ($this->multiple) {
				$nameb="[0]";
				if (is_array($this->value) && isset($this->value[0]))
					$value=$this->value[0];
				else	$value="";
			}

			$code=$this->getHTMLForValue($extra, $value, $nameb);
			$novalcode=$this->getHTMLForValue($extra, "", $nameb);

			$others="";
			if ($this->multiple && is_array($this->value)) {
				for ($i=1; $i<count($this->value); $i++) {
					if (trim($this->value[$i])!="") {
						$others.="DSD.addInput(\"$rnd\",\"";
						$others.=str_replace(array("\r","\n"),array("",""),addslashes($this->getHTMLForValue($extra, $this->value[$i],$nameb)));
						$others.="\");\n";
					}
				}
			}
			if ($this->multiple) {
				$html="<br /><span id=\"$rnd\"><span name=\"add\"></span></span><script type=\"text/javascript\">$others</script>";
				$html.="<a href=\"javascript:DSD.addInput('$rnd','".str_replace(array("\r","\n"),array("",""),htmlspecialchars($novalcode))."');".$this->multipleChange."\">Ajouter</a>";
				$html=$code.$html;
			} else $html=$code;
			return $html;
	}

	public function getSource() {
		return "";
	}

	public function isChecked() {
		return true;
	}

	public function source() {
		return false;
	}

}
?>
