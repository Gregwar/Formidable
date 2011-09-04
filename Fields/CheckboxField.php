<?php

namespace Gregwar\DSD\Fields;

class CheckboxField extends Field
{
	private $checked;
	
    public function __construct()
    {
		$this->type = "checkbox";
		$this->checked = false;
	}

    public function push($name, $value)
    {
		if ($name=="checked" && $value==null) {
			$this->checked = true;
		} else {
			DSDField::push($name, $value);
		}
	}

    public function setValue($val)
    {
		if ($val!="" && $val!=="0") {
			$this->checked = true;
		} else {
			$this->checked = false;
		}
	}

    public function getValue()
    {
		if ($this->checked) {
			return $this->value;
		} else {
			return '';
		}
	}

    public function check()
    {
		return;
	}

    public function getHTML()
    {
		if ($this->checked) {
			return parent::getHTML('checked');
		} else {
			return parent::getHTML();
		}
	}
}
