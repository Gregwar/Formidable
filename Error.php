<?php

namespace Gregwar\Formidable;

/**
 * Erreur sur un champ
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Error
{
    /**
     * Champ correspondant
     */
    private $field;

    /**
     * Message de l'erreur
     */
    private $message;

    public function __construct($field, $message)
    {
        $this->field = $field;
        $this->message = $message;
    }

    public function __toString()
    {
        return $this->message;
    }

    /**
     * Obtenir le champs
     */
    public function getField()
    {
        return $this->field;
    }
}
