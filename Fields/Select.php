<?php

namespace Gregwar\DSD\Fields;

/**
 * Une ComboBox
 *
 * @author GrÃ©goire Passault <g.passault@gmail.com>
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

    public function addOption($opt,$pos=null)
    {
        $opt->setParent($this);
        if ($pos==null) {
            $this->options[] = $opt;
        } else {
            for ($i=$this->countOptions(); $i>$pos; $i--) {
                $this->options[$i] = $this->options[$i-1];
            }
            $this->options[$pos] = $opt;
        }
    }

    public function addValue($c)
    {
        $this->options[count($this->options)-1]->addValue($c);
    }

    public function check()
    {
        foreach ($this->options as $opt) {
            if ($this->value == $opt->getValue()) {
                return;
            }
        }

        return 'Vous devez choisur une valeur pour le champ '.$this->printName();
    }

    public function getHTML()
    {
        $html = '<select name="'.$this->name.'" ';
        foreach ($this->attributes as $name => $value) {
            $html.= $name.'="'.$value.'" ';
        }
        $html.= ">\n";
        foreach ($this->options as $option) {
            if ($option->getValue() == $this->value)
                $html .= $option->getHTML(true);
            else
                $html .= $option->getHTML(false);
        }
        $html .= "</select>\n";

        return $html;
    }
}
