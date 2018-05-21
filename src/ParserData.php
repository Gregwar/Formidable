<?php

namespace Gregwar\Formidable;

use Gregwar\Formidable\Fields\Field;
use Gregwar\Formidable\Fields\RadioField;

class ParserData
{
    /**
     * Objects in the form
     */
    protected $data = array();

    /**
     * Sources
     */
    protected $sources = array();

    /**
     * Fields
     */
    protected $fields = array();

    /**
     * Does we need js?
     */
    protected $needJs = false;

    /**
     * Placeholders
     */
    protected $placeholders = array();

    /**
     * Form header
     */
    protected $head = null;

    public function __sleep()
    {
        return array('data', 'sources', 'fields', 'needJs', 'head', 'placeholders');
    }

    /**
     * Form components
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Fields mapped by name
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Sources
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * CSRF token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Gets the head
     */
    public function getHead()
    {
        return $this->head;
    }

    /**
     * Does we need js?
     */
    public function needJs()
    {
        return $this->needJs;
    }

    /**
     * Gets a field
     */
    public function getField($name)
    {
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        } else {
            throw new \InvalidArgumentException('Field with name '.$name.' not found');
        }
    }

    public function getPlaceholder($name)
    {
        if (isset($this->placeholders[$name])) {
            return $this->placeholders[$name];
        } else {
            throw new \InvalidArgumentException('Placeholder with name '.$name.' not found');
        }
    }
}
