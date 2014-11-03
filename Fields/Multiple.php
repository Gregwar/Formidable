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

    /**
     * Entries constraints
     */
    protected $minEntries = null;
    protected $maxEntries = null;


    public function setParserData(ParserData $parserData)
    {
        $this->parserData = $parserData;
    }

    public function push($name, $value = null)
    {
        switch ($name) {
        case 'min-entries':
            $this->minEntries = (int)$value;
            return;
        case 'max-entries':
            $this->maxEntries = (int)$value;
            return;
        case 'entries':
            $this->minEntries = (int)$value;
            $this->maxEntries = (int)$value;
            return;
        }

        parent::push($name, $value);        
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
        $this->forms = array();

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

    public function check()
    {
        $count = count($this->forms);

        if ($this->minEntries !== null && $count < $this->minEntries) {
            return array('multiple_min', $this->minEntries, $this->printName());
        }

        if ($this->maxEntries !== null && $count > $this->maxEntries) {
            return array('multiple_max', $this->maxEntries, $this->printName());
        }
    }

    public function checkForms()
    {
        $errors = array();

        foreach ($this->forms as $form) {
            $errors = array_merge($errors, $form->check());
        }

        return $errors;
    }

    public function getValue()
    {
        $value = array();

        foreach ($this->forms as $index => $form) {
            $value[$index] = $form->getValues();
        }

        return $value;
    }

    public function getMappingValue()
    {
        $value = array();

        foreach ($this->forms as $index => $form) {
            $value[$index] = $form->getData();
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
        $js = $this->needJs();

        for ($i=0; $i<$this->minEntries; $i++) {
            $this->getForm($i);
        }

        $id = 'multiple'.uniqid(time().mt_rand());
        $prototype = new DataForm($this->parserData, $this->language);

        $html = '<div class="multiple">'."\n";
        $html .= '<div id="'.$id.'" class="multiple-forms">'."\n";
        foreach ($this->forms as $index => $form) {
            $fid = 'multiple'.uniqid(time().mt_rand());
            $html .= '<div class="multiple-element" id="'.$fid.'"/>';
            $html .= $this->getHtmlOfForm($form, $index);
            if ($js) {
                $html .= '<span class="multiple-remove">';
                $html .= '<a href="javascript:Formidable.removeInput(\''.$fid.'\')">';
                $html .= $this->language->translate('remove');
                $html .= '</a><br />';
                $html .= '</span>';
            }
            $html .= '</div>';
        }
        $html .= '</div>'."\n";
        $html .= '<div class="multiple-buttons">';
        if ($js) {
            $html .= '<script type="text/javascript">';
            $html .= 'var '.$id.'_code = ';
            $html .= json_encode($this->getHtmlOfForm($prototype, '{number}'));
            $html .= ";\n";
            $html .= 'Formidable.multiple["'.$id.'"] = '.count($this->forms).";\n";
            $html .= '</script>';
            $html .= '<a href="javascript:Formidable.addInput(\''.$id.'\', '.$id.'_code);">';
            $html .= $this->language->translate('add');
            $html .= '</a>';
        }
        $html .= '</div>';
        $html .= '</div>'."\n";

        return $html;
    }

    public function __sleep()
    {
        return array_merge(array('parserData', 'minEntries', 'maxEntries'), 
            parent::__sleep());
    }

    public function needJs()
    {
        return ($this->minEntries === null
            || $this->maxEntries === null
            || ($this->minEntries != $this->maxEntries));
    }
}
