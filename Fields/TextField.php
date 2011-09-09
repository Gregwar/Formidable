<?php

namespace Gregwar\DSD\Fields;

/**
 * Champ text
 *
 * @author GrÃ©goire Passault <g.passault@gmail.com>
 */
class TextField extends Field
{
    public function __construct()
    {
        $this->type = 'text';
    }
}
