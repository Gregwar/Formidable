<?php
/*
 * DSDField_multicheckbox class
 *
 */

require_once("DSDField.class.php");

class DSDField_multicheckbox extends DSDField {
	private $datas;
	private $checked;
	private $source;

	public function check() {
		return;
	}

	public function push($var, $val) {
		switch ($var) {
			case "source":
				$this->source = $val;
			break;
			default:
				parent::push($var,$val);
			break;
		}
	}
	
	public function __construct() {
		$this->checked = array();
	}

	public function getSource() {
		return $this->source;
	}

	public function source($d) {
		$this->datas = $d;
	}

	public function setValue($val) {
		$this->checked=array();
		if (!is_array($val)) {
			$tmp=explode(",",$val);
			$val = array();
			foreach ($tmp as $k=>$v) {
				$val[$v] = "1";
			}
		}
		foreach ($val as $k => $v) {
			if (isset($this->datas[$k]) && $v=="1") {
				$this->checked[$k]="1";
			}
		}
	}

	public function getValue() {
		$tmp = array();
		foreach ($this->checked as $k=>$v) {
			$tmp[] = $k;
		}
		return $tmp;
	}

	public function getHTML() {
		$s="";
		if (is_array($this->datas))
		foreach ($this->datas as $val => $label) {
			if (isset($this->checked[$val]))
				$checked=" checked";
			else	$checked="";
			$s.="<div class=\"".$this->class."\">\n";
			$s.="<input type=\"checkbox\" name=\"".$this->name."[$val]\"$checked id=\"".$this->name."_$val\" value=\"1\" />\n";
			$s.=" <label for=\"".$this->name."_$val\">".$label."</label>\n";
			$s.="</div>\n";
		}
		return $s;
	}
}
?>
