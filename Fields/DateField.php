<?php

namespace Gregwar\Formidable\Fields;

/**
 * Date field
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class DateField extends Field
{
    /**
     * Push save
     */
    private $pushSave = array();

    /**
     * Sub-field
     */
    private $fields;

    public function __sleep()
    {
        return array_merge(parent::__sleep(), array(
            'pushSave', 'fields'
        ));
    }

    public function push($var, $value = null)
    {
	if ($var == 'name' || $var == 'required' || $var == 'mapping') {
	    parent::push($var, $value);
	} else {
	    $this->pushSave[$var] = $value;
	}
    }

    public function setValue($value, $default = false)
    {
	if (is_string($value)) {
	    $value = new \DateTime($value);
	}

	if ($value instanceof \DateTime) {
	    $value = array(
		'day' => $value->format('d'),
		'month' => $value->format('m'),
		'year' => $value->format('Y')
	    );
	}

	$this->value = $value;
    }

    public function getValue()
    {
	if (!$this->check()) {
	    return new \DateTime(sprintf('%04d-%02d-%02d',
		$this->value['year'],
		$this->value['month'],
		$this->value['day']
	    ));
	}

	return null;
    }

    public function check()
    {
	$this->generate();
	$filled = 0;

	foreach ($this->fields as $field) {
	    if ($field->getValue() && !$field->check()) {
		$filled++;
	    }
	}

        if (($this->required && $filled==0)||($filled>0 && $filled<count($this->fields))) {
            return array('bad_date', $this->printName());
	}
    }

    private function generate()
    {
	$this->fields = array();

	$this->fields[] = $this->createSelect('day', range(1, 31));
	$this->fields[] = $this->createSelect('month', range(1, 12));
	$this->fields[] = $this->createSelect('year', range(date('Y')-120, date('Y')));
    }

    private function createSelect($name, $options)
    {
        $select = new Select;
        $select->setLanguage($this->language);
	$select->push('name', $this->getName().'['.$name.']');

	if ($this->value && $this->value[$name]) {
	    $select->setValue($this->value[$name]);
	}

	$this->proxyPush($select);
	$this->buildOptions($select, $options);

	return $select;
    }

    private function buildOptions(&$select, $range)
    {
	foreach ($range as $value) {
	    $option = new Option;
	    $option->setValue($value, true);
	    $option->setLabel($value);
	    $select->addOption($option);
	}
    }

    private function proxyPush($target)
    {
	foreach ($this->pushSave as $var => $value) {
	    $target->push($var, $value);
	}
    }

    public function getHtml()
    {
	$this->generate();
	$html = '';

	foreach ($this->fields as $field) {
	    $html .= $field->getHtml();
	}

	return $html;
    }
}
