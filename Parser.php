<?php

namespace Gregwar\Formidable;

/**
 * Parses a Formidable form
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Parser
{
    /**
     * Default type (for untyped inputs)
     */
    public static $fallback = 'text';

    /**
     * Factory
     */
    private $factory;

    /**
     * Objects in the form
     */
    private $datas = array();

    /**
     * Sources
     */
    private $sources = array();

    /**
     * Fields
     */
    private $fields = array();

    /**
     * Hash CSRF
     */
    private $hash = '';

    /**
     * Does we need js?
     */
    private $needJs = false;

    /**
     * Form header
     */
    private $head = null;

    /**
     * Current line
     */
    private $currentLine = 1;

    public function __construct($content, $factory = null)
    {
        if (null === $factory) {
            $this->factory = new Factory;
        } else {
            $this->factory = $factory;
        }

        $this->parse($content);
    }

    /**
     * Form components
     */
    public function getDatas()
    {
        return $this->datas;
    }

    /**
     * Fields mapped by name
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
     * CSRF hash
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Gets the head
     */
    public function getHead()
    {
        return $this->head;
    }

    /**
     * Does we need js?
     */
    public function needJs()
    {
        return $this->needJs;
    }

    /**
     * Parses the form and build Formidable objects
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
                    $return = $this->parseTag($buffer);
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
                                if (isset($_SESSION['formidable_secret'])) {
                                    $secret = $_SESSION['formidable_secret'];
                                } else {
                                    $secret = sha1(uniqid(mt_rand(), true));
                                    $_SESSION['formidable_secret'] = $secret;
                                }
                                if ($this->head && $this->head->has('name')) {
                                    $secret .= '/'.$this->head->get('name');
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
                                    $this->fields[$return->getName()] = $this->factory->getObject('radios');
                                    $this->fields[$return->getName()]->setName($return->getName());
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

                                if ($return->getSource()) {
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
            throw new ParserException('Le formulaire Formidable doit avoir une balise <form>');
        }
    }

    /**
     * Parses a tag
     *
     * @param string $data le contenu de la balise
     */
    private function parseTag($data)
    {
        $spaces = explode(' ', $data, 2);

        return $this->doParseTag($spaces[0], $spaces[1]);
    }

    /**
     * Parses a tag
     */
    public function doParseTag(&$name, &$data)
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

            $field = $this->factory->getField($type);

            break;
        case 'form':
        case 'textarea':
        case 'select':
        case 'option':
        case 'options':
        case 'custom':
            $field = $this->factory->getObject($name);
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
     */
    public function __clone()
    {
        // Cloning head
        $this->head = clone $this->head;

        // Cloning fields
        foreach ($this->fields as &$field) {
            $field = clone $field;
        }

        // Cloning sources
        foreach ($this->sources as &$source) {
            if ($source instanceof Fields\Options) {
                $name = $source->getParent()->getName();
                $source = clone $source;
                $source->setParent($this->fields[$name]);
            } else {
                $source = $this->fields[$source->getName()];
            }
        }

        // Getting data references
        foreach ($this->datas as &$data) {
            if (is_object($data)) {
                if ($data instanceof Head) {
                    $data = $this->getHead();
                } else {
                    if ($data instanceof Fields\RadioField) {
                        $data = clone $data;
                        $this->fields[$data->getName()]->addRadio($data);
                    } else {
                        $data = $this->fields[$data->getName()];
                    }
                }
            }
        }
    }
}
