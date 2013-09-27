<?php

namespace Gregwar\Formidable;

/**
 * Error on a field
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Error
{
    /**
     * Field name
     */
    private $field;

    /**
     * Error message
     */
    private $message;

    public function __construct($field, $message)
    {
        $this->field = $field;
        $this->message = $message;
    }

    /**
     * Gets the message
     */
    public function __toString()
    {
        return $this->message;
    }

    /**
     * Get the field
     */
    public function getField()
    {
        return $this->field;
    }
}
