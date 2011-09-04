<?php

namespace Gregwar\DSD;

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
 * Inclusion des types
 */
require_once(__DIR__.'/Fields/Textarea.php');
require_once(__DIR__.'/Fields/Select.php');
require_once(__DIR__.'/Fields/Option.php');
require_once(__DIR__.'/Fields/Options.php');
require_once(__DIR__.'/Fields/Custom.php');

/**
 * Parse un formulaire pour DSD
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Parser
{
    /**
     * Objets constituant le formulaire
     */
    private $datas = array();

    /**
     * Sources
     */
    private $sourcers = array();

    /**
     * Hash CSRF
     */
    private $hash = '';

    /**
     * Ligne du fichier courante
     */
    private $currentLine = 1;

    public function __construct($content)
    {
        $this->parse($content);
    }

    public function getDatas()
    {
        return $this->datas;
    }

    public function getSourcers()
    {
        return $this->sourcers;
    }

    public function getHash()
    {
        return $this->hash;
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

            if ($content[$i] == "\n") {
                $this->currentLine++;
            }

            if (!isset($this->datas[$idx])) {
                $this->datas[] = '';
            }

            if (!$balise) {
                if ($content[$i] == '<') {
                    $balise = true;
                    $buffer = '';
                } else {
                    if ($textarea || $option) {
                        $this->datas[$idx-1]->addValue($content[$i]);
                    } else if (!$select) {
                        $this->datas[$idx] .= $content[$i];
                    }
                }
            } else {
                if ($content[$i] == '>') {
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
                            if (isset($_SESSION["DSDSecret"]))
                                $secret=$_SESSION["DSDSecret"];
                            else {
                                $secret=sha1(mt_rand().time().mt_rand());
                                $_SESSION["DSDSecret"]=$secret;
                            }
                            $this->hash = md5($secret);
                            $return = '<input type="hidden" name="DSDCsrf" value="'.$this->hash.'" /></form>';
                        default:
                            $this->datas[$idx] .= $return;
                        }

                        if ($textarea) {
                            $this->datas[$idx-1]->addValue($return);
                        }
                    } else {
                        if ($return instanceof Fields\Options) {
                            if (!$this->datas[$idx-1] instanceof Fields\Select) {
                                $this->error("Options out of select.");
                            }
                            $this->sourcers[] = $return;
                            $return->setParent($this->datas[$a-1]);
                        } else {
                            if ($return instanceof Fields\Option) {
                                $option = true;

                                if (!$this->datas[$idx-1] instanceof Fields\Select) {
                                    $this->error("Option out of select.");
                                } else {
                                    $this->datas[$idx-1]->addOption($return);
                                }
                            } else {
                                $this->datas[] = $return;
                                $idx += 2;

                                if ($return instanceof Fields\Textarea) {
                                    $textarea = true;
                                }

                                if ($return instanceof Fields\Select) {
                                    $select = true;
                                }

                                if ($return instanceof Fields\Custom || 
                                    $return instanceof FIelds\MultiCheckboxField ||
                                    $return instanceof Fields\MultiradioField) {
                                        $this->sourcers[] = $return;
                                    }
                            }
                        }
                    }
                } else {
                    $buffer .= $content[$i];
                }
            }
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
            $find = explode(' ',substr($data, strpos($data, 'type=') +5));
            $type = strtolower(str_replace("\"","",str_replace("'","",$find[0])));

            if (!$type) {
                Form::fatal('Untyped input');
            } else {
                if ($type=='submit') {
                    return '<'.$name.' '.$data.'>';
                }

                $classname = sprintf('Gregwar\DSD\Fields\%sField', ucfirst(strtolower($type)));

                try {
                    $class = new \ReflectionClass($classname);
                    if ($class->isInstantiable()) {
                        $field = $class->newInstance();
                    } else {
                        Form::fatal('Class '.$classname.' is not instanciable');
                    }
                } catch (\ReflectionException $e) {
                    Form::fatal('Type '.htmlspecialchars($classname).' doesn\'t exists');
                }
            }
            break;
        case 'textarea':
            $field=(new Fields\Textarea($data));
            break;
        case 'select':
            $field=(new Fields\Select($data));
            break;
        case 'option':
            $field=(new Fields\Option($data));
            break;
        case 'options':
            $field=(new Fields\Options($data));
            break;
        case 'custom':
            $field = (new FIelds\Custom($data));
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
            $buffer = '';
            $guillemet="";
            for ($i=0; $i<strlen($data); $i++) {
                $c = $data[$i];
                if ($c == " ") {
                    if (!$guillemet) {
                        $tmp = split("=", $buffer);
                        if (isset($tmp[1]) && $tmp[1][strlen($tmp[1])-1]=="/")
                            $tmp[1]=substr($tmp[1],0,-1);
                        if (isset($tmp[1])) {
                            if ($tmp[1][0]=="\"" || $tmp[1][0]=="'")
                                $tmp[1] = substr($tmp[1], 1, strlen($tmp[1])-2);	
                        } else {
                            $tmp[1]="";
                        }
                        $field->push(strtolower($tmp[0]), $tmp[1]);
                        $buffer="";
                    } else {
                        $buffer.=" ";
                    }
                } else {
                    if (!$guillemet) {
                        if ($c=="'" || $c=="\"")
                            $guillemet = $c;
                    } else {
                        if ($c == $guillemet)
                            $guillemet = "";
                    }
                    $buffer .= $c;
                }
            }
            if ($buffer!="") {
                $tmp = split("=", $buffer);
                if (isset($tmp[1]) && $tmp[1][strlen($tmp[1])-1]=="/")
                    $tmp[1]=substr($tmp[1],0,-1);
                if (!isset($tmp[1])) $tmp[1]="";
                else
                    if ($tmp[1][0]=="\"" || $tmp[1][0]=="'")
                        $tmp[1] = substr($tmp[1], 1, strlen($tmp[1])-2);
                $field->push(strtolower($tmp[0]), $tmp[1]);
            }
            return $field;
        } else {
            return "";
        }
    }

    /**
     * Meurt en cas d'erreur
     */    
    private function error($message)
    {
        Form::fatal($message, sprintf('(l.%d)', $this->currentLine));
    }
}
