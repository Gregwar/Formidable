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
        if (!$this->required && !$this->value) {
            return;
        }


        if ($error = parent::check()) {
            return $error;
        }

        if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            return array('bad_email', $this->printName());
        }

        return;
    }
}
