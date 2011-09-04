<?php

namespace Gregwar\DSD\Fields;

class Option extends Field {

	private $parent;
	private $isSelected;
	private $label;
	
	public function __construct() {
		$this->parent = null;
	}

	public function setParent($p) {
		$this->parent = $p;
		if ($this->isSelected)
			$this->parent->setValue($this->value);
	}

	public function push($name, $value) {
		if ($name == "selected" && $value==NULL) {
			$this->isSelected = true;
		} else {
			parent::push($name, $value);
		}
	}

	public function addValue($c) {
		$this->label .= $c;
	}

	public function setLabel($l) {
		$this->label = $l;
	}

	public function getHTML($selected) {
		return "<option class=\"".$this->class."\" ".($selected ? "selected " : "")."value=\"".htmlspecialchars($this->value)."\"".$this->HTML.">".$this->label."</option>\n";
	}
}
?>
