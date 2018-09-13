<?php

namespace Gregwar\Formidable;

/**
 * Form header (<form>)
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Head
{
    /**
     * Attributes
     */
    private $attributes = array();

    public function push($name, $value)
    {
        $this->set($name, $value);
    }

    public function set($name, $value) 
    {
        $this->attributes[$name] = $value;
    }

    public function has($name)
    {
        return isset($this->attributes[$name]);
    }

    public function get($name)
    {
        return $this->attributes[$name];
    }

    public function getHtml()
    {
        $html = '<form';
        foreach ($this->attributes as $name => $value) {
            $html.= ' '.$name.'="'.$value.'"';
        }
        $html.= '>';

        return $html;
    }

    public function __toString()
    {
        return $this->getHtml();
    }

    public function needJs()
    {
        return false;
    }

    public function getSource()
    {
        return null;
    }
}
