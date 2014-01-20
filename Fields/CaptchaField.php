<?php

namespace Gregwar\Formidable\Fields;

use Gregwar\Captcha\CaptchaBuilder;

/**
 * Captcha field
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class CaptchaField extends Field
{
    /**
     * Captcha value
     */
    protected $builder = null;

    /**
     * Captcha is required
     */
    protected $required = true;

    /**
     * Field type (text)
     */
    protected $type = 'text';

    public function __wakeup()
    {
        $this->generate();
    }

    public function __construct()
    {
        $this->generate();
    }

    protected function generate()
    {
        $this->builder = new CaptchaBuilder;
        $this->builder->build();
    }

    public function push($var, $value = null)
    {
        if ($var !== 'type') {
            parent::push($var, $value);
        }
    }

    public function getCaptchaValue()
    {
        return $this->builder->getPhrase();
    }

    public function check()
    {
        $this->value = strtolower($this->value);

        if (!isset($_SESSION['Formidable_Captcha']) || $_SESSION['Formidable_Captcha']!=$this->value) {
            return array('bad_captcha');
        }
        unset($_SESSION["Formidable_Captcha"]);
    }

    public function getHtml()
    {
        $temp = $this->value;
        $this->value = '';
        $input_html = parent::getHtml();
        $this->value = $temp;

        $_SESSION['Formidable_Captcha'] = $this->getCaptchaValue();

        $html = '<img src="'.$this->builder->inline().'" class="Formidable_Captcha" alt="Code visuel" ><br />'.$input_html;

        return $html;
    }
}
