<?php
/*
 * DSDField_hidden class
 *
 */

require_once("DSDField.class.php");

class DSDField_hidden extends DSDField {
	public function __construct() {
		$this->type = "hidden";
	}

	public function check() {
		return;
	}
}
?>
