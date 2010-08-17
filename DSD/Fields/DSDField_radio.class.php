<?php
/*
 * DSDField_text class
 *
 */

require_once("DSDField.class.php");

class DSDField_radio extends DSDField {
	private $checked;
	
	public static $CHECKED = 0;
	public static $NOTCHECKED = 1;
	public static $OPTIONAL = 2;

	public static function error($n) {
		return "Vous devez cocher une des cases pour le champ $n";
	}
	
	public function __construct() {
		$this->type = "radio";
		$this->checked = false;
	}

	public function push($name, $value) {
		if ($name=="checked") {
			$this->checked = true;
		} else {
			DSDField::push($name, $value);
		}
	}

	public function setValue($val) {
		if ($this->value == $val) {
			$this->checked = true;
		} else {
			$this->checked = false;
		}
	}

	public function isChecked() {
		return $this->checked;
	}

	public function check() {
		if ($this->optional)
			return DSDField_radio::$OPTIONAL;
		if ($this->checked)
			return DSDField_radio::$CHECKED;
		return DSDField_radio::$NOTCHECKED;
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
