<?php

namespace Gregwar\DSD\Fields;

/**
 * Adresse e-mail
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class EmailField extends FIeld
{
    public function __construct()
    {
        $this->type = 'email';
    }

    public function check()
    {
        if ($this->optional && !$this->value)
            return;

        $error = parent::check();
        if ($error)
            return $error;

        if (!($this->multiple && is_array($this->value)) && !filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            return 'Le champ '.$this->printName().' doit être une adresse e-mail valide';
        }
        return;
    }	
}
