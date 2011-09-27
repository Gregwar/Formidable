<?php

namespace Gregwar\DSD\Fields;

/**
 * ReprÃ©sente une balise <textarea>
 *
 * @author GrÃ©goire Passault <g.passault@gmail.com>
 */
class Textarea extends Field
{
    public function __construct()
    {
        $this->type = '';
    }

    public function addValue($value)
    {
        $this->value .= $value;
    }

    public function getHTML()
    {
        $html ='<textarea name="'.$this->name.'" ';
        foreach ($this->attributes as $name => $value) {
            $html.= $name.'="'.$value.'" ';
        }
        $html.= ">";
        $html.= htmlspecialchars($this->value);
        $html.= "</textarea>\n";
        return $html;
    }
}
