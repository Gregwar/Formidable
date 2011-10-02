<?php

namespace Gregwar\DSD;

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
    protected $formClass = '\Gregwar\DSD\Form';

    /**
     * Résolution des types de champs
     */
    protected $typeClasses = array(
        'text' => '\Gregwar\DSD\Fields\TextField',
        'email' => '\Gregwar\DSD\Fields\EmailField',
        'number' => '\Gregwar\DSD\Fields\NumberField',
        'int' => '\Gregwar\DSD\Fields\IntField',
        'file' => '\Gregwar\DSD\Fields\FileField',
        'radio' => '\Gregwar\DSD\Fields\RadioField',
        'checkbox' => '\Gregwar\DSD\Fields\CheckboxField',
        'captcha' => '\Gregwar\DSD\Fields\CaptchaField',
        'hidden' => '\Gregwar\DSD\Fields\HiddenField',
        'multicheckbox' => '\Gregwar\DSD\Fields\MulticheckboxField',
        'multiradio' => '\Gregwar\DSD\Fields\MultiradioField',
        'password' => '\Gregwar\DSD\Fields\PasswordField'
    );

    /**
     * Classe pour les objets
     */
    private $objectClasses = array(
        'form' => '\Gregwar\DSD\Head',
        'textarea' => '\Gregwar\DSD\Fields\Textarea',
        'options' => '\Gregwar\DSD\Fields\Options',
        'option' => '\Gregwar\DSD\Fields\Option',
        'radios' => '\Gregwar\DSD\Fields\Radios',
        'select' => '\Gregwar\DSD\Fields\Select',
        'custom' => '\Gregwar\DSD\Fields\Custom'
    );

    /**
     * Classe du Parser
     */
    protected $parserClass = '\Gregwar\DSD\Parser';

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
