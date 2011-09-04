<?php

namespace Gregwar\DSD\Fields;

class CaptchaField extends Field
{

    public function __construct()
    {
		$this->type = 'text';
	}
	
    public function check()
    {
		$this->value=strtolower($this->value);
		$this->value=strtr($this->value,"lo","10");
		if (!isset($_SESSION['DSD_Captcha']) || $_SESSION['DSD_Captcha']!=$this->value) {
			return 'La valeur du code visuel n\'est pas la bonne';
		}
		unset($_SESSION["DSD_Captcha"]);
	}

    public function getHTML()
    {
		global $DSDHttp;
		$nodraw = $this->value;
		$this->value="";
		$val = parent::getHTML();
        $this->value = $nodraw;
        // XXX: Implémentation du CAPTCHA inline
//		return "<img src=\"".$DSDHttp."/captcha.php?".time()."\" class=\"DSD_Captcha\" alt=\"Code visuel\" ><br />$val";
	}
}
