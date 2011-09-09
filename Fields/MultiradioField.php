<?php

namespace Gregwar\DSD\Fields;

/**
 * Radios
 *
 * @author GrÃ©goire Passault <g.passault@gmail.com>
 */
class MultiradioField extends Field 
{
    private $datas;
    private $source;

    public function check()
    {
        if (!$this->optional && ($this->value===false))
            return "Vous devez saisir une valeur pour ".$this->printName();
        return;
    }

    public function push($var, $value)
    {
        switch ($var) {
        case 'source':
            $this->source = $value;
            break;
        default:
            parent::push($var, $value);
            break;
        }
    }

    public function getSource()
    {
        return $this->source;
    }

    public function source($d)
    {
        $this->datas = $d;
    }

    public function setValue($value)
    {
        foreach ($this->datas as $name => $data)  {
            if ($name == $value) {
                $this->value = $name;
            }
        }
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getHTML()
    {
        $html = '';

        if (is_array($this->datas)) {
            foreach ($this->datas as $value => $label) {
                if ($value == $this->value) {
                    $checked = ' checked="checked"';
                } else {
                    $checked = '';
                }

                $html.= "<div class=\"".$this->getAttribute('class')."\">\n";
                $html.= "<input type=\"radio\" name=\"".$this->name."\"$checked id=\"".$this->name."_$value\" value=\"".$value."\" />\n";
                $html.= "<label for=\"".$this->name."_$value\">".$label."</label>\n";
                $html.= "</div>\n";
            }
        }

        return $html;
    }
}
