<?php

namespace Gregwar\Formidable;

use Gregwar\Formidable\Language\Language;
use Gregwar\Formidable\Factory;

/**
 * A Formidable Form
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class DataForm extends Form
{
    public function __construct(ParserData &$data, Language $language)
    {
        $this->originalParserData = &$data;
        $this->factory = new Factory;
        $this->setLanguage($language);
        $this->reset();
    }
}
