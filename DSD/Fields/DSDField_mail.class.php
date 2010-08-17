<?php
/*
 * DSDField_mail class
 *
 */

require_once("DSDField.class.php");

class DSDField_mail extends DSDField {
	public function check() {
		if ($this->optional && !$this->value)
			return;

		$err=DSDField::check();
		if ($err)
			return $err;

		if (!($this->multiple && is_array($this->value)) && !filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
			return "Le champ ".$this->printName()." doit être une adresse e-mail valide";
		}
		return;
	}	
}
?>
