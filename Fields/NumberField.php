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
     * Valeur minimum
     */
    private $min = null;

    /**
     * Valeur maximum
     */
    private $max = null;

    public function __construct()
    {
        $this->type = 'text';
    }

    public function push($name, $value)
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
        if ($this->optional && !$this->value)
            return;

        $err=parent::check();
        if ($err)
            return $err;

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
