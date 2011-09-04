<?php

namespace Gregwar\DSD;

require_once('Parser.php');
require_once('Dispatcher.php');
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
     * Position dans la ligne courante
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
                        $d->setValue('');
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
            if (!is_object($d)) {
                $html .= $d;
            } else {
                $html .= $d->getHTML();
            }
        }

        return $html;
    }

    public function check() {
        $n = func_get_args();
        $e = array();
        $radios = array();
        foreach ($this->datas as $d) {
            if (is_object($d)) {
                if (count($n) == 0 || array_search($d->getName(), $n)!==FALSE) {
                    $r = $d->check();
                    if ($d instanceof Fields\RadioField) {
                        if ($r == Fields\RadioField::$OPTIONAL)
                            $radios[$d->getName()] = true;
                        else {
                            if (!isset($radios[$d->getName()]) || $radios[$d->getName()] == false) {
                                if ($r == Fields\RadioField::$CHECKED)
                                    $radios[$d->getName()] = true;
                                else
                                    $radios[$d->getName()] = false;
                            }
                        }
                    } else {
                        if ($r) {
                            $e[] = new Error($d->getName(), $r);
                        }
                    }
                }
            }
        }
        foreach ($radios as $name => $val) {
            if ($val == false) {
                $e[] = new Error($name, Fields\RadioField::error($name));
            }
        }

        return $e;
    }

    public function source($source, $data) {
        if (is_array($this->sourcers))
            foreach ($this->sourcers as $s) {
                if ($s->getSource() == $source) {
                    $s->source($data);
                }
            }
    }

    public function SQL($table) {
        if (gettype($table) == "string")
            $table = new Table($table);
        foreach ($this->datas as $d) {
            if (is_object($d)) {
                $sql = $d->getSQLName();
                if ($sql) {
                    if ($d instanceof Fields\MulticheckboxField)
                        continue;
                    if ($d instanceof Fields\RadioField || $d->isChecked()==true) {
                        $table->$sql = $d->getValue();
                        if ($d instanceof Fields\CheckboxField) {
                            if ($d->isChecked()) {
                                $table->$sql=$d->getValue();
                            } else $table->$sql=0;
                        }						
                    }
                }
            }
        }
        return $table;
    }

    public function __get($var) {
        foreach ($this->datas as $d) {
            if (is_object($d)) {
                if ($d instanceof Fields\RadioFIeld && $d->isChecked()==false) 
                    continue;
                if ($d->getName() == $var)
                    return $d->getValue();
            }
        }
        return false;
    }

    public function getValue($var) {
        return $this->__get($var);
    }

    public function __set($var, $val) {
        $this->setValue($var, $val);
    }

    public function posted() {
        global $_POST, $_SESSION;
        if (isset($_POST['DSDCsrf']) && $_POST['DSDCsrf'] == $this->hash) {
            $this->setValues($_POST, $_FILES);
            return true;
        } else {
            return false;
        }
    }

    public function rewind() {
        $this->position = 0;
        $this->next();
    }

    public function next() {
        $i = $this->position+1;
        while ($i<count($this->datas)) {
            if (is_object($this->datas[$i]))
                break;
            $i++;
        }
        $this->position = $i;
    }

    public function current() {
        return ($this->datas[$this->position]->getValue());
    }

    public function valid() {
        return isset($this->datas[$this->position]);
    }

    public function key() {
        return $this->datas[$this->position]->getName();
    }

    public static function fatal($message, $prefix = '')
    {
        echo '<span style="font-family: Courier;">';
        echo '<b>DSD error '.$prefix.':</b> '.$message;
        echo '</span>';
        exit();
    }
}
