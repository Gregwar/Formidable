<?php

namespace Gregwar\DSD;

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
        return Dispatcher::dispatch($spaces[0], $spaces[1]);
    }

    /**
     * Meurt en cas d'erreur
     */    
    private function error($message)
    {
        Form::fatal($message, sprintf('(l.%d)', $this->currentLine));
    }
}
