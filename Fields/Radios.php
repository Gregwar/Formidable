<?php

namespace Gregwar\Formidable\Fields;

/**
 * Gestion d'un groupe de radios du mÃªme nom
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Radios extends Field
{
    /**
     * Enfants
     */
    protected $radios = array();

    /**
     * La valeur est t-elle correcte ?
     */
    protected $valueSet = false;

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
        if (!$this->optional && !$this->valueSet) {
            return 'Vous devez cocher une case pour '.$this->radios[0]->printName();
        }
    }

    public function __clone()
    {
        $this->radios = array();
    }
}
