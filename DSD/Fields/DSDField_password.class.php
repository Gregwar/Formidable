<?php
/*
 * DSDField_password class
 *
 */

require_once("DSDField.class.php");

class DSDField_password extends DSDField {
	public function __construct() {
		$this->type = "password";
	}
}
?>
