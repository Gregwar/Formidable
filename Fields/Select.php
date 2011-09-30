<?php

namespace Gregwar\DSD\Fields;

/**
 * Une ComboBox
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Select extends Field
{
    /**
     * Options (enfants)
     */
    private $options = array();

    public function countOptions()
    {
        return count($this->options);
    }

    public function addOption($option, $position = null)
    {
        $option->setParent($this);

        if ($position == null) {
            $this->options[] = $option;
        } else {
            for ($i = $this->countOptions(); $i > $position; $i--) {
                $this->options[$i] = $this->options[$i-1];
            }
            $this->options[$position] = $option;
        }
    }

    public function addValue($c)
    {
        $this->options[count($this->options)-1]->addValue($c);
    }

    public function check()
    {
        if ($error = parent::check()) {
            return $error;
        }

        foreach ($this->options as $opt) {
            if ($this->value == $opt->getValue()) {
                return;
            }
        }

        return 'Vous devez choisur une valeur pour le champ '.$this->printName();
    }

    public function getHtml()
    {
        $html = '<select name="'.$this->name.'" ';
        foreach ($this->attributes as $name => $value) {
            $html.= $name.'="'.$value.'" ';
        }
        $html.= ">\n";
        foreach ($this->options as $option) {
            if ($option->getValue() == $this->value)
                $html .= $option->getHtml(true);
            else
                $html .= $option->getHtml(false);
        }
        $html .= "</select>\n";

        return $html;
    }
}
