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
    protected $type = 'number';

    /**
     * Minimal value
     */
    protected $min = null;

    /**
     * Maximum value
     */
    protected $max = null;

    public function __construct()
    {
        $this->setAttribute('step', 'any');
    }
    
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
                break;
            case 'max':
                $this->max = $value;
                break;
        }

        parent::push($name, $value);
    }

    public function check()
    {
        if (!$this->required && !$this->value) {
            return;
        }

        if ($error = parent::check()) {
            return $error;
        }

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
