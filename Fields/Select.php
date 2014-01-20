<?php

namespace Gregwar\Formidable\Fields;

/**
 * A select field
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Select extends Field
{
    /**
     * Childrens
     */
    protected $options = array();
    
    public function __sleep()
    {
        return array_merge(parent::__sleep(), array(
            'options'
        ));
    }

    public function countOptions()
    {
        return count($this->options);
    }

    public function addOption($option, $position = null)
    {
        $option->setParent($this);

        if ($position == null) {
            $this->options[] = $option;
        } else {
            for ($i = $this->countOptions(); $i > $position; $i--) {
                $this->options[$i] = $this->options[$i-1];
            }
            $this->options[$position] = $option;
        }
    }

    public function addValue($c)
    {
        $this->options[count($this->options)-1]->addValue($c);
    }

    public function check()
    {
        if ($error = parent::check()) {
            return $error;
        }

        foreach ($this->options as $opt) {
            if ($this->value == $opt->getValue()) {
                return;
            }
        }

        return array('should_choose', $this->printName());
    }

    public function getHtml()
    {
        $html = '<select name="'.$this->getName().'" ';
        foreach ($this->attributes as $name => $value) {
            $html.= $name.'="'.$value.'" ';
        }
	$html.= ">\n";

	if (!$this->required) {
	    $html .= '<option value=""></option>';
	}

        foreach ($this->options as $option) {
            if ($option->getValue() == $this->value) {
                $html .= $option->getOptionHtml(true);
            } else {
                $html .= $option->getOptionHtml(false);
            }
        }
        $html .= "</select>\n";

        return $html;
    }
}
