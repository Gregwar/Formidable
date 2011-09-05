<?php

namespace Gregwar\DSD\Fields;

/**
 * Radios
 *
 * @author Grégoire Passault <g.passault@gmail.com>
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

    public function push($var, $val)
    {
        switch ($var) {
        case 'source':
            $this->source = $val;
            break;
        default:
            parent::push($var,$val);
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

    public function setValue($val)
    {
        foreach ($this->datas as $k=>$data)  {
            if ($k == $val) {
                $this->value = $val;
            }
        }
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getHTML()
    {
        $s="";
        if (is_array($this->datas))
            foreach ($this->datas as $val => $label) {
                if ($val == $this->value)
                    $checked=" checked";
                else	$checked="";
                $s.="<div class=\"".$this->class."\">\n";
                $s.="<input type=\"radio\" name=\"".$this->name."\"$checked id=\"".$this->name."_$val\" value=\"".$val."\" />\n";
                $s.=" <label for=\"".$this->name."_$val\">".$label."</label>\n";
                $s.="</div>\n";
            }
        return $s;
    }
}
