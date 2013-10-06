<?php

namespace Gregwar\Formidable\Fields;

/**
 * Sourced options
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Options extends Field
{
    /**
     * Source name
     */
    protected $source;

    /**
     * Select field
     */
    protected $parent;

    /**
     * Positioning of the options in the parent
     */
    protected $position;

    /**
     * Saving pushed data
     */
    protected $pushSave = array();
    
    public function __sleep()
    {
        return array_merge(parent::__sleep(), array(
            'source', 'parent', 'position', 'pushSave'
        ));
    }

    public function push($name, $value = null)
    {
        if ($name == 'source') {
            $this->source = $value;
        } else {
            $this->pushSave[$name] = $value;
        }
    }

    public function setParent($parent)
    {
        $this->parent = $parent;

        if (null === $this->position) {
            $this->position = $this->parent->countOptions();
        }
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function check()
    {
        return;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function source($options)
    {
        foreach ($options as $key => $label) {
            if (is_object($label)) {
                $key = $label->getKey();
                $label = $label->getValue();
            }

            $option = new Option();

            foreach ($this->pushSave as $name => $value) {
                $option->push($name, $value);
            }

            $option->setValue($key);
            $option->setLabel($label);

            $this->parent->addOption($option, $this->position);
        }
    }
}

