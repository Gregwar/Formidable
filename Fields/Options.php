<?php

namespace Gregwar\DSD\Fields;

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
    private $source;

    /**
     * Champ select correspondant
     */
    private $parent;

    /**
     * Position des options dans le parent
     */
    private $position;

    /**
     * Sauvegarde les données poussées pour les répercuter plus
     * tard sur les options
     */
    private $pushSave = array();

    public function push($name, $value)
    {
        if ($name == 'source') {
            $this->source = $value;
        } else {
            $pushSave[$name] = $value;
            parent::push($name, $value);
        }
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
        $this->position = $this->parent->countOptions();
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
        foreach ($options as $key => $value) {
            if (is_object($value)) {
                $key = $value->getKey();
                $value = $value->getValue();
            }

            $option = new Option();
            foreach ($this->pushSave as $name => $value) {
                $option->push($name, $value);
            }
            $option->setValue($key);
            $option->setLabel($value);

            $this->parent->addOption($option, $this->position);
        }
    }
}

