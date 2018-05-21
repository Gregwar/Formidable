<?php

namespace Gregwar\Formidable\Fields;

/**
 * An option field
 *
 * @author Grégoirr Passault <g.passault@gmail.com>
 */
class Option extends Field
{
    /**
     * Parent select
     */
    protected $parent;

    /**
     * Is this option selected ?
     */
    protected $selected = false;
    
    public function __sleep()
    {
        return array_merge(parent::__sleep(), array(
            'parent', 'selected', 'label'
        ));
    }

    /**
     * Label
     */
    protected $label;

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

    public function push($name, $value = null)
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

    public function getOptionHtml($selected)
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
