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

    /**
     * Gets a field
     */
    public function getField($name)
    {
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        } else {
            throw new \Exception('Field with name '.$name.' not found');
        }
    }

    public function copyParserData(ParserData $other)
    {
        // Cloning head
        $this->head = clone $other->head;

        // Cloning fields
        foreach ($other->fields as &$field) {
            $this->fields[$field->getName()] = clone $field;
        }
        
        // Setting references for radios
        foreach ($other->data as &$data) {
            if (is_object($data)) {
                if ($data instanceof Field) {
                    if ($data instanceof Fields\RadioField) {
                        $data = clone $data;
                        $radios = $this->fields[$data->getName()];
                        $radios->addRadio($data);
                    }
                }
            }
        }

        // Data
        $this->data = array();
        foreach ($other->data as $entry) {
            if (is_object($entry)) {
                if ($entry instanceof RadioField) {
                    $radios = $this->fields[$entry->getName()];
                    $this->data[] = $radios->getRadioForValue($entry->getValue());
                } else if ($entry instanceof Field) {
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
    }
}
