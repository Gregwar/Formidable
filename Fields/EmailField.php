<?php

namespace Gregwar\Formidable\Fields;

/**
 * E-mail address
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class EmailField extends Field
{
    /**
     * Field type
     */
    protected $type = 'email';

    public function check()
    {
        if ($this->optional && !$this->value) {
            return;
        }


        if ($error = parent::check()) {
            return $error;
        }

        if (!($this->multiple && is_array($this->value)) && !filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            return array('bad_email', $this->printName());
        }

        return;
    }
}
