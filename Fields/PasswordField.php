<?php

namespace Gregwar\DSD\Fields;

class PasswordField extends Field
{
    public function __construct()
    {
		$this->type = 'password';
	}
}
