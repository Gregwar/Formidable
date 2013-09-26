<?php

namespace Gregwar\Formidable;

/**
 * Inclusion des types
 */
require_once(__DIR__.'/Head.php');
require_once(__DIR__.'/Fields/Field.php');
require_once(__DIR__.'/Fields/Textarea.php');
require_once(__DIR__.'/Fields/Radios.php');
require_once(__DIR__.'/Fields/Select.php');
require_once(__DIR__.'/Fields/Option.php');
require_once(__DIR__.'/Fields/Options.php');
require_once(__DIR__.'/Fields/Custom.php');

/**
 * Inclusion de Fields/xxxField.php
 */

$fields_dir = __DIR__.'/Fields';

$dir = opendir($fields_dir);
while ($file = readdir($dir)) {
    if (substr($file, -9) == 'Field.php') {
        require_once($fields_dir.'/'.$file);
    }
}
closedir($dir);

/**
 * Contexte de formulaire
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class FormContext
{
    /**
     * Contexte par défaut
     */
    public static $defaultContext = null;

    /**
     * Classe du formulaire
     */
    protected $formClass = '\Gregwar\Formidable\Form';

    /**
     * Résolution des types de champs
     */
    protected $typeClasses = array(
        'text' => '\Gregwar\Formidable\Fields\TextField',
        'email' => '\Gregwar\Formidable\Fields\EmailField',
        'number' => '\Gregwar\Formidable\Fields\NumberField',
        'int' => '\Gregwar\Formidable\Fields\IntField',
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
     * Classe pour les objets
     */
    private $objectClasses = array(
        'form' => '\Gregwar\Formidable\Head',
        'textarea' => '\Gregwar\Formidable\Fields\Textarea',
        'options' => '\Gregwar\Formidable\Fields\Options',
        'option' => '\Gregwar\Formidable\Fields\Option',
        'radios' => '\Gregwar\Formidable\Fields\Radios',
        'select' => '\Gregwar\Formidable\Fields\Select',
        'custom' => '\Gregwar\Formidable\Fields\Custom'
    );

    /**
     * Classe du Parser
     */
    protected $parserClass = '\Gregwar\Formidable\Parser';

    /**
     * Enregistrer un type
     */
    public function registerType($type, $class)
    {
        $this->typeClasses[$type] = $class;
    }

    /**
     * Obtenir le Parser
     */
    public function getParser($content)
    {
        $parserClass = $this->parserClass;
        return new $parserClass($content, $this);
    }

    /**
     * Obtenir une tête
     */
    public function getHead()
    {
        return new $this->headClass;
    }

    /**
     * Obtenir un champs du type donné
     */
    public function getField($name)
    {
        return new $this->typeClasses[$name];
    }

    /**
     * Obtenir une balise
     */
    public function getObject($name)
    {
        return new $this->objectClasses[$name];
    }

    /**
     * Obtenir un formulaire pour ce contexte
     */
    public function getForm($pathOrContent, array $vars = array()) {
        $formClass = $this->formClass;
        return new $formClass($pathOrContent, $vars, $this);
    }

    public static function getDefault()
    {
        if (null === self::$defaultContext) {
            self::$defaultContext = new self;
        }

        return self::$defaultContext;
    }
}
