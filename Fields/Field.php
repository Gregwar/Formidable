<?php

namespace Gregwar\Formidable\Fields;

use Gregwar\Formidable\Language\LanguageAware;

/**
 * Parent class for fields
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
abstract class Field extends LanguageAware
{
    /**
     * Field type for the HTML code
     */
    protected $type = 'text';

    /**
     * Field name
     */
    protected $name;
    protected $index = null;
    protected $hook = null;

    /**
     * HTML attributes
     */
    protected $attributes = array();

    /**
     * Field value
     */
    protected $value = null;

    /**
     * Is this field required ?
     */
    protected $required = false;

    /**
     * Regular expression
     */
    protected $regex;

    /**
     * Text size
     */
    protected $minlength;
    protected $maxlength;

    /**
     * Pretty name, for error messages
     */
    protected $prettyname;

    /**
     * Is this field read only?
     */
    protected $readonly = false;

    /**
     * Is the value changed?
     */
    protected $valueChanged = false;

    /**
     * Constraints on the field
     */
    protected $constraints = array();

    /**
     * Data for the mapping
     */
    protected $mapping;

    public function __sleep()
    {
        return array(
            'constraints', 'mapping', 'valueChanged',
            'readonly', 'prettyname', 'minlength', 'maxlength', 'regex', 'required',
            'type', 'name', 'attributes', 'value', 'index'
        );
    }

    /**
     * Define an attribute
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Get an attribute
     */
    public function getAttribute($name)
    {
        if ($this->hasAttribute($name)) {
            return $this->attributes[$name];
        } else {
            return null;
        }
    }

    /**
     * Does this has the attribute $name ?
     */
    public function hasAttribute($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Remove the attribute $name
     */
    public function unsetAttribute($name)
    {
        unset($this->attributes[$name]);
    }

    /**
     * Function called by the dispatcher
     */
    public function push($name, $value = null)
    {
        switch ($name) {
        case 'name':
            $this->setName($value);
            break;
        case 'type':
            break;
        case 'value':
            $this->setValue($value, true);
            break;
        case 'required':
            $this->required = true;
            break;
        case 'regex':
            $this->regex = $value;
            break;
        case 'minlength':
            $this->minlength = $value;
            break;
        case 'maxlength':
            $this->maxlength = $value;
            $this->attributes['maxlength'] = $value;
            break;
        case 'mapping':
            $this->mapping = $value;
            break;
        case 'prettyname':
            $this->prettyname = $value;
            break;
        case 'readonly':
            $this->readonly = true;
            $this->attributes['readonly'] = 'readonly';
            break;
        default:
            if (preg_match('#^([a-z0-9_-]+)$#mUsi', $name)) {
                if (null !== $value) {
                    $this->setAttribute($name, $value);
                } else {
                    $this->setAttribute($name, $name);
                }
            }
        }
    }

    public function printName()
    {
        if ($this->prettyname) {
            return $this->prettyname;
        }

        return $this->name;
    }

    /**
     * Constraints check
     */
    public function check()
    {
        if ($this->valueChanged && $this->readonly) {
            return array('read_only', $this->printName());
        }

        if (null === $this->value || '' === $this->value) {
            if ($this->required) {
                return array('value_required', $this->printName());
            }
        } else {
            // Expressions régulière
            if ($this->regex) {
                if (!preg_match('/'.$this->regex.'/mUsi', $this->value)) {
                    return array('bad_format', $this->printName());
                }
            }

            // Longueur minimum et maximum
            if ($this->minlength && strlen($this->value) < $this->minlength) {
                return array('at_least', $this->printName(), $this->minlength);
            }

            if ($this->maxlength && strlen($this->value) > $this->maxlength) {
                return array('not_more', $this->printName(), $this->maxlength);
            }
        }

        // Contraintes custom
        foreach ($this->constraints as $constraint) {
            $err = $constraint($this->value);
            if ($err) {
                return $err;
            }
        }
    }

    public function getName()
    {
        $name = $this->getBaseName();

        if ($this->index !== null) {
            $name .= '['.$this->index.']';
        }

        if ($this->hook !== null) {
            $hook = $this->hook;
            return $hook($name);
        } else {
            return $name;
        }
    }

    public function getBaseName()
    {
        return $this->name;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function hookName(\Closure $hook)
    {
        $this->hook = $hook;
    }

    public function setName($name)
    {
        if (preg_match('#^(.+)\[(.*)\]$#mUsi', $name, $match) == 1) {
            $this->name = $match[1];
            $this->index = $match[2] ?: '';
        } else {
            $this->name = $name;
        }
    }

    public function getMappingName()
    {
        return $this->mapping;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getMappingValue()
    {
        return $this->getValue();
    }

    /**
     * Définition de la valeur
     */
    public function setValue($value, $default = false)
    {
        if ($value != $this->value && !$default) {
            $this->valueChanged = true;
        }

        if (is_string($value) || is_int($value) || is_float($value)) {
            $this->value = (string)$value;
        } else {
            $this->value = null;
        }
    }

    public function __toString()
    {
        return $this->getHtml();
    }

    public function getHtml()
    {
        $html = '<input ';

        foreach ($this->attributes as $name => $value) {
            $html.= $name.'="'.$value.'" ';
        }

        if ($this->required) {
            $html.= 'required="required" ';
        }

        $html.= 'type="'.$this->type.'" ';
        $html.= 'name="'.$this->getName().'" ';

        if (($value = $this->getValue()) !== null) {
            $html.= 'value="'.htmlspecialchars($value).'" ';
        }

        $html.= '/>';

        return $html;
    }

    public function getSource()
    {
        return '';
    }

    public function source($values)
    {
    }

    public function needJs()
    {
        return false;
    }

    public function addConstraint($closure)
    {
        if (!$closure instanceof \Closure) {
            throw new \InvalidArgumentException('addConstraint() argument should be a \Closure');
        }

        $this->constraints[] = $closure;
    }

    public function readOnly()
    {
        return $this->readonly;
    }
}
