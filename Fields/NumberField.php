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

    /**
     * Step
     */
    protected $step = 'any';

    public function __construct()
    {
        $this->attributes['step'] = $this->step;
    }

    public function __sleep()
    {
        return array_merge(parent::__sleep(), array(
            'min', 'max', 'step'
        ));
    }

    public function push($name, $value = null)
    {
        switch ($name) {
            case 'min':
                $this->min = $value;
                $this->attributes['min'] = $value;
                break;
            case 'max':
                $this->max = $value;
                $this->attributes['max'] = $value;
                break;
            case 'step':
                $this->step = $value;
                $this->attributes['step'] = $value;
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

        if ($this->step != 'any') {
            $step = abs((float)$this->step);
            $value = abs((float)$this->value);
            $factor = round($value/$step)*$step;
            $delta = $value-$factor;
            if ($delta > 0.00001) {
                return array('number_step', $this->printName(), $this->step);
            }
        }
    }
}
