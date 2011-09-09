<?php
namespace Gregwar\DSD\Fields;

/**
 * Un champ radio
 *
 * @author GrÃ©goire Passault <g.passault@gmail.com>
 */
class RadioField extends Field
{
    /**
     * La case est t-elle cochÃ©e ?
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
        throw new \Exception('Un champ radio ne peut pas Ãªtre vÃ©rifiÃ©');
    }
}
