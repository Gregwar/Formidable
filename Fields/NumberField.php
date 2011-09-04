<?php

namespace Gregwar\DSD\Fields;

class NumberField extends Field 
{
	private $min = null;
	private $max = null;
	
    public function __construct()
    {
		$this->type = 'text';
	}

    public function push($name, $val)
    {
		if ($name == "min") {
			$this->min = $val;
		} else
		if ($name == "max") {
			$this->max = $val;
		} else
		DSDField::push($name, $val);
	}

    public function check()
    {
		if ($this->optional && !$this->value)
			return;
		
		$err=parent::check();
		if ($err)
			return $err;

		if ($this->multiple && is_array($this->value))
			return;
		
		if (!is_numeric($this->value)) {
			return "Le champ ".$this->printName()." doit être un nombre";
		}
		if ($this->min !== NULL) {
			if ($this->value<$this->min)
				return "Le champ ".$this->printName()." doit être au moins égal à ".$this->min;
		}
		if ($this->max !== NULL) {
			if ($this->value>$this->max)
				return "Le champ ".$this->printName()." ne doit pas dépasser ".$this->max;
		}
	}
}
