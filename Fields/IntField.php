<?php

namespace Gregwar\DSD\Fields;

class IntField extends NumberField {
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
