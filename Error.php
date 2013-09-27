<?php

namespace Gregwar\Formidable;

use Gregwar\Formidable\Language\Language;
use Gregwar\Formidable\Language\LanguageAware;

/**
 * Error on a field
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Error extends LanguageAware
{
    /**
     * Field name
     */
    private $field;

    /**
     * Error message
     */
    private $message;

    public function __construct($field, $message, Language $language)
    {
        $this->field = $field;
        $this->message = $message;
        $this->language = $language;
    }
    
    /**
     * Gets the message
     */
    public function getMessage()
    {
        $message = $this->message;

        if (is_array($message)) {
            $message = $this->language->translateArray($message);
        }

        return $message;
    }

    /**
     * Gets the message
     */
    public function __toString()
    {
        return $this->getMessage();
    }

    /**
     * Get the field
     */
    public function getField()
    {
        return $this->field;
    }
}
