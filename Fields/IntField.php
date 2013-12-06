<?php

namespace Gregwar\Formidable\Fields;

/**
 * Integer
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class IntField extends NumberField
{
    /**
     * Field type
     */
    protected $type = 'number';

    public function check()
    {
        if ($this->optional && !$this->value) {
            return;
        }

        $error = parent::check();

        if ($error) {
            return $error;
        }

        if ((int)($this->value) != $this->value) {
            return array('integer', $this->printName());
        }

        return;
    }
}

