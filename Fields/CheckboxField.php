<?php

namespace Gregwar\DSD\Fields;

/**
 * Champ de type checkbox
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class CheckboxField extends Field
{
    /**
     * La case est t-elle cochée ?
     */
    private $checked = false;

    /**
     * Valeur pour la checker
     */
    private $checkedValue = '1';

    public function __construct()
    {
        $this->type = 'checkbox';
    }

    public function push($name, $value)
    {
        if ($name === 'checked' && $value==null) {
            $this->checked = true;
        } elseif ($name === 'value') {
            $this->checkedValue = $value;
            $this->setAttribute('value', $value);
        } else {
            parent::push($name, $value);
        }
    }

    public function setValue($value)
    {
        $this->checked = ($value == $this->checkedValue);
    }

    public function getValue()
    {
        return $this->checked ? $this->checkedValue : '';
    }

    public function check()
    {
        return;
    }

    public function getHTML()
    {
        $this->unsetAttribute('checked');

        if ($this->checked) {
            $this->setAttribute('checked', 'checked');
        }

        return parent::getHTML();
    }
}
