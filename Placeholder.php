<?php

namespace Gregwar\Formidable;

class Placeholder
{
    protected $name;
    protected $value = '';

    public function __construct($name)
    {
        $this->name = trim($name);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return (string)($this->value);
    }

    public function __sleep()
    {
        return array('name');
    }

    public function __wakeup()
    {
        $this->value = '';
    }
}
