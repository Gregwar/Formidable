<?php

namespace Gregwar\Formidable\Language;

/**
 * A language aware object knows the language
 */
class LanguageAware
{
    protected $language = null;

    public function setLanguage(Language $language)
    {
        $this->language = $language;
    }

    public function getLanguage()
    {
        return $this->language;
    }
}
