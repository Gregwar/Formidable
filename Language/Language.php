<?php

namespace Gregwar\Formidable\Language;

class Language
{
    protected $messages = array();
    
    protected function getFormat($slug)
    {
        return $this->messages[$slug];
    }

    public function translate($slug)
    {
        $args = func_get_args();
        $args[0] = $this->getFormat($slug);
        
        return call_user_func_array('sprintf', $args);
    }
}
