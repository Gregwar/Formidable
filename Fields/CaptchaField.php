<?php

namespace Gregwar\DSD\Fields;

include(__DIR__.'/../Captcha/Captcha.php');

use Gregwar\DSD\Captcha\Captcha;

/**
 * Champ de type CAPTCHA
 *
 * @author GrÃ©goire Passault <g.passault@gmail.com>
 */
class CaptchaField extends Field
{
    private $captchaValue = '';

    public function __construct()
    {
        $this->type = 'text';
        $this->generate();
    }

    /**
     * GÃ©nÃ¨re la valeur du CAPTCHA
     */
    private function generate()
    {
        $chars = str_split('0123456789abcdefghijkmnpqrstuvwxyz');

        for ($i=0; $i<5; $i++) {
            $this->captchaValue .= $chars[array_rand($chars)];
        }

        $_SESSION['DSD_Captcha'] = $this->captchaValue;
    }

    public function check()
    {
        $this->value = strtolower($this->value);
        $this->value = strtr($this->value, 'lo', '10');

        if (!isset($_SESSION['DSD_Captcha']) || $_SESSION['DSD_Captcha']!=$this->value) {
            return 'La valeur du code visuel n\'est pas la bonne';
        }
        unset($_SESSION["DSD_Captcha"]);
    }

    public function getHTML()
    {
        $temp = $this->value;
        $this->value = '';
        $input_html = parent::getHTML();
        $this->value = $temp;

        $captcha = new Captcha($this->captchaValue);

        $html = '<img src="'.$captcha->inline().'" class="DSD_Captcha" alt="Code visuel" ><br />'.$input_html;

        return $html;
    }
}
