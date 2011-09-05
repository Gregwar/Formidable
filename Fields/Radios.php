<?php

namespace Gregwar\DSD\Fields;

/**
 * Gestion d'un groupe de radios du même nom
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
    }

    public function setValue($value)
    {
        $this->value = $value;

        foreach ($this->radios as $radio) {
            $radio->setValue($value);
            if ($radio->getValue() == $value)
                $this->value_set = true;
        }
    }

    public function getValue()
    {
        return $this->value;
    }

    public function check()
    {
        echo "Check!";
        if (!$this->optional && !$this->value_set) {
            return 'Vous devez cocher une case pour '.$this->radios[0]->printName();
        }
    }
}
