<?php

namespace Gregwar\DSD\Fields;

/**
 * Représente une balise <textarea>
 */
class Textarea extends Field
{
    public function __construct()
    {
		$this->type = '';
	}

    public function addValue($value)
    {
		$this->value .= $value;
	}

    public function getHTML()
    {
        return '<textarea class="'.$this->class.'" name="'.$this->name.'"'.$this->HTML.'>'
            .htmlspecialchars($this->value)
            ."</textarea>\n";
	}
}
