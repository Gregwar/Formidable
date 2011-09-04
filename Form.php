<?php

namespace Gregwar\DSD;

require_once('Parser.php');
require_once('Table.php');
require_once('Error.php');

/**
 * Classe principale de DSD
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Form implements \Iterator
{
    /**
     * Contenu (code HTML du formulaire)
     */
    private $content;

    /**
     * Objets et chaîne de caractères représentant le formulaire DSD
     */
    private $datas = array();

    /**
     * Champs, indexés par nom
     */
    private $fields = array();

    /**
     * Sources d'information
     */
    private $sourcers;

    /**
     * Hash du formulaire
     */
    private $hash;

    /**
     * Position courrante pour l'itération
     */
    private $position;

    /**
     * Chemin du fichier
     */
    private $path;

    public function __construct($path = '', $vars = array())
    {
        if (isset($path)) {
            $this->path = $path;
            $this->getContent($vars);
        }
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

        $this->position = 0;
        $this->parse();
    }

    /**
     * Parse le contenu du formulaire pour construire les objets
     */
    private function parse()
    {
        $parser = new Parser($this->content);

        $this->datas = $parser->getDatas();
        $this->sourcers = $parser->getSourcers();
        $this->hash = $parser->getHash();

        foreach ($this->datas as $field) {
            if (is_object($field)) {
                $this->fields[$field->getName()] = $field;
            }
        }
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
                    if ($field instanceof Fields\CheckboxField
                        || $field instanceof Fields\MulticheckboxField) {
                        $field->setValue('');
                    }
                }
            }
        }
    }

    /**
     * Définir les valeurs par noms du mapping
     */
    public function setSQLValues($table)
    {
        foreach ($this->datas as $field) {
            if (is_object($field)) {
                $sql = $field->getSQLName();
                if ($sql) {
                    if (isset($table->$sql)) {
                        $field->setValue($table->$sql, 1);
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
    public function getValue($var) {
        foreach ($this->fields as $name => $field) {
            if ($d instanceof Fields\RadioFIeld && $d->isChecked()==false) {
                continue;
            }
            if ($field_name === $name) {
                return $d->getValue();
            }
        }
        return null;
    }

    /**
     * Définir la classe d'un champ
     */
    public function setClass($name, $value)
    {
        $this->fields[$name]->setClass($value);
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
        return $this->getHTML();
    }

    /**
     * Création du code HTML
     */
    public function getHTML() {
        $html = '';

        foreach ($this->datas as $d) {
            if (is_string($d)) {
                $html .= $d;
            } else {
                $html .= $d->getHTML();
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
        $radios = array();

        foreach ($this->fields as $name => $field) {
            if (!count($to_check) || isset($to_check[$name])) {
                $error = $field->check();

                // XXX: Le cas particulier des radio devrait être géré ailleurs qu'ici..
                if ($field instanceof Fields\RadioField) {
                    if ($error == Fields\RadioField::$OPTIONAL)
                        $radios[$name] = true;
                    else {
                        if (!isset($radios[$name]) || $radios[$name] == false) {
                            if ($r == Fields\RadioField::$CHECKED)
                                $radios[$name] = true;
                            else
                                $radios[$name] = false;
                        }
                    }
                } else {
                    if ($error) {
                        $errors[] = new Error($field->getName(), $error);
                    }
                }
            }
        }
        foreach ($radios as $name => $val) {
            if ($val == false) {
                $errors[] = new Error($name, Fields\RadioField::error($name));
            }
        }

        return $errors;
    }

    public function source($source, $data)
    {
        if (is_array($this->sourcers))
            foreach ($this->sourcers as $s) {
                if ($s->getSource() == $source) {
                    $s->source($data);
                }
            }
    }

    /**
     * Transformation des données en un objet
     */
    public function SQL($table)
    {
        if (gettype($table) == 'string')
            $table = new Table($table);

        foreach ($this->fields as $name => $field) {
            $sql = $field->getSQLName();

            if ($sql) {
                if ($field instanceof Fields\MulticheckboxField)
                    continue;

                if ($field instanceof Fields\RadioField || $field->isChecked()==true) {
                    $table->$sql = $field->getValue();
                    if ($field instanceof Fields\CheckboxField) {
                        if ($field->isChecked()) {
                            $table->$sql = $d->getValue();
                        } else $table->$sql = 0;
                    }						
                }
            }
        }
        return $table;
    }

    /**
     * Obtention de la valeur d'un champ
     */
    public function __get($field_name)
    {
        return $this->getValue($var);
    }

    /**
     * Définition de la valeur d'un champ
     */
    public function __set($var, $val)
    {
        $this->setValue($var, $val);
    }

    /**
     * Savoir si le formulaire a été posté
     */
    public function posted()
    {
        if (isset($_POST['DSDCsrf']) && $_POST['DSDCsrf'] == $this->hash) {
            $this->setValues($_POST, $_FILES);
            return true;
        } else {
            return false;
        }
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

    /**
     * Erreur fatale
     */
    public static function fatal($message, $prefix = '')
    {
        echo '<span style="font-family: Courier;">';
        echo '<b>DSD error '.$prefix.':</b> '.$message;
        echo '</span>';
        exit();
    }
}
