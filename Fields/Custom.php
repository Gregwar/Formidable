<?php

namespace Gregwar\Formidable\Fields;

/**
 * Custom field type
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Custom extends Field
{
    /**
     * Source name
     */
    protected $source;

    /**
     * Data
     */
    protected $data;

    public function push($name, $value = null)
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
