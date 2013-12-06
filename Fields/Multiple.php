<?php

namespace Gregwar\Formidable\Fields;

use Gregwar\Formidable\Language\Language;
use Gregwar\Formidable\ParserData;
use Gregwar\Formidable\DataForm;
use Gregwar\Formidable\Form;

/**
 * Multiple zone
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Multiple extends Field
{
    /**
     * Parser data of the internal form
     */
    protected $parserData = null;

    /**
     * Internal forms
     */
    protected $forms = array();

    public function setParserData(ParserData $parserData)
    {
        $this->parserData = $parserData;
    }

    protected function getForm($index)
    {
        if (!isset($this->forms[$index])) {
            $this->forms[$index] = new DataForm($this->parserData, $this->language);
        }

        return $this->forms[$index];
    }

    public function setValues($values, array $files)
    {
        if (is_array($values)) {
            $index = 0;
            foreach ($values as $v) {
                $this->getForm($index)->setValues($v, $files);
                $index++;
            }
        }
    }

    public function setValue($value, $default = false)
    {
        $this->setValues($value, array());
    }

    public function getValue()
    {
        $value = array();

        foreach ($this->forms as $index => $form) {
            $value[$index] = $form->getValues();
        }

        return $value;
    }

    protected function getHtmlOfForm(Form $form, $index)
    {
        $myName = $this->name;
        $form->hookNames(function($name) use ($myName, $index) {
            return $myName.'['.$index.']['.$name.']';
        });

        return (string)$form;
    }

    public function getHtml()
    {
        $id = uniqid('multiple'.time().mt_rand());
        $prototype = new DataForm($this->parserData, $this->language);

        $html = '<div class="multiple">'."\n";
        $html .= '<div id="'.$id.'" class="multiple-forms">'."\n";
        foreach ($this->forms as $index => $form) {
            $fid = uniqid('multipleelement'.time().mt_rand());
            $html .= '<span id="'.$fid.'"/>';
            $html .= $this->getHtmlOfForm($form, $index);
            $html .= '<a href="javascript:Formidable.removeInput(\''.$fid.'\')">Remove</a><br />';
            $html .= '</span>';
        }
        $html .= '</div>'."\n";
        $html .= '<div class="multiple-buttons">';
        $html .= '<script type="text/javascript">';
        $html .= 'var '.$id.'_code = ';
        $html .= json_encode($this->getHtmlOfForm($prototype, '{number}'));
        $html .= ";\n";
        $html .= 'FormidableMultiple["'.$id.'"] = '.count($this->forms).";\n";
        $html .= '</script>';
        $html .= '<a href="javascript:Formidable.addInput(\''.$id.'\', '.$id.'_code);">Add</a>';
        $html .= '</div>';
        $html .= '</div>'."\n";

        return $html;
    }

    public function __sleep()
    {
        return array_merge(array('parserData'), 
            parent::__sleep());
    }

    public function needJs()
    {
        return true;
    }
}
