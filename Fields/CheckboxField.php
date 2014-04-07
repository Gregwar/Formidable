<?php

namespace Gregwar\Formidable\Fields;

/**
 * Checkbox field
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class CheckboxField extends Field
{
    /**
     * Is the box checked?
     */
    protected $checked = false;

    /**
     * Value to send to check the box
     */
    protected $checkedValue = '1';

    /**
     * Field type
     */
    protected $type = 'checkbox';

    public function __sleep()
    {
        return array_merge(parent::__sleep(), array(
            'checked', 'checkedValue'
        ));
    }

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

    public function getCheckedValue()
    {
        return $this->checkedValue;
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
