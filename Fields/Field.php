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

    /**
     * HTML attributes
     */
    protected $attributes = array();

    /**
     * Field value
     */
    protected $value = false;

    /**
     * Is this field optional ?
     */
    protected $optional = false;

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
    protected $valuechanged = false;

    /**
     * Constraints on the field
     */
    protected $constraints = array();

    /**
     * Multiple values ?
     */
    protected $multiple = false;
    protected $multipleChange = '';

    /**
     * Data for the mapping
     */
    protected $mapping;

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
        case 'required':
            break;
        case 'name':
            $this->name = $value;
            break;
        case 'type':
            break;
        case 'value':
            $this->setValue($value, true);
            break;
        case 'optional':
            $this->optional = true;
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
        case 'multiple':
            $this->multiple = true;
            break;
        case 'multiplechange':
            $this->multipleChange = $value;
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
        if ($this->prettyname)

            return $this->prettyname;
        return $this->name;
    }

    /**
     * Constraints check
     */
    public function check()
    {
        if ($this->valuechanged && $this->readonly) {
            return $this->language->translate('read_only', $this->printName());
        }

	if ($this->multiple && is_array($this->value)) {
            $nodata = implode('', $this->value) === '';

            if (!$this->optional && $nodata) {
                return $this->language->translate('value_required', $this->printName());
            }

            // Répectution du test sur chaque partie
            $values = $this->value;
            foreach ($values as $value) {
                $this->value = $value;
                $error = $this->check();
                if ($error) {
                    $this->value = $values;
                    return $error;
                }
            }
            $this->value = $values;

            return;
        }

        if (null === $this->value || '' === $this->value) {
            if (!$this->optional) {
                return $this->language->translate('value_required', $this->printName());
            }
        } else {
            // Expressions régulière
            if ($this->regex) {
                if (!preg_match('/'.$this->regex.'/mUsi', $this->value)) {
                    return $this->language->translate('bad_format', $this->printName());
                }
            }

            // Longueur minimum et maximum
            if ($this->minlength && strlen($this->value) < $this->minlength) {
                return $this->language->translate('at_least', $this->printName(), $this->minlength);
            }

            if ($this->maxlength && strlen($this->value) > $this->maxlength) {
                return $this->language->translate('not_more', $this->printName(), $this->maxlength);
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
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getMappingName()
    {
        return $this->mapping;
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * Définition de la valeur
     */
    public function setValue($value, $default = false)
    {
        if ($value != $this->value && !$default) {
            $this->valuechanged = true;
        }

        if (is_string($value) || is_int($value) || is_float($value)) {
            $this->value = (string)$value;
        } else {
            $this->value = null;
        }

	if ($this->multiple) {
            $this->value = null;
            if (is_array($value)) {
                foreach ($value as $val) {
                    if (!is_string($val)) {
                        return;
                    }
                }
		$this->value = $value;
            }
        }
    }

    public function getHtmlForValue($given_value = '', $name_suffix = '')
    {
        $html = '<input ';
        foreach ($this->attributes as $name => $value) {
            $html.= $name.'="'.$value.'" ';
        }
        if (!$this->optional) {
            $html.= 'required="required" ';
        }
        $html.= 'type="'.$this->type.'" ';
        $html.= 'name="'.$this->name.$name_suffix.'" ';
        if ($given_value) {
            $html.= 'value="'.htmlspecialchars($given_value).'" ';
        }
        $html.= '/>';

        return $html;
    }

    public function getHtml()
    {
        if (!$this->multiple) {
            return $this->getHtmlForValue($this->value);
	} else {
            $rnd = sha1(mt_rand().time().mt_rand());

	    if (!is_array($this->value) || !$this->value) {
                $this->value = array('');
            }

            $others = '';
	    if ($this->multiple && is_array($this->value)) {
                foreach ($this->value as $id => $value) {
                    $others.="Formidable.addInput(\"$rnd\",\"";
                    $others.=str_replace(
                        array("\r", "\n"), array('', ''),
                        addslashes($this->getHtmlForValue($value, '[]'))
                    );
                    $others.="\");\n";
                }
            }

            $prototype = $this->getHtmlForValue('', '[]');

            $html= '<span id="'.$rnd.'"></span>';
            $html.= '<script type="text/javascript">'.$others.'</script>';
            $html.= "<a href=\"javascript:Formidable.addInput('$rnd','".str_replace(array("\r","\n"),array("",""),htmlspecialchars($prototype))."');".$this->multipleChange."\">".$this->language->translate('add')."</a>";

            return $html;
        }
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
        return $this->multiple;
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
