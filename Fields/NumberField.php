<?php

namespace Gregwar\Formidable\Fields;

/**
 * Nombre
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class NumberField extends Field
{
    /**
     * Type du champ
     */
    protected $type = 'text';

    /**
     * Valeur minimum
     */
    protected $min = null;

    /**
     * Valeur maximum
     */
    protected $max = null;

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
            return $this->language->translate('number', $this->printName());
        }
        if ($this->min !== null) {
            if ($this->value < $this->min) {
                return $this->language->translate('number_min', $this->printName(), $this->min);
            }
        }
        if ($this->max !== null) {
            if ($this->value > $this->max) {
                return $this->language->translate('number_max', $this->printName(), $this->max);
            }
        }
    }
}
