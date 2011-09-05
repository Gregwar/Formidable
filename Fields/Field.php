<?php

namespace Gregwar\DSD\Fields;

/**
 * Classe parente des champs
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
abstract class Field
{
    /**
     * Type du champ (à placer dans le type="")
     */
    protected $type = 'text';

    /**
     * Nom du champ
     */
    protected $name;

    /**
     * Code HTML supplémentaire
     */
    protected $attributes = array();

    /**
     * Une value a t-elle été fournie ?
     */
    protected $value = false;

    /**
     * Le champ est t-il optionnel ?
     */
    protected $optional = false;

    /**
     * Expression régulière à respecter
     */
    protected $regex;

    /**
     * Dimensions à respecter
     */
    protected $minlength;
    protected $maxlength;

    /**
     * Nom "joli" (pour les messages d'erreur)
     */
    protected $prettyname;

    /**
     * Lecture seule ?
     */
    protected $readonly = false;

    /**
     * La valeur a t-elle changé ?
     */
    protected $valuechanged = false;

    /**
     * Plusieurs valeurs ?
     */
    protected $multiple = false;
    protected $multipleChange = '';

    /**
     * Permet d'appliquer des contraintes sql
     */
    protected $in = '';
    protected $notin = '';

    /**
     * Donnée de mapping pour la base de données
     */
    protected $sqlname;

    /**
     * Définir un attribut
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Obtenir un attribut 
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
     * A t-il l'attribut $name ?
     */
    public function hasAttribute($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Enlever l'attribut
     */
    public function unsetAttribute($name)
    {
        unset($this->attributes[$name]);
    }

    /**
     * Fonction apellée par le dispatcher
     */
    public function push($name, $value = null)
    {
        switch ($name) {
        case 'class':
            $this->attributes['class'] = $value;
            break;
        case 'name':
            $this->name = $value;
            break;
        case 'type':
            if (!$this->type) {
                $this->type = $value;
            }
            break;
        case 'value':
            $this->setValue($value);
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
        case 'sqlname':
            $this->sqlname = $value;
            break;
        case 'in':
            $this->in = $value;
            break;
        case 'notin':
            $this->notin = $value;
            break;
        case 'prettyname':
            $this->prettyname=$value;
            break;
        case 'readonly':
            $this->readonly=true;
            $this->attributes['readonly'] = 'readonly';
            break;
        default:
            if (preg_match('#^([a-z0-9_-]+)$#mUsi', $name)) {
                if ($value !== null) {
                    $this->attributes[$name] = $value;
                } else {
                    $this->attributes[$name] = $name;
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
     * Test des contraintes
     */
    public function check()
    {
        if ($this->valuechanged && $this->readonly) {
            return 'Le champ '.$this->printName().' est en lecture seule';
        }

        if ($this->multiple && is_array($this->value)) {
            $tmp = $this->value;
            $nodata=true;
            foreach ($tmp as $val) {
                if ($val!="")
                    $nodata=false;
                $this->value = $val;
                $err = $this->check();
                if ($err) {
                    $this->value = $tmp;
                    return $err;
                }
            }
            if (!$this->optional && $nodata)
                return 'Vous devez saisir une valeur pour '.$this->printName();
            $this->value = $tmp;
            return;
        }
        if ($this->value===false || (is_string($this->value) && $this->value=="")) {
            if ($this->optional || $this->multiple)
                return;
            else {
                return 'Vous devez saisir une valeur pour '.$this->printName();
            }
        } else {
            if ($this->regex) {
                if (!eregi($this->regex, $this->value))
                    return 'Le format du champ '.$this->printName().' est incorrect';
            }
            if ($this->minlength && strlen($this->value)<$this->minlength)
                return 'Le champ '.$this->printName().' doit faire au moins '.$this->minlength.' caracteres.';
            if ($this->maxlength && strlen($this->value)>$this->maxlength)
                return 'Le champ '.$this->printName().' ne doit pas dépasser '.$this->maxlength.' caracteres.';

            $err = $this->inNotIn();
            if ($err)
                return $err;
        }
    }

    function inNotIn()
    {
        if ($this->in) {
            if ($this->checkInQuery($this->in)==0)
                return "La valeur du champ ".$this->printName()." doit être présent dans la base";
        }
        if ($this->notin) {
            if ($this->checkInQuery($this->notin)!=0)
                return "La valeur du champ ".$this->printName()." doit pas déja être présent dans la base";
        }
    }

    //XXX: très sale...
    function checkInQuery($v)
    {
        $field = $this->name;

        if (isset($this->sqlname)) {
            $field = $this->sqlname;
        }

        $tmp = explode('.', $v);
        $table = $tmp[0];

        if (isset($tmp[1])) {
            $field = $tmp[1];
        }

        $q = mysql_query('SELECT COUNT(*) AS NB FROM `'.$table.'` WHERE `'.$field.'`="'.mysql_real_escape_string($this->value).'"');
        $r = mysql_fetch_assoc($q);
        return $r['NB'];
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSQLName()
    {
        return $this->sqlname;
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * Définition de la valeur
     */
    public function setValue($val, $default = 0)
    {
        if ($val!=$this->value && !$default)
            $this->valuechanged = true;
        if (!($this->valuechanged && $this->readonly))
            $this->value = $val;
        if ($this->multiple && !is_array($this->value)) {
            $this->value = explode(",",$this->value);
        }
        if ($this->multiple && is_array($this->value)) {
            $valuez = array();
            foreach ($this->value as $v) {
                if ($v!="")
                    $valuez[] = $v;
            }
            $this->value = $valuez;
        }
    }

    public function getHTMLForValue($given_value = '', $name_suffix = '')
    {
        $html = '<input ';
        foreach ($this->attributes as $name => $value) {
            $html.= $name.'="'.$value.'" ';
        }
        $html.= 'type="'.$this->type.'" ';
        $html.= 'name="'.$this->name.$name_suffix.'" ';
        $html.= 'value="'.htmlspecialchars($given_value).'" ';
        $html.= "/>\n";

        return $html;
    }

    public function getHTML()
    {
        if (!$this->multiple) {
            return $this->getHTMLForValue($this->value);
        } else {
            $rnd = sha1(mt_rand().time().mt_rand());

            if (!is_array($this->value) || !$this->value) {
                $this->value = array('');
            }

            $others = '';
            if ($this->multiple && is_array($this->value)) {
                foreach ($this->value as $id => $value) {
                    $others.="DSD.addInput(\"$rnd\",\"";
                    $others.=str_replace(
                        array("\r", "\n"), array('', ''),
                        addslashes($this->getHTMLForValue($value, '['.$id.']'))
                    );
                    $others.="\");\n";
                }
            } 

            $prototype = $this->getHTMLForValue('', '[]');

            $html= '<span id="'.$rnd.'"></span>';
            $html.= '<script type="text/javascript">'.$others.'</script>';
            $html.= "<a href=\"javascript:DSD.addInput('$rnd','".str_replace(array("\r","\n"),array("",""),htmlspecialchars($prototype))."');".$this->multipleChange."\">Ajouter</a>";

            return $html;
        }
    }

    public function getSource()
    {
        return '';
    }

    public function source()
    {
    }

    public function needJs()
    {
        return $this->multiple;
    }
}
