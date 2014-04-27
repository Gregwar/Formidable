<?php

namespace Gregwar\Formidable;

use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * A Formidable Form
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Form
{
    /**
     * HTML contents of the form
     */
    protected $content = null;

    /**
     * File path
     */
    protected $path = null;

    /**
     * File variables
     */
    protected $variables = null;

    /**
     * Current position for iterator
     */
    protected $position = 0;

    /**
     * Factory
     */
    protected $factory;

    /**
     * Parser raw data
     */
    protected $originalParserData;
    protected $parserData;

    /**
     * Property accessor
     */
    protected $accessor = null;

    /***
     * Cache system
     */
    protected $cache = null;

    /**
     * Form constraints
     */
    protected $constraints = array();

    /**
     * Is the form cached?
     */
    public $isCached = true;

    public function __construct($pathOrContent = '', $variables = null, $cache = false, $factory = null)
    {
        if (null === $factory) {
            $this->factory = new Factory;
        } else {
            $this->factory = $factory;
        }

        if ($cache !== null && $cache !== false) {
            if ($cache === true) {
                $this->cache = new \Gregwar\Cache\Cache;
            } else if ($cache instanceof \Gregwar\Cache\Cache) {
                $this->cache = $cache;
            } else {
                throw new \InvalidArgumentException('The parameter $cache should be false, true or an instance of Gregwar\Cache\Cache');
            }
        }

        if ($pathOrContent) {
            if (strpos($pathOrContent, "\n") !== false) {
                $this->content = $pathOrContent;
            } else {
                $this->path = $pathOrContent;
                $this->variables = $variables;
            }
        }

        $this->parse();
    }

    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Gets the parser data
     */
    public function getParserData()
    {
        return $this->parserData;
    }

    /**
     * Sets the language
     */
    public function setLanguage(Language\Language $language)
    {
        $this->factory->setLanguage($language);
        $this->pushLanguage();
    }

    /**
     * Push the language to all the fields
     */
    protected function pushLanguage()
    {
        $language = $this->factory->getLanguage();

	if ($language && $this->parserData) {
	    $fields = $this->parserData->getFields();
            foreach ($fields as &$field) {
                $field->setLanguage($this->factory->getLanguage());
            }
        }
    }

    /**
     * Get the form contents
     */
    public function getContent()
    {
        if ($this->content === null) {
            if (is_array($this->variables)) {
                extract($this->variables);
            } else {
                if ($this->variables !== null) {
                    throw new \InvalidArgumentException('$variables argument should be null or an array');
                }
            }

            ob_start();
            include($this->path);
            $this->content = ob_get_clean();
        }

        return $this->content;
    }

    /**
     * Parses the form contents to build objects
     */
    protected function parse()
    {
        $formidable = $this;
        $generate = function() use ($formidable) {
            // Parses the contents
            $parser = $formidable->getFactory()->getParser($formidable->getContent());
            $formidable->isCached = false;

            return $parser;
        };

        if ($this->cache) {
            $formInfos = array(
                'path' => $this->path,
                'content' => $this->content
            );
            $cacheFile = sha1(serialize($formInfos));

            $conditions = array();
            if ($this->path !== null) {
                $conditions['younger-than'] = $this->path;
            }

            $cacheData = $this->cache->getOrCreate($cacheFile, $conditions, function($cacheFile) use ($generate) {
                $parserData = $generate();
                file_put_contents($cacheFile, serialize($parserData));
            });

            $this->originalParserData = unserialize($cacheData);
        } else {
            $this->originalParserData = $generate();
        }
        
        $this->reset();
    }

    /**
     * Resets the form
     */
    public function reset()
    {
        $this->parserData = unserialize(serialize($this->originalParserData));
        $this->pushLanguage();
    }

    /**
     * Get all the values
     */
    public function getValues()
    {
        $values = array();
        
        foreach ($this->getFields() as $field) {
            $name = $field->getBaseName();
            $index = $field->getIndex();

            if ($index === null) {
                $values[$name] = $field->getValue();
            } else {
                if (!isset($values[$name])) {
                    $values[$name] = array();
                }

                if (trim($index)) {
                    $values[$name][$index] = $field->getValue();
                } else {
                    $values[$name][] = $field->getValue();
                }
            }
        }

        return $values;
    }

    /**
     * Define the values
     */
    public function setValues($values, array $files = array())
    {
        foreach ($this->getFields() as $name => $field) {
            $name = $field->getBaseName();
            $index = $field->getIndex();

            if ($index === null) { 
                if ($present = isset($values[$name])) {
                    $value =& $values[$name];
                }
            } else {
                if (trim($index)) {
                    if ($present = isset($values[$name]) && is_array($values[$name])
                        && isset($values[$name][$index])) {
                            $value = $values[$name][$index];
                    }
                } else {
                    if ($present = isset($values[$name]) && is_array($values[$name])) {
                        $value = in_array($field->getValue(), $values[$name]);
                    }
                }
            }

            if ($present) {
                if ($field instanceof Field\Multiple) {
                    $field->setValues($value, $files);
                } else {
                    $field->setValue($value, $files);
                }
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

        foreach ($this->getFields() as $field) {
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
     * Defines a placeholder value
     */
    public function setPlaceholder($name, $value)
    {
        $this->parserData->getPlaceholder($name)->setValue($value);
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
    public function addConstraint($name, $closure = null)
    {
        if ($name instanceof \Closure) {
            $closure = $name;
            $name = null;
        }

        if ($name == null) {
            $this->constraints[] = $closure;
        } else {
            $this->getField($name)->addConstraint($closure);
        }
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
     * Get a field
     */
    public function getField($name)
    {
        return $this->parserData->getField($name);
    }

    public function getFields()
    {
        return $this->parserData->getFields();
    }

    /**
     * Convert to HTML
     */
    public function __toString()
    {
        return $this->getHtml();
    }

    /**
     * Get the JavaScript code to embed
     */
    public function getJs()
    {
        $html = '<script type="text/javascript">';
        $js = file_get_contents(__DIR__.'/Js/formidable.js');
        $html .= str_replace("\n", '', str_replace('{remove}', $this->factory->getLanguage()->translate('remove'), $js));
        $html .= '</script>';

        return $html;
    }

    /**
     * Convert to HTML
     */
    public function getHtml()
    {
        $html = '';

        if ($this->parserData->needJs()) {
            $html .= $this->getJs();
        }

        foreach ($this->parserData->getData() as $data) {
            $html .= (string)$data;
        }

        if ($html[strlen($html)-1] != "\n") {
            $html .= "\n";
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

        foreach ($this->getFields() as $name => $field) {
            if (!count($toCheck) || isset($toCheck[$name])) {
                $error = $field->check();

                if ($error) {
                    $errors[] = new Error($field, $error, $this->factory->getLanguage());
                }
            
                if ($field instanceof Fields\Multiple) {
                    $errors = array_merge($errors, $field->checkForms());
                }
            }
        }

        foreach ($this->constraints as $constraint) {
            $error = $constraint($this);

            if ($error) {
                $errors[] = new Error(null, $error, $this->factory->getLanguage());
            }
        }

        return $errors;
    }

    /**
     * Values sourcing
     */
    public function source($source, $data)
    {
        $sources = $this->parserData->getSources();

        $sources[$source]->source($data);
    }

    /**
     * Gets the data using mapping
     */
    public function getData($entity = array())
    {
        if ($this->accessor == null) {
            $this->accessor = new PropertyAccessor;
        }

        foreach ($this->getFields() as $name => $field) {
            if ($mapping = $field->getMappingName()) {
                if (is_array($entity)) {
                    $entity[$mapping] = $field->getMappingValue();
                } else {
                    $this->accessor->setValue($entity, $mapping, $field->getMappingValue());
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
     * Get the CSRF manager
     */
    public function getPostIndicator()
    {
        foreach ($this->parserData->getData() as $entry) {
            if ($entry instanceof PostIndicator) {
                return $entry;
            }
        }

        return null;
    }

    /**
     * Gets the post indicator
     */
    public function getToken()
    {
        return $this->getPostIndicator()->getToken();
    }

    /**
     * Check if the form was posted
     */
    public function posted()
    {
        $postIndicator = $this->getPostIndicator();

        if ($postIndicator->posted()) {
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

    public function hookNames(\Closure $hook)
    {
        foreach ($this->getFields() as $field) {
            $field->hookName($hook);
        }
    }
}
