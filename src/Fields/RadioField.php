<?php

namespace Gregwar\Formidable\Fields;

/**
 * Radio field
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class RadioField extends Field
{
    /**
     * Is this box checked ?
     */
    protected $checked = false;

    /**
     * Parend radios
     */
    protected $parent;

    /**
     * Field type
     */
    protected $type = 'radio';
    
    public function __sleep()
    {
        return array_merge(parent::__sleep(), array(
            'checked', 'parent'
        ));
    }

    public function setParent($parent)
    {
        $this->parent = $parent;

        if ($this->checked) {
            $parent->setValue($this->value);
        }

        if ($this->required) {
            $parent->push('required', null);
        }
    }

    public function push($name, $value = null)
    {
        if ($name == 'checked') {
            $this->checked = true;
            $this->setAttribute('checked', 'checked');
            if (null !== $this->parent) {
                $this->parent->setValue($this->value);
            }
        } else {
            parent::push($name, $value);
        }
    }

    public function setChecked($checked)
    {
        if ($checked) {
            $this->setAttribute('checked', 'checked');
        } else {
            $this->unsetAttribute('checked');
        }
        $this->checked = $checked;
    }

    public function isChecked()
    {
        return $this->checked;
    }

    public function check()
    {
        throw new \Exception('A radio field should not be checked');
    }
}
