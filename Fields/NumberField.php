<?php

namespace Gregwar\DSD\Fields;

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

    public function push($name, $value)
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
            return 'Le champ '.$this->printName().' doit être un nombre';
        }
        if ($this->min !== null) {
            if ($this->value < $this->min)

                return 'Le champ '.$this->printName().' doit être au moins égal à '.$this->min;
        }
        if ($this->max !== null) {
            if ($this->value > $this->max) {
                return 'Le champ '.$this->printName().' ne doit pas dépasser '.$this->max;
            }
        }
    }
}
