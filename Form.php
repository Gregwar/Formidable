<?php

namespace Gregwar\Formidable;

/**
 * Classe principale de Formidable
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Form implements \Iterator
{
    /**
     * Contenu (code HTML du formulaire)
     */
    protected $content;

    /**
     * Objets et chaîne de caractères représentant le formulaire Formidable
     */
    protected $datas = array();

    /**
     * Champs, indexés par nom
     */
    protected $fields = array();

    /**
     * Sources d'information
     */
    protected $sources = array();

    /**
     * Token de sécurité
     */
    protected $token;

    /**
     * Position courrante pour l'itération
     */
    protected $position = 0;

    /**
     * En-tête
     */
    protected $head = null;

    /**
     * Besoin de Js ?
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
     * Chemin du fichier
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
            $this->parse();
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
     * Obtenir le contenu du formulaire
     */
    public function getContent($vars = array()) {
        foreach ($vars as $k=>$v)
            $$k = $v ;

        ob_start();
        include($this->path);
        $this->content = ob_get_clean();
    }

    /**
     * Parse le contenu du formulaire pour construire les objets
     */
    protected function parse()
    {
        $parser = $this->context->getParser($this->content);
        $this->setParsedDatas($parser);
    }

    /**
     * Définit les données récupérées par le parser
     */
    protected function setParsedDatas(Parser $parser) {
        $this->parser = clone $parser;

        $this->datas = $parser->getDatas();
        $this->fields = $parser->getFields();
        $this->sources = $parser->getSources();
        $this->token = $parser->getHash();
        $this->needJs = $parser->needJs();
        $this->head = $parser->getHead();
    }

    /**
     * Remet à zéro le formulaire
     */
    public function reset()
    {
        $this->setParsedDatas($this->parser);
    }

    /**
     * Obtenir le token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Obtenir un champ par nom
     */
    public function getField($name)
    {
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        }

        return null;
    }

    /**
     * Définir les valeurs
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
     * Définir les valeurs par noms du mapping
     */
    public function setDatas($entity)
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
     * Définir la valeur d'un champ
     */
    public function setValue($name, $value)
    {
        $this->fields[$name]->setValue($value, 1);
    }

    /**
     * Obtention de la valeur d'un champ
     */
    public function getValue($name)
    {
        return $this->fields[$name]->getValue();
    }

    /**
     * Ajouter une contrainte à un champ
     */
    public function addConstraint($name, $closure)
    {
        $this->fields[$name]->addConstraint($closure);
    }

    /**
     * Définir la valeur d'un attribut
     */
    public function setAttribute($name, $attr_name, $value)
    {
        $this->fields[$name]->setAttribute($attr_name, $value);
    }

    /**
     * Obtenir un attribut sur un champ
     */
    public function getAttribute($name, $attr_name)
    {
        return $this->fields[$name]->getAttribute($attr_name);
    }

    /**
     * Définir la classe d'un champ option
     */
    public function setOptionClass($select, $val, $class)
    {
        $this->fields[$select]->setOptionClass($val, $class);
    }

    /**
     * Conversion en HTML
     */
    public function __toString()
    {
        return $this->getHtml();
    }

    /**
     * Création du code HTML
     */
    public function getHtml()
    {
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
     * Checker les erreurs
     */
    public function check()
    {
        $to_check = array_flip(func_get_args());
        $errors = array();

        foreach ($this->fields as $name => $field) {
            if (!count($to_check) || isset($to_check[$name])) {
                $error = $field->check();

                if ($error) {
                    $errors[] = new Error($field, $error);
                }
            }
        }

        return $errors;
    }

    /**
     * Sourcer un champ avec des valeurs
     */
    public function source($source, $data)
    {
        $this->sources[$source]->source($data);
    }

    /**
     * Transformation des données en un objet
     */
    public function getDatas($tableOrEntity = null)
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
     * Obtention de la valeur d'un champ
     */
    public function __get($name)
    {
        return $this->getValue($name);
    }

    /**
     * Définition de la valeur d'un champ
     */
    public function __set($var, $val)
    {
        $this->setValue($var, $val);
    }

    /**
     * Obtenir un champ
     */
    public function get($name)
    {
        return $this->fields[$name];
    }

    /**
     * Savoir si le formulaire a été posté
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
     * Gérer un formulaire, fonction raccourcie
     */
    public function handle($callback = null)
    {
        if ($this->posted()) {
            $errors = $this->check();

            if (!$errors) {
                if (null !== $callback) {
                    $callback($this->getDatas());
                }
            } else {
                return $errors;
            }
        }

        return array();
    }

    /**
     * Permet d'itérer sur les champs
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
