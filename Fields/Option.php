<?php

namespace Gregwar\DSD\Fields;

/**
 * Une option
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Option extends Field
{
    /**
     * Select parent
     */
    private $parent;

    /**
     * Séléctionnée ?
     */
    private $isSelected;

    /**
     * Label
     */
    private $label;

    public function __construct()
    {
        $this->parent = null;
    }

    public function setParent($p)
    {
        $this->parent = $p;
        if ($this->isSelected)
            $this->parent->setValue($this->value);
    }

    public function push($name, $value)
    {
        if ($name == "selected" && $value==NULL) {
            $this->isSelected = true;
        } else {
            parent::push($name, $value);
        }
    }

    public function addValue($content)
    {
        $this->label .= $content;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getHTML($selected)
    {
        $html = '<option ';
        foreach ($this->attributes as $name => $value) {
            $html.= $name.'="'.$value.'" ';
        }
        if ($selected) {
            $html.='selected="selected" ';
        }
        $html.= 'value="'.htmlspecialchars($this->value).'"';
        $html.= '>'.$this->label;
        $html.= "</option>\n";

        return $html;
    }
}
