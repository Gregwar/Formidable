<?php

namespace Gregwar\Formidable\Fields;

/**
 * Checkboxs
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class MulticheckboxField extends Field
{
    /**
     * Source name
     */
    protected $source;

    /**
     * Checkboxes
     */
    protected $checkboxes = array();

    /**
     * Labels
     */
    protected $labels = array();

    /**
     * Push saving
     */
    protected $pushSave = array();
    
    public function __sleep()
    {
        return array_merge(parent::__sleep(), array(
            'labels', 'source', 'checkboxes', 'pushSave'
        ));
    }

    public function check()
    {
        return;
    }

    public function push($var, $value = null)
    {
        switch ($var) {
        case 'source':
            $this->source = $value;
            break;
        default:
            parent::push($var, $value);
            break;
        }
    }

    public function getSource()
    {
        return $this->source;
    }

    public function source($datas)
    {
        foreach ($datas as $value => $label) {
            $this->checkboxes[$value] = $checkbox = new CheckboxField;
            $checkbox->push('name', $this->nameFor($value));
            $checkbox->push('value', '1');
            $this->labels[$this->nameFor($value)] = $label;

            foreach ($this->pushSave as $var => $val) {
                $checkbox->push($var, $val);
            }
        }
    }

    protected function nameFor($name) 
    {
        return $this->getName().'['.$name.']';
    }

    public function setValue($values, $default = false)
    {
        if (!is_array($values)) {
            return;
        }

        $checked = array();

        foreach ($values as $name => $one) {
            $checked[$this->nameFor($name)] = true;
        }

        foreach ($this->checkboxes as $checkbox) {
            if (isset($checked[$checkbox->getName()])) {
                $checkbox->setChecked(true);
            } else {
                $checkbox->setChecked(false);
            }
        }
    }

    public function getValue()
    {
        $values = array();
        foreach ($this->checkboxes as $key => $checkbox) {
            if ($checkbox->isChecked()) {
                $values[] = $key;
            }
        }
        return $values;
    }

    public function getHtml()
    {
        $html = '';

        if ($this->checkboxes) {
            foreach ($this->checkboxes as $checkbox) {
                $html.= '<div class="'.$this->getAttribute('class').'">';
                $html.= '<label>';
                $html.= $checkbox->getHtml();
                $html.= $this->labels[$checkbox->getName()];
                $html.= '</label>';
                $html.= '</div>';
            }
        }

        return $html;
    }
}
