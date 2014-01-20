<?php

namespace Gregwar\Formidable;

/**
 * Form factory
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Factory extends Language\LanguageAware
{
    /**
     * Form clas
     */
    protected $formClass = '\Gregwar\Formidable\Form';

    /**
     * Parser class
     */
    protected $parserClass = '\Gregwar\Formidable\Parser';

    /**
     * Field types
     */
    protected $typeClasses = array(
        'text' => '\Gregwar\Formidable\Fields\TextField',
        'email' => '\Gregwar\Formidable\Fields\EmailField',
        'number' => '\Gregwar\Formidable\Fields\NumberField',
        'numeric' => '\Gregwar\Formidable\Fields\NumberField',
        'int' => '\Gregwar\Formidable\Fields\IntField',
        'integer' => '\Gregwar\Formidable\Fields\IntField',
        'file' => '\Gregwar\Formidable\Fields\FileField',
        'radio' => '\Gregwar\Formidable\Fields\RadioField',
        'checkbox' => '\Gregwar\Formidable\Fields\CheckboxField',
        'captcha' => '\Gregwar\Formidable\Fields\CaptchaField',
        'hidden' => '\Gregwar\Formidable\Fields\HiddenField',
        'multicheckbox' => '\Gregwar\Formidable\Fields\MulticheckboxField',
        'multiradio' => '\Gregwar\Formidable\Fields\MultiradioField',
	'password' => '\Gregwar\Formidable\Fields\PasswordField',
	'date' => '\Gregwar\Formidable\Fields\DateField',
    );

    /**
     * Objects types
     */
    private $objectClasses = array(
        'form' => '\Gregwar\Formidable\Head',
        'textarea' => '\Gregwar\Formidable\Fields\Textarea',
        'options' => '\Gregwar\Formidable\Fields\Options',
        'option' => '\Gregwar\Formidable\Fields\Option',
        'radios' => '\Gregwar\Formidable\Fields\Radios',
        'select' => '\Gregwar\Formidable\Fields\Select',
        'custom' => '\Gregwar\Formidable\Fields\Custom',
        'multiple' => '\Gregwar\Formidable\Fields\Multiple'
    );

    /**
     * Register a type
     */
    public function registerType($type, $class)
    {
        $this->typeClasses[$type] = $class;
    }

    protected function inject($object)
    {
        if ($object instanceof Language\LanguageAware) {
            $object->setLanguage($this->language);
        }

        return $object;
    }

    /**
     * Get a parser
     */
    public function getParser($content, $offset = 0)
    {
        $parserClass = $this->parserClass;

        return $this->inject(new $parserClass($content, $this, $offset));
    }

    /**
     * Get a field of a given type
     */
    public function getField($name)
    {
        return $this->inject(new $this->typeClasses[$name]);
    }

    /**
     * Get an object
     */
    public function getObject($name)
    {
        return $this->inject(new $this->objectClasses[$name]);
    }

    /**
     * Get a form in this factory
     */
    public function getForm($pathOrContent, array $vars = array(), $cache = false) {
        $formClass = $this->formClass;

        return $this->inject(new $formClass($pathOrContent, $vars, $cache, $this));
    }

    /**
     * Constructs the factory
     */
    public function __construct()
    {
        $this->language = new Language\English;
    }
}
