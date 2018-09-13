<?php

namespace Gregwar\Formidable\Fields;

/**
 * Managing a radios group with the same name
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Radios extends Field
{
    /**
     * Children
     */
    protected $radios = array();

    /**
     * Is the value set ?
     */
    protected $valueSet = false;
    
    public function __sleep()
    {
        return array_merge(parent::__sleep(), array(
            'valueSet', 'radios'
        ));
    }

    public function getRadioForValue($value)
    {
        foreach ($this->radios as $radio) {
            if ($radio->getValue() == $value) {
                return $radio;
            }
        }

        return null;
    }

    public function addRadio(RadioField $radio)
    {
        $this->radios[] = $radio;

        if ($radio->getMappingName()) {
            $this->mapping = $radio->getMappingName();
        }

        $radio->setParent($this);
    }

    public function setValue($value, $default = false)
    {
        $this->value = $value;
        $this->valueSet = false;

        foreach ($this->radios as $radio) {
            if ($radio->getValue() == $value) {
                $this->valueSet = true;
                $radio->setChecked(true);
            } else {
                $radio->setChecked(false);
            }
        }
    }

    public function getValue()
    {
        return $this->value;
    }

    public function check()
    {
        if ($this->required && !$this->valueSet) {
            return array('should_check', $this->radios[0]->printName());
        }
    }

    public function __clone()
    {
        $this->radios = array();
    }
}
