<?php

namespace Gregwar\Formidable;

/**
 * Parses a Formidable form
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Parser extends ParserData
{
    /**
     * Default type (for untyped inputs)
     */
    public static $fallback = 'text';

    /**
     * Parser data
     */
    protected $parserData;

    /**
     * Factory
     */
    private $factory;

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

            if (!isset($this->data[$idx])) {
                $this->data[] = '';
            }

            if (!$balise) {
                if ($char == '<') {
                    $balise = true;
                    $buffer = '';
                } else {
                    if ($textarea || $option) {
                        $this->data[$idx-1]->addValue($char);
                    } else if (!$select) {
                        $this->data[$idx] .= $char;
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
                                $this->token = sha1($secret);

                                $return = '<input type="hidden" name="csrf_token" value="'.$this->token.'" /></form>';
                            } else {
                                $return = '</form>';
                            }
                        default:
                            $this->data[$idx] .= $return;
                        }

                        if ($textarea) {
                            $this->data[$idx-1]->addValue($return);
                        }
                    } else {
                        $this->needJs = $this->needJs || $return->needJs();
                        if ($return instanceof Fields\Options) {
                            if (!$this->data[$idx-1] instanceof Fields\Select) {
                                throw new ParserException('<option> should always be in a <select>');
                            }
                            $this->sources[$return->getSource()] = $return;
                            $return->setParent($this->data[$idx-1]);
                        } else {
                            if ($return instanceof Fields\Option) {
                                $option = true;

                                if (!$this->data[$idx-1] instanceof Fields\Select) {
                                    throw new ParserException('<option> should always be in a <select>');
                                } else {
                                    $this->data[$idx-1]->addOption($return);
                                }
                            } else if ($return instanceof Fields\RadioField) {
                                $this->data[] = $return;
                                $idx += 2;

                                if (!isset($this->fields[$return->getName()])) {
                                    $this->fields[$return->getName()] = $this->factory->getObject('radios');
                                    $this->fields[$return->getName()]->setName($return->getName());
                                }
                                $this->fields[$return->getName()]->addRadio($return);
                            } else {
                                $this->data[] = $return;
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
            throw new ParserException('The Formidable form should have a <form> tag');
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
}
