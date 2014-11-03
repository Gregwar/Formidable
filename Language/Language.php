<?php

namespace Gregwar\Formidable\Language;

class Language
{
    protected $messages = array();
    
    protected function getFormat($slug)
    {
        if (isset($this->messages[$slug])) {
            return $this->messages[$slug];
        } else {
            return $slug;
        }
    }

    public function translate($slug)
    {
        $args = func_get_args();
        $args[0] = $this->getFormat($slug);
        
        return call_user_func_array('sprintf', $args);
    }

    public function set($key, $value)
    {
        $this->messages[$key] = $value;
    }

    public function translateArray(array $params)
    {
        $params[0] = $this->getFormat($params[0]);

        return call_user_func_array('sprintf', $params);
    }
}
