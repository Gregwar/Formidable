<?php

namespace Gregwar\Formidable;

/**
 * A formidable Form
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Form implements \Iterator
{
    /**
     * HTML contents of the form
     */
    protected $content;

    /**
     * Objects & strings
     */
    protected $data = array();

    /**
     * Fields by name
     */
    protected $fields = array();

    /**
     * Information sources
     */
    protected $sources = array();

    /**
     *
     * Security token
     */
    protected $token;

    /**
     * Current position for iterator
     */
    protected $position = 0;

    /**
     * Form header
     */
    protected $head = null;

    /**
     * Does we need js?
     */
    protected $needJs = false;

    /**
     * Parser
     */
    protected $parser;

    /**
     * Factorye
     */
    protected $factory;

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
        $parser = $this->factory->getParser($this->content);
        $this->setParsedData($parser);
    }

    /**
     * Gt data from the parser
     */
    protected function setParsedData(Parser $parser)
    {
        $this->parser = clone $parser;

        $this->data = $parser->getData();
        $this->fields = $parser->getFields();
        $this->sources = $parser->getSources();
        $this->token = $parser->getHash();
        $this->needJs = $parser->needJs();
        $this->head = $parser->getHead();
    }

    /**
     * Resets the form
     */
    public function reset()
    {
        $this->setParsedData($this->parser);
    }

    /**
     * Get the security token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Get a field by name
     */
    public function getField($name)
    {
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        }

        return null;
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
        foreach ($this->fields as $field) {
            if (is_object($field)) {
                if (($mapping = $field->getMappingName()) && !$field->readOnly()) {
                    if (is_array($entity)) {
                        if (isset($entity[$mapping])) {
                            $field->setValue($entity[$mapping], 1);
                        }
                    } else {
                        if (isset($entity->$mapping)) {
                            $field->setValue($entity->$mapping, 1);
                        }
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
        $this->fields[$name]->setValue($value, 1);
    }

    /**
     * Gets a field value
     */
    public function getValue($name)
    {
        return $this->fields[$name]->getValue();
    }

    /**
     * Add a constraint on a field
     */
    public function addConstraint($name, $closure)
    {
        $this->fields[$name]->addConstraint($closure);
    }

    /**
     * Defines an attribute value
     */
    public function setAttribute($name, $attr_name, $value)
    {
        $this->fields[$name]->setAttribute($attr_name, $value);
    }

    /**
     * Get a field attribute
     */
    public function getAttribute($name, $attr_name)
    {
        return $this->fields[$name]->getAttribute($attr_name);
    }

    /**
     * Sets the class of an option field
     */
    public function setOptionClass($select, $val, $class)
    {
        $this->fields[$select]->setOptionClass($val, $class);
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
    public function getData($tableOrEntity = null)
    {
        if (gettype($tableOrEntity) == 'string') {
            $entity = new Entity($tableOrEntity);
        } else {
            if (null !== $tableOrEntity) {
                $entity = $tableOrEntity;
            } else {
                $entity = array();
            }
        }

        foreach ($this->fields as $name => $field) {
            if ($mapping = $field->getMappingName()) {
                if (is_array($entity)) {
                    $entity[$mapping] = $field->getValue();
                } else {
                    $entity->$mapping = $field->getValue();
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
     * Get a field
     */
    public function get($name)
    {
        return $this->fields[$name];
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
