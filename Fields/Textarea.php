<?php

namespace Gregwar\Formidable\Fields;

/**
 * Représente une balise <textarea>
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Textarea extends Field
{
    /**
     * Type du champ
     */
    protected $type = '';

    public function addValue($value)
    {
        $this->value .= $value;
    }

    public function getHtml()
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
