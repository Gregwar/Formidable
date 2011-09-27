<?php

namespace Gregwar\DSD\Fields;

require_once(__DIR__.'/NumberField.php');

/**
 * Entier
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class IntField extends NumberField
{
    public function __construct()
    {
        $this->type = 'text';
    }

    public function check()
    {
        if ($this->optional && !$this->value) {
            return;
        }

        $error = parent::check();

        if ($error) {
            return $error;
        }

        if ($this->multiple && is_array($this->value)) {
            return;
        }

        if ((int)($this->value) != $this->value) {
            return 'Le champ '.$this->printName().' doit être un entier';
        }

        return;
    }
}

