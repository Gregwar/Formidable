<?php

namespace Gregwar\Formidable\Fields;

/**
 * Radios
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class MultiradioField extends Field
{
    /**
     * Source name
     */
    protected $source;

    /**
     * Radios
     */
    protected $radios = array();

    /**
     * Radios labels
     */
    protected $labels = array();

    /**
     * Push saves
     */
    protected $pushSave = array();

    public function __sleep()
    {
        return array_merge(parent::__sleep(), array(
            'source', 'radios', 'labels', 'pushSave'
        ));
    }

    public function check()
    {
        if ($this->required && !$this->value) {
            return array('should_check', $this->printName());
        }
    }

    public function push($var, $value = null)
    {
        switch ($var) {
        case 'type':
            break;
        case 'source':
            $this->source = $value;
            break;
        default:
            $this->pushSave[$var] = $value;
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
        foreach ($datas as $key => $label) {
            $this->radios[] = $radio = new RadioField;
            $radio->push('name', $this->getName());
            $radio->setValue($key);

            foreach ($this->pushSave as $var => $val) {
                $radio->push($var, $val);
            }
            $this->labels[$key] = $label;
        }
    }

    public function setValue($value, $default = false)
    {
        $set = false;

        if (!is_string($value) && !is_float($value) && !is_int($value)) {
            return;
        }

        foreach ($this->radios as $radio) {
            if ($radio->getValue() == $value) {
                $radio->setChecked(true);
                $set = true;
            } else {
                $radio->setChecked(false);
            }
        }

        parent::setValue($set ? $value : null);
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getHtml()
    {
        $html = '';

        if ($this->radios) {
            foreach ($this->radios as $radio) {
                $html.= '<div class="'.$this->getAttribute('class').'">';
                $html.= '<label>';
                $html.= $radio->getHtml();
                $html.= $this->labels[$radio->getValue()];
                $html.= '</label>';
                $html.= '</div>';
            }
        }

        return $html;
    }
}
