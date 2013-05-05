<?php

namespace Gregwar\DSD\Fields;

/**
 * Champ de type checkbox
 *
 * @author GrÃ©goire Passault <g.passault@gmail.com>
 */
class CheckboxField extends Field
{
    /**
     * La case est t-elle cochÃ©e ?
     */
    protected $checked = false;

    /**
     * Valeur pour la checker
     */
    protected $checkedValue = '1';

    /**
     * Type du champ
     */
    protected $type = 'checkbox';

    public function push($name, $value = null)
    {
        if ($name === 'checked') {
            $this->checked = true;
        } elseif ($name === 'value') {
            $this->checkedValue = $value;
            $this->setAttribute('value', $value);
        } else {
            parent::push($name, $value);
        }
    }

    public function setValue($value, $default = false)
    {
        $this->checked = ($value == $this->checkedValue);
    }

    public function setChecked($checked) 
    {
        $this->checked = $checked;
    }

    public function isChecked()
    {
        return $this->checked;
    }

    public function getValue()
    {
        return $this->checked ? $this->checkedValue : '';
    }

    public function check()
    {
        return;
    }

    public function getHtml()
    {
        $this->unsetAttribute('checked');

        if ($this->checked) {
            $this->setAttribute('checked', 'checked');
        }

        return parent::getHtml();
    }
}
