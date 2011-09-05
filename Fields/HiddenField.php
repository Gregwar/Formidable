<?php

namespace Gregwar\DSD\Fields;

/**
 * Champs caché
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class HiddenField extends Field
{
    public function __construct()
    {
        $this->type = 'hidden';
    }

    public function check()
    {
        return;
    }
}
