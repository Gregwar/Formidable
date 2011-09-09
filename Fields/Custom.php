<?php

namespace Gregwar\DSD\Fields;

/**
 * Champ de type custom
 *
 * @author GrÃ©goire Passault <g.passault@gmail.com>
 */
class Custom extends Field
{
    private $src;
    private $source;

    public function push($name, $value)
    {
        if ($name == 'source') {
            $this->source = $value;
        } else {
            parent::push($name, $value);
        }
    }

    public function getSource()
    {
        return $this->source;
    }

    public function source($src)
    {
        $this->src = $src;
    }

    public function getHTML()
    {
        return $this->src;
    }

    public function check()
    {
        return;
    }
}
