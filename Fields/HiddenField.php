<?php

namespace Gregwar\DSD\Fields;

/**
 * Champs cachÃ©
 *
 * @author GrÃ©goire Passault <g.passault@gmail.com>
 */
class HiddenField extends Field
{
    public function __construct()
    {
        $this->type = 'hidden';
        $this->optional = true;
    }

    public function check()
    {
        return;
    }
}
