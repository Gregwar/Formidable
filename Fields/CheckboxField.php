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

    public function __construct()
    {
        $this->type = 'checkbox';
    }

    public function push($name, $value)
    {
        if ($name === 'checked' && $value==null) {
            $this->checked = true;
        } else {
            parent::push($name, $value);
        }
    }

    public function setValue($value)
    {
        $this->checked = ($value !== '' && $value !== '0');
    }

    public function getValue()
    {
        return $this->checked ? $this->value : '';
    }

    public function check()
    {
        return;
    }

    public function getHTML()
    {
        if ($this->checked) {
            return parent::getHTML('checked');
        } else {
            return parent::getHTML();
        }
    }
}
