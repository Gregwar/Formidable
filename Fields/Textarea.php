<?php

namespace Gregwar\Formidable\Fields;

/**
 * A textarea field
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Textarea extends Field
{
    /**
     * Field type
     */
    protected $type = '';

    public function addValue($value)
    {
        $this->value .= $value;
    }

    public function getHtml()
    {
        $html ='<textarea name="'.$this->getName().'" ';

        foreach ($this->attributes as $name => $value) {
            $html.= $name.'="'.$value.'" ';
        }

        if ($this->required) {
            $html.= 'required="required" ';
        }

        $html.= '>';
        
        if ($this->value) {
            $html.= htmlspecialchars($this->value);
        }
        
        $html.= '</textarea>'."\n";

        return $html;
    }
}
