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

    // XXX: c'est horrible, à modifier
    public static $CHECKED = 0;
    public static $NOTCHECKED = 1;
    public static $OPTIONAL = 2;

    public static function error($name)
    {
        return 'Vous devez cocher une des cases pour le champ '.$name;
    }

    public function __construct()
    {
        $this->type = 'radio';
    }

    public function push($name, $value)
    {
        if ($name=='checked') {
            $this->checked = true;
        } else {
            parent::push($name, $value);
        }
    }

    public function setValue($val)
    {
        if ($this->value == $val) {
            $this->checked = true;
        } else {
            $this->checked = false;
        }
    }

    public function isChecked()
    {
        return $this->checked;
    }

    public function check()
    {
        if ($this->optional)
            return self::$OPTIONAL;
        if ($this->checked)
            return self::$CHECKED;
        return self::$NOTCHECKED;
    }

    public function getHTML()
    {
        if ($this->checked) {
            return self::getHTML('checked');
        } else {
            return self::getHTML();
        }
    }
}
