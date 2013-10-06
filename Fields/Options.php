<?php

namespace Gregwar\Formidable\Fields;

/**
 * Des options sourcées
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Options extends Field
{
    /**
     * Nom de la source d'alimentation
     */
    protected $source;

    /**
     * Champ select correspondant
     */
    protected $parent;

    /**
     * Position des options dans le parent
     */
    protected $position;

    /**
     * Sauvegarde les donnÃ©es poussÃ©es pour les rÃ©percuter plus
     * tard sur les options
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

