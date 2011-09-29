<?php

namespace Gregwar\DSD;

/**
 * Inclusion de l'exception
 */
require_once(__DIR__.'/ParserException.php');

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
 * Parse un formulaire pour DSD
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Parser
{
    /**
     * Type par défaut (pour les <input> non typés)
     */
    public static $fallback = 'text';

    /**
     * Objets constituant le formulaire
     */
    private $datas = array();

    /**
     * Sources
     */
    private $sources = array();

    /**
     * Champs
     */
    private $fields = array();

    /**
     * Hash CSRF
     */
    private $hash = '';

    /**
     * Besoin de JS ?
     */
    private $needJs = false;

    /**
     * En-tête du formulaire
     */
    private $head = null;

    /**
     * Ligne du fichier en cours de lecture
     */
    private $currentLine = 1;

    public function __construct($content)
    {
        $this->parse($content);
    }

    /**
     * Composants du formulaire
     */
    public function getDatas()
    {
        return $this->datas;
    }

    /**
     * Champs, mappés par leur nom
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Sources
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * Le hash CSRF
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Obtenir l'en-tête
     */
    public function getHead()
    {
        return $this->head;
    }

    /**
     * Besoin de JS ?
     */
    public function needJs()
    {
        return $this->needJs;
    }

    /**
     * Parse le formulaire et construit les objets DSD
     *
     * @param string $content le contenu du code du formulaire
     */
    private function parse($content)
    {
        $buffer = '';
        $idx = 0;
        $len = strlen($content);

        $balise = $textarea = $select = $option = false;

        for ($i=0; $i<$len; $i++) {
            $char = $content[$i];

            if ($char == "\n") {
                $this->currentLine++;
            }

            if (!isset($this->datas[$idx])) {
                $this->datas[] = '';
            }

            if (!$balise) {
                if ($char == '<') {
                    $balise = true;
                    $buffer = '';
                } else {
                    if ($textarea || $option) {
                        $this->datas[$idx-1]->addValue($char);
                    } else if (!$select) {
                        $this->datas[$idx] .= $char;
                    }
                }
            } else {
                if ($char == '>') {
                    $balise = false;
                    $return = $this->parseBalise($buffer);
                    if (!is_object($return)) {
                        switch ($return) {
                        case '</textarea>':
                            $textarea = false;
                            break;
                        case '</select>':
                            $select = false;
                            break;
                        case '</option>':
                            $option = false;
                            break;
                        case '</form>':
                            if (!isset($this->fields['csrf_token'])) {
                                if (isset($_SESSION['dsd_secret']))
                                    $secret = $_SESSION['dsd_secret'];
                                else {
                                    $secret = sha1(uniqid(mt_rand(), true));
                                    $_SESSION['dsd_secret'] = $secret;
                                }
                                if ($this->head && $this->head->has('name')) {
                                    $secret.= '/'.$this->head->get('name');
                                }
                                $this->hash = sha1($secret);

                                $return = '<input type="hidden" name="csrf_token" value="'.$this->hash.'" /></form>';
                            } else {
                                $return = '</form>';
                            }
                        default:
                            $this->datas[$idx] .= $return;
                        }

                        if ($textarea) {
                            $this->datas[$idx-1]->addValue($return);
                        }
                    } else {
                        $this->needJs = $this->needJs || $return->needJs();
                        if ($return instanceof Fields\Options) {
                            if (!$this->datas[$idx-1] instanceof Fields\Select) {
                                throw new ParserException('<option> en dehors d\'un <select>');
                            }
                            $this->sources[$return->getSource()] = $return;
                            $return->setParent($this->datas[$idx-1]);
                        } else {
                            if ($return instanceof Fields\Option) {
                                $option = true;

                                if (!$this->datas[$idx-1] instanceof Fields\Select) {
                                    throw new ParserException('<option> en dehors d\'un <select>');
                                } else {
                                    $this->datas[$idx-1]->addOption($return);
                                }
                            } else if ($return instanceof Fields\RadioField) {
                                $this->datas[] = $return;
                                $idx += 2;

                                if (!isset($this->fields[$return->getName()])) {
                                    $this->fields[$return->getName()] = new Fields\Radios;
                                }
                                $this->fields[$return->getName()]->addRadio($return);
                            } else {
                                $this->datas[] = $return;
                                if ($return instanceof Head) {
                                    $this->head = $return;
                                } else {
                                    $this->fields[$return->getName()] = $return;
                                }
                                $idx += 2;

                                if ($return instanceof Fields\FileField && $this->head) {
                                    $this->head->set('enctype', 'multipart/form-data');
                                }

                                if ($return instanceof Fields\Textarea) {
                                    $textarea = true;
                                }

                                if ($return instanceof Fields\Select) {
                                    $select = true;
                                }

                                if ($return instanceof Fields\Custom ||
                                    $return instanceof FIelds\MulticheckboxField ||
                                    $return instanceof Fields\MultiradioField) {
                                        $this->sources[$return->getSource()] = $return;
                                    }
                            }
                        }
                    }
                } else {
                    $buffer .= $char;
                }
            }
        }

        if (null === $this->getHead()) {
            throw new ParserException('Le formulaire DSD doit avoir une balise <form>');
        }
    }

    /**
     * Parser une balise
     *
     * @param string $data le contenu de la balise
     */
    private function parseBalise($data)
    {
        $spaces = explode(' ', $data, 2);

        return $this->doParseBalise($spaces[0], $spaces[1]);
    }

    /**
     * Parser une balise
     */
    public static function doParseBalise(&$name, &$data)
    {
        $field = null;

        switch (strtolower($name)) {
        case 'input':
            $type = null;

            if (preg_match('#type="(.+)"#mUsi', $data, $match)) {
                $type = $match[1];
            }

            if (!$type) {
                $type = self::$fallback;
            }
           
            if ($type === 'submit') {
                return '<'.$name.' '.$data.'>';
            }

            $classname = sprintf('Gregwar\DSD\Fields\%sField', ucfirst(strtolower($type)));
            $field = new $classname;

            break;
        case 'form':
            $field = new Head;
            break;
        case 'textarea':
            $field = new Fields\Textarea;
            break;
        case 'select':
            $field = new Fields\Select;
            break;
        case 'option':
            $field = new Fields\Option;
            break;
        case 'options':
            $field=new Fields\Options;
            break;
        case 'custom':
            $field = new FIelds\Custom;
            break;
        case '/textarea':
            return '</textarea>';
            break;
        case '/select':
            return '</select>';
            break;
        case '/option':
            return "</option>";
            break;
        case '/form':
            return '</form>';
            break;
        default:
            if (!$data)

                return "<$name>";
            else

                return "<$name $data>";
            break;
        }
        if (null !== $field) {
            $data = preg_replace_callback('#="([^"]+)"#mUsi', function($matches) {
                return '="'.urlencode($matches[1]).'"';
            }, $data);

            $attributes = explode(' ', $data);

            foreach ($attributes as $attribute) {
                if (preg_match("#([^=]+)(=\"(.+)\"|)#muSi", $attribute, $match)) {
                    $field->push($match[1], isset($match[3]) ? html_entity_decode(urldecode($match[3])) : null);
                }
            }

            return $field;
        } else {

            return '';
        }
    }

    /**
     * Clonage
     * XXX: Ne marchera pas avec un <options>
     */
    public function __clone()
    {
        $this->head = clone $this->head;
        foreach ($this->fields as &$field) {
            $field = clone $field;
        }
        foreach ($this->sources as &$source) {
            $source = clone $source;
        }
        foreach ($this->datas as &$data) {
            if (is_object($data)) {
                if ($data instanceof Head) {
                    $data = $this->getHead();
                } else {
                    $data = $this->fields[$data->getName()];
                }
            }
        }
    }
}
