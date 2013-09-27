<?php

namespace Gregwar\Formidable\Fields;

/**
 * Entier
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class IntField extends NumberField
{
    /**
     * Type de champ
     */
    protected $type = 'text';

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
            return array('integer', $this->printName());
        }

        return;
    }
}

