<?php

namespace Gregwar\DSD\Fields;

/**
 * Des options sourcées
 * XXX: à réécrire pour créer un select et y injecter des options
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Options extends Field
{

    private $source;
    private $parent;
    private $pushSave = array();
    private $pos;

    public function push($name, $value)
    {
        if ($name == "source") {
            $this->source = $value;
        } else {
            $pushSave[] = array($name, $value);
            parent::push($name, $value);
        }
    }

    public function setParent($p)
    {
        $this->parent = $p;
        $this->pos = $this->parent->countOptions();
    }

    public function check()
    {
        return;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function source($data)
    {
        foreach ($data as $k => $v) {
            if (is_object($v)) {
                $k = $v->getKey();
                $v = $v->getValue();
            }
            $opt = new DSDOption();
            foreach ($this->pushSave as $p) {
                $opt->push($p[0], $p[1]);
            }
            $opt->push("value", $k);
            $opt->setLabel($v);
            $this->parent->addOption($opt, $this->pos);
        }
    }
}

