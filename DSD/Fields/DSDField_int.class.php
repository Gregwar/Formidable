<?php
/*
 * DSDField_int class
 *
 */

require_once("DSDField_number.class.php");

class DSDField_int extends DSDField_number {
	public function check() {
		if ($this->optional && !$this->value)
			return;

		$err=DSDField_number::check();
		if ($err)
			return $err;
		if ($this->multiple && is_array($this->value))
			return;
		
		if ((int)($this->value) != $this->value)
			return "Le champ ".$this->printName()." doit être un entier";

		return;
	}
}
?>
