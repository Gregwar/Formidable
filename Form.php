<?php

namespace Gregwar\Formidable;

use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * A Formidable Form
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Form extends ParserData implements \Iterator
{
    /**
     * HTML contents of the form
     */
    protected $content;

    /**
     * Current position for iterator
     */
    protected $position = 0;

    /**
     * Parser
     */
    protected $parser;

    /**
     * Factory
     */
    protected $factory;

    /**
     * Property accessor
     */
    protected $accessor = null;

    /**
     * File path
     */
    protected $path;

    public function __construct($pathOrContent = '', array $vars = array(), $factory = null)
    {
        if (null === $factory) {
            $this->factory = new Factory;
        } else {
            $this->factory = $factory;
        }

        if ($pathOrContent) {
            if (strlen($pathOrContent) > 100 || strpos($pathOrContent, "\n") !== false) {
                $this->content = $pathOrContent;
            } else {
                $this->path = $pathOrContent;
                $this->getContent($vars);
            }
        }

        $this->parse();
    }

    /**
     * Sets the language
     */
    public function setLanguage(Language\Language $language)
    {
        $this->factory->setLanguage($language);
    }

    /**
     * Get the form contents
     */
    public function getContent($vars = array())
    {
        foreach ($vars as $k=>$v) {
            $$k = $v;
        }

        ob_start();
        include($this->path);
        $this->content = ob_get_clean();
    }

    /**
     * Parses the form contents to build objects
     */
    protected function parse()
    {
        $this->parser = $this->factory->getParser($this->content);
        $this->copyParserData($this->parser);
    }

    /**
     * Resets the form
     */
    public function reset()
    {
        $this->copyParserData($this->parser);
    }

    /**
     * Get all the values
     */
    public function getValues()
    {
        $values = array();
        
        foreach ($this->fields as $name => $field) {
            $values[$name] = $field->getValue();
        }

        return $values;
    }

    /**
     * Define the values
     */
    public function setValues($values, array $files = array())
    {
        foreach ($this->fields as $name => $field) {
            if (isset($values[$name])) {
                $field->setValue($values[$name]);
            } else {
                if ($field instanceof Fields\FileField && isset($files[$name])) {
                    $field->setValue($files[$name]);
                } else {
                    $field->setValue('');
                }
            }
        }
    }

    /**
     * Define the values using mapping
     */
    public function setData($entity)
    {
        if ($this->accessor == null) {
            $this->accessor = new PropertyAccessor;
        }

        foreach ($this->fields as $field) {
            if (is_object($field)) {
                if (($mapping = $field->getMappingName()) && !$field->readOnly()) {
                    if (is_array($entity)) {
                        if (isset($entity[$mapping])) {
                            $field->setValue($entity[$mapping], 1);
                        }
                    } else {
                        $value = $this->accessor->getValue($entity, $mapping);
                        $field->setValue($value, 1);
                    }
                }
            }
        }
    }

    /**
     * Defines a field value
     */
    public function setValue($name, $value)
    {
        $this->getField($name)->setValue($value, 1);
    }

    /**
     * Gets a field value
     */
    public function getValue($name)
    {
        return $this->getField($name)->getValue();
    }

    /**
     * Add a constraint on a field
     */
    public function addConstraint($name, $closure)
    {
        $this->getField($name)->addConstraint($closure);
    }

    /**
     * Defines an attribute value
     */
    public function setAttribute($name, $attribute, $value)
    {
        $this->getField($name)->setAttribute($attribute, $value);
    }

    /**
     * Get a field attribute
     */
    public function getAttribute($name, $attribute)
    {
        return $this->getField($name)->getAttribute($attribute);
    }

    /**
     * Sets the class of an option field
     */
    public function setOptionClass($select, $val, $class)
    {
        $this->getField($select)->setOptionClass($val, $class);
    }

    /**
     * Convert to HTML
     */
    public function __toString()
    {
        return $this->getHtml();
    }

    /**
     * Convert to HTML
     */
    public function getHtml()
    {
        $html = '';

        if ($this->needJs) {
            $html.= '<script type="text/javascript">'.file_get_contents(__DIR__.'/Js/formidable.js').'</script>';
        }

        foreach ($this->data as $d) {
            if (is_string($d)) {
                $html .= $d;
            } else {
                $html .= $d->getHtml();
            }
        }

        return $html;
    }

    /**
     * Error checking
     */
    public function check()
    {
        $toCheck = array_flip(func_get_args());
        $errors = array();

        foreach ($this->fields as $name => $field) {
            if (!count($toCheck) || isset($toCheck[$name])) {
                $error = $field->check();

                if ($error) {
                    $errors[] = new Error($field, $error, $this->factory->getLanguage());
                }
            }
        }

        return $errors;
    }

    /**
     * Values sourcing
     */
    public function source($source, $data)
    {
        $this->sources[$source]->source($data);
    }

    /**
     * Gets the data using mapping
     */
    public function getData($entity = array())
    {
        if ($this->accessor == null) {
            $this->accessor = new PropertyAccessor;
        }

        foreach ($this->fields as $name => $field) {
            if ($mapping = $field->getMappingName()) {
                if (is_array($entity)) {
                    $entity[$mapping] = $field->getValue();
                } else {
                    $this->accessor->setValue($entity, $mapping, $field->getValue());
                }
            }
        }

        return $entity;
    }

    /**
     * Get a field's value
     */
    public function __get($name)
    {
        return $this->getValue($name);
    }

    /**
     * Set a field value
     */
    public function __set($var, $val)
    {
        $this->setValue($var, $val);
    }

    /**
     * Check if the form was posted
     */
    public function posted()
    {
        if (isset($_POST['csrf_token']) && $_POST['csrf_token'] == $this->token) {
            $this->setValues($_POST, $_FILES);
            return true;
        }

        return false;
    }

    /**
     * Check a form, helper function
     */
    public function handle($callback = null, $errorsCallback = null)
    {
        if ($this->posted()) {
            $errors = $this->check();

            if (!$errors) {
                if (null !== $callback) {
                    $callback($this->getData());
                }
            } else {
                if (null !== $errorsCallback) {
                    $errorsCallback($errors);
                }
                return $errors;
            }
        }

        return array();
    }

    /**
     * Iterator methods
     */
    public function rewind()
    {
        $this->position = 0;
    }

    public function next()
    {
        $this->position++;
    }

    public function current()
    {
        return ($this->fields[$this->position]);
    }

    public function valid()
    {
        return isset($this->fields[$this->position]);
    }

    public function key()
    {
        return $this->fields[$this->position]->getName();
    }
}
