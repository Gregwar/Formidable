<?php

namespace Gregwar\Formidable\Fields;

/**
 * Number
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class NumberField extends Field
{
    /**
     * Field type
     */
    protected $type = 'text';

    /**
     * Minimal value
     */
    protected $min = null;

    /**
     * Maximum value
     */
    protected $max = null;
    
    public function __sleep()
    {
        return array_merge(parent::__sleep(), array(
            'min', 'max'
        ));
    }

    public function push($name, $value = null)
    {
        switch ($name) {
            case 'min':
                $this->min = $value;

                return;
            case 'max':
                $this->max = $value;

                return;
        }

        parent::push($name, $value);
    }

    public function check()
    {
        if ($this->optional && !$this->value)

            return;

        if ($error = parent::check()) {
            return $error;
        }

        if ($this->multiple && is_array($this->value))

            return;

        if (!is_numeric($this->value)) {
            return array('number', $this->printName());
        }
        if ($this->min !== null) {
            if ($this->value < $this->min) {
                return array('number_min', $this->printName(), $this->min);
            }
        }
        if ($this->max !== null) {
            if ($this->value > $this->max) {
                return array('number_max', $this->printName(), $this->max);
            }
        }
    }
}
