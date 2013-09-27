<?php

namespace Gregwar\Formidable\Fields;

use Gregwar\Formidable\Captcha\Captcha;

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
    protected $captchaValue = '';

    /**
     * Field type (text)
     */
    protected $type = 'text';

    public function push($var, $value = null)
    {
        if ($var !== 'type') {
            parent::push($var, $value);
        }
    }

    /**
     * Generates the captcha code
     */
    protected function generate()
    {
        $chars = str_split('0123456789abcdefghijkmnpqrstuvwxyz');

        for ($i=0; $i<5; $i++) {
            $this->captchaValue .= $chars[array_rand($chars)];
        }

        $_SESSION['Formidable_Captcha'] = $this->captchaValue;
    }

    public function getCaptchaValue()
    {
        return $this->captchaValue;
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
        $this->generate();
        $temp = $this->value;
        $this->value = '';
        $input_html = parent::getHtml();
        $this->value = $temp;

        $captcha = new Captcha($this->captchaValue);

        $html = '<img src="'.$captcha->inline().'" class="Formidable_Captcha" alt="Code visuel" ><br />'.$input_html;

        return $html;
    }
}
