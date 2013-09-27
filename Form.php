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
     * Is the fomr parsed
     */
    protected $parsed = false;

    /**
     * Objects & strings
     */
    protected $datas = array();

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
     * Contexte
     */
    protected $context;

    /**
     * File path
     */
    protected $path;

    public function __construct($pathOrContent = '', array $vars = array(), $context = null)
    {
        if (null === $context) {
            $this->context = new Context;
        } else {
            $this->context = $context;
        }

        if (isset($pathOrContent)) {
            if (strlen($pathOrContent) > 100 || strpos($pathOrContent, "\n") !== false) {
                $this->content = $pathOrContent;
            } else {
                $this->path = $pathOrContent;
                $this->getContent($vars);
            }
        }
    }

    /**
     * Sets the language
     */
    public function setLanguage(Language\Language $language)
    {
        $this->context->setLanguage($language);
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
        if (!$this->parsed) {
            $this->parsed = true;
            $parser = $this->context->getParser($this->content);
            $this->setParsedDatas($parser);
        }
    }

    /**
     * Gt data from the parser
     */
    protected function setParsedDatas(Parser $parser)
    {
        $this->parser = clone $parser;

        $this->datas = $parser->getDatas();
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
        $this->parse();

        $this->setParsedDatas($this->parser);
    }

    /**
     * Get the security token
     */
    public function getToken()
    {
        $this->parse();

        return $this->token;
    }

    /**
     * Get a field by name
     */
    public function getField($name)
    {
        $this->parse();

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
        $this->parse();

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
        $this->parse();

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
        $this->parse();

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
        $this->parse();

        $this->fields[$name]->setValue($value, 1);
    }

    /**
     * Gets a field value
     */
    public function getValue($name)
    {
        $this->parse();

        return $this->fields[$name]->getValue();
    }

    /**
     * Add a constraint on a field
     */
    public function addConstraint($name, $closure)
    {
        $this->parse();

        $this->fields[$name]->addConstraint($closure);
    }

    /**
     * Defines an attribute value
     */
    public function setAttribute($name, $attr_name, $value)
    {
        $this->parse();

        $this->fields[$name]->setAttribute($attr_name, $value);
    }

    /**
     * Get a field attribute
     */
    public function getAttribute($name, $attr_name)
    {
        $this->parse();

        return $this->fields[$name]->getAttribute($attr_name);
    }

    /**
     * Sets the class of an option field
     */
    public function setOptionClass($select, $val, $class)
    {
        $this->parse();

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
        $this->parse();
        $html = '';

        if ($this->needJs) {
            $html.= '<script type="text/javascript">'.file_get_contents(__DIR__.'/Js/formidable.js').'</script>';
        }

        foreach ($this->datas as $d) {
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
        $this->parse();

        $toCheck = array_flip(func_get_args());
        $errors = array();

        foreach ($this->fields as $name => $field) {
            if (!count($toCheck) || isset($toCheck[$name])) {
                $error = $field->check();

                if ($error) {
                    $errors[] = new Error($field, $error);
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
        $this->parse();

        $this->sources[$source]->source($data);
    }

    /**
     * Gets the data using mapping
     */
    public function getData($tableOrEntity = null)
    {
        $this->parse();

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
        $this->parse();

        return $this->getValue($name);
    }

    /**
     * Set a field value
     */
    public function __set($var, $val)
    {
        $this->parse();

        $this->setValue($var, $val);
    }

    /**
     * Get a field
     */
    public function get($name)
    {
        $this->parse();

        return $this->fields[$name];
    }

    /**
     * Check if the form was posted
     */
    public function posted()
    {
        $this->parse();

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
        $this->parse();

        if ($this->posted()) {
            $errors = $this->check();

            if (!$errors) {
                if (null !== $callback) {
                    $callback($this->getDatas());
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
        $this->parse();

        $this->position = 0;
    }

    public function next()
    {
        $this->parse();

        $this->position++;
    }

    public function current()
    {
        $this->parse();

        return ($this->fields[$this->position]);
    }

    public function valid()
    {
        $this->parse();

        return isset($this->fields[$this->position]);
    }

    public function key()
    {
        $this->parse();

        return $this->fields[$this->position]->getName();
    }
}
