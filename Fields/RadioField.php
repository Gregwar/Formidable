<?php
namespace Gregwar\DSD\Fields;

/**
 * Un champ radio
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class RadioField extends Field
{
    /**
     * La case est t-elle cochée ?
     */
    protected $checked = false;

    /**
     * Radios parent
     */
    protected $parent;

    /**
     * Type du champ
     */
    protected $type = 'radio';

    public function setParent($parent)
    {
        $this->parent = $parent;

        if ($this->checked) {
            $parent->setValue($this->value);
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
        throw new \Exception('Un champ radio ne peut pas être vérifié');
    }
}
