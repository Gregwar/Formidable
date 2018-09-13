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
     * Is it a multiple select?
     */
    protected $multiple = false;

    /**
     * Allow adding values ?
     */
    protected $allowAdd = false;

    /**
     * Childrens
     */
    protected $options = array();

    public function push($name, $value = null)
    {
        if ($name == 'multiple') {
            $this->multiple = true;
        }
        if ($name == 'allow-add') {
            $this->allowAdd = true;
        }

        parent::push($name, $value);
    }

    public function __sleep()
    {
        return array_merge(parent::__sleep(), array(
            'options', 'multiple', 'allowAdd'
        ));
    }

    public function setValue($value, $default = false)
    {
        if ($this->multiple) {
            if (is_array($value)) {
                $availableOptions = [];
                foreach ($this->options as $option) {
                    $availableOptions[$option->getValue()] = true;
                }

                foreach ($value as &$v) {
                    $v = (string)$v;
                    if ($this->allowAdd && !isset($availableOptions[$v])) {
                        $option = new Option;
                        $option->setValue($v);
                        $option->setLabel($v);
                        $this->addOption($option);
                    }
                }
                $this->value = $value;
            } else {
                $this->value = null;
            }
        } else {
            parent::setValue($value, $default);
        }
    }

    public function countOptions()
    {
        return count($this->options);
    }

    public function addOption($option, $position = null)
    {
        $this->addOptions(array($option), $position);
    }

    public function addOptions(array $options, $position = null)
    {
        foreach ($options as $option) {
            $option->setParent($this);
        }

        if ($position === null) {
            $this->options = array_merge($this->options, $options);
        } else {
            $before = array_slice($this->options, 0, $position);
            $after = array_slice($this->options, $position);

            $this->options = array_merge($before, $options, $after);
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

        if (!$this->required && $this->value == '') {
            return;
        } else {
            foreach ($this->options as $opt) {
                if ($this->allowAdd || (($this->multiple && in_array($opt->getValue(), $this->value))
                    || (!$this->multiple && $this->value == $opt->getValue()))) {
                    return;
                }
            }
        }

        return array('should_choose', $this->printName());
    }

    public function getHtml()
    {
        $arr = '';
        if ($this->multiple) {
            $arr = '[]';
        }

        $html = '<select name="'.$this->getName().$arr.'" ';
        foreach ($this->attributes as $name => $value) {
            $html.= $name.'="'.$value.'" ';
        }
        $html.= ">\n";

        foreach ($this->options as $option) {
            if (($this->multiple && is_array($this->value) && in_array($option->getValue(), $this->value))
                || (!$this->multiple && $option->getValue() == $this->value)) {
                $html .= $option->getOptionHtml(true);
            } else {
                $html .= $option->getOptionHtml(false);
            }
        }
        $html .= "</select>\n";

        return $html;
    }
}
