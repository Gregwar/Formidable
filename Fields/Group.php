<?php

namespace Gregwar\Formidable\Fields;

use Gregwar\Formidable\Language\Language;

/**
 * A group handles the [] entries of an array
 */
class Group extends Field
{
    /**
     * Childs
     */
    protected $childs = array();

    /**
     * A bad value was passed
     */
    protected $badValue = false;

    public function addChild(Field $child)
    {
        $this->childs[] = $child;
    }

    public function check()
    {
        if ($this->badValue) {
            return array('bad_array_value', $this->printName());
        }

        foreach ($this->childs as $child) {
            if ($error = $child->check()) {
                return $error;
            }
        }
    }

    public function setValue($value, $default = false)
    {
        $this->badValue = false;

        if (!$value) {
            return;
        }

        if (!is_array($value)) {
            $this->badValue = true;
            return;
        }

        $position = 0;

        foreach ($this->childs as $child) {
            if ($child instanceof CheckboxField) {
                if (in_array($child->getCheckedValue(), $value)) {
                    $child->setChecked(true);
                }
            } else {
                if ($position < count($value)) {
                    $child->setValue($value[$position++]);
                }
            }
        }
    }

    public function getValue()
    {
        $value = array();

        foreach ($this->childs as $child) {
            $fieldValue = $child->getValue();

            if ($fieldValue != '') {
                $value[] = $fieldValue;
            }
        }

        return $value;
    }

    public function setLanguage(Language $language)
    {
        parent::setLanguage($language);

        foreach ($this->childs as $child) {
            $child->setLanguage($language);
        }
    }

    public function __sleep()
    {
        return array_merge(parent::__sleep(), array('childs'));
    } 
}
