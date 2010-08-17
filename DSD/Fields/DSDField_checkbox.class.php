<?php
/*
 * DSDField_checkbox class
 *
 */

require_once("DSDField.class.php");

class DSDField_checkbox extends DSDField {
	private $checked;
	
	public function __construct() {
		$this->type = "checkbox";
		$this->checked = false;
	}

	public function push($name, $value) {
		if ($name=="checked" && $value==null) {
			$this->checked = true;
		} else {
			DSDField::push($name, $value);
		}
	}

	public function setValue($val) {
		if ($val!="" && $val!=="0") {
			$this->checked = true;
		} else {
			$this->checked = false;
		}
	}

	public function getValue() {
		if ($this->checked) {
			return $this->value;
		} else {
			return "";
		}
	}

	public function check() {
		return;
	}

	public function getHTML() {
		if ($this->checked) {
			return DSDField::getHTML("checked");
		} else {
			return DSDField::getHTML();
		}
	}
}
?>
