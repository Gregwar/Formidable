<?php

namespace Gregwar\DSD\Fields;

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
    private $radios = array();

    /**
     * La valeur est t-elle correcte ?
     */
    private $value_set = false;

    public function addRadio(RadioField $radio)
    {
        $this->radios[] = $radio;

        if ($radio->getMappingName()) {
            $this->mapping = $radio->getMappingName();
        }

        $radio->setParent($this);
    }

    public function setValue($value)
    {
        $this->value = $value;

        foreach ($this->radios as $radio) {
            if ($radio->getValue() == $value) {
                $this->value_set = true;
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
        if (!$this->optional && !$this->value_set) {
            return 'Vous devez cocher une case pour '.$this->radios[0]->printName();
        }
    }
}
