<?php

namespace Gregwar\DSD\Fields;

class HiddenField extends Field
{
    public function __construct()
    {
		$this->type = 'hidden';
	}

    public function check()
    {
		return;
	}
}
