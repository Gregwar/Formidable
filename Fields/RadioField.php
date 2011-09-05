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
    private $checked = false;

    public function __construct()
    {
        $this->type = 'radio';
    }

    public function push($name, $value)
    {
        if ($name == 'checked') {
            $this->checked = true;
            $this->setAttribute('checked', 'checked');
        } else {
            parent::push($name, $value);
        }
    }

    public function setValue($val)
    {
        if ($this->value == $val) {
            $this->checked = true;
            $this->setAttribute('checked', 'checked');
        } else {
            $this->checked = false;
            $this->unsetAttribute('checked');
        }
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
