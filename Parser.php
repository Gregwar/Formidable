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
     * Current position in the data
     */
    protected $idx;

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

    protected function push($something)
    {
        $this->data[] = $something;
        $this->idx = count($this->data);
    }

    /**
     * Parses the form and build Formidable objects
     *
     * @param string $content le contenu du code du formulaire
     */
    private function parse($content)
    {
        $buffer = '';
        $idx = &$this->idx;
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
                    $newNode = $this->parseTag($buffer);
                    if (!is_object($newNode)) {
                        switch ($newNode) {
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
                                $name = '';
                                if ($this->head && $this->head->has('name')) {
                                    $name .= $this->head->get('name');
                                }

                                $this->push(new Csrf($name));
                            } 

                            $this->push('</form>');
                            break;
                        default:
                            $this->data[$idx] .= $newNode;
                        }

                        if ($textarea) {
                            $this->data[$idx-1]->addValue($newNode);
                        }
                    } else {
                        $this->needJs = $this->needJs || $newNode->needJs();
                        if ($newNode instanceof Fields\Options) {
                            if (!$this->data[$idx-1] instanceof Fields\Select) {
                                throw new ParserException('<option> should always be in a <select>');
                            }
                            $this->sources[$newNode->getSource()] = $newNode;
                            $newNode->setParent($this->data[$idx-1]);
                        } else {
                            if ($newNode instanceof Fields\Option) {
                                $option = true;

                                if (!$this->data[$idx-1] instanceof Fields\Select) {
                                    throw new ParserException('<option> should always be in a <select>');
                                } else {
                                    $this->data[$idx-1]->addOption($newNode);
                                }
                            } else if ($newNode instanceof Fields\RadioField) {
                                $this->push($newNode);

                                if (!isset($this->fields[$newNode->getName()])) {
                                    $this->fields[$newNode->getName()] = $this->factory->getObject('radios');
                                    $this->fields[$newNode->getName()]->setName($newNode->getName());
                                }
                                $this->fields[$newNode->getName()]->addRadio($newNode);
                            } else {
                                $this->push($newNode);

                                if ($newNode instanceof Head) {
                                    $this->head = $newNode;
                                } else {
                                    $this->fields[$newNode->getName()] = $newNode;
                                }

                                if ($newNode instanceof Fields\FileField && $this->head) {
                                    $this->head->set('enctype', 'multipart/form-data');
                                }

                                if ($newNode instanceof Fields\Textarea) {
                                    $textarea = true;
                                }

                                if ($newNode instanceof Fields\Select) {
                                    $select = true;
                                }

                                if ($newNode->getSource()) {
                                    $this->sources[$newNode->getSource()] = $newNode;
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
