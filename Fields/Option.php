<?php

namespace Gregwar\DSD\Fields;

/**
 * Une option
 *
 * @author Grégoirr Passault <g.passault@gmail.com>
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
    private $selected = false;

    /**
     * Label
     */
    private $label;

    public function __construct()
    {
        $this->parent = null;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;

        if ($this->selected) {
            $this->parent->setValue($this->value, true);
        }
    }

    public function push($name, $value)
    {
        if ($name == 'selected') {
            $this->selected = true;
            if (null !== $this->parent) {
                $this->parent->setValue($this->value);
            }
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

    public function getHtml($selected)
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
