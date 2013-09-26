<?php

namespace Gregwar\Formidable\Fields;

/**
 * Champs caché
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class HiddenField extends Field
{
    /**
     * Type du champ
     */
    protected $type = 'hidden';

    /**
     * Optionel par défaut
     */
    protected $optional = true;
}
