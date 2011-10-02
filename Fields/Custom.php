<?php

namespace Gregwar\DSD\Fields;

/**
 * Champ de type custom
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Custom extends Field
{
    /**
     * Nom de la source
     */
    protected $source;

    /**
     * Données
     */
    protected $data;

    public function push($name, $value)
    {
        if ($name == 'source') {
            $this->source = $value;
        } else {
            parent::push($name, $value);
        }
    }

    public function getSource()
    {
        return $this->source;
    }

    public function source($data)
    {
        $this->data = $data;
    }

    public function getHtml()
    {
        return $this->data;
    }

    public function check()
    {
        return;
    }
}
