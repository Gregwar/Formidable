<?php

namespace Gregwar\Formidable;

use Gregwar\Formidable\Fields\Field;

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
     * Hash CSRF
     */
    protected $token = '';

    /**
     * Does we need js?
     */
    protected $needJs = false;

    /**
     * Form header
     */
    protected $head = null;

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

    public function copyParserData(ParserData $other)
    {
        // Cloning head
        $this->head = clone $other->head;

        // Cloning fields
        foreach ($other->fields as &$field) {
            $this->fields[$field->getName()] = clone $field;
        }

        // Token
        $this->token = $other->token;

        // Data
        $this->data = array();
        foreach ($other->data as $entry) {
            if (is_object($entry)) {
                if ($entry instanceof Field) {
                    $this->data[] = $this->fields[$entry->getName()];
                } else {
                    $this->data[] = clone $entry;
                }
            } else {
                $this->data[] = $entry;
            }
        }

        // Cloning sources
        foreach ($other->sources as $key => &$source) {
            if ($source instanceof Fields\Options) {
                $name = $source->getParent()->getName();
                $mySource = clone $source;
                $mySource->setParent($this->fields[$name]);
            } else {
                $mySource = $this->fields[$source->getName()];
            }
            $this->sources[$key] = $mySource;
        }

        // Getting data references
        foreach ($other->data as &$data) {
            if (is_object($data)) {
                if ($data instanceof Head) {
                    $data = $other->getHead();
                } else {
                    if ($data instanceof Fields\RadioField) {
                        $data = clone $data;
                        $this->fields[$data->getName()]->addRadio($data);
                    } else {
                        $data = $this->fields[$data->getName()];
                    }
                }
            }
        }
    }
}
